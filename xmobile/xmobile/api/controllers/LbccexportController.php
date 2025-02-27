<?php

namespace api\controllers;

use yii\rest\ActiveController;
use Yii;
use backend\models\Lbcc;

class LbccexportController extends ActiveController {

    public $modelClass = 'backend\models\Lbcc';

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

    function binarydecode($bin_str) {
        if (!empty($bin_str)) {
            $text_str = '';
            $chars = explode("\n", chunk_split(str_replace("\n", '', $bin_str), 8));
            $_I = count($chars);
            for ($i = 0; $i < $_I; $i++) {
                $text_str .= chr(bindec($chars[$i]));
            }
            return utf8_decode($text_str);
        }
    }

    public function actionView($id) {
        $r = explode(',', $id);
        $acclbcc = [];
        $sqlx = " SELECT * FROM pedidos WHERE id = " . $r[0];
        $docum = Yii::$app->db->createCommand($sqlx)->queryAll();
        $sqlc = " SELECT * FROM clientes WHERE CardCode = '" . $docum[0]['CardCode'] . "'";
        try {
            $sql = 'SELECT * FROM lbcc WHERE User = ' . $r[1];
            $acclbcc = Yii::$app->db->createCommand($sql)->queryAll()[0];
        } catch (\Exception $e) {
            $acclbcc = [];
        }
        return ['lbcc' => $acclbcc, 'order' => $docum[0], 'cliente' => Yii::$app->db->createCommand($sqlc)->queryAll()[0]];
    }

}
