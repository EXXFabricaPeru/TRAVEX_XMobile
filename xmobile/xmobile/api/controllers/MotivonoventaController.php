<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Usuariosincronizamovil;

class MotivonoventaController extends ActiveController
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
    $motivonoVenta= Yii::$app->db->createCommand("select * from motivonoventa order by id limit 1000 OFFSET {$salto}")->queryAll();

    if (count($motivonoVenta) > 0) {
      return $this->correcto($motivonoVenta);
    }
    return $this->error('Sin datos',201);
  }

  public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from motivonoventa")->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Motivonoventa');
      return $this->correcto($resultado, 'OK'); 
  }

}
