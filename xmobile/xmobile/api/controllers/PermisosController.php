<?php

namespace api\controllers;

use backend\models\Persona;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Permisos;


class PermisosController extends ActiveController
{

    use Respuestas;

    public $modelClass = 'backend\models\Usuariopersona';

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
        //unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
       
        $post = Yii::$app->request->post();
        /* $respuesta = Yii::$app->db->createCommand("select * from vi_configuracionusuario where idUser = {$post["usuario"]}")->queryOne(); 
        */
        
        $respuesta = Permisos::find()
                    ->where(['IdUser' => $post["usuario"]])
                    ->all();        
        if (count($respuesta) > 0) {
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos', 201);
    }
}