<?php

namespace api\controllers;

use backend\models\Persona;
use yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Usuariopersona;

class PersonaController extends ActiveController
{

    use Respuestas;

    public $modelClass = 'backend\models\Usuariopersona';

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
        //unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $sql = "SELECT idPersona,nombrePersona, apellidoPPersona,apellidoMPersona,fechaUMPersona,estadoPersona,documentoIdentidadPersona"
            . " FROM usuariopersona";
        $respuesta = Yii::$app->db->createCommand($sql)->queryAll();
        if (count($respuesta) > 0) {
            return $this->correcto($respuesta);
        }
        return $this->error('Sin datos', 201);
    }

    public function actionFindpersonbyname()
    {

        $post = Yii::$app->request->post();
        if (count($post)) {
            $filtroLower = strtolower($post['filtro']);

            $sql = "SELECT * FROM usuariopersona WHERE `nombrePersona` LIKE '%" . $filtroLower . "%' OR `apellidoPPersona` LIKE '%" . $filtroLower . "%' OR `apellidoMPersona` LIKE '%" . $filtroLower . "%'";
            $personas = Yii::$app->db->createCommand($sql)->queryAll();

            if (count($personas) > 0) {
                return $this->correcto($personas);
            }
        }
        return $this->error('Sin datos', 201);
    }

    public function actionFindonebyidperson()
    {

        $post = Yii::$app->request->post();
        if (count($post)) {
            $filtroLower = strtolower($post['filtro']);

            $sql = "SELECT * FROM usuariopersona WHERE idPersona LIKE " . $filtroLower;
            $personas = Yii::$app->db->createCommand($sql)->queryAll();

            if (count($personas) > 0) {
                return $this->correcto($personas);
            }
        }
        return $this->error('Sin datos', 201);
    }

    public function actionView()
    {
        //return $_REQUEST;
        //$sql = "SELECT idPersona,nombrePersona, apellidoPPersona,apellidoMPersona,fechaUMPersona,estadoPersona"
        //. " FROM persona where id=1";
        //return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionCreate()
    {
        $nombrePersona = Yii::$app->request->post('nombrePersona');
        $apellidoPPersona = Yii::$app->request->post('apellidoPPersona');
        $apellidoMPersona = Yii::$app->request->post('apellidoMPersona');
        $estadoPersona = Yii::$app->request->post('estadoPersona');
        $fechaUMPersona = date("Y-m-d");
        $documentoIdentidadPersona = Yii::$app->request->post('documentoIdentidadPersona');


        $sql = "CALL `pa_insertPersona`(" .
            "'$nombrePersona'," .
            "'$apellidoPPersona'," .
            "'$apellidoMPersona'," .
            "'$estadoPersona'," .
            "'$fechaUMPersona'," .
            "'$documentoIdentidadPersona'" .
            ");";
        $response = Yii::$app->db->createCommand($sql)->queryOne();
        if (count($response) == 1) {
            return $this->error('Registro no Correcto', 201);
        } else {
            return $this->correcto($response, 'Registro Correcto');

        }
    }
}
