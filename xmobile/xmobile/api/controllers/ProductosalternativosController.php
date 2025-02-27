<?php

namespace api\controllers;

use backend\models\Clientes;
use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use api\traits\Respuestas;
use backend\models\Productosalternativos;

class ProductosalternativosController extends ActiveController {

    public $modelClass = 'backend\models\Usuario';

    use Respuestas;
    /* public function init()
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
      } */

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

    public function actionIndex() {
      $oProducto = Yii::$app->db->createCommand('SELECT * FROM productosalternativos')
        ->queryAll();
      return $oProducto;
      if (count($oProducto) > 0) {
        return $this->correcto($oProducto);
      }
      return $this->error('Sin datos',201);
    }

    public function actionBuscar() {
      $oProducto = Yii::$app->db->createCommand('SELECT * FROM productosalternativos WHERE ItemCode = :itemReal AND ComboCode=:combo AND (BarCode = :alternativo OR ItemCodeAlternative = :alternativo)')
        ->bindValue(':itemReal' ,Yii::$app->request->post('itemreal'))
        ->bindValue(':alternativo' ,Yii::$app->request->post('itemcode'))
        ->bindValue(':combo' ,Yii::$app->request->post('combo'))
        ->queryOne();
      if ($oProducto) {
        return $this->correcto($oProducto);
      }
      return $this->error('Sin datos',201);
    }

    public function actionListartodos(){
      
      //$oProducto = Yii::$app->db->createCommand('SELECT * FROM productosalternativos WHERE ItemCode = :itemReal AND ComboCode=:combo ;')
      $oProducto = Yii::$app->db->createCommand('CALL pa_lista_productos_alternativos(:itemReal, :combo, :almacen)')
      ->bindValue(':itemReal' ,Yii::$app->request->post('itemreal'))
      ->bindValue(':combo' ,Yii::$app->request->post('combo'))
      ->bindValue(':almacen' ,Yii::$app->request->post('almacen'))
      ->queryAll();
      
      $outProductos = [];
      if (count($oProducto)) {
        foreach($oProducto as $producto){
          array_push($outProductos, $producto);
        }
      }
    if ($oProducto) {
      return $this->correcto($outProductos);
    }
    return $this->error('Sin datos',201);
    }

    public static function actionFindonebyitemcode($itemCode){
      $oProducto = Yii::$app->db->createCommand('SELECT * FROM productosalternativos WHERE ComboCode = :itemCode')
                  ->bindValue(':itemCode' ,$itemCode)
                  ->queryAll();
      if (count($oProducto) > 0) {      
        return $oProducto;// $this->correcto($oProducto);
      }
      return null;// $this->error('Sin datos',201);
    }


}
