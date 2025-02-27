<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use backend\models\Unidadesmedida;
use api\traits\Respuestas;

class ListaunidadesController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Listaunidades';

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

    }
	
	 public function actionCreate() {
        $unidadesMedida = Unidadesmedida::find()->asArray()->all();
        if (count($unidadesMedida) > 0 ) {
            return $this->correcto($unidadesMedida);
        }
        return $this->error('Sin datos',201);
    }

}
