<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Documentohtml;
use yii\filters\auth\QueryParamAuth;

class DocumentohtmlController extends ActiveController
{

  use Respuestas;

  public $modelClass = 'backend\models\Documentohtml';
  /*public function init()
  {
    parent::init();
    \Yii::$app->user->enableSession = false;
  }

  public function behaviors()
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
      'index' => ['GET', 'HEAD'],
      'view' => ['GET', 'HEAD'],
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
    $data = Yii::$app->request->post();
    $docpedido = $data["idDocPedido"];
    $tipodocumento = $data["tipodocumento"];
    $documento = Documentohtml::find()->where(['=', 'idDocPedido', $docpedido  ])->One();
    if ($documento != null) {    
      $resultado = [
        "id" => $documento["id"],
        "idDocPedido" => $documento["idDocPedido"],
        "html" => $documento["html"],
        "contador" => 0
      ];
      if ($tipodocumento != ''){
        $sql = "SELECT COUNT(*) AS TOTAL from reimpresion WHERE tipodocumento = :tipo AND iddocumento = :documento";
        $contador = Yii::$app->db->createCommand($sql)->bindValue(':tipo' , $tipodocumento)->bindValue(':documento' , $docpedido)->queryOne();
        $resultado["contador"] = (int)($contador["TOTAL"]) + 1;
        return $this->correcto($resultado);
      }
      else{
        $sql = "SELECT COUNT(*) AS TOTAL from reimpresion WHERE iddocumento = :documento";
        $contador = Yii::$app->db->createCommand($sql)->bindValue(':documento' , $docpedido)->queryOne();
        $resultado["contador"] = (int)($contador["TOTAL"]) + 1;
        return $this->correcto($resultado);
      }
    }
    return $this->error('Sin datos',201);
  }

  public function actionCreate(){
    $data = Yii::$app->request->post();
    $docpedido = $data["idDocPedido"];
    $html = $data["html"];
    $resultado = new Documentohtml();
    $resultado->id = null;
    $resultado->idDocPedido = $docpedido;
    $resultado->html = $html;
    if ($resultado->save()) return $this->correcto($resultado);
    else return $this->error('Ocurrio un error al guardar',201);
  }
}
