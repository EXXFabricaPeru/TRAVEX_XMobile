<?php

namespace api\controllers;

use api\traits\Respuestas;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use common\models\User;
use Carbon\Carbon;

class UsuarioController extends ActiveController
{

    use Respuestas;
    public $modelClass = 'backend\models\User';

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

    public function actionCreate()
    {
        $usuario = new User();
        $usuario->username = Yii::$app->request->post('username');
        $usuario->auth_key = Yii::$app->request->post('auth_key');
        $usuario->password_hash = Yii::$app->getSecurity()->generatePasswordHash(Yii::$app->request->post('password_hash'));
        $usuario->password_reset_token = Yii::$app->request->post('password_reset_token');
        $usuario->status = 10;
        $usuario->verification_token = Yii::$app->request->post('verification_token');
        $usuario->idPersona = Yii::$app->request->post('idPersona');
        $usuario->estadoUsuario = 1;
        $usuario->fechaUMUsuario = date("Y-m-d");
        $usuario->plataformaUsuario = Yii::$app->request->post('plataformaUsuario');
        $usuario->plataformaPlataforma = Yii::$app->request->post('plataformaPlataforma');
        $usuario->plataformaEmei = Yii::$app->request->post('plataformaEmei');
        $usuario->reset = 0;
        if ($usuario->save(false)) {
            return [
                "estado" => 200,
                "respuesta" => [],
                "mensaje" => "Registro Correcto"
            ];
        }
        return [
            "estado" => 100,
            "mensaje" => "Registro no Correcto"
        ];
    }

    public function actionFindusersbyusername($username)
    {
        $users = Yii::$app->db->createCommand("SELECT * FROM `user` WHERE `username` LIKE '%" . $username . "%'")
            ->queryAll();
        if ($users) {
            if (count($users) > 0) {
                return $this->correcto($users);
            }
        }
        return $this->error();
    }

    public function actionFindonebyusername($username)
    {
        $user = Yii::$app->db->createCommand("SELECT * FROM `user` WHERE `username` = '" . $username . "'")
            ->queryOne();
        if ($user) {
            if (count($user)) {
                return $this->correcto($user);
            }
        }
        return $this->error();
    }
    public function actionFindonebyid($id)
    {
        $user = Yii::$app->db->createCommand("SELECT * FROM `user` WHERE `id` = '" . $id. "'")
            ->queryOne();
        if ($user) {
            if (count($user)) {
                return $this->correcto($user);
            }
        }
        return $this->error();
    }

    public function actionFindonebyusernameandpassword()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password_hash');
        $user = Yii::$app->db->createCommand("SELECT * FROM `user` WHERE `username` = '" . $username . "' and `password_hash` = '" . $password . "'")
            ->queryOne();
        if ($user) {
            if (count($user)) {
                return $this->correcto($user);
            }
        }

        return $this->error();
    }

    public function actionGetemeissolicitados(){
        $users = Yii::$app->db->createCommand("SELECT id,username,plataformaEmei FROM `user` WHERE estadoUsuario = 2 ")
            ->queryAll();
        if ($users) {
            if (count($users)) {
                return $this->correcto($users);
            }
        }
        return $this->error();
    }

    public function actionDeleteuser(){
        $id = Yii::$app->request->post('id');
        if($id){
            $modelUser = User::find()->where(['id' => $id])->one();
            $modelUser->setAttribute('estadoUsuario',0);
            $modelUser->save(false);
            return $this->correcto($modelUser->toArray());
        }
        return $this->error();
    }

}
