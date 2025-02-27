<?php

namespace api\controllers;

use backend\models\Vendedores;
use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;

class VendedoresController extends ActiveController {

    use Respuestas;
    public $modelClass = 'backend\models\Vendedores';

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
        $filtro = "0";
        $model = new Servislayer();
        $filtro == "0" ? $filter = '' : $filter = '&$filter=' . rawurlencode("contains(SalesEmployeeName,'" . $filtro . "')");
        $model->actiondir = 'SalesPersons?$select=SalesEmployeeCode,SalesEmployeeName,EmployeeID' . $filter;
        return $model->executex();
    }

    public function actionCreate() {
        $vendedores = Yii::$app->db->createCommand('CALL pa_seleccionarVendedor()')
                        ->queryAll();
        if (count($vendedores) > 0) {
        return $this->correcto($vendedores);
        }
        return $this->correcto([],'Sin datos',201);
    }

    /*public function actionFindonebysalesemployeecode($salesEmployeeCode) {
        $oVendedor = Vendedores::find()->where(['SalesEmployeeCode' => $salesEmployeeCode])->one();
        if(is_object($oVendedor)) {
            return $this->correcto($oVendedor);
        } else {
            return $this->error();
        }

    }*/
}
