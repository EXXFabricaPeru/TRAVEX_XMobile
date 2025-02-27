<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use api\traits\Respuestas;
use yii\data\ActiveDataProvider;

class FacturasappmovilController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\ViDocumentosimportados';

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

	public function actionSearch() {
        $sql = "SELECT * FROM `vi_documentosimportados` WHERE CardCode IN ('".$_GET['cardcode']."') AND DocType = 'DFA'  ";
        $cabecera = Yii::$app->db->createCommand($sql)->queryAll();
        $resultado = [];
        if(count($cabecera) > 0) {
			foreach($cabecera as $c){
				  $sql = "SELECT * FROM `vi_documentosimportadosdetalle` WHERE DocNum = '".$c["DocNum"]."'";
				  $detalle = Yii::$app->db->createCommand($sql)->queryAll();
				  if (count($detalle) > 0) {
					array_push($resultado, ["factura" => $c, "facturasproductos" => $detalle ]);
				  }
			}
            return $resultado;
        }else{
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

    public function actionCreate(){
		set_time_limit(0);
        $data = Yii::$app->request->post();
        $usuario = $data['usuario'];
        $tipo =  $data['tipo'];
        $texto = $data['texto'];
        $sql = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
        $usuario = Yii::$app->db->createCommand($sql)->QueryOne();
        $codEmpleadoVenta = $usuario["codEmpleadoVenta"];
        /*
        $clientes = "(SELECT clientes.CardCode from clientes ".
                    "LEFT JOIN clientesgrupo ON clientesgrupo.`Code` = clientes.`GroupCode` ".
                    "LEFT JOIN territorios ON territorios.`TerritoryID` = clientes.`Territory` ".
                    "LEFT JOIN industrias ON industrias.`id` = clientes.`Industry` ".
                    "WHERE SalesPersonCode=".$codEmpleadoVenta." ORDER BY CardName)";
        $sql = "SELECT * FROM `vi_documentosimportadosmovil` WHERE CardCode IN ".$clientes;
        */
        $sql = "SELECT * FROM `vi_documentosimportadosmovil` WHERE SalesPersonCode=".$codEmpleadoVenta;
        $cabecera = Yii::$app->db->createCommand($sql)->queryAll();
        $resultado = [];
        if(count($cabecera) > 0) {
			  foreach($cabecera as $c){
				  $sql2 = "SELECT * FROM `vi_documentosimportadosdetalle2` WHERE DocNum = '".$c["DocNum"]."'";
                  $detalle = Yii::$app->db->createCommand($sql2)->queryAll();
                  array_push($resultado, ["factura" => $c, "facturasproductos" => $detalle ]);
                  /*
				  if (count($detalle) > 0) {
					array_push($resultado, ["factura" => $c, "facturasproductos" => $detalle ]);
                  }
                  */
			  }
            return $resultado;
        }else{
			return $this->correcto([], "Sin datos", 201);
        }
    }
}
