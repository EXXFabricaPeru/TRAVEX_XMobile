<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\LineasPedidos;
use yii\filters\auth\QueryParamAuth;
use backend\models\Servislayer;
use backend\models\Cabeceradocumentos;
use backend\models\Detalledocumentos;
use backend\models\Seriesproductos;
use api\traits\Respuestas;

class DocumentosController extends ActiveController
{

    use Respuestas;

    public $modelClass = 'backend\models\User';

    /* public function init() {
      parent::init();
      \Yii::$app->user->enableSession = false;
      }

      public function behaviors() {
      $behaviors = parent::behaviors();
      $behaviors['authenticator'] = [
      'tokenParam' => 'access-token',
      'class' => QueryParamAuth::className(),
      ];
      return $behaviors;
      } */

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $sql = "CALL pa_obtenerFacturaSap(" . $data['usuario'] . ", '" . $data['tipo'] . "', '" . $data['texto'] . "')";
        $resp = Yii::$app->db->createCommand($sql)->queryAll();
        $resp2 = [];
        foreach($resp as $elem) {
            $sql2 = "SELECT OcrCode2 FROM facturasproductos WHERE DocNum = '".$elem["DocNum"]."'";
            $arrayCentros = [];
            $centros = Yii::$app->db->createCommand($sql2)->queryAll();
            foreach($centros as $centro){
                if (!in_array($centro["OcrCode2"], $arrayCentros)) {
                    array_push($arrayCentros, $centro["OcrCode2"]);
                }
            }
            $elem["UnidadNegocio"] = implode( ", ", $arrayCentros );
            array_push($resp2, $elem);
        }
        if (count($resp2) > 0) {
            return $this->correcto($resp2, 'OK');
        } else {
            return $this->correcto([], "Sin datos", 201);
        }
    }

    public function actionFacturareserva()
    {
        /* $resp = Yii::$app->db->createCommand("SELECT * FROM cabeceradocumentos WHERE ( Reserve = 1 OR DocDueDate > DocDate ) AND DocType = 'DFA'") */
        $data = Yii::$app->request->post();
        $sql = "CALL pa_obtenerFacturaSap(" . $data['usuario'] . ", '" . $data['tipo'] . "', '" . $data['texto'] . "')";
        $resp = Yii::$app->db->createCommand($sql)->queryAll();
        Yii::error("Facturas de Reserva: " . json_encode($resp));
        if (count($resp) > 0) {
            return $this->correcto($resp, 'OK');
        } else {
            return $this->correcto([], "Sin datos", 201);
        }
    }

    public function actionDetallereserva()
    {
        $data = Yii::$app->request->post();
        $resp = Yii::$app->db->createCommand("SELECT * FROM facturasproductos WHERE DocNum = " . $data['id'])
            ->queryAll();
        foreach ($resp as $key => $producto) {
            $resp[$key]["Serie"] = $this->series($producto["ItemCode"]);
        }
        if (count($resp) > 0) {
            return $this->correcto($resp, "OK");
        } else {
            return $this->correcto([], "Sin datos", 201);
        }
    }

        public function actionDetalleproductos()
    {
        $data = Yii::$app->request->post();
        $tipo = $data['tipo'];
        if ($tipo === "P"){
            $resp = Yii::$app->db->createCommand("SELECT * FROM pedidosproductos WHERE DocNum = " . $data['id'])
            ->queryAll();
        } else {
            $resp = Yii::$app->db->createCommand("SELECT * FROM facturasproductos WHERE DocNum = " . $data['id'])
            ->queryAll();
        }
        foreach ($resp as $key => $producto) {
            $resp[$key]["Serie"] = $this->series($producto["ItemCode"]);
        }
        if (count($resp) > 0) {
            return $this->correcto($resp, "OK");
        } else {
            return $this->correcto([], "Sin datos", 201);
        }
    }

    private function series($item)
    {
        $series = Seriesproductos::find()
            ->where("ItemCode = '{$item}' AND Status = 1")
            ->all();
        if (count($series)) {
            return $series;
        }
        return 0;
    }
    // FUNCION DE CREADA EM CSAPEK
    public function actionConsultaestadodocumento(){
       
        Yii::error("CONSULTA ESTADO DEL DOCUMENTO");
        $codigo=Yii::$app->request->post('codigo');

        $sql = "SELECT ESTADO FROM  cabeceradocumentos d where d.idDocPedido = '" . $codigo . "'";
        $resp = Yii::$app->db->createCommand($sql)->queryAll();
        Yii::error("Estado del Documento " . json_encode($resp));
        if (count($resp) > 0) {
            return $this->correcto($resp, 'OK');
        } else {
            return $this->correcto([], "Sin datos", 201);
        }
    }

}
