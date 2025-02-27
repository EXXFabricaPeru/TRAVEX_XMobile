<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Almacenes;

class AlmacenesController extends ActiveController {

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

    public function actionIndex() {
        $almacenes = Almacenes::find()->asArray()->all();
        if (count($almacenes) > 0) {
            return $this->correcto($almacenes);
        }
        return $this->error('Sin datos', 201);
    }

    public function actionCreate() {
        $almacenes = Yii::$app->db->createCommand('CALL pa_obtenerAlmacenes(:usuario)')
                ->bindValue(':usuario', Yii::$app->request->post('usuario'))
                ->queryAll();
        if (count($almacenes) > 0) {
            return $this->correcto($almacenes, 'OK');
        }
        return $this->correcto([], 'Sin datos', 201);
    }

    public function actionFindalmacenesbyidusuario() {
        $almacenes = Yii::$app->db->createCommand('CALL pa_obtenerAlmacenes(:usuario)')
                ->bindValue(':usuario', Yii::$app->request->post('usuario'))
                ->queryAll();
        if (count($almacenes) > 0) {
            return $this->correcto($almacenes, 'OK');
        }
        return $this->correcto([], 'Sin datos', 201);
    }

    public function actionFindalmacenesbyname($texto) {
        $almacenes = Yii::$app->db->createCommand('SELECT * FROM almacenes WHERE WarehouseCode Like :texto OR WarehouseName Like :texto')
                    ->bindValue(':texto', $texto)
                    ->queryAll();
        //$almacenes = Almacenes::find()->where(['like','WarehouseCode', $texto])->asArray()->all();
        if (count($almacenes) > 0) {
            return $this->correcto($almacenes);
        }
        return $this->error('Sin datos', 201);
    }

}
