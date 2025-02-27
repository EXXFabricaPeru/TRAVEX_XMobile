<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\filters\auth\QueryParamAuth;
use backend\models\Usuariosincronizamovil;


class CentrocostosController extends \yii\rest\ActiveController
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
      //'index' => ['GET', 'HEAD'],
      //'view' => ['GET', 'HEAD'],
      'index' => ['POST'],
      'view' => ['POST'],
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
  
  public function actionCreate(){
    $usuario=Yii::$app->request->post('usuario');
    $salto=Yii::$app->request->post('pagina');
    $centro= Yii::$app->db->createCommand("select * from centroscostos order by idcentro limit 1000 OFFSET {$salto}")->queryAll();

    /*$sql= " Select PrcCode,PrcName from centroscostos";
    $centro=Yii::$app->db->createCommand($sql)->queryAll();*/
    //Yii::error("centro de costos".json_encode($centro));
    if (count($centro)){
      return $this->correcto($centro);
    }
    return $this->error('Sin datos');
  }  

  public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from centroscostos")->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Centrocostos');
      return $this->correcto($resultado, 'OK'); 
  }
}
