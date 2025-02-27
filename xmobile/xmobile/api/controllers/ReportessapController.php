<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
//use backend\models\Servislayer;
use backend\models\Sincronizar;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Almacenes;

class ReportessapController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Usuario';

    /* public function init()
      {
      parent::init();
      \Yii::$app->user->enableSession = false;
      }

      public function behaviors()
      {
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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }
  
	public function actionSaldos(){ // 25
		$datos = Yii::$app->request->post();
		$data = json_encode(array("accion" => 25, "fecha"=> $datos["fecha"]));
		$model = new Sincronizar();
		return $model->executex($data);
	}

	public function actionCardex(){ //26
		$datos = Yii::$app->request->post();
		$data = json_encode(array("accion" => 26, "item"=> $datos["item"]));
		$model = new Sincronizar();
		return $model->executex($data);
	}
  
	public function actionSaldosresumen(){  //27
		$datos = Yii::$app->request->post();
		$data = json_encode(array("accion" => 27, "fecha"=> $datos["fecha"] ));
		$model = new Sincronizar();
		return $model->executex($data);
	}

  

}
