<?php

/**
 * Created by PhpStorm.
 * User: rafaelgutierrezgaspar
 * Date: 2019-07-01
 * Time: 14:45
 */

namespace api\controllers;

use backend\models\User;
use yii\rest\ActiveController;
use Yii;
use api\traits\Respuestas;

class SolicitudregistroController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\User';

    /**
     * @var User $modelUser
     */
    public $modelUser;

    /*public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }*/

    protected function verbs() {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions() {
        $this->modelUser = new User();
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex() {
        $solicitudes = User::find()->select(['id','username','idPersona','plataformaUsuario','plataformaPlataforma','plataformaEmei'])->where(['estadoUsuario'=>2])->with(['persona'=>function($query){
            $query->select(['idPersona','nombrePersona','apellidoPPersona','apellidoMPersona']);
        }])->asArray()->all();
        if (count($solicitudes) > 0) {
            return $this->correcto($solicitudes,'OK');
        }
        return $this->correcto([],"Sin solicitudes",201);
    }

    public function actionView($id) {
        if (count($this->modelUser->findByDocumentoIdentidadPersona($id))>0) {
            return $this->correcto($this->modelUser->findByDocumentoIdentidadPersona($id)[0],"Persona encontrada",200);
        }
        return $this->correcto([],"Persona no encontrada",201);
    }

    public function actionCreate() {
        $data = Yii::$app->request->post();
        $validacion = [];
        foreach ($data as $value) {
            if ($value == '' || is_null($value)) {
                array_push($validacion,false);
            }
        }
        if (count($validacion) > 0) {
            return $this->error("Existen campos vacios",101);
        }
        return $this->modelUser->paInsertSolicitudRegistro($data);
    }
    
    public function actionUpdate($id)
    {
        $usuario = User::findOne(["id"=>$id]);
        if (is_null($usuario)) {
            return $this->correcto([],'Usuario no encontrado',201);
        }
        $usuario->estadoUsuario = Yii::$app->request->post('estadoUsuario');
        if ($usuario->save(false)) {
            return $this->correcto([],'Se actualizo Correctamente');
        }
        return $this->error('Error al actualizar');
    }
}
