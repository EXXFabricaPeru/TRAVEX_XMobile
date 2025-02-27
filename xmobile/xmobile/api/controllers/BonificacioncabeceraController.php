<?php

namespace api\controllers;

use backend\models\BonificacionCa;
use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class BonificacioncabeceraController extends ActiveController
{
use Respuestas;
public $modelClass = 'backend\models\Usuario';

	  /*public function init()
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
	  }*/

protected function verbs()
  {
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

	public function actionIndex()
    {
      $usuario=Yii::$app->request->post('usuario');
      $salto=Yii::$app->request->post('pagina');
      $resultado= Yii::$app->db->createCommand("select * from v_bonificacion_cabezera where idUser=".$usuario." limit 1000 OFFSET {$salto}")->queryAll();

      
      
      $arraybono= array(
            "id" => "0",
            "Code" => "OLDSP001",
            "nombre" => "PRUEBA INACTIVO",
            "fecha_inicio" => "2020-12-01",
            "fecha_fin" => "2020-12-01",
            "maximo_regalo" => "0",
            "U_observacion"=> "",
            "tipo" => "PRODUCTOS ESPECIFICOS",
            "cantidad_compra" => "3",
            "unindad_compra"=> "UNI",
            "cantidad_regalo"=> "1.00",
            "unindad_regalo"=> "UNI",
            "cabezera_tipo"=> "BONIFICACION",
            "grupo_cliente"=> "0",
            "extra_descuento"=> "0",
            "opcional"=> "OBLIGATORIO",
            "territorio"=> "LP001",
            "idTerritorio"=> "13",
            "id_cabecera_tipo"=> "1",
            "tipo_regla_compra"=> "GLOBAL",
            "detalle_especifico"=> "Simple global LINEA",
            "monto_total"=> "0.00",
            "cantidad_maxima_compra"=> "0",
            "id_regla_bonificacion"=> "13",
            "codigo_canal"=> "0",
            "porcentaje"=> "0.00",
            "TerritoryID"=> "13",
            "idUser"=> "29",
            "Description"=>"LP001");

      array_push($resultado,$arraybono);
      Yii::error("Bonificaciones77: ".json_encode($resultado));
	    if (count($resultado) > 0) {
	        return $this->correcto($resultado, 'OK');
	    }
	    return $this->correcto([], "No se encontro Datos", 201);
    }
    public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');
      $resultado=Yii::$app->db->createCommand("Select count(*) as contador from v_bonificacion_cabezera where idUser=".$usuario)->queryOne();
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Bonificacioncabecera');
      return $this->correcto($resultado, 'OK'); 
  }

}
