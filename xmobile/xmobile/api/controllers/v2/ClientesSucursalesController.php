<?php

namespace api\controllers\v2;


use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;
use backend\models\v2\Clientes;
use backend\models\v2\Convertirutf8data;

class ClientessucursalesController extends ActiveController {
    use Respuestas;
    public $modelClass = 'backend\models\Usuario';
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
        Yii::error("Datos Ingreso: ".json_encode(Yii::$app->request->post()));
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $modelo=new Clientes;
        $modelo= $modelo->obtenerSucursales($usuario,$salto);
        Yii::error("Sucursales--------> ".json_encode($modelo));
        $modelo=Convertirutf8data::remplaceString($modelo);
        Yii::error("Cantidad Sucursales: ".count($modelo));
        if (count($modelo)){ 
          return $this->correcto($modelo);
        }
        Yii::error("Error ClientesSuc "); 
        return $this->error('Sin datos',201);
    }  
    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');        
        $modelo=New Clientes;
        $modelo= $modelo->obtenerSucursalesContador($usuario);
        return $this->correcto($modelo, 'OK'); 
    }  
    public function actionBuscador(){
        $usuario=Yii::$app->request->post('usuario'); 
        $texto=Yii::$app->request->post('texto');       
        $modelo=New Clientes;
        $modelo= $modelo->obtenerSucursalesTodos($usuario,$texto);
        return $this->correcto($modelo, 'OK'); 
    }

}