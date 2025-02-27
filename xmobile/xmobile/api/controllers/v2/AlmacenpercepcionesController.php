<?php

namespace api\controllers\v2;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\v2\Almacenpercepciones;

class AlmacenpercepcionesController extends ActiveController {

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
        $respuesta = Yii::$app->db->createCommand('select * from almacenespercepciones')                
                ->queryAll();        
        if (count($respuesta)){
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos',201);
    }
	
	public function actionTodossinfiltro() {
        $respuesta = Yii::$app->db->createCommand('select * from almacenespercepciones')                
                ->queryAll();
        if (count($respuesta)){
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos',201);
    }

    public function actionCreate() {
        
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $equipo=Yii::$app->request->post('equipo')?Yii::$app->request->post('equipo'):0;
        $modelo=new Almacenpercepciones;
        $modelo= $modelo->obtenerAlmacenPercepciones($equipo,$usuario,$salto);
        return $this->correcto($modelo, 'OK'); 
    }

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        
       $modelo=new Almacenpercepciones;
       $modelo= $modelo->obtenerAlmacenPercepcionesContador();
       return $this->correcto($modelo, 'OK');
    }
}
