<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use api\traits\Respuestas;
use backend\models\Equipox;

class RegisterequipoController extends ActiveController {

    public $modelClass = 'backend\models\Equipox';

    /* public function init() {
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
      } */

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
        //unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

}
