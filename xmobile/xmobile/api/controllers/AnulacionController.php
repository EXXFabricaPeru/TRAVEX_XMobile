<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Anulacion;

class AnulacionController extends ActiveController
{
  use Respuestas;
  
  public $modelClass = 'backend\models\Anulacion';
  

  protected function verbs()
  {
    return [
      'index'  => ['GET', 'HEAD'],
      'view'   => ['GET', 'HEAD'],
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

  
  public function actionCreate(){	  
	$fechahora = Yii::$app->request->post('fechahora');
	$tipodocumento = Yii::$app->request->post('tipodocumento');
	$iddocumento = Yii::$app->request->post('iddocumento');
	$usuario = Yii::$app->request->post('usuario');
	$registrar = new Anulacion();
	$registrar->id = 0;
	$registrar->fechahora = $fechahora;
	$registrar->tipodocumento = $tipodocumento;
	$registrar->iddocumento = $iddocumento;
	$registrar->usuario = $usuario;
	if($registrar->save(false)){
		$this->correcto($registrar, "Anulacion registrada");
	}
	else{
		return $this->error('Error al registrar',100);
	}
  }
}
