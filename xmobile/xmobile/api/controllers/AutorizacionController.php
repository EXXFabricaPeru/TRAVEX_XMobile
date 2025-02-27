<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Autorizacion;
use yii\filters\auth\QueryParamAuth;

class AutorizacionController extends ActiveController
{

  use Respuestas;

  public $modelClass = 'backend\models\Autorizacion';
  /*public function init()
  {
    parent::init();
    \Yii::$app->user->enableSession = false;
  }

  public function behaviors()
  {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
      'tokenParam' => 'access-token',
      'class' => QueryParamAuth::className(),
    ];
    return $behaviors;
  }*/
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

  public function actionIndex()
  {
    $data = Yii::$app->request->post();    
    $autorizacion = $data["autorizacion"];
    $resultado = Autorizacion::find()->where(['=', 'autorizacion', $autorizacion  ])->One();
    if (count($resultado) > 0) {
      return $this->correcto('1');
    }
    return $this->correcto('0');
  }
}
