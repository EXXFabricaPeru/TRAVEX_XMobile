<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Lbcc;

class LbccController extends ActiveController
{
  use Respuestas;

  public $modelClass = 'backend\models\Usuario';

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


    public function actionIndex() {
        $resultado = Yii::$app->db->createCommand("CALL pa_sincronizarlbcc(:usuario)")
                ->bindValue(':usuario', Yii::$app->request->post('usuario'))
                ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }

    public function actionCreate() {
        $resultado = Yii::$app->db->createCommand("CALL pa_sincronizarlbcc(:usuario)")
                        ->bindValue(':usuario', Yii::$app->request->post('usuario'))->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }

    public function actionUpdate($id) {
        $cant = Yii::$app->request->post('cant');
        $sql = "UPDATE lbcc SET U_NumeroSiguiente = :cant WHERE id = :id";
        return Yii::$app->db->createCommand($sql)->bindValue(':id', $id)->bindValue(':cant', $cant)->execute();
    }

    public function actionActualizar() {
        $siguiente = Yii::$app->request->post('siguiente');
        $autorizacion = Yii::$app->request->post('autorizacion');
        $llave = Yii::$app->request->post('llave');
        if (isset($siguiente)) {
            $sql = "UPDATE lbcc SET U_NumeroSiguiente=".$siguiente." WHERE U_NumeroAutorizacion='".$autorizacion."' AND U_LlaveDosificacion LIKE '".$llave."' ";
        } else {
            $sql = "UPDATE lbcc SET U_NumeroSiguiente=U_NumeroSiguiente+1 WHERE U_NumeroAutorizacion='".$autorizacion."' AND U_LlaveDosificacion LIKE '".$llave."' ";
        }
        $resultado = Yii::$app->db->createCommand($sql)->execute();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        } else {
            return $this->correcto([], "Datos no Coinciden", 201);
        }
    }

    public function actionRevisar() {
        $equipoId = Yii::$app->request->post('equipoId');
        // $llave = Yii::$app->request->post('llave');
        $autorizacion = Yii::$app->request->post('autorizacion');
        //$sql = "SELECT * FROM vi_m_login_b WHERE  U_NumeroAutorizacion='".$autorizacion."' AND U_LlaveDosificacion LIKE '".$llave."' AND equipoId = " . $equipoId.";";
        $sql = "SELECT * FROM vi_m_login_b WHERE  U_NumeroAutorizacion='".$autorizacion."' AND equipoId = " . $equipoId.";";
        $resultado = Yii::$app->db->createCommand($sql)->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        } else {
            return $this->correcto([], "Datos no Coinciden", 201);
        }
    }

    /* {
        
    } */
}
