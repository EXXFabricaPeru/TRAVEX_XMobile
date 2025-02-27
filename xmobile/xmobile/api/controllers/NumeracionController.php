<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Numeracion;

class NumeracionController extends ActiveController
{

  use Respuestas;

  public $modelClass = 'backend\models\Numeracion';

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

  public function actionView($id){
	/*	$sql = 'SELECT 
    numeracion.id,
    (numeracion.numcli+1) as numcli,
    (numeracion.numdof+1) as numdof,
    (numeracion.numdoe+1) as numdoe,
    (numeracion.numdfa+1) as numdfa,
    (numeracion.numdop+1)as numdop,
    (numeracion.numgp+1) as numgp,
    (numeracion.numgpa+1) as numgpa,
    (numeracion.numccaja+1)as numccaja,
    numeracion.iduser FROM numeracion  WHERE iduser = '.$id;
		return Numeracion::findBySql($sql)->one(); */
		$sql = 'SELECT * FROM vi_numeracionusuario  WHERE iduser = '.$id;
		return Numeracion::findBySql($sql)->one(); 
  }	
  
  public function actionCreate(){
	$data =  Yii::$app->request->post();
	$sql = "SET @p0 = '".$data["numcli"]."';SET @p1 = '".$data["numdof"]."';SET @p2 = '".$data["numdoe"]."';SET @p3 = '".$data["numdfa"]."';SET @p4 = '".$data["numdop"]."';SET @p5 = '".$data["numgp"]."';SET @p6 = '".$data["numgpa"]."';SET @p7 = '".$data["numccaja"]."';SET @p8 = '".$data["iduser"]."';CALL `actionNumeracion`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8);";
  return $responseContacto = Yii::$app->db->createCommand($sql)->execute();
  }	
  
    public function actionUpdate($id) {
        $data = Yii::$app->request->post();
        $sql = '';
        switch ($data['tipo']) {
            case('DOP'):
                $sql = ' UPDATE numeracion SET numdop = (numdop+1) WHERE iduser = ' . $id;
                break;
            case('DOF'):
                $sql = ' UPDATE numeracion SET numdof = (numdof+1) WHERE iduser = ' . $id;
                break;
            case('DFA'):
                $sql = ' UPDATE numeracion SET numdfa = (numdfa+1) WHERE iduser = ' . $id;
                break;
            case('DOE'):
                $sql = ' UPDATE numeracion SET numdoe = (numdoe+1) WHERE iduser = ' . $id;
                break;
        }
        return Yii::$app->db->createCommand($sql)->execute();
    }

    public function actionActualizanum() {
      Yii::error("ACTUALIZA LA NUMERACION DESDE EL XMOBILE");
      $idUser=Yii::$app->request->post('idUser');
      $numerox=Yii::$app->request->post('numerox');
      $tipoDoc=Yii::$app->request->post('tipoDoc');

      switch ($tipoDoc) {
        case('DOP'):
            $sql = ' UPDATE numeracion SET numdop = '.$numerox.' WHERE iduser = ' . $idUser;
            break;
        case('DOF'):
            $sql = ' UPDATE numeracion SET numdof = '.$numerox.' WHERE iduser = ' . $idUser;
            break;
        case('DFA'):
            $sql = ' UPDATE numeracion SET numdfa = '.$numerox.' WHERE iduser = ' . $idUser;
            break;
        case('DOE'):
            $sql = ' UPDATE numeracion SET numdoe = '.$numerox.' WHERE iduser = ' . $idUser;
            break;
      }
      Yii::error($sql);
      return Yii::$app->db->createCommand($sql)->execute();

   }
  
}
