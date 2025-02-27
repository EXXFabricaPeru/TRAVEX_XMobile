<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Visitas;

class VisitasController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Usuario';

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
        $usuario = Yii::$app->request->post('usuario');
        $finicial = Yii::$app->request->post('fini');
        $ffinal = Yii::$app->request->post('ffin');
        $sql = "SELECT id, CardCode, CardName, fecha, hora, horafin, lat, lng, foto, usuario, estadoEnviado FROM visitas WHERE usuario = :usuario AND fecha >= :inicial AND fecha <= :final";
        $respuesta = Yii::$app->db->createCommand($sql)
                     ->bindValue(':usuario', $usuario)
                     ->bindValue(':inicial', $finicial)
                     ->bindValue(':final', $ffinal)
                     ->queryAll();
        if (count($respuesta)){
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos',201);
    }

    public function actionCreate() {
        Yii::error("inserta visitas:: ".json_encode(Yii::$app->request->post()));
        $visitas = Yii::$app->request->post();
        $respuesta = [];
        foreach($visitas as $v){
            $visita = new Visitas();
            $visita->id = 0;
            $visita->CardCode   = $v["CardCode"];
            $visita->CardCode 	= $v["CardCode"];
            $visita->CardName 	= $v["CardName"];
            $visita->fecha		  = $v["fecha"];
            $visita->hora       = $v["hora"];
            $visita->horafin    = $v["horafin"];
            $visita->lat        = $v["lat"];
            $visita->lng        = $v["lng"];
            $visita->foto       = $v["foto"];
            $visita->usuario    = $v["usuario"];
            $visita->estadoEnviado = $v["estadoEnviado"];
            $visita->motivoRazon    = $v["motivoRazon"];
            $visita->descripcion    = $v["descripcionTxt"];
            if (!$visita->save(false)){
                return $this->error('Error al crear las visitas');
            } else{
                /* $sql = "UPDATE visitas SET estadoEnviado = :estado WHERE id = :estado";
                $id = $visita["id"];
                $recuperar = Visitas::find()->where("id = {$id}")->one();
                $recuperar->estadoEnviado = $id;
                if (!$recuperar->update(false))
                    return $this->error('Error al actualizar el estado de las visitas'); 
                $visita->estadoEnviado = $id;
                    */
                array_push($respuesta, $visita);
            }
        }
        return $this->correcto($respuesta);
    }
}
