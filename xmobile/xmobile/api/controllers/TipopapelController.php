<?php

namespace api\controllers;

use backend\models\Tipopapel;
use api\traits\Respuestas;
use Yii;

class TipopapelController extends \yii\rest\ActiveController
{
    use Respuestas;

    public $modelClass = 'backend\models\User';

    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }

    /*public function behaviors() {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
    'tokenParam' => 'access-token',
    'class' => QueryParamAuth::className(),
    ];
    return $behaviors;
    }*/

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {   
        $tipopapel = Tipopapel::find()->all();
        if (count($tipopapel)>0) {
            return $this->correcto($tipopapel, 'OK');
        }
        return $this->correcto([], 'Sin datos');
    }

    public function actionCreate()
    {
        return true;
    }

    

}
