<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Usuariosincronizamovil;


class PromocionesController extends ActiveController
{
  public $modelClass = 'backend\models\Promociones';
  use Respuestas;
  public function init()
  {
      parent::init();
      \Yii::$app->user->enableSession = false;
  }

  protected function verbs()
  {
    return [
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
    $resultado= Yii::$app->db->createCommand("select * from promociones  limit 1000 OFFSET {$salto}")->queryAll();
    if (count($resultado)) {
      return $this->correcto($resultado);
    }
    return $this->error('Sin datos', 201);
  }
  public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from promociones")->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Promociones');
      return $this->correcto($resultado, 'OK'); 
  }

}
