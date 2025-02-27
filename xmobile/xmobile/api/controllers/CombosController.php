<?php

namespace api\controllers;

use backend\models\Clientes;
use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use api\traits\Respuestas;
use backend\models\Combos;
use backend\models\Usuariosincronizamovil;

class CombosController extends ActiveController {

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

    public function actionIndex() {
      $usuario=Yii::$app->request->post('usuario');
      $salto=Yii::$app->request->post('pagina');
      $combos= Yii::$app->db->createCommand("select * from v_combos order by TreeCode limit 1000 OFFSET {$salto}")->queryAll();

       /*
        $combos = Yii::$app->db->createCommand('SELECT * FROM v_combos')
                  ->queryAll();
                  */
        if (count($combos)){
          return $this->correcto($combos);
        }
        return $this->error('Sin datos',201);
    }

    public function actionBuscar() {
        $combos = Yii::$app->db->createCommand('SELECT * FROM v_combos WHERE TreeCode = :itemCode')
                  ->bindValue(':itemCode' ,Yii::$app->request->post('ItemCode'))
                  ->queryAll();
        if (count($combos)){
          return $this->correcto($combos);
        }
        return $this->error('Sin datos',201);
    }

    public static function actionFindonebyitemcode($itemCode){
      $oProducto = Yii::$app->db->createCommand('SELECT * FROM v_combos WHERE TreeCode = :itemCode')
                  ->bindValue(':itemCode' ,$itemCode)
                  ->queryAll();
      if (count($oProducto) > 0) {      
        return $oProducto;// $this->correcto($oProducto);
      }
      return $oProducto;//$this->error('Sin datos',201);
    }

    public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from combos")->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
 $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Combos');
      return $this->correcto($resultado, 'OK'); 
  }

}
