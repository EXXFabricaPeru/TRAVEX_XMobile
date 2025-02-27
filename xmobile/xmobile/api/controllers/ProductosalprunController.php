<?php

namespace api\controllers;

use Yii;
use Exception;
use Carbon\Carbon;
use api\traits\Respuestas;
use yii\rest\ActiveController;
use backend\models\Seriesproductos;
use backend\models\Productos;
use yii\filters\auth\QueryParamAuth;

class ProductosalprunController extends ActiveController
{
    use Respuestas;

    public $modelClass = 'backend\models\Productosalmacenes';

  /*public function init()
  {
    parent::init();
    Yii::$app->user->enableSession = false;
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

  public function actionCreate()
  {
    $productosAlmacenes = Yii::$app->db->createCommand('CALL pa_validar_productos(:almacen,:listanum,:producto)')
                        ->bindValue(':almacen',Yii::$app->request->post('almacen'))
                        ->bindValue(':listanum',Yii::$app->request->post('lista'))
                        ->bindValue(':producto',Yii::$app->request->post('producto'))
                        ->queryAll();
    if (count($productosAlmacenes) > 0) {
      $resultado = Productos::find()->where(['like', 'ItemCode', Yii::$app->request->post('producto')])->One();
        $productoCabecera = [
            "ItemCode" => $productosAlmacenes[0]["ItemCode"],
            "WarehouseCode" => $productosAlmacenes[0]["WarehouseCode"],
            "Combo" => $resultado->combo
        ];
        $productoDetalle = [];
        foreach ($productosAlmacenes as $value ){
            unset($value["ItemCode"]);
            unset($value["warehouseCode"]);
            array_push($productoDetalle,$value);
        }
        $producto=[
          "cabecera" => $productoCabecera,
          "detalle" => $productoDetalle,
          "combo" => $this->combo(Yii::$app->request->post('producto'),Yii::$app->request->post('almacen')),
          "descuento" => $this->descuento(Yii::$app->request->post('producto'),Yii::$app->request->post('lista'),Yii::$app->request->post('cliente'),Yii::$app->request->post('grupo')),
          "series" => $this->series(Yii::$app->request->post('producto'),Yii::$app->request->post('almacen'))
        ];
        return $this->correcto($producto,'OK');
    }
    return $this->correcto([],'Sin datos',201);
  }

  private function combo($item,$almacen)
  {
    $query="SELECT *,
          (SELECT WarehouseCode from productosalmacenes where productosalmacenes.ItemCode COLLATE utf8_spanish_ci=v_combos.ItemCode and productosalmacenes.WarehouseCode='{$almacen}') as almacenCode,    
          (SELECT InStock from productosalmacenes where productosalmacenes.ItemCode COLLATE utf8_spanish_ci= v_combos.ItemCode and productosalmacenes.WarehouseCode='{$almacen}') as stockcomboalmacen
          FROM v_combos WHERE TreeCode COLLATE utf8_spanish_ci = '{$item}'";
    $combos = Yii::$app->db->createCommand($query)->queryAll();
        if (count($combos)){
          return $combos;
        }
        return 0;
  }

  private function descuento($item,$listaPrecio,$cliente,$grupo)
  {
    
    $descuentos = Yii::$app->db->createCommand("SELECT tipodescuento,
                    ItemCode,CardCode,PriceListNum,
                    Price,Currency,DiscountPercent,
                    paid,free,max,prioridad,
                    linea,ValidTo,ValidFrom
                    FROM descuentos
                    WHERE
                    ItemCode = :itemCode AND
                    ((CardCode = :CardCode) OR
                    (CardCode = :GroupCode) OR
                    (CardCode = '*') OR
                    (PriceListNum = :PriceListNum))
                    and
                    (((ValidFrom='0000-00-00')and (ValidTo='0000-00-00')) or (:fecha BETWEEN ValidFrom and ValidTo) ) 
                    ORDER BY prioridad ASC,linea ASC,
                    DiscountPercent ASC LIMIT 1;")
                    ->bindValue(':itemCode',$item)
                    ->bindValue(':CardCode',$cliente)
                    ->bindValue(':GroupCode',$grupo)
                    ->bindValue(':PriceListNum',$listaPrecio)
                    ->bindValue(':fecha',Carbon::today()->format('Y-m-d'))
                    ->queryAll();
          if (count($descuentos)){
            return $descuentos;
          }
          return 0;
  }

  private function series($item,$almacen)
  {
    $series = Seriesproductos::find()
                ->where("ItemCode = '{$item}' AND  WsCode='{$almacen}' AND Status = 1")
                ->all();
    if (count($series)){
      return $series;
    }
    return 0;
  }
}

