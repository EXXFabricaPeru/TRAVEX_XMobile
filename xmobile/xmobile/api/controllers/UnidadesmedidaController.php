<?php

  namespace api\controllers;

  use yii;
  use backend\models\Unidadesmedida;
  use yii\rest\ActiveController;
  use api\traits\Respuestas;

  class UnidadesmedidaController extends ActiveController
  {

    use Respuestas;

    public $modelClass = 'backend\models\Unidadesmedida';

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

    public function actionIndex()
    {
      $unidadesMedida = Unidadesmedida::find()
                                      ->where('')
                                      ->all();
      if (count($unidadesMedida)) {
        return $this->correcto($unidadesMedida);
      }
      return $this->error('Sin datos', 201);
    }

    public function actionFindunidades($texto, $producto){
      $sql = 'SELECT DISTINCT u.* FROM productosprecios p, unidadesmedida u'.
             ' WHERE p.ItemCode = :producto AND p.IdUnidadMedida = u.id AND  (u.Code Like :texto OR u.Name Like :texto)';

      $unidades = Yii::$app->db->createCommand($sql)
                    ->bindValue(':texto', $texto)
                    ->bindValue(':producto', $producto)
                    ->queryAll();
        if (count($unidades) > 0) {
            return $this->correcto($unidades);
        }
        return $this->error('Sin datos', 201);
    }

  }
