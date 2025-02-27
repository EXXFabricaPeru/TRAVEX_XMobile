<?php

namespace api\controllers;

use Yii;
use backend\models\User;
use api\traits\Respuestas;
use yii\rest\ActiveController;

class BuscarciController extends ActiveController
{
    use Respuestas;
    
    public $modelClass = 'backend\models\User';

    /**
     * @var User $modelUser
     */
    public $modelUser;

    /*public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
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
        $this->modelUser = new User();
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
        return $this->render('index');
    }

    public function actionCreate() {
        if (count($this->modelUser->findByDocumentoIdentidadPersona(Yii::$app->request->post('ci')))>0) {
            return $this->correcto($this->modelUser->findByDocumentoIdentidadPersona(Yii::$app->request->post('ci'))[0],"Persona encontrada",200);
        }
        return $this->correcto([],"Persona no encontrada",201);
    }
}
