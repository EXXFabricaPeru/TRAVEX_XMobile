<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use backend\models\Sap;

class SapController extends ActiveController {

    public $modelClass = 'backend\models\Sap';

    /* public function init() {
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
      }
     */

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

    public function actionIndex() {
        set_time_limit(0);
        //ini_set('memory_limit', '2048M');
        $sap = new Sap();
        /*$sap->almacenes();
        $sap->listasPrecios();
        $sap->unidadesMedida();
        $sap->vendedores();
        $sap->clientesGrupos();
        $sap->Monedas();
        $sap->clientes();
        $sap->monedas();
        $sap->lotes();
        $sap->productos();
        $sap->lotes();
        $monedas = $sap->tipoCambio();*/
        //$condicionesPago = $sap->codicionesPagos();
        $sap->monedas();
        return 1;
    }

}
