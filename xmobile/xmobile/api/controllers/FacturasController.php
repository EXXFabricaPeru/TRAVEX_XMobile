<?php

namespace api\controllers;

use api\traits\Respuestas;
use backend\models\Facturas;
use backend\models\Cabeceradocumentos;
use backend\models\Detalledocumentos;
use Yii;

class FacturasController extends \yii\rest\ActiveController
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
      'index'  => ['GET', 'HEAD'],
      'view'   => ['GET', 'HEAD'],
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
    $facturas = Yii::$app->db->createCommand('CALL pa_obtenerFacturas(:usuario)',
                [':usuario' => Yii::$app->request->post('usuario')])
                ->queryAll();
    if (count($facturas)){
      return $this->correcto($facturas);
    }
    return $this->error('Sin datos',201);
  }

  public function actionReportedocumentos(){
    $respuesta = Yii::$app->db->createCommand("CALL pa_reporte_venta(:usuario, :inicio, :fin)")
      ->bindValue(':usuario',Yii::$app->request->post('usuario'))
      ->bindValue(':inicio',Yii::$app->request->post('inicio'))
      ->bindValue(':fin',Yii::$app->request->post('fin'))
    ->queryAll();
    if (count($respuesta)){
      return $this->correcto($respuesta);
    }
    return $this->error('Sin datos',201);
  }

  public function actionReportelocallistacabeceras(){
    $tipo = Yii::$app->request->post('tipo');
    $inicio = Yii::$app->request->post('inicio');
    $fin = Yii::$app->request->post('fin');
    $busqueda = Yii::$app->request->post('clienteCardCode');
    $sql = "SELECT * FROM cabeceradocumentos cd WHERE cd.DocType LIKE '".$tipo."'";
    if ($inicio && $fin){
      $sql .= " AND cd.DocDate BETWEEN '".$inicio."' AND '".$fin."'";
    }
    if ($busqueda) {
      $sql .= " AND cd.CardCode LIKE '%".$busqueda."%'";
    }
    $sql .= ";";
    $respuesta = Yii::$app->db->createCommand($sql)
      ->queryAll();
    if (count($respuesta)){
      return $this->correcto($respuesta);
    }
    return $this->error('Sin datos',201);
  }

  public function actionListadocumentos(){
    $respuesta = Yii::$app->db->createCommand("CALL pa_lista_documentos_por_fecha(:usuario, :tipo, :inicio, :fin)")
      ->bindValue(':usuario',Yii::$app->request->post('usuario'))
      ->bindValue(':tipo',Yii::$app->request->post('tipo'))
      ->bindValue(':inicio',Yii::$app->request->post('inicio'))
      ->bindValue(':fin',Yii::$app->request->post('fin'))
    ->queryAll();
    if (count($respuesta)){
      return $this->correcto($respuesta);
    }
    return $this->error('Sin datos',201);
  }

  public function actionObtenerfactura() {
    $data = Yii::$app->request->post();
    $id = Yii::$app->request->post('idDocumento');
    
    $cabecera = Cabeceradocumentos::find()->where(['idDocPedido' => $id])->one();
    
    $detalles = Detalledocumentos::find()->where(['idCabecera' => $cabecera->id])->all();

    $response = [
      'cabecera' => $cabecera,
      'detalles' => $detalles
    ];
    if ($cabecera) {
      return $this->correcto($response);
    } else {
      return $this->error('No se encontro documento solicitado',201);
    }
  }

  public function actionItemventa() {
    $data = Yii::$app->request->post();
    $sql = "SELECT * FROM `vi_reporteitemventa`";
    $usuarioId = Yii::$app->request->post('usuario');
    $tipo = Yii::$app->request->post('tipo');
    $id = Yii::$app->request->post('itemCode');
    $centro = Yii::$app->request->post('centro');
    $vendedor = Yii::$app->request->post('vendedor');
    $inicio = Yii::$app->request->post('inicio');
    $fin = Yii::$app->request->post('fin');
    if ($inicio && $fin) {
      $sql .= " WHERE idUser = ".$usuarioId." AND SlpCode = '".$vendedor."' AND DocDate BETWEEN '" . $inicio . "' AND '" . $fin . "'";
    } 
    if ($id) {
      $sql .= " AND ItemCode LIKE '" . $id . "'";
    }
    if ($tipo) {
      $sql .= " AND DocType LIKE '" . $tipo . "'";
    }
    if ($centro) {
      $sql .= " AND producto_std1 LIKE '" . $centro . "'";
    }
    $sql .= ";";
    $respuesta = Yii::$app->db->createCommand($sql)
        ->queryAll();
      if (count($respuesta) > 0) {
        return $this->correcto($respuesta);
      }
      return $this->error('Sin datos', 201);
  }

}
