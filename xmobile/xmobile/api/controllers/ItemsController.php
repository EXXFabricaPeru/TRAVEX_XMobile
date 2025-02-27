<?php

namespace api\controllers;

use backend\models\Productos;
use backend\models\Copiaproductos;
use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use api\controllers\ProductosalternativosController;
use api\controllers\CombosController;
use api\controllers\ProductosalmacenesController;
use api\controllers\ProductospreciosController;
use api\controllers\Lotesproductos;
use backend\models\Usuariosincronizamovil;

class ItemsController extends ActiveController
{

  use Respuestas;
  public $modelClass = 'backend\models\Usuario';

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
      //'index' => ['GET', 'HEAD'],
      //'view' => ['GET', 'HEAD'],
      'index' => ['POST'],
      'view' => ['POST'],
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
  { }

  public function actionCreate()
  {
    if(Yii::$app->request->post('almacen')){
      $aux_almacen=Yii::$app->request->post('almacen');
     
    }else{
      $aux_almacen="0";
    }
    //return $aux_almacen;

    $usuario=Yii::$app->request->post('usuario');
    $salto=Yii::$app->request->post('pagina');
    //$resultado= Yii::$app->db->createCommand("select * from vi_obtenerproductos order by ItemCode limit 1000 OFFSET {$salto}")->queryAll();

   $resultado = Yii::$app->db->createCommand("CALL pa_obtenerProductos(:usuario,:texto,:almacen,:sucursal,:salto)")
      ->bindValue(':usuario', Yii::$app->request->post('usuario'))
      ->bindValue(':texto', Yii::$app->request->post('texto', 0))
      ->bindValue(':almacen', $aux_almacen)
      ->bindValue(':sucursal', Yii::$app->request->post('sucursal'))
      ->bindValue(':salto', Yii::$app->request->post('pagina'))
      ->queryAll();
    //die($usuario);
    if (count($resultado) > 0) {
      return $this->correcto($resultado, 'OK');
    }
    return $this->correcto([], "No se encontro Datos", 201);
    
  }

  public function actionContador(){
    $usuario=Yii::$app->request->post('usuario');
    $resultado=Yii::$app->db->createCommand("Select count(*) as contador from productos")->queryOne();
    $usuariosincronizamovil= new Usuariosincronizamovil();
    $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Items');
    return $this->correcto($resultado, 'OK'); 
  }


  public function actionFindonebyitemcode($itemCode, $priceList){
      $items = Productos::find()->where(['like', 'ItemCode', trim($itemCode,'?')])->one();
      $alternativos = ProductosalternativosController::actionFindonebyitemcode($itemCode);
      $combos = CombosController::actionFindonebyitemcode($itemCode);
      $vPrecios = explode(',',$priceList);
      $resultPrecios = [];
      foreach($vPrecios as $p){
        $precios = ProductospreciosController::actionFindonebyitemcode($itemCode, $p);
        foreach($precios as $v) array_push($resultPrecios, $v);
      }
      $almacenes = ProductosalmacenesController::actionFindonebyitemcode($itemCode);
      $resultLotes = [];      
      $lotes = LotesproductosController::actionFindbyalmacen($itemCode);
      foreach($lotes as $l) array_push($resultLotes, $l);
      $result = [
        'items' => $items,
        'precios' => $resultPrecios,
        'almacenes' => $almacenes,
        'combos' => $combos,
        'alternativos' => $alternativos,
        'lotes' => $resultLotes
      ];
      if (is_object($items)) {
          return $this->correcto($result);
      } else {
          return $this->error();
      }
  }

  public function actionFinditemsbyname($texto, $almacen) {
    $sql= 'SELECT p.*, a.InStock FROM productosalmacenes a,productos p WHERE a.WarehouseCode = :almacen AND a.ItemCode=p.ItemCode AND (p.ItemCode Like :texto OR p.ItemName Like :texto)';
    $almacenes = Yii::$app->db->createCommand($sql)
                ->bindValue(':almacen', $almacen)
                ->bindValue(':texto', $texto)
                ->queryAll();
    //$almacenes = Almacenes::find()->where(['like','WarehouseCode', $texto])->asArray()->all();
    if (count($almacenes) > 0) {
        return $this->correcto($almacenes);
    }
    return $this->error('Sin datos', 201);
  }

  public function actionGuardarproducto(){
      $datos = Yii::$app->request->post();
      $producto = $datos["producto"];
      $p = new Copiaproductos();
      $p->id = null;
      $p->ItemCode = $producto['ItemCode'];
      $p->ItemName = $producto['ItemName'];
      $p->ItemsGroupCode = $producto['ItmsGrpCod'];
      $p->ForeignName = $producto['FrgnName'];
      $p->CustomsGroupCode = $producto['CstGrpCode'];
      $p->BarCode = $producto['CodeBars'];
      $p->PurchaseItem = $producto['PrchseItem'];
      $p->SalesItem = $producto['SellItem'];
      $p->InventoryItem = $producto['InvntItem'];
      $p->UserText = $producto['UserText'];
      $p->SerialNum = '';
      $p->QuantityOnStock = $producto['OnHand'];
      $p->QuantityOrderedFromVendors = $producto['IsCommited'];
      $p->QuantityOrderedByCustomers = $producto['OnOrder'];
      $miaux_series = 0;
      if($producto['ManSerNum'] == "tYES") $miaux_series = 1;
      $p->ManageSerialNumbers = $miaux_series;
      $miaux_lotes = 0;
      if($producto['ManBtchNum'] == "tYES") $miaux_lotes = 1;
      $p->ManageBatchNumbers = $miaux_lotes;
      $p->SalesUnit = $producto['SalUnitMsr'];
      $p->SalesUnitLength = $producto['SLength1'];
      $p->SalesUnitWidth = $producto['SWidth1'];
      $p->SalesUnitHeight = $producto['BHeight1'];
      $p->SalesUnitVolume = $producto['SVolume'];
      $p->PurchaseUnit = $producto['BuyUnitMsr'];
      $p->DefaultWarehouse = $producto['DfltWH'];
      $p->ManageStockByWarehouse = $producto['ByWh'];
      $p->ForceSelectionOfSerialNumber = $producto['EnAstSeri'];
      $p->Series = $producto['Series'];
      $p->UoMGroupEntry = $producto['UgpEntry'];
      $p->DefaultSalesUoMEntry = $producto['SUoMEntry'];
      $p->User = 1;
      $p->Status = 1;
      $p->DateUpdate = date('Y-m-d');
      $p->Manufacturer = $producto['FirmCode'];
      $p->NoDiscounts = $producto['NoDiscount'];
      $p->created_at = $producto['CreateDate'];
      $p->updated_at = $producto['UpdateDate'];

      $miaux_combo = 0;
      if ($producto["TreeType"] == "iTemplateTree") $miaux_combo = 1;
      $p->combo = $miaux_combo;
      //$p->save();
      return $this->correcto($p);
  }
}
