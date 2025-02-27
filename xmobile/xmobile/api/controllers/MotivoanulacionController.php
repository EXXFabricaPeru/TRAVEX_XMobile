<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Usuariosincronizamovil;

class MotivoanulacionController extends ActiveController
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

  public function actionCreate()
  { 
    $usuario=Yii::$app->request->post('usuario');
    $salto=Yii::$app->request->post('pagina');
    $motivosAnulacion= Yii::$app->db->createCommand("select * from motivosanulacion order by id limit 1000 OFFSET {$salto}")->queryAll();

    /*$motivosAnulacion = Yii::$app->db->createCommand('select * from motivosanulacion')
                  ->queryAll();*/
    if (count($motivosAnulacion) > 0) {
      return $this->correcto($motivosAnulacion);
    }
    return $this->error('Sin datos',201);
  }

  public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from motivosanulacion")->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Motivoanulacion');
      return $this->correcto($resultado, 'OK'); 
  }

}
