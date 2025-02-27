<?php

namespace backend\controllers;

use Yii;
use backend\models\TipoCambioParalelo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * PersonaController implements the CRUD actions for Persona model.
 */
class TipocambioparaleloController extends Controller
{
    public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'rules' => [
          // deny all POST requests
          [
            'actions' => [
              'index',
              'create'
            ],
            'allow' => true,
            'roles' => ['@'],
          ],
          // everything else is denied
        ],
      ],
    ];
  }

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
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex(){
        $resultado = Yii::$app->db->createCommand('SELECT * FROM tipoCambioParalelo WHERE fecha = "'.date('Y-m-d').'"' )
                    ->queryAll();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (count($resultado)){
          return $resultado;// $this->correcto($resultado);
        }
        else{
          $arrayError= [];
          $error = ['tipoCambio' => '0.00'];
          array_push($arrayError,$error);
          return $arrayError;
        }
      }

      public function actionCreate(){
        $usuario = Yii::$app->user->identity->getId();
        $resultado = Yii::$app->db->createCommand("CALL pa_tipocambioparalelo(:tipocambio,:usuario,:estado)")
                ->bindValue(':tipocambio', Yii::$app->request->post('tipocambio'))
                ->bindValue(':usuario', $usuario)
                ->bindValue(':estado', '1')
                ->execute();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($resultado = 1) {
            return  'OK';
        }
        return 'ERROR';
      }
}
