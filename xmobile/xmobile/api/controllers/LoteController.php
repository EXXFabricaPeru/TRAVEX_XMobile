<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use api\traits\Respuestas;

class LoteController extends ActiveController
{
    use Respuestas;
    public $modelClass = 'backend\models\Usuario';

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

    public function actionIndex()
    {
        $model = new Servislayer();
        $model->actiondir = 'BatchNumberDetails';
        return $model->executex();
    }
    public function actionCreate()
    {
        $cardCode=Yii::$app->request->post('texto', 0);
        $productosAlacenes = Yii::$app->db->createCommand("SELECT * FROM lotes Where ItemCode='".$cardCode."'")
            //->where(['ItemCode' =>$cardCode ])
            //->bindValue('usuario',Yii::$app->request->post('usuario'))
            //->bindValue('texto',Yii::$app->request->post('texto',0))
            ->queryAll();
        if (count($productosAlacenes) > 0) {
            return $this->correcto($productosAlacenes);
        }
        return $this->correcto([],'Sin datos',201);
    }

    public function actionSincronizarlotes(){
        $almacen = Yii::$app->request->post('almacen', 0);
        $lotes = Yii::$app->db->createCommand("SELECT * FROM lotes")
            ->queryAll();
        if (count($lotes) > 0) {
            return $this->correcto($lotes);
        }
        return $this->correcto([], 'Sin datos', 201);
    }
}
