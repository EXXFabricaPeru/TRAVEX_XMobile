<?php

namespace api\controllers;

use backend\models\Clientes;
use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class ClientescontactoController extends ActiveController {

    public $modelClass = 'backend\models\Usuario';

    use Respuestas;
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
            'index' => ['POST'],
            'view' => ['POST'],
            //'index' => ['GET', 'HEAD'],
            //'view' => ['GET', 'HEAD'],
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
        $usuario=Yii::$app->request->post('usuario');
        //$salto=Yii::$app->request->post('pagina');
        //$resultado = Yii::$app->db->createCommand("select * from vi_clientesContactos order by cardcode limit 1000 OFFSET {$salto}")->queryAll();
        $resultado = Yii::$app->db->createCommand("CALL pa_obtenerClientesContactos(:usuario,:contador,:salto)")
                ->bindValue(':usuario', Yii::$app->request->post('usuario'))
                ->bindValue(':contador',0)
                ->bindValue(':salto',Yii::$app->request->post('pagina',0))
                ->queryAll();
        
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        $resultado = Yii::$app->db->createCommand("CALL pa_obtenerClientesContactos(:usuario,:contador,:salto)")
        ->bindValue(':usuario', Yii::$app->request->post('usuario'))
        ->bindValue(':contador',1)
        ->bindValue(':salto',0)
        ->queryOne();
        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Clientescontacto');
        return $this->correcto($resultado, 'OK'); 
    }


}
