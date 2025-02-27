<?php

namespace api\controllers;


use api\traits\Respuestas;
use backend\models\Territorios;
use Yii;
use yii\db\Query;
use yii\rest\ActiveController;

class TerritoriosController extends ActiveController
{
  use Respuestas;
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
  public $modelClass = 'backend\models\Territorios';
  public function actionIndex()
  { 
    if(Yii::$app->request->post('usuario')!=""){
        $idUser = Yii::$app->request->post('usuario');
    }
    elseif(Yii::$app->request->post('iduser')!=""){
        $idUser = Yii::$app->request->post('iduser');
    }
    else{
        $idUser=0;   
    }
      //$idUser = Yii::$app->request->post('usuario');
      Yii::error(Yii::$app->request->post());
      Yii::error("user: ".$idUser);
        
      $sql_usaTerritorio = "SELECT valor FROM configuracion WHERE parametro = 'asig_user_territorio' and estado=1";
      $data_usaTerritorio = Yii::$app->db->createCommand($sql_usaTerritorio)->queryOne();
      
      if($data_usaTerritorio['valor']==1){

        $territorios = (new Query())
                        ->select(['*'])
                        ->from('territorios')
                        ->where('Status=1 and `TerritoryID` in(select idTerritorio from usuariomovilterritoriodetalle where idUserMovil='.$idUser.')')
                        ->orderby('TerritoryID ASC')
                        ->all();
      }else{
        $territorios = (new Query())
                        ->select(['*'])
                        ->from('territorios')
                        ->where('Status=1')
                        ->orderby('TerritoryID ASC')
                        ->all();
      }
    
        if (count($territorios)){
          Yii::error($territorios);
          return $this->correcto($territorios);
        }
        return $this->error('Sin Datos',201);
  }
  
  public function actionContador(){
    if(Yii::$app->request->post('usuario')!=""){
        $idUser = Yii::$app->request->post('usuario');
    }
    elseif(Yii::$app->request->post('iduser')!=""){
        $idUser = Yii::$app->request->post('iduser');
    }
    else{
        $idUser=0;   
    }
    //$idUser = Yii::$app->request->post('usuario');
    Yii::error(Yii::$app->request->post());
    Yii::error("user: ".$idUser);
      
    $sql_usaTerritorio = "SELECT valor FROM configuracion WHERE parametro = 'asig_user_territorio' and estado=1";
    $data_usaTerritorio = Yii::$app->db->createCommand($sql_usaTerritorio)->queryOne();
    
    if($data_usaTerritorio['valor']==1){

      $territorios = (new Query())
                      ->select(['COUNT(*) AS contador'])
                      ->from('territorios')
                      ->where('Status=1 and `TerritoryID` in(select idTerritorio from usuariomovilterritoriodetalle where idUserMovil='.$idUser.')')
                      ->orderby('TerritoryID ASC')
                      ->one();
    }else{
      $territorios = (new Query())
                      ->select(['COUNT(*) AS contador'])
                      ->from('territorios')
                      ->where('Status=1')
                      ->orderby('TerritoryID ASC')
                      ->one();
    }
  
    if (count($territorios)){
      return $this->correcto($territorios,'OK');
    }
    return $this->error('Sin Datos',201);
  }

}
