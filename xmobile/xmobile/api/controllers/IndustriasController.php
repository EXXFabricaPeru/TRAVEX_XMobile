<?php

namespace api\controllers;

use backend\models\Industrias;
use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class IndustriasController extends ActiveController {

    use Respuestas;
    public $modelClass = 'backend\models\Industrias';

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
            //'index' => ['GET', 'HEAD'],
            //'view' => ['GET', 'HEAD'],
            'index' => ['POST'],
            'view' => ['POST'],
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

    public function actionIndex(){
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $salto=0;
        $resultado= Yii::$app->db->createCommand("select * from industrias order by id limit 1000 OFFSET {$salto}")->queryAll();


        /*$resultado = Yii::$app->db->createCommand('SELECT * FROM industrias')
                    ->queryAll();*/
        if (count($resultado)){
          return $this->correcto($resultado);
        }
        return $this->error('Sin datos',201);
    }  
    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        $resultado=Yii::$app->db->createCommand("Select count(*) as contador from industrias")->queryOne();
        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Industrias');
        return $this->correcto($resultado, 'OK'); 
    }  
}
