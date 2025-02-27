<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Reimpresion;

class ReimpresionController extends ActiveController
{
  use Respuestas;
  
  public $modelClass = 'backend\models\Reimpresion';
  

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
    $reimpresiones = Yii::$app->request->post('reimpresiones');
    if (count($reimpresiones) > 0) {
      foreach($reimpresiones as $reimpresion){
      	$fechahora = $reimpresion["fechahora"];//Yii::$app->request->post('fechahora');
	      $tipodocumento = $reimpresion["tipodocumento"];//Yii::$app->request->post('tipodocumento');
	      $iddocumento = $reimpresion["iddocumento"];//Yii::$app->request->post('iddocumento');
    	  $usuario = $reimpresion["usuario"];//Yii::$app->request->post('usuario');
	      $equipo = $reimpresion["equipo"];//Yii::$app->request->post('equipo');
	      $registrar = new Reimpresion();
  	    $registrar->id = 0;
  	    $registrar->fechahora = $fechahora;
	      $registrar->tipodocumento = $tipodocumento;
  	    $registrar->iddocumento = $iddocumento;
	      $registrar->usuario = $usuario;
  	    $registrar->equipo = $equipo;
        if(!$registrar->save(false)) return $this->error('Error al registrar', 100);
      }
    	return $this->correcto([], "reimpresiones registradas");
    }
    else{
      return $this->error("no se registraron datos");
    }
  }
}
