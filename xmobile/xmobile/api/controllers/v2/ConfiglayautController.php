<?php

namespace api\controllers\v2;

use backend\models\v2\Layautconfig;
use yii;
use yii\db\Query;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

use backend\models\v2\Convertirutf8data;

class ConfiglayautController extends ActiveController
{
use Respuestas;
public $modelClass = 'backend\models\v2\Layautconfig';
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
  public function actionIndex(){
    /*$usuario=Yii::$app->request->post('usuario');
    $salto=Yii::$app->request->post('pagina');
    $modelo=new Banco;
    $modelo= $modelo->obtenerBancos($salto);
    $resultado = Convertirutf8data::convert_to_utf8_recursively($modelo);
    if (count($resultado)){
      return $this->correcto($resultado);
    }
    return $this->error('Sin datos',201);*/
    $layautConfig="SELECT valor FROM configuracion WHERE parametro='templateLayautImprecion' and estado=1";
    $layautConfigData = Yii::$app->db->createCommand($layautConfig)->queryOne();
    //$resp[0]["usaFacturaReserva"] =];

    $dataConfig=Layautconfig::find()->where(['layaut_style' =>  $layautConfigData["valor"]])->all();
    if($dataConfig){
        return $dataConfig;
    }
    return [];
  }  
  public function actionContador(){
     /* $modelo=New Banco;
      $resultado= $modelo->obtenerBancosContador();
      return $this->correcto($resultado, 'OK');*/ 
  } 

}
