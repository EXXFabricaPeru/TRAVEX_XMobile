<?php

  namespace api\controllers;

  use yii;
  use yii\rest\ActiveController;
  use backend\models\Servislayer;
  use yii\filters\auth\QueryParamAuth;
  use backend\models\Listaprecios;
  use api\traits\Respuestas;

  class ListapreciosController extends ActiveController
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

    public function actionIndex()
    {
      $listaPrecios = Yii::$app->db->createCommand("select * from v_listaprecios")->queryAll();
      return $this->correcto($listaPrecios);
    }

    public function actionFindonebypricelistno($priceListNo)
    {
      $oPriceListNo = Listaprecios::find()->where(['like', 'PriceListNo', $priceListNo])->one();
      if (is_object($oPriceListNo)) {
        return $this->correcto($oPriceListNo);
      } else {
        return $this->error();
      }

    }

    public function actionCreate()
    {
      $listaPrecios = Yii::$app->db->createCommand("CALL pa_obtenerProductosListaPrecios(:usuario,:texto,:lstprecio)")
        ->bindValue(':usuario', Yii::$app->request->post('usuario'))
        ->bindValue(':texto', Yii::$app->request->post('texto', 0))
        ->bindValue(':lstprecio', Yii::$app->request->post('lstprecio', 0))
        ->queryAll();
      if (count($listaPrecios) > 0) {
        return $this->correcto($listaPrecios);
      }
      return $this->error('Sin datos', 201);
    }

    public function actionFindbypricelistuser()
    {
      $listaPrecios = Yii::$app->db->createCommand("SELECT clientes.PriceListNum, listaprecios.PriceListName FROM clientes LEFT JOIN listaprecios ON clientes.PriceListNum = listaprecios.PriceListNo GROUP BY clientes.PriceListNum")
        ->queryAll();
      if (count($listaPrecios)){
        return $this->correcto($listaPrecios);
      }
      return $this->error('Sin datos',201);
    }
  }
