<?php

namespace api\controllers;

use backend\models\Clientes;
use backend\models\Clientessucursales;
use Yii;
use api\traits\Respuestas;
use yii\rest\ActiveController;
use backend\models\Usuariosincronizamovil;

class ClientessucursalesController extends ActiveController
{
    public $modelClass = 'backend\models\Clietessucursales';
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
        //'index' => ['GET', 'HEAD'],
        //'view' => ['GET', 'HEAD'],
        'index' => ['POST'],
        'view' => ['POST'],
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
        $usuario=Yii::$app->request->post('usuario');
        //$salto=Yii::$app->request->post('pagina');
        $clientesSucursales = Yii::$app->db->createCommand('CALL pa_obtenerClientesSucursales(:usuario,:contador,:salto)')
        ->bindValue('usuario',Yii::$app->request->post('usuario'))
        ->bindValue(':contador',0)
        ->bindValue(':salto',Yii::$app->request->post('pagina',0))
        ->queryAll(); 
        if (count($clientesSucursales) > 0) {
        return $this->correcto($clientesSucursales);
        }
        return $this->correcto([],'Sin datos',201);
    }

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        //$resultado=Yii::$app->db->createCommand("Select count(*) as contador from clientessucursales")->queryOne();
        $resultado = Yii::$app->db->createCommand('CALL pa_obtenerClientesSucursales(:usuario,:contador,:salto)')
        ->bindValue('usuario',Yii::$app->request->post('usuario'))
        ->bindValue(':contador',1)
        ->bindValue(':salto',0)
        ->queryOne();       
        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Clientessucursales');
        return $this->correcto($resultado, 'OK'); 
    }

    public function actionFindsucursalesbycardcode($cardCode) {
        $oSucursales = Clientessucursales::find()
            ->where(['CardCode' => $cardCode])->all();
        
        if(count($oSucursales)) {
            return $this->correcto($oSucursales);
        } else {
            return $this->error();
        }
    }
}
