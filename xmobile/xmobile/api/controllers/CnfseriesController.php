<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\filters\auth\QueryParamAuth;

class CnfseriesController extends \yii\rest\ActiveController
{
  use Respuestas;

  public $modelClass = 'backend\models\User';

  public function init() {
      parent::init();
      Yii::$app->user->enableSession = false;
  }

  /*public function behaviors() {
      $behaviors = parent::behaviors();
      $behaviors['authenticator'] = [
          'tokenParam' => 'access-token',
          'class' => QueryParamAuth::className(),
      ];
      return $behaviors;
  }*/

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
  
  public function actionIndex(){
    
    $sql= " Select Name,Series,id from series where Document='13'";
    $centro=Yii::$app->db->createCommand($sql)->queryAll();
    //Yii::error("centro de costos".json_encode($centro));
    if (count($centro)){
      return $this->correcto($centro);
    }
    return $this->error('Sin datos');
  }  
}
