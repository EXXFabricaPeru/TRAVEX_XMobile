<?php

namespace api\controllers;


use Yii;
use Carbon\Carbon;
use api\traits\Respuestas;
use yii\rest\ActiveController;
use backend\models\Gestionbancos;
use yii\filters\auth\QueryParamAuth;

class GestionbancosController extends ActiveController
{
    use Respuestas;

    public $modelClass = 'backend\models\User';
    
    /*
    public function init() {
        parent::init();
        Yii::$app->user->enableSession = false;
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

    public function actionCreate()
    {
        $bancos = Gestionbancos::find()
            ->asArray()
            ->all();
        if (count($bancos) > 0){
            return $this->correcto($bancos);
        }
        return $this->error('Sin datos', 201);
    }

}
