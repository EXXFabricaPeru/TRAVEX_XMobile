<?php

namespace api\controllers;

use backend\models\Clientesgrupo;
use Yii;
use api\traits\Respuestas;
use yii\rest\ActiveController;

class ClientesgruposController extends ActiveController
{
    public $modelClass = 'backend\models\Clietesgrupos';
    use Respuestas;

    /*public function init()
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

    public function actionCreate()
    {
        $clientesSucursales = Yii::$app->db->createCommand('select * from clientesgrupo')
                        //->bindValue('usuario',Yii::$app->request->post('usuario'))
        //->bindValue('texto',Yii::$app->request->post('texto',0))
        ->queryAll();
        if (count($clientesSucursales) > 0) {
        return $this->correcto($clientesSucursales);
        }
        return $this->correcto([],'Sin datos',201);
    }

    public function actionFindonebycode($code) {
        $clienteGrupo = Clientesgrupo::find()->where(['Code' => $code])->one();
        if(is_object($clienteGrupo)) {
            return $this->correcto($clienteGrupo);
        } else {
            return $this->error();
        }

    }
}
