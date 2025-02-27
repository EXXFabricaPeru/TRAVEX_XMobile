<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\filters\auth\QueryParamAuth;
use backend\models\Equipox;
use backend\models\Usuariosincronizamovil;


class SeriesproductosController extends \yii\rest\ActiveController
{
  use Respuestas;

  public $modelClass = 'backend\models\Seriesproductos';

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
      // 'index' => ['GET', 'HEAD'],
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
    $series= Yii::$app->db->createCommand("select * from vi_seriesproductos order by id limit 1000 OFFSET {$salto}")->queryAll();

    /*
	  $data = Yii::$app->request->post();
	  $sql = "CALL pa_seriesproductos('{$data["equipo"]}');";
	  $series = Yii::$app->db->createCommand($sql)->queryAll();
    */

	  if (count($series)){
      return $this->correcto($series);
    }
    return $this->error('Sin datos',201);
  }
  
  public function actionContador(){
    $usuario=Yii::$app->request->post('usuario');
    $resultado=Yii::$app->db->createCommand("Select count(*) as contador from seriesproductos")->queryOne();
    $usuariosincronizamovil= new Usuariosincronizamovil();
    $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Seriesproductos');
    return $this->correcto($resultado, 'OK'); 
 }
  public function actionSeriestraspaso() {
    $itemcode = Yii::$app->request->post('itemcode');
    $whscode = Yii::$app->request->post('whscode');
    $lotes = Yii::$app->db->createCommand("SELECT '' AS checkedvalue, seriesproductos.* FROM seriesproductos WHERE WsCode = :WHS AND ItemCode = :ITM AND Status <> 0")
        ->bindValue(':WHS', $whscode)
        ->bindValue(':ITM', $itemcode)
        ->queryAll();
    if (count($lotes) > 0) {
        return $this->correcto($lotes);
    }
    return $this->correcto([],'Sin datos',201);
}
}
