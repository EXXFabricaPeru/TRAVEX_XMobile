<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Divisadocumentos;
use Exception;

class CambiodivisasmovilController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Divisadocumentos';

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
        $detalle = Yii::$app->db->createCommand("Select * from divisadocumentos ")->queryAll();
        if (count($detalle) > 0)
            return $this->correcto($detalle);
        else
            return $this->error('Sin datos', 201);
    }

    public function actionCreate() {
        $datosorg = Yii::$app->request->post();
        try {
            $arr = [];
            foreach ($datosorg as $key) {
                $model = new Divisadocumentos();
                $id = $key['id'];
                $model->iddocdocumento = $key['iddocdocumento'];
                $model->CardCode = $key['CardCode'];
                $model->monedaDe = $key['monedaDe'];
                $model->monedaA = $key['monedaA'];
                $model->ratio = $key['ratio'];
                $model->monto = $key['monto'];
                $model->cambio = $key['cambio'];
                $model->usuario = $key['usuario'];
                $model->created_at = $key['created_at'];
                $model->updated_at = $key['updated_at'];
                $model->sap = $key['sap'];
                $model->save(false);
                $arr[] = ['idx' => $model->id, 'id' => $id];
            }
            return $this->correcto($arr, "Divisas registrados");
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 100);
        }
    }

}
