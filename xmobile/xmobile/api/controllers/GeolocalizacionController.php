<?php

namespace api\controllers;

use backend\models\Geolocalizacion;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;

class GeolocalizacionController extends ActiveController
{

    use Respuestas;

    public $modelClass = 'backend\models\Geolocalizacion';

    /*public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'tokenParam' => 'access-token',
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }*/


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
        //unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        /*$sql = "SELECT idPersona,nombrePersona, apellidoPPersona,apellidoMPersona,fechaUMPersona,estadoPersona,documentoIdentidadPersona"
            . " FROM usuariopersona";
        $respuesta = Yii::$app->db->createCommand($sql)->queryAll();
        if (count($respuesta) > 0) {
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos', 201);*/
    }    

    public function actionView()
    {
        //return $_REQUEST;
        //$sql = "SELECT idPersona,nombrePersona, apellidoPPersona,apellidoMPersona,fechaUMPersona,estadoPersona"
        //. " FROM persona where id=1";
        //return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionCreate()
    {
        $datos = Yii::$app->request->post();
        $fecha = date("Y-m-d");
        $sql = '';
        foreach($datos as $dato){
            $geo = new Geolocalizacion();
            /*
            $idequipox    = $dato["idequipox"];
            $latitud      = $dato["latitud"];
            $longitud     = $dato["longitud"];
            $fecha        = $dato["fecha"];
            $hora         = $dato["hora"];
            $idcliente    = $dato["idcliente"];
            $documentocod = $dato["documentocod"];
            $tipodoc      = $dato["tipodoc"];
            $estado       = $dato["estado"];
            $actividad    = $dato["actividad"];
            $anexo        = $dato["anexo"];
            $usuario      = $dato["usuario"];
            $sql = "INSERT INTO `geolocalizacion`(`id`, `idequipox`, `latitud`, `longitud`, `fecha`, `hora`, `idcliente`, `documentocod`, `tipodoc`, `estado`, `actividad`, `anexo`, `usuario`, `status`, `dateUpdate`) VALUES (DEFAULT,";        
            $sql = $sql."'{ $idequipox }','{ $latitud }','{ $longitud }','{ $fecha }','{ $hora }','{ $idcliente }','{ $documentocod }','{ $tipodoc }','{ $estado }','{ $actividad }','{ $anexo }','{ $usuario }',1,'{ $fecha }');";
            $response = Yii::$app->db->createCommand($sql)->queryOne();
            */
			$geo->idequipox    = $dato["idequipox"];
			$geo->latitud      = $dato["latitud"];
			$geo->longitud     = $dato["longitud"];
			$geo->fecha        = $dato["fecha"];
			$geo->hora         = $dato["hora"];
			$geo->idcliente    = $dato["idcliente"];
			$geo->documentocod = $dato["documentocod"];
			$geo->tipodoc      = $dato["tipodoc"];
			$geo->estado       = $dato["estado"];
			$geo->actividad    = $dato["actividad"];
			$geo->anexo        = $dato["anexo"];
            $geo->usuario      = $dato["usuario"];
            $geo->status       = 1;
            $geo->dateUpdate   = $fecha;
            if (!$geo->save(false)){
                return $this->error('Registro no Correcto', 201);
            }
        }
            return $this->correcto([], 'Registro Correcto');
    }
}
