<?php

namespace api\controllers;

use api\traits\Respuestas;
use backend\models\Series;
use backend\models\Seriesmarketing;
use backend\models\Seriesproductos;
use Carbon\Carbon;
use Yii;
use yii\filters\auth\QueryParamAuth;

class SeriesController extends \yii\rest\ActiveController
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
      'index' => ['GET', 'HEAD'],
      'view' => ['GET', 'HEAD'],
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
  
  public function actionIndex(){
    $series = Seriesproductos::find()
                ->all();
    if (count($series)){
      return $this->correcto($series);
    }
    return $this->error('Sin datos');
  }
  
  public function actionCreate(){
    $datos = Yii::$app->request->post('itemcode');
    $series = Seriesproductos::find()
                ->where("ItemCode = '{$datos}' AND Status = 1")
                ->all();
    if (count($series)){
      return $this->correcto($series);
    }
    return $this->error('Sin datos');
  }

  public function actionGuardar(){
    $serie = new Seriesmarketing();
    $serie->Status = 1;
    Yii::error(json_encode(Yii::$app->request->post()));
    $serie->User = Yii::$app->request->post('usuario');
    $serie->SystemNumber = Yii::$app->request->post('systemNumber');
    $serie->SerialNumber = Yii::$app->request->post('serialNumber');
    $serie->DocumentId = Yii::$app->request->post('idDocumento');
    $serie->ItemCode  = Yii::$app->request->post('itemCode');
    $serie->DateUpdate = Carbon::today()->format('Y-m-d');
    if ($serie->save(false)){
      
      $item=Yii::$app->request->post('itemCode');
      $serial=Yii::$app->request->post('serialNumber');
      $sysnum=Yii::$app->request->post('systemNumber');
      $sql = 'Update seriesproductos set Status=0 where itemCode="'.$item.'" and serialNumber="'.$serial.'" and systemNumber="'. $sysnum.'";';
      $resp = Yii::$app->db->createCommand($sql)->execute();
      return $this->correcto();
    }
    return $this->error();
  }

  public function actionFiltrar() {
    $almacen = Yii::$app->request->post('almacen');
    $series = Yii::$app->db->createCommand("SELECT * FROM seriesproductos WHERE WsCode = :almacen AND `Status` = 1;")
      ->bindValue(':almacen', $almacen)
      ->queryAll();
    return $series;
  }
}
