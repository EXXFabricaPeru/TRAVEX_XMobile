<?php

namespace api\controllers\v2;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\v2\Indicadoresimpuestos;

class IndicadoresimpuestosController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Usuario';

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
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex() {
        
    }
	
	public function actionTodossinfiltro() {
        
    }

    public function actionCreate() {
        
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $equipo=Yii::$app->request->post('equipo')?Yii::$app->request->post('equipo'):0;
        $modelo=new Indicadoresimpuestos;
        $modelo= $modelo->obtenerIndicadoresimpuestos($equipo,$usuario,$salto);
        return $this->correcto($modelo, 'OK'); 
    }

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        
       $modelo=new Indicadoresimpuestos;
       $modelo= $modelo->obtenerIndicadoresimpuestosContador();
       return $this->correcto($modelo, 'OK');
    }
}
