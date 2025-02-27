<?php

namespace api\controllers;

use backend\models\Bancos;
use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class BancosController extends ActiveController
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
    $resultado= Yii::$app->db->createCommand("select * from bancos order by id limit 1000 OFFSET {$salto}")->queryAll();

    //	$resultado = Yii::$app->db->createCommand("SELECT * FROM bancos")->queryAll();
	    if (count($resultado) > 0) {
	        return $this->correcto($resultado, 'OK');
	    }
	    return $this->correcto([], "No se encontro Datos", 201);
  }

  public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from bancos")->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Bancos');
     
      return $this->correcto($resultado, 'OK'); 
  }

}
