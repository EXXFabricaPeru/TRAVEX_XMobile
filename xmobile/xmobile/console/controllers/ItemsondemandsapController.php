<?php

  namespace console\controllers;

 
  use backend\models\Configlayer;
  use backend\models\Servislayer; 
  use backend\models\Sincronizar;
  use backend\models\Productos;
  use backend\models\Productosprecios;
  use backend\models\Clientes;
  use Carbon\Carbon;
  use GuzzleHttp\Client;
  use Yii;
  use yii\console\Controller;
  use backend\models\Unidadesmedida;
  use backend\models\Listaprecios;
  use Exception;

  class ItemsondemandsapController extends Controller
  {
    public $id;
    private  $cliente;
    private $conf;
    private $serviceLayer;

    public function __construct($id, $module, $config = [])
    {
      parent::__construct($id, $module, $config);
      $this->conf = new Configlayer();
    
      $this->cliente = new Client([
        'base_uri' => $this->conf->path,
        'timeout' => 30,
        'verify' => false,
        'cookies' => true
      ]);
      $this->serviceLayer = new Servislayer();
      //$this->odbc = new Sincronizar();
    }

    public function actionSicronizarsap()
    {
      set_time_limit(0);
      $items = $this->obtenerItems();
      $items = $this->obtenerClientes();
    }

    private function obtenerItems(){//aqui
		Yii::error("items on demand");
        //$items=Yii::$app->db->createCommand("Select id,ItemCode from detalledocumentos where (actsl=0) or actsl is null")->queryALl();
        $items=Yii::$app->db->createCommand("Select * from vi_obteneritems")->queryALl();
        if (count($items)){
            foreach ($items as $item){
                $this->Actualizarproductos($item["ItemCode"],$item["id"]);
                //Yii::error(json_encode($item));
            }

        }
    }
    private function obtenerItemTraspasos(){
      Yii::error("items on demand Traspasos");
          $items=Yii::$app->db->createCommand("Select id,ItemCode from traspasodetalle where (status=1) and (actsl=0  or actsl is null) ")->queryALl();
          if (count($items)){
              foreach ($items as $item){
                  $this->Actualizarproductostraspaso($item["ItemCode"],$item["id"]);
                  //Yii::error(json_encode($item));
              }
  
          }
      }
    private function obtenerClientes(){//aqui
		Yii::error("clientes on demand");
      //$items=Yii::$app->db->createCommand("Select id,CardCode from cabeceradocumentos where actsl is null  ")->queryALl();
      $items=Yii::$app->db->createCommand("Select id,CardCode from cabeceradocumentos where actsl is null AND (estado = 4 or estado = 7)  ")->queryALl();
      if (count($items)){
          foreach ($items as $item){
             $this->ActualizarClientes($item["CardCode"],$item["id"]);
             // Yii::error('Actualizar Cliente:'.json_encode($item));
          }

      }
  }
    
    public function Actualizarproductos($item,$id){
       // Yii::error("items on demand");
        //$serviceLayer = new Servislayer();
        $odbc = new Sincronizar();
        $data = json_encode(array("accion" => 30,"ItemCode"=>$item));
        $respuesta = $odbc->executex($data);
        $productos = json_decode($respuesta);

        /*$serviceLayer->actiondir = 'Items?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,Properties8,Properties9,Properties10,Properties11,Properties12,Properties13,Properties14,Properties15,Properties16,Properties17,Properties18,Properties19,Properties20,Properties21,Properties22,Properties23,Properties24,Properties25,Properties26,Properties27,Properties28,Properties29,Properties30,Properties31,Properties32,Properties33,Properties34,Properties35,Properties36,Properties37,Properties38,Properties39,Properties40,Properties41,Properties42,Properties43,Properties44,Properties45,Properties46,Properties47,Properties48,Properties49,Properties50,Properties51,Properties52,Properties53,Properties54,Properties55,Properties56,Properties57,Properties58,Properties59,Properties60,Properties61,Properties62,Properties63,Properties64,Manufacturer,NoDiscounts&$filter=ItemCode eq \''.$item.'\'';
        $productos = $serviceLayer->executex(1);
        $productos = $productos->value;     
        $fecha = date("Y-m-d");
       // Yii::error(json_encode($productos)); 
        $actualizacion=Yii::$app->db->createCommand("Update detalledocumentos set actsl=1 where id='".$id."' ")->execute();
        */
        $db = Yii::$app->db;
        $sumatotal=0;
        foreach ($productos as $puntero) {
          $cantidad = round($puntero->OnHand,0);
          $comprometido=round($puntero->IsCommited,0);
          $almacen=$puntero->WhsCode;
          $sumatotal=$cantidad+$sumatotal;
          $sql= "UPDATE productosalmacenes set InStock='{$cantidad}', Committed='{$comprometido}' where ItemCode='".$item."' and  WarehouseCode='".$almacen."'";
          $sql2= "UPDATE detalledocumentos set actsl=1 where id={$id}";
         // Yii::error("items on demand".$sql);
          //$db = Yii::$app->db;
          $db->createCommand($sql)->execute(); 
          $db->createCommand($sql2)->execute();            
        }
        $sql3= "UPDATE productos set QuantityOnStock='{$sumatotal}' where ItemCode='{$item}'";
        $db->createCommand($sql3)->execute();

    }
    public function Actualizarproductostraspaso($item,$id){
       Yii::error("items traspasos on demand");
       //$serviceLayer = new Servislayer();
       $odbc = new Sincronizar();
       $data = json_encode(array("accion" => 30,"ItemCode"=>$item));
       $respuesta = $odbc->executex($data);
       $productos = json_decode($respuesta);

       /*$serviceLayer->actiondir = 'Items?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,Properties8,Properties9,Properties10,Properties11,Properties12,Properties13,Properties14,Properties15,Properties16,Properties17,Properties18,Properties19,Properties20,Properties21,Properties22,Properties23,Properties24,Properties25,Properties26,Properties27,Properties28,Properties29,Properties30,Properties31,Properties32,Properties33,Properties34,Properties35,Properties36,Properties37,Properties38,Properties39,Properties40,Properties41,Properties42,Properties43,Properties44,Properties45,Properties46,Properties47,Properties48,Properties49,Properties50,Properties51,Properties52,Properties53,Properties54,Properties55,Properties56,Properties57,Properties58,Properties59,Properties60,Properties61,Properties62,Properties63,Properties64,Manufacturer,NoDiscounts&$filter=ItemCode eq \''.$item.'\'';
       $productos = $serviceLayer->executex(1);
       $productos = $productos->value;     
       $fecha = date("Y-m-d");
      // Yii::error(json_encode($productos)); 
       $actualizacion=Yii::$app->db->createCommand("Update detalledocumentos set actsl=1 where id='".$id."' ")->execute();
       */
       $db = Yii::$app->db;
       $sumatotal=0;
       foreach ($productos as $puntero) {
         $cantidad = round($puntero->OnHand,0);
         $comprometido=round($puntero->IsCommited,0);
         $almacen=$puntero->WhsCode;
         $sumatotal=$cantidad+$sumatotal;
         $sql= "UPDATE productosalmacenes set InStock='{$cantidad}', Committed='{$comprometido}' where ItemCode='".$item."' and  WarehouseCode='".$almacen."'";
         $sql2= "UPDATE traspasodetalle set actsl=1 where id={$id}";
        // Yii::error("items on demand".$sql);
         //$db = Yii::$app->db;
         $db->createCommand($sql)->execute(); 
        // $db->createCommand($sql2)->execute();            
       }
       $sql3= "UPDATE productos set QuantityOnStock='{$sumatotal}' where ItemCode='{$item}'";
       $db->createCommand($sql3)->execute();

   }
    private function idUnidadNumero($unidad)
    {
        $unidad = Unidadesmedida::find()->where(['AbsEntry' => $unidad])->one();
        if (is_null($unidad)) {
          $unidad = Unidadesmedida::find()->where(['like','Name', 'UNI%'])->one();
        }
        try {
          return $unidad->id;
        } catch (Exception $e){
          echo $unidad;
      }


    }
    public function ActualizarClientes($code,$id){
      $serviceLayer = new Servislayer();
      $serviceLayer->actiondir = 'BusinessPartners?$select=CardCode,MaxCommitment,CurrentAccountBalance&$filter=CardCode eq \''.$code.'\'';
      $clientes = $serviceLayer->executex(1);
      $clientes = $clientes->value;

      $fecha = date("Y-m-d");
      //Yii::error("Clientes ACT SL: ".json_encode($clientes)); 
      
      $actualizacion=Yii::$app->db->createCommand("Update cabeceradocumentos set actsl=1 where id='".$id."' ")->execute();
      foreach ($clientes as $puntero) {
        $actualizacion=Yii::$app->db->createCommand("Update clientes set MaxCommitment='".$puntero->MaxCommitment."', CurrentAccountBalance='".$puntero->CurrentAccountBalance."' where CardCode='".$code."' ")->execute();
      }        
      // Yii::error("ACTUALIZACION On Demand :".$code); 
    }
    private function idListaPrecio($lista)
    {
      $listaPrecio = Listaprecios::find()->where(['PriceListNo' => $lista])->one();
      return $listaPrecio->id;
    }
    private function idUnidad($unidad)
    {
        $unidadMedida = Unidadesmedida::find()->where(['Name' => $unidad])->one();
        if (is_null($unidadMedida)) {
          $unidadMedida = Unidadesmedida::find()->where(['like', 'Name', 'UNI%'])->one();
        }
       return (is_null($unidadMedida)) ? $this->nuevaUnidad() : $unidadMedida->id;
    }
  }
