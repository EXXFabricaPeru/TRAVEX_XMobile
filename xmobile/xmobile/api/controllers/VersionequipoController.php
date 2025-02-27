<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use Carbon\Carbon;
use yii\rest\ActiveController;
use backend\models\Versionequipo;

class VersionequipoController extends ActiveController
{
  use Respuestas;
  
  public $modelClass = 'backend\models\Versionequipo';
  

  protected function verbs()
  {
    return [
      'index'  => ['POST', 'HEAD'],
      'view'   => ['POST', 'HEAD'],
      'create' => ['POST','HEAD'],
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
  public function actionIndex(){
    Yii::error("LLega datos Version");
    //$fechaRegistro = Carbon::today('America/La_Paz');
    date_default_timezone_set('America/La_Paz');
    $usuario = Yii::$app->request->post('usuario');
    $equipo = Yii::$app->request->post('equipo');
    $version = Yii::$app->request->post('version');
    $fecha = Yii::$app->request->post('fecha');
    
    ////verifica si el equipo esta registrodo////
    $dataVersionequipo = Versionequipo::find()
        ->where([
          "usuario" => $usuario,
          "equipo" => $equipo,
          "estado" => 'activo',
        ])
        ->one();
    if(count($dataVersionequipo)>0){
  
       $dataVersionequipo->estado = 'inactivo';
       $dataVersionequipo->update(false);
        
    }

    $versionequipo = new Versionequipo();
    $versionequipo->id = 0;
   
    $versionequipo->usuario = $usuario;
    $versionequipo->equipo = $equipo;
    $versionequipo->version = $version;
    $versionequipo->fechaVersion = $fecha;
    $versionequipo->fechaRegistro = date('Y-m-d H:i:s');
    $versionequipo->estado = 'activo';
    if($versionequipo->save(false)){
      return $this->correcto($versionequipo, "Version registrada");
    }
    else{
      return $this->error('Error al registrar',100);
    }

  }
  
  public function actionCreate(){	  
   
  }
}
