<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use backend\models\User;
use yii\filters\auth\QueryParamAuth;


class ResetearController extends ActiveController {

    public $modelClass = 'backend\models\User';

  /*  public function init() {
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
	
	public function actionCreate() {
		$data = Yii::$app->request->post();
		$emei = $data['emei'];
		$usuario = $data['usuario'];
		$model = new User();
		return  $model->resetSolicitud($usuario);
    }

    public function actionUpdate($id) {
		$pass = '';
		$data = Yii::$app->request->post();
		if(isset($data['pass']) && $data['pass'] != ''){
			$pass = $data['pass'];
		}else{
			$pass = '123456';
		}
		$model = new User();
		return  $model->resetPass($id,$pass,0);
    }

}
