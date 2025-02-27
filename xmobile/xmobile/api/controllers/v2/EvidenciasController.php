<?php

namespace api\controllers\v2;


use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;
use backend\models\v2\Evidencias;
use backend\models\v2\Kmion;

class EvidenciasController extends ActiveController {
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
        return $this->error('Sin datos',201);
    }  


    public function actionCreate() {
        $request=Yii::$app->request->post(); 
        //Yii::error(" llega documento de movil ->" .json_encode($request));
        $modelo=New Evidencias;
        $response=$modelo->createEvidencias($request);
        Yii::error("Respuesta Creacion evidencia: ".json_encode($response));
        return $response;
      
    }

    public function actionPrueba() {
        $request=Yii::$app->request->post();
        Yii::error("Datos ingreso: ".json_encode($request));
        $Kmion = new Kmion();
        $resultado=$Kmion->obtenerKmion(0);
      
        if (count($resultado)){
          return $this->correcto($resultado);
        }
        return $this->error('Sin datos',201);
    }

}
?>