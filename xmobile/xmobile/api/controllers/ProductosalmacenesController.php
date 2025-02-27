<?php

namespace api\controllers;

use Yii;
use api\traits\Respuestas;
use yii\rest\ActiveController;
use backend\models\Productosalmacenes;
use backend\models\Copiaproductosalmacenes;
use backend\models\Usuariosincronizamovil;
use backend\models\Sap;

class ProductosalmacenesController extends ActiveController
{
    use Respuestas;

    public $modelClass = 'backend\models\Productosalmacenes';

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

  public function actionCreate()
  {
    $usuario=Yii::$app->request->post('usuario');
    $salto=Yii::$app->request->post('pagina');
    $productosAlacenes= Yii::$app->db->createCommand("select * from vi_obtenerproductosalmacen order by ItemCode limit 1000 OFFSET {$salto}")->queryAll();

    $productosAlacenes = Yii::$app->db->createCommand('CALL pa_obtenerProductosAlmacenes(:usuario,:texto,:terminal,:contador,:salto)')
                        ->bindValue('usuario',Yii::$app->request->post('usuario'))
                        ->bindValue('texto',Yii::$app->request->post('texto',0))
                        ->bindValue('terminal',Yii::$app->request->post('equipo','0'))
                        ->bindValue('contador',0)
                        ->bindValue(':salto',Yii::$app->request->post('pagina',0))
                        ->queryAll();
    if (count($productosAlacenes) > 0) {
        return $this->correcto($productosAlacenes,'OK');
    }
    return $this->correcto([],'Sin datos',201);
  }

  public function actionContador(){
    Yii::error("DATA campo y valor : " .json_encode(Yii::$app->request->post()));
    $usuario=Yii::$app->request->post('usuario');
    //$resultado=Yii::$app->db->createCommand("Select count(*) as contador from productosalmacenes where InStock >0")->queryOne();
    $usuariosincronizamovil= new Usuariosincronizamovil();
    $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Productosalmacenes');

    $resultado = Yii::$app->db->createCommand('CALL pa_obtenerProductosAlmacenes(:usuario,:texto,:terminal,:contador,:salto)')
                        ->bindValue('usuario',Yii::$app->request->post('usuario'))
                        ->bindValue('texto',Yii::$app->request->post('texto',0))
                        ->bindValue('terminal',Yii::$app->request->post('equipo','0'))
                        ->bindValue('contador',1)
                        ->bindValue('salto',0)
                        ->queryOne();

    return $this->correcto($resultado, 'OK'); 
  }

  public static function actionFindonebyitemcode($itemCode){
    $sql ="SELECT p.*, l.WarehouseName FROM productosalmacenes p, almacenes l where p.ItemCode='".trim($itemCode,"?").
    "' AND p.InStock<>'0' AND p.WarehouseCode=l.WarehouseCode";
  $oProducto = Yii::$app->db->createCommand($sql)->queryAll();
  //$oProducto = Productosprecios::find()->where(['ItemCode' => trim($itemCode,'?')])->all();
  if (count($oProducto) > 0) {
      return $oProducto;//$this->correcto($oProducto);
    }
    return $oProducto;//$this->error('Sin datos',201);



/*
$oProducto = Productosalmacenes::find()                                    
                              //->where(['ItemCode' => trim($itemCode,'?')])
                              ->where("ItemCode = '".trim($itemCode,'?')."' AND InStock <> '0'")->all();
if (count($oProducto) > 0) {      
return $oProducto;//$this->correcto($oProducto);
}
return $oProducto;//$this->error('Sin datos',201);
return $oProducto;//$this->error('Sin datos',201); */
  }

  public function actionProductoenalmacen(){
    $producto = Yii::$app->request->post('producto');
    $almacen = Yii::$app->request->post('almacen');
    // select inStock from productosalmacenes where productosalmacenes.ItemCode=facturasproductos.ItemCode and productosalmacenes.WarehouseCode= facturasproductos.WhsCode
    $sql="Select * from vi_productosalmacenes where ItemCode='".$producto."' and  if (producto_std10='tNO',ItemCode IS NOT NULL,WarehouseCode='".$almacen."')";
    $productoEnAlmacen = Yii::$app->db->createCommand($sql)->queryOne();
    if (isset($productoEnAlmacen["ItemCode"])) {
      return $this->correcto([
        'producto' => $producto,
        'almacen' => $almacen,
        'encontrado' => isset($productoEnAlmacen["ItemCode"]),
        'stock' => $productoEnAlmacen["InStock"]
        ]);
    } else {
      return $this->error('Sin datos',201);
    }
  }
  
  public function actionGuardarproductosalmacenes(){
    $datos = Yii::$app->request->post();
    $producto = $datos["producto"];
    $p = new copiaproductosalmacenes();
    $p->id = null;
    $p->ItemCode = $producto["ItemCode"];
    $p->WarehouseCode = $producto["WhsCode"];
    $p->InStock = $producto["OnHand"];
    $p->Committed = $producto["IsCommited"];
    $p->Locked = $producto["Locked"];
    $p->Ordered = $producto["OnOrder"];
    $p->User = 1;
    $p->Status = 1;
    $p->DateUpdate = date("Y-m-d");
    //$p->save();
    return $this->correcto($p);
  }
  public function actionProductosalmacensap(){
    $sap= new Sap();
    Yii::error("PRODUCTOS ALMACEN SAP: ");
    $codigo=Yii::$app->request->post('codigo');
    $almacen=Yii::$app->request->post('almacen');
    $resultado = $sap->Productos_por_almacen($codigo,$almacen);
    Yii::error($resultado);
    
    return $this->correcto($resultado, 'OK');
  }
}
