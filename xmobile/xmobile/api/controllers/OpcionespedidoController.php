<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use DateTime;
use yii\rest\ActiveController;
use backend\models\LineasPedidos;
use backend\models\CabecerasPedidos;
use yii\filters\auth\QueryParamAuth;
use backend\models\OpcionesDocumento;

class OpcionespedidoController extends ActiveController
{
    use Respuestas;
    public $modelClass = 'backend\models\OpcionesPedido';

    public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

//    public function behaviors() {
//        $behaviors = parent::behaviors();
//        $behaviors['authenticator'] = [
//            'tokenParam' => 'access-token',
//            'class' => QueryParamAuth::className(),
//        ];
//        return $behaviors;
//    }

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

    public function actionCreate()
    {
      $confDocumento = OpcionesDocumento::find()
                        ->where([
                          "configuracionId" => Yii::$app->request->post('configuracionId'),
                          "tipoDocumento" =>  Yii::$app->request->post('tipoDocuento')
                        ])
                        ->one();
      $confDocumento = is_null($confDocumento)?new OpcionesDocumento():$confDocumento;
      $confDocumento->numeroDocumento = Yii::$app->request->post('numeroDocumento');
      $confDocumento->formatoPapel    = Yii::$app->request->post('formatoPapel');
      $confDocumento->estadoDocumento = Yii::$app->request->post('estadoDocumento');
      $confDocumento->almacen         = Yii::$app->request->post('almacen');
      $confDocumento->opcionImprimir  = Yii::$app->request->post('opcionImprimir');
      $confDocumento->opcionCancelar  = Yii::$app->request->post('opcionCancelar');
      $confDocumento->configuracionId = Yii::$app->request->post('configuracionId');
      $confDocumento->tipoDocumento    = Yii::$app->request->post('tipoDocumento');
      if ($confDocumento->save()) {
        return [
          "code" => 200,
          "respuesta" => $confDocumento,
          "mensaje" => "Registro Correcto",
        ];
      }
      return [
        "code" => 203,
        "respuesta" => [],
        "mensaje" => "Registro Incorrecto",
      ];
    }

    public function actionFindonebyconfiguracionid($configuracionId){
        $oOpcionesDocumento = OpcionesDocumento::find()->where(['like','configuracionId',$configuracionId])->one();
        if(is_object($oOpcionesDocumento)) {
            return $this->correcto($oOpcionesDocumento);
        } else {
            return $this->error();
        }
    }


}
