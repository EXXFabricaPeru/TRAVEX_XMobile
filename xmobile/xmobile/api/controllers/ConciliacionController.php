<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use backend\models\Servislayer;
use yii\rest\ActiveController;

class ConciliacionController extends ActiveController
{
  use Respuestas;

  public $modelClass = 'backend\models\User';

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
    unset($actions['index']); // GET
    unset($actions['view']); // GET / 1
    unset($actions['create']);  // POST
    unset($actions['update']);  // PUT
    unset($actions['delete']);  // DELETE
    return $actions;
  }
	
	public function actionIndex(){
		//return "HOLA";
	}	
	
	public function actionCreate(){
    //return "HOLA desde create";
    $datos = Yii::$app->request->post();
    $serviceLayer = new Servislayer();
    $result = [];
    //return var_dump($datos);
    //$datos['ReconDate'];
    $cabera = "INSERT INTO `conciliaciones`(`CardorAccount`, `Recondate`, `CreateDate`, `User`) VALUES (". 
    "'".$datos['CardOrAccount']."',".
    "'".$datos['ReconDate']."',".
    "'".date('Y-m-d H:i:s')."',".
    "'1')";
    $res = Yii::$app->db->createCommand($cabera)->execute();
    $id = Yii::$app->db->getLastInsertID();
    Yii::error("DATA Resultado cabecera :: " . $res);
    Yii::error("DATA ultimo ID :: " . $id);
    $values = array();
    foreach ($datos['InternalReconciliationOpenTransRows'] as $recon) {
      //print $recon['ReconcileAmount'].' -- ';
      $values[] = "('".$id."',".
      "'".$recon['TransRowId']."',".
      "'".$recon['ShortName']."',".
      "'".$recon['CreditOrDebit']."',".
      "'".$recon['ReconcileAmount']."',".
      "'".$recon['Selected']."',".
      "'".$recon['SrcObjAbs']."',".
      "'".$recon['SrcObjTyp']."',".
      "'".$recon['TransId']."',".
      "'".date('Y-m-d H:i:s')."',".
      "'1',".
      "'0')";
    }
    $sql = "insert into conciliacionesdetalles (`idMaestro`, `TransIdRow`, 
    `ShortName`, `CreditOrDebit`, `Amount`, `Selected`, `SrcObjAbs`, `SrcObjTyp`, 
    `TransId`, `CreateDate`, `User`, `StatusSend`) VALUES " . implode(',',$values);
    $res2 = Yii::$app->db->createCommand($sql)->execute();

    $jsonConciliacion = $datos;

    $serviceLayer->actiondir = "InternalReconciliations";
    Yii::error("OBJETO SERVICE_LAYER" . json_encode($jsonConciliacion));
    $conciliacion = $serviceLayer->executePost($jsonConciliacion);

    if($conciliacion==false){                  
          Yii::error("-->>> error".$conciliacion);
          //$responseUpdate = Yii::$app->db->createCommand($sqlUpdate)->queryOne();
          //Yii::error(json_encode($responseUpdate));
          //$ret['codigo'] = '200';
          //$ret['mensaje'] = 'success';
		  return $this->error('Error al conciliar',201);
    }//else{
     //     $update = "UPDATE `conciliaciones` SET StatusSend = 1  WHERE id = ".$id;
     //     $responseUpdate = Yii::$app->db->createCommand($update)->queryOne();
     //     Yii::error(json_encode($responseUpdate));
    //}

    return $this->correcto([], "OK");
  }

}
