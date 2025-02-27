<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;

class UnidadmedidarelController extends ActiveController
{
use Respuestas;
public $modelClass = 'backend\models\Usuario';

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
    	$resultado = Yii::$app->db->createCommand("SELECT * FROM vi_uomrel")->queryAll();
	    if (count($resultado) > 0) {
	        return $this->correcto($resultado, 'OK');
	    }
	    return $this->correcto([], "No se encontro Datos", 201);
    }

}
