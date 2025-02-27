<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Usuariosincronizamovil;

class MigratesmovilController extends ActiveController
{
  use Respuestas;

  public $modelClass = 'backend\models\Usuario';

  /*public function init() {
      parent::init();
      \Yii::$app->user->enableSession = false;
  }

  public function behaviors() {
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
      //'index'  => ['GET', 'HEAD'],
      //'view'   => ['GET', 'HEAD'],
      'index'  => ['POST'],
      'view'   => ['POST'],
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
    //$usuario=Yii::$app->request->post('usuario');
    //$salto=Yii::$app->request->post('pagina');
    //$resultado = Yii::$app->db->createCommand("select * from migratesmovil order by id limit 1000 OFFSET {$salto}")->queryAll();
    $resultado = Yii::$app->db->createCommand("select tabla as 'table',campo as nameCampo, tipodato as typeCampo from migratesmovil")->queryAll();
    if (count($resultado)){
      return $this->correcto($resultado);
    }
    return $this->error('Sin datos',201);
  }
  public function actionContador(){
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from migratesmovil")->queryOne();
      return $this->correcto($resultado, 'OK'); 
  }

}
