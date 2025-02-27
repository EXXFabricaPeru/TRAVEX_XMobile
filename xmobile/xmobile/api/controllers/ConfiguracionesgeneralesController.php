<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use backend\models\Configuracionesgenerales;
use yii\filters\auth\QueryParamAuth;

class ConfiguracionesgeneralesController extends ActiveController {

    public $modelClass = 'backend\models\Configuracionesgenerales';

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
        //   unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

}
