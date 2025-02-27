<?php

namespace api\controllers\v2;


use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;
use backend\models\v2\Documentos;

class FacturasdetalleController extends ActiveController {
    use Respuestas;
    public $modelClass = 'backend\models\Usuario';
    private $tipodoc=1;
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
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $modelo=new Documentos;
        $modelo= $modelo->obtenerDetalles($this->tipodoc,$equipo,$usuario,$salto);
        if (count($modelo)){
          return $this->correcto($modelo);
        }
        return $this->error('Sin datos',201);
    }  
    public function actionContador(){
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');        
        $modelo=new Documentos;
        $modelo= $modelo->obtenerDetallesContador($this->tipodoc,$equipo,$usuario);
        return $this->correcto($modelo, 'OK'); 
    }  
    
    public function actionBuscador(){
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');        
        $texto=Yii::$app->request->post('texto');              
        $modelo=new documentos;
        $modelo= $modelo->obtenerDetallesTodos($this->tipodoc,$equipo,$usuario,$texto);
        return $this->correcto($modelo, 'OK'); 
    }
    
}