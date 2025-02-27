<?php

namespace api\controllers;

use backend\models\Productos;
use backend\models\Copiaproductos;
use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Grupoproductodocificacion;
use backend\models\Usuariosincronizamovil;

class GrupoproductodocificacionController extends ActiveController
{

  use Respuestas;
  public $modelClass = 'backend\models\Grupoproductodocificacion';

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
  
    public function actionCreate(){
      $usuario=Yii::$app->request->post('usuario');
      $salto=Yii::$app->request->post('pagina');
      $data= Yii::$app->db->createCommand("select * from grupoproductodocificacion order by id limit 1000 OFFSET {$salto}")->queryAll();

	   //	$data = Grupoproductodocificacion::find()->all();
        if (count($data) > 0) {
            return $this->correcto($data);
        }
        return $this->error('Sin datos', 201);
  }
  
  public function actionContador(){
    $usuario=Yii::$app->request->post('usuario');
    $resultado=Yii::$app->db->createCommand("Select count(*) as contador from grupoproductodocificacion")->queryOne();
    $usuariosincronizamovil= new Usuariosincronizamovil();
    $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Grupoproductodocificacion');
   
    return $this->correcto($resultado, 'OK'); 
}
}
