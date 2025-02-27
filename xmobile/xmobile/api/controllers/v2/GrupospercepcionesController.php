<?php

namespace api\controllers\v2;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\v2\Grupopercepciones;

class GrupospercepcionesController extends ActiveController {

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
        $respuesta = Yii::$app->db->createCommand('select * from clientesPercepciones')                
                ->queryAll();        
        if (count($respuesta)){
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos',201);
    }
	
	public function actionTodossinfiltro() {
        $respuesta = Yii::$app->db->createCommand('select * from clientesPercepciones')                
                ->queryAll();
        if (count($respuesta)){
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos',201);
    }

    public function actionCreate() {
        //$resultado = Yii::$app->db->createCommand("SELECT * FROM tipostarjetas")->queryAll();
       /*  
        $resultado= Yii::$app->db->createCommand("select * from gruposPercepciones order by id limit 1000 OFFSET {$salto}")->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201); */
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $equipo=Yii::$app->request->post('equipo')?Yii::$app->request->post('equipo'):0;
        $modelo=new Grupopercepciones;
        $modelo= $modelo->obtenerGrupoPercepciones($equipo,$usuario,$salto);
        return $this->correcto($modelo, 'OK'); 
    }

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        //$resultado=Yii::$app->db->createCommand("Select count(*) as contador from gruposPercepciones")->queryOne();
        //$usuariosincronizamovil= new Usuariosincronizamovil();
        //$usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Tarjetas');
       // return $this->correcto($resultado, 'OK'); 
     
       $modelo=new Grupopercepciones;
       $modelo= $modelo->obtenerGrupoPercepcionesContador();
       return $this->correcto($modelo, 'OK');
    }
}
