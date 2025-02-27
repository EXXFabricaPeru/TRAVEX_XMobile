<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use api\traits\Respuestas;
use backend\models\Sap;
class FacturasappController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\User';

    protected function verbs() {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate() {
        $data = Yii::$app->request->post();
        $resp = Yii::$app->db->createCommand("CALL pa_obtenerFacturaSap(:usuario,:tipo,:texto)")
                ->bindValue(':usuario', $data['usuario'])
                ->bindValue(':tipo', $data['tipo'])
                ->bindValue(':texto', $data['texto'])
                ->queryAll();
        if (count($resp) > 0) {
            $arr = [];
            foreach ($resp as $key => $val) {
				//array_push($arr, ["factura" => $val, "facturasproductos" => $this->detallereserva($val['DocNum'])]);
                switch ($data['tipo']) {
                    case(1)://Facturas
                        array_push($arr, ["factura" => $val, "facturasproductos" => $this->detallereserva("SELECT * FROM facturasproductos WHERE DocNum = " . $val['DocNum']) ]);
                        break;
                    case(2)://Detalles
                        array_push($arr, ["factura" => $val, "facturasproductos" => $this->detallereserva("SELECT * FROM sapofertasdetalle WHERE DocEntry = '".$val['DocEntry']."' ") ]);
                        break;
                }
            }
            return $arr;
        } else {
            return $this->correcto([], "Sin datos", 201);
        }
    }

    private function detallereserva($sql) {
        $resp = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($resp as $key => $producto) {
            $resp[$key]["Serie"] = $this->series($producto["ItemCode"]);
        }
        if (count($resp) > 0) {
            return $resp;
        } else {
            return [];
        }
    }

    private function series($item) {
        $series = Seriesproductos::find()
                ->where("ItemCode = '{$item}' AND Status = 1")
                ->all();
        if (count($series)) {
            return $series;
        }
        return 0;
    }

    public function actionDocumentos(){
        $data = Yii::$app->request->post();
        
        $usuario = $data['usuario'];
        $tipo =  $data['tipo'];
        $texto = $data['texto'];
        
        $sql = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
        $usuario = Yii::$app->db->createCommand($sql)->QueryOne();
        $codEmpleadoVenta = $usuario["codEmpleadoVenta"];
        
        $clientes = "(SELECT clientes.CardCode from clientes ".
                    "LEFT JOIN clientesgrupo ON clientesgrupo.`Code` = clientes.`GroupCode` ".
                    "LEFT JOIN territorios ON territorios.`TerritoryID` = clientes.`Territory` ".
                    "LEFT JOIN industrias ON industrias.`id` = clientes.`Industry` ".
                    "WHERE SalesPersonCode=".$codEmpleadoVenta." ORDER BY CardName)";

        $sql = "SELECT * FROM `vi_documentosimportados1` WHERE CardCode IN ".$clientes;

        $cabecera = Yii::$app->db->createCommand($sql)->queryAll();
        $resultado = [];
      if (count($cabecera) > 0) {
          foreach($cabecera as $c){
              $sql = "SELECT * FROM `vi_documentosimportadosdetalle1` WHERE DocNum = '".$c["DocNum"]."'";
              $detalle = Yii::$app->db->createCommand($sql)->queryAll();
              if (count($detalle) > 0) {
                array_push($resultado, ["factura" => $c, "facturasproductos" => $detalle ]);
              }
          }
          return $resultado;
      }
      else{
        return $this->correcto([], "Sin datos", 201);
      }
    }
    public function actionConsultacufsap(){
        $sap= new Sap();
        
        $iddoc=Yii::$app->request->post('iddoc');
        $nit=Yii::$app->request->post('nit');
        $accion=Yii::$app->request->post('accion');

        $sql = "SELECT DocEntry,DocType, 1 AS CANTIDAD FROM `cabeceradocumentos` WHERE idDocPedido = '".$iddoc."'";
        $detalle = Yii::$app->db->createCommand($sql)->queryAll();
        
        if (count($detalle) > 0) {
            foreach($detalle as $c){
                Yii::error("rafael:".$c["DocType"]);

                if ($c["DocType"] == 'DFA') {
                    Yii::error("CONULTA CUF EN SAP: ".$c["DocEntry"]." ".$nit." ".$accion  );
                    $resultado = $sap->Consulta_cuf_sap($c["DocEntry"],$nit,$accion);
                    Yii::error($resultado);
                    return $this->correcto($resultado, 'OK');
                }else{

                    return $this->correcto($c, 'OK');
                }
            }
        }
        
        
    }
}
