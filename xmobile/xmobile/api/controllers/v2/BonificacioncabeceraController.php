<?php

namespace api\controllers\v2;

use backend\models\BonificacionCa;
use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class BonificacioncabeceraController extends ActiveController
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
      $resultado= Yii::$app->db->createCommand("select * from v_bonificacion_cabezera where idUser=".$usuario." limit 1000 OFFSET {$salto}")->queryAll();

      Yii::error("Bonificaciones77: ".json_encode($resultado));
	    if (count($resultado) > 0) {
	        return $this->correcto($resultado, 'OK');
	    }
	    return $this->correcto([], "No se encontro Datos", 201);
    }
    public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from v_bonificacion_cabezera where idUser=".$usuario)->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Bonificacioncabecera');
      return $this->correcto($resultado, 'OK'); 
  }

}
