<?php

namespace api\controllers;

use yii\rest\ActiveController;
use Yii;
use backend\models\Pedidos;
use backend\models\Pedidosproductos;
use backend\models\Numeracionapp;
use backend\models\Cabeceradocumentos;
use backend\models\Detalledocumentos;
use backend\models\Condicionespagos;

class DocumentoscloneexportController extends ActiveController {

    public $modelClass = 'backend\models\Pedidos';

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

    public function actionUpdate($id) {
        $data = Yii::$app->request->post();
        $sql = 'UPDATE cabeceradocumentos SET Reserve = 1, DocDueDate = "' . $data['fecha'] . '" WHERE id =' . $id;
        return Yii::$app->db->createCommand($sql)->execute();
    }

    public function actionView($id) {
        $document = Cabeceradocumentos::findOne($id);
        $detalles = Detalledocumentos::find()->where(['idCabecera' => $id])->all();
        $condicion = Condicionespagos::find()->all();
        return $this->renderPartial('view', [
                    'data' => $document,
                    'detalles' => $detalles,
                    'condicion' => $condicion
        ]);
    }

    public function actionCreate() {
        $data = Yii::$app->request->post();
        $num = new Numeracionapp($data['cod'], $data['idUser']);
        $numeracion = $num->run();
        $sql = "CALL clon(" . $data['id'] . ", '".$numeracion."','" . $data['cod'] . "', @idp, '" . $data['codControl'] . "'," . $data['num'] . ", " . $data['idUser'] . ", " . $data['authorization'] . "); ";
        $resp = Yii::$app->db->createCommand($sql)->execute();
        $sqlx = "SELECT @idp as idxp;";
        $respx = Yii::$app->db->createCommand($sqlx)->queryOne();
        $sql = '';
        $id = $data['idUser'];
        switch ($data['cod']) {
            case('DOP'):
                $sql = ' UPDATE numeracion SET numdop = (numdop+1) WHERE iduser = ' . $id;
                break;
            case('DOF'):
                $sql = ' UPDATE numeracion SET numdof = (numdof+1) WHERE iduser = ' . $id;
                break;
            case('DFA'):
                $sql = ' UPDATE numeracion SET numdfa = (numdfa+1) WHERE iduser = ' . $id;
                break;
            case('DOE'):
                $sql = ' UPDATE numeracion SET numdoe = (numdoe+1) WHERE iduser = ' . $id;
                break;
        }
        Yii::$app->db->createCommand($sql)->execute();
        return json_encode($respx);
    }

}
