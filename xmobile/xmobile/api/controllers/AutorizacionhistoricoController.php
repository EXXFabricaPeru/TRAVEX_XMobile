<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Autorizacionhistorico;

class AutorizacionhistoricoController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Usuario';

    /* public function init()
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
      } */

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

    public function actionIndex() {}

    public function actionCreate() {
        $datos = Yii::$app->request->post();
        $historico = new Autorizacionhistorico();
        $historico->id = 0;
        $historico->solicitante = $datos["solicitante"];
        $historico->autorizador = $datos["autorizador"];
        $historico->documento = $datos["documento"];
        $historico->tipo = $datos["tipo"];
        $historico->fechahora = $datos["fechahora"];
        if (!$historico->save(false)){
            return $this->error('Error al crear el historico');
        } else{
            return $this->correcto('Historico almacenado correctamente');
        }
    }
}
