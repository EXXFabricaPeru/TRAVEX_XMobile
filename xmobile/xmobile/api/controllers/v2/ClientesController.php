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

class ClientesController extends ActiveController {
    use Respuestas;
    public $modelClass = 'backend\models\Usuario';
    protected function verbs() {
        return [
            //'index' => ['GET', 'HEAD'],
            //'view' => ['GET', 'HEAD'],
            'index' => ['POST'],
            'view' => ['POST'],
            'create' => ['POST'],
            //'update' => ['PUT', 'PATCH'],
             'update' => ['POST'],

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
        $modelo=new Clientes;
        $modelo= $modelo->obtenerClientes($usuario,$salto);
        $modelo=Convertirutf8data::remplaceString($modelo);
        //Yii::error("Respuesta Clientes: ".json_encode($modelo));
        if (count($modelo)){
          return $this->correcto($modelo);
        }
        return $this->error('Sin datos',201);
    }  
    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');        
        $modelo=New Clientes;
        $modelo= $modelo->obtenerClientesContador($usuario);
        return $this->correcto($modelo, 'OK'); 
    }  
    public function actionBuscador(){
        $usuario=Yii::$app->request->post('usuario'); 
        $texto=Yii::$app->request->post('texto');       
        $modelo=New Clientes;
        $modelo= $modelo->obtenerTodosClientes($usuario,$texto);
        return $this->correcto($modelo, 'OK'); 
    }

    public function actionValidate(){
        $request=Yii::$app->request->post(); 
         $modelo=New Clientes;
        $response=$modelo->validateClientByNitOrCardCode($request['CardCode'],$request['rut'],$request['idUser']);
        return $response; 
    }
    public function actionCreate(){
        $request=Yii::$app->request->post(); 
         $modelo=New Clientes;
        $response=$modelo->createClient($request);
        Yii::error("Respuesta Creacion cliente: ".json_encode($response));
        return $response; 
    }

     public function actionUpdate(){
        $request=Yii::$app->request->post(); 
        $modelo=New Clientes;
        $response=$modelo->updateCliente($request);
        Yii::error("Respuesta Actualizar cliente: ".json_encode($response));
        return $response; 
    }
}