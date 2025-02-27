<?php

namespace api\controllers\v2;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Usuariosincronizamovil;
use backend\models\v2\Condicionespagos;



class CondicionpagoController extends ActiveController
{
  public $modelClass = 'backend\models\Condicionespagos';
  use Respuestas;
  public function init()
  {
      parent::init();
      \Yii::$app->user->enableSession = false;
  }

  /*public function behaviors()
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
 /*    $resultado= Yii::$app->db->createCommand("select * from condicionespagos WHERE Status = 1  limit 1000 OFFSET {$salto}")->queryAll(); */
$condicionPago=new Condicionespagos; 
 //$resultado=Condicionespagos::getAll($salto);
 $resultado=$condicionPago->getAll($salto);

   /* $sql = "SELECT * FROM condicionespagos WHERE Status = 1";
    $codicionesPagos = Yii::$app->db->createCommand($sql)->queryAll();*/
    if (count($resultado)) {
      return $this->correcto($resultado);
    }
    return $this->correcto([], 201);
  }
  public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from condicionespagos")->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Condicionpago');
      return $this->correcto($resultado, 'OK'); 
  }

}
