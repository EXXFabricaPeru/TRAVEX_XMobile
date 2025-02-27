<?php

namespace api\controllers\v2;

use backend\models\BonificacionDe2;
use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class BonificacioncompraController extends ActiveController
{
use Respuestas;
public $modelClass = 'backend\models\Usuario';

protected function verbs()
  {
    return [
      //'index' => ['GET', 'HEAD'],
      //'view' => ['GET', 'HEAD'],
      'index' => ['POST'],
      'view' => ['POST'],
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
      $usuario=Yii::$app->request->post('usuario');
      $salto=Yii::$app->request->post('pagina');
      $resultado= Yii::$app->db->createCommand("select * from v_bonificacion_detalle_compra  limit 1000 OFFSET {$salto}")->queryAll();

	    if (count($resultado) > 0) {
	        return $this->correcto($resultado, 'OK');
	    }
	    return $this->correcto([], "No se encontro Datos", 201);
  }

  public function actionContador(){
    $usuario=Yii::$app->request->post('usuario');
    $resultado=Yii::$app->db->createCommand("Select count(*) as contador from v_bonificacion_detalle_compra")->queryOne();
    $usuariosincronizamovil= new Usuariosincronizamovil();
    $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Bonificacionde2');
    return $this->correcto($resultado, 'OK'); 
}

}
