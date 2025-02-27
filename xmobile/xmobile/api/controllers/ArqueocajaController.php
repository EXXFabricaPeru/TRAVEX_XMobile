<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Arqueocaja;
use backend\models\Arqueodetalle;

class ArqueocajaController extends ActiveController{
  use Respuestas;

  public $modelClass = 'backend\models\Arqueocaja';

  protected function verbs(){
    return [
      'index'  => ['GET', 'HEAD'],
      'view'   => ['GET', 'HEAD'],
      'create' => ['POST'],
      'update' => ['PUT', 'PATCH'],
      'delete' => ['DELETE']
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
    
	
  public function actionCreate(){
    $inicial = Yii::$app->request->post('inicial');
    $final = Yii::$app->request->post('final');
    $usuario = Yii::$app->request->post('usuario');
    $tipo = Yii::$app->request->post('tipo');
    if ($tipo == '1'){
        $sql = "SELECT * FROM vi_arqueocaja WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
        $resultado = Yii::$app->db->createCommand($sql)
              ->bindValue(':usuario' , $usuario)
              ->bindValue(':inicial' , $inicial)
              ->bindValue(':final' , $final)
              ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }
    else {
        $sql = "SELECT * FROM vi_arqueodetalle WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
        $resultado = Yii::$app->db->createCommand($sql)
              ->bindValue(':usuario' , $usuario)
              ->bindValue(':inicial' , $inicial)
              ->bindValue(':final' , $final)
              ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }
  }
  
  	public function actionIndex(){
		return Yii::$app->request->post();
		$inicial = Yii::$app->request->post('incial');
		$final = Yii::$app->request->post('final');
		$usuario = Yii::$app->request->post('usuario');
		$sql = "SELECT * FROM vi_arqueocaja WHERE usuario = STR_TO_DATE(:usuario,'%m/%d/%Y') AND (fecha >= :inicial AND fecha <= STR_TO_DATE(:final,'%m/%d/%Y'))";
		$resultado = Yii::$app->db->createCommand($sql)
                  ->bindValue(':usuario' , $usuario)
				  ->bindValue(':inicial' , $inicial)
				  ->bindValue(':final' , $final)
                  ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }
}

  
  
  
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
  } 
  
  protected function verbs()
  {
    return [
      'index'  => ['GET', 'HEAD'],
      'view'   => ['GET', 'HEAD'],
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
	  return "1";
    $inicial = Yii::$app->request->post('incial');
	$final = Yii::$app->request->post('final');
    $usuario = Yii::$app->request->post('usuario');
    $sql = "SELECT * FROM vi_arqueocaja WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
    $resultado = Yii::$app->db->createCommand($sql)
                  ->bindValue(':usuario' , $usuario)
				  ->bindValue(':inicial' , $inicial)
				  ->bindValue(':final' , $final)
                  ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
  }

  public function actionCreate(){
	  return Yii::$app->request->post();
	$inicial = Yii::$app->request->post('incial');
	$final = Yii::$app->request->post('final');
    $usuario = Yii::$app->request->post('usuario');
	$tipo = Yii::$app->request->post('tipo');
	if ($tipo == '1'){
		$sql = "SELECT * FROM vi_arqueocaja WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
		$resultado = Yii::$app->db->createCommand($sql)
                  ->bindValue(':usuario' , $usuario)
				  ->bindValue(':inicial' , $inicial)
				  ->bindValue(':final' , $final)
                  ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
	}
	else {
		$sql = "SELECT * FROM vi_arqueodetalle WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
		$resultado = Yii::$app->db->createCommand($sql)
                  ->bindValue(':usuario' , $usuario)
				  ->bindValue(':inicial' , $inicial)
				  ->bindValue(':final' , $final)
                  ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
		}
	}
  
  public function actionArqueo(){
	$inicial = Yii::$app->request->post('incial');
	$final = Yii::$app->request->post('final');
    $usuario = Yii::$app->request->post('usuario');
	$tipo = Yii::$app->request->post('tipo');
	if ($tipo == '1'){
		$sql = "SELECT * FROM vi_arqueocaja WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
		$resultado = Yii::$app->db->createCommand($sql)
                  ->bindValue(':usuario' , $usuario)
				  ->bindValue(':inicial' , $inicial)
				  ->bindValue(':final' , $final)
                  ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
	}
	else {
		$sql = "SELECT * FROM vi_arqueodetalle WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
		$resultado = Yii::$app->db->createCommand($sql)
                  ->bindValue(':usuario' , $usuario)
				  ->bindValue(':inicial' , $inicial)
				  ->bindValue(':final' , $final)
                  ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
	}
  }
*/

