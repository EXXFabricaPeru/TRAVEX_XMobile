<?php

namespace api\controllers\v2;

use backend\models\v2\Pagos;
use Yii;
use backend\models\Servislayer;
use Carbon\Carbon;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Sincronizar;
use backend\models\v2\Sapenviopagos;


class PagosmovilesController extends ActiveController
{

    public $modelClass = 'backend\models\Usuario';

    use Respuestas;

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
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

    public function actionCreate(){
        $pagos = new Pagos;
        $datos=Yii::$app->request->post();
        Yii::error("INGRESA OBJETO PAGOS: ".json_encode($datos));
        Yii::error($datos);
        $respuesta= $pagos->registrarHistorial($datos);
        Yii::error("RESPUESTA HISTORIAL:".json_encode($respuesta));
        if($respuesta['registro']){  

            $respuesta= $pagos->registrarPago($datos,0,$respuesta['id']);// Registro middleware 
            Yii::error("RESPUESTA REGISTRO PAGO: ".json_encode($respuesta));
            if($respuesta['registro']){
                Yii::error("PASO REGISTRO PAGO 20");
                $respuesta=Sapenviopagos::pagar($respuesta['id']);
                if($respuesta['registro']){
                    Yii::error("RESPUESTA-PAGO: ".json_encode($respuesta));
                    return $this->correcto($respuesta);
                }
                //return $this->correcto($respuesta);
            }
            else {
                Yii::error("PASO HISTO"); 
            }  
                
        }        
        //return $this->error($respuesta,201);
        Yii::error("RESPUESTA-PAGO: ".json_encode($respuesta));
        return $this->correcto($respuesta);
    }
}