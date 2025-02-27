<?php

namespace api\controllers;

use yii\rest\ActiveController;
use Yii;
use backend\models\Pedidos;

class PedidosexportController extends ActiveController {

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

    public function actionIndex() {
        $pedidos = Pedidos::find()->all();
        $arr = [];
        foreach ($pedidos as $key) {
            $arr[] = [
                'DocType' => 'DOP',
                'CardName' => $key->CardName,
                'CardCode' => $key->CardCode,
                'fechasend' => '20-20-2020',
                'DocTotalPay' => 2000,
                'id' => $key->id
            ];
        }
        return ['data' => $arr, 'pagx' => 1];
    }

    public function actionView($id) {
        $documento = \backend\models\Cabeceradocumentos::findOne($id);
        $sqlcli = 'SELECT * FROM clientes WHERE CardCode = "' . $documento->CardCode . '" ';
        $cliente = []; //Yii::$app->db->createCommand($sqlcli)->queryAll();
        $sqldetalles = 'SELECT * FROM detalledocumentos WHERE idCabecera =  ' . $id;
        $detalles = []; // Yii::$app->db->createCommand($sqldetalles)->queryAll();
        return $this->renderPartial('view', ["documento" => $documento, "cliente" => [], "detalles" => []]);
    }

    public function paginador($id, $cant) {
        $pg = 10;
        $t = $cant / $pg;
        $pag = [];
        $cont = -$pg;
        for ($i = 0; $i <= $t; $i++) {
            array_push($pag, [($cont += $pg), $pg]);
        }
        return ['pag' => $pag[$id][0] . ', ' . $pag[$id][1], 'tx' => $t];
    }

    public function actionCreate() {
        $data = Yii::$app->request->post();
        $sql = '';
        if ($data['searchdata'] != '')
            $sql .= ' AND CardName LIKE "%' . $data['searchdata'] . '%" ';
        if ($data['desde'] != '' && $data['hasta'] != '')
            $sql .= ' AND DocDueDate BETWEEN "' . $data['desde'] . '" AND "' . $data['hasta'] . '" ';
        $sqltotal = 'SELECT count(*) AS total FROM pedidos WHERE 1 ' . $sql . ' ';
        $totalCount = Yii::$app->db->createCommand($sqltotal)->queryAll();
        $pagx = $this->paginador($data['pag'], $totalCount[0]['total']);
        $sqlx = 'SELECT id AS id, "DOP" AS DocType, DocDueDate AS fechasend, CardName  AS CardName, CardCode AS CardCode, DocTotal AS DocTotalPay FROM pedidos WHERE 1 ' . $sql . ' LIMIT ' . $pagx['pag'];
        $resul = Yii::$app->db->createCommand($sqlx)->queryAll();
        return ['data' => $resul, 'pagx' => ceil($pagx['tx'])];
    }

}
