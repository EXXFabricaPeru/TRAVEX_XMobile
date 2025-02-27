<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\User;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;

class UserController extends ActiveController
{

    use Respuestas;

    public $modelClass = 'backend\models\User';

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

    private function getTipe($id)
    {
        if ($id == 1) {
            return "m";
        } else {
            return "w";
        }
    }

    public function actionIndex()
    {
        $sql = "SELECT * FROM v_usuariosactivos where estadoUsuario != 0";
        $datos = Yii::$app->db->createCommand($sql)->queryAll();
        if (count($datos) > 0) {
            return $this->correcto($datos, 'OK');
        }
        return $this->correcto([], 'Sin datos', 201);
    }

    public function actionCreate()
    {
        $datos = Yii::$app->db->createCommand('CALL pa_obtenerUsuarios(:usuario)')
            ->bindValue(':usuario', Yii::$app->request->post('usuario'))
            ->queryAll();
        if (count($datos) > 0) {
            return $this->correcto($datos, 'OK');
        }
        return $this->correcto([], 'Sin datos', 201);
    }

    public function actionInsertuser()
    {
		Yii::error('InsertarUsusario' . json_encode(Yii::$app->request->post()));
        $username = Yii::$app->request->post('username');
        $auth_key = Yii::$app->request->post('auth_key');
        $password_hash = Yii::$app->getSecurity()->generatePasswordHash(Yii::$app->request->post('password_hash'));
        $password_reset_token = Yii::$app->request->post('password_reset_token');
        $status = Yii::$app->request->post('status');
        $created_at = Yii::$app->request->post('created_at');
        $updated_at = Yii::$app->request->post('updated_at');
        $verification_token = Yii::$app->request->post('verification_token');
        $access_token = Yii::$app->request->post('access_token');
        $idPersona = Yii::$app->request->post('idPersona');
        $estadoUsuario = Yii::$app->request->post('estadoUsuario');
        $fechaUMUsuario = Yii::$app->request->post('fechaUMUsuario');
        $plataformaUsuario = Yii::$app->request->post('plataformaUsuario');
        $plataformaPlataforma = Yii::$app->request->post('plataformaPlataforma');
        $plataformaEmei = Yii::$app->request->post('plataformaEmei');
        $reset = Yii::$app->request->post('reset');
        $userId = Yii::$app->request->post('userId');

        $sql = "CALL `pa_insertUsuario`(" .
            "'$username'," .
            "'$auth_key'," .
            "'$password_hash'," .
            "10," .
            "$created_at," .
            "$updated_at," .
            "'$verification_token'," .
            "'$access_token'," .
            "$idPersona," .
            "2," .
            "'$fechaUMUsuario'," .
            "'$plataformaUsuario'," .
            "'$plataformaPlataforma'," .
            "'$plataformaEmei'," .
            "0," .
            "'$userId'" .
            ");";
        $response = Yii::$app->db->createCommand($sql)->queryOne();
        if($response){

            if (count($response) == 1) {
                return $this->error('Registro no Correcto', 201);
            } else {
                return $this->correcto($response, 'Registro Correcto');
            }
        }
        return $this->error('Registro no Correcto', 201);
    }
}
