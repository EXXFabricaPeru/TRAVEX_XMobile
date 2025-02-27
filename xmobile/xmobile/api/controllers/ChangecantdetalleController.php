<?php

namespace api\controllers;

use yii\rest\ActiveController;
use Yii;
use backend\models\Detalledocumentos;
use backend\models\Cabeceradocumentos;

class ChangecantdetalleController extends ActiveController {

    public $modelClass = 'backend\models\Detalledocumentos';

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

    public function actionCreate() {
        $data = Yii::$app->request->post();
        $LineTotal = $data['cantidad'] * $data['precio'];
        $header = Detalledocumentos::findOne($data['id']);
        $sql = 'UPDATE detalledocumentos SET LineTotal = ' . $LineTotal . ', Quantity = ' . $data['cantidad'] . ' WHERE id = ' . $data['id'];
        $resp = Yii::$app->db->createCommand($sql)->execute();
        $sqlxi = 'SELECT SUM(LineTotal) AS total FROM detalledocumentos WHERE idCabecera = ' . $header->idCabecera;
        $headerx = Yii::$app->db->createCommand($sqlxi)->queryOne();
        $sqlxx = 'UPDATE cabeceradocumentos SET DocTotal = ' . $headerx['total'] . ' WHERE id = ' . $header->idCabecera;
        Yii::$app->db->createCommand($sqlxx)->execute();
        return $resp;
    }

    public function actionView($id) {
        $sql = 'DELETE FROM detalledocumentos WHERE id = ' . $id;
        return Yii::$app->db->createCommand($sql)->execute();
    }

}
