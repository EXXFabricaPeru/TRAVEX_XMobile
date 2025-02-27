<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;


class ReportefacturasController extends ActiveController
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

  public function actionIndex()
  { 
    $reporte = Yii::$app->db->createCommand('CALL pa_ReporteDocumentos(0,"0",0,"0","0","0");')
                  ->queryAll();
    if (count($reporte) > 0) {
      return $this->correcto($reporte);
    }
    return $this->error('Sin datos',201);
  }

  public function actionCreate()
  {
    $reporte = Yii::$app->db->createCommand("CALL pa_ReporteDocumentos(:usuario,:tipo,:estado,:cliente,:documento,:estadof)")
      ->bindValues([
        ':usuario'    => Yii::$app->request->post('user','0'),
        ':tipo'    => Yii::$app->request->post('tipo','DFA'),
        ':estado' => Yii::$app->request->post('estado',0),
        ':cliente' => Yii::$app->request->post('cliente','0'),
        ':documento' => Yii::$app->request->post('documento','0'),
        ':estadof' => Yii::$app->request->post('estadof','0'),          
      ])
      ->queryAll();
      $salida=[];
      $linea=[];
    if (count($reporte) > 0) {
      foreach ($reporte as $value ){
        $detalle=Yii::$app->db->createCommand("Select * from detalledocumentos where idCabecera=".$value["id"]." ")->queryAll();
          foreach ($detalle as $combo ){
            $esCombo=Yii::$app->db->createCommand("Select TreeCode from combos where TreeCode='".$combo["ItemCode"]."' ")->queryAll();
            if (count($esCombo) > 0){
              $combo["escombo"]=1;
              
              $prodcombo=Yii::$app->db->createCommand("Select * from v_combos where TreeCode='".$combo["ItemCode"]."' ")->queryAll();
              $combo["combo"]=$prodcombo;
            }
            else{
              $combo["escombo"]=0;
              $combo["combo"]=[];
            }
            array_push($linea,$combo);
          }
        $value["detalle"]=$linea;
        array_push($salida,$value);
    }

      return $this->correcto($salida);
    }
    return $this->error('Sin datos',201);
  }

}
