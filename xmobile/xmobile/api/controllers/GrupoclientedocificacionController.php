<?php

namespace api\controllers;

use backend\models\Productos;
use backend\models\Copiaproductos;
use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Grupoclientedocificacion;

class GrupoclientedocificacionController extends ActiveController
{

  use Respuestas;
  public $modelClass = 'backend\models\Grupoclientedocificacion';

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
  
  public function actionIndex(){
		$data = Grupoclientedocificacion::find()->all();
        if (count($data) > 0) {
            return $this->correcto($data);
        }
        return $this->error('Sin datos', 201);
  }
}
