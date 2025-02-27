<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Cabeceradocumentos;
use backend\models\Pagos;

class AutorizaciondocController extends ActiveController
{
  use Respuestas;
  
  public $modelClass = 'backend\models\Cabeceradocumentos';
  

  protected function verbs()
  {
    return [
      'index'  => ['POST', 'HEAD'],
      'view'   => ['POST', 'HEAD'],
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

  
  public function actionIndex(){
    $idUser = Yii::$app->request->post('idUsuario');
    $docType = Yii::$app->request->post('tipoDoc');
    $codigoDoc = Yii::$app->request->post('codDoc');  

    if($docType=='PAGO'){
      $respuesta = Pagos::find()
      ->where(" usuario=".$idUser."  and  recibo='".$codigoDoc."' and  anulaAutorizado=1")
      ->all();
      Yii::error('data doc: ' .json_encode($respuesta));
    }else{
      $respuesta = Cabeceradocumentos::find()
      ->where(" idUser=".$idUser."   and  idDocPedido='".$codigoDoc."' and  anulaAutorizado=1")
      ->all();
      Yii::error('data doc: ' .json_encode($respuesta));
    }

    if(count($respuesta)>0){
      return $this->correcto(true,true);
    }
    else{
      //return $this->correcto(false, "incorrecto");
      return $this->error(false);
    }
  }
}
