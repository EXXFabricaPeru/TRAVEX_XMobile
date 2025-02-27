<?php

namespace api\controllers\v2;
use backend\models\Bancos;
use yii;
use yii\db\Query;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;
use backend\models\v2\Banco;
use backend\models\v2\Pagos;
use backend\models\v2\Sapenviodoc;




class CancelarpagoController extends ActiveController
{
use Respuestas;
public $modelClass = 'backend\models\Usuario';
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
  /*   $usuario=Yii::$app->request->post('usuario');
    $salto=Yii::$app->request->post('pagina');
    $modelo=new Banco;
    $resultado= $modelo->obtenerBancos($salto);
    if (count($resultado)){
      return $this->correcto($resultado);
    }
    return $this->error('Sin datos',201); */
  }  
  public function actionContador(){
     /*  $modelo=New Banco;
      $resultado= $modelo->obtenerBancosContador($salto);
      return $this->correcto($resultado, 'OK');  */
  } 

  public function actionCreate(){
    $data=Yii::$app->request->post();
    //validando si el pago paso a sap
    $dataPago=Pagos::verificadorByreciboOne($data["nro_recibo"]);
    Yii::error("pago cancelado".json_encode($dataPago));
    $dataEnvio=Array();
    Yii::error("pago cancelado".$dataPago->cancelado);
    Yii::error("pago cancelado".$dataPago['cancelado']);
    if($dataPago &&  $dataPago['cancelado']==0){
        
       $dataEnvio= Sapenviodoc::pagoAnular($data["nro_recibo"],$data["equipo"]);  
    }else{
        $dataEnvio=Array("estado"=>1,"mensaje"=>"El documento de pago ya fue anulado");  
    }
    return $dataEnvio;
    
    //Sapenviodoc::pagoCancelar($val["recibo"],$val["equipo"]);
  }

}
