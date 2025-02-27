<?php

  namespace console\controllers;

  use backend\models\Configlayer;
  use backend\models\Servislayer;
  use backend\models\TraspasosCabecera;
  use backend\models\TraspasosDetalle;
  use backend\models\TraspasosLote;
  use Carbon\Carbon;
  use GuzzleHttp\Client;
  use Yii;
  use yii\console\Controller;

  class TraspasossapController extends Controller
  {
    public $id;
    private  $cliente;
    private $conf;
    private $serviceLayer;
    const IT = 3;

    public function __construct($id, $module, $config = [])
    {
      parent::__construct($id, $module, $config);
      $this->conf = new Configlayer();
      $this->cliente = new Client([
        'base_uri' => $this->conf->path,
        'timeout' => 0,
        'verify' => false,
        'cookies' => true
      ]);
      $this->serviceLayer = new Servislayer();
    }

    public function actionSicronizarsap()
    {
      set_time_limit(0);
      $this->traspasos();
      /*$this->pedidoCancelar();*/
    }

    private function traspasos(){
        Yii::error('inicio');
        $serviceLayer = new Servislayer();
        $serviceLayer->actiondir = "StockTransfers";
        $traspasos = TraspasosCabecera::find()
          ->where("estado = 3")
          ->limit(50)
          ->asArray()
          ->all();
        if (count($traspasos)){
          foreach ($traspasos as $traspaso){
            $datos = [
              "CardCode"        => $traspaso["CardCode"],
              "FromWarehouse"   => $traspaso["origenWarehouse"],
              "ToWarehouse"     => $traspaso["destinoWarehouse"],
              "DocDueDate"      => $traspaso["dateUpdate"],
              "SalesPersonCode" => 39
            ];
            $aLineas = [];
            $detalles = traspasodetalle::find()->where("idcabecera = ".$traspaso["id"])->asArray()->all();
            foreach ($detalles as $detalle) {
              $linea = [
                "ItemCode"     => $detalle["itemCode"],
                "Quantity"   => $detalle["cantidad"],
                "WarehouseCode"   => $detalle["origenwarehouse"],
                "FromWarehouseCode" => $detalle["destinowarehouse"],
                "MeasureUnit"   => $this->unidadEntry($detalle["unidadmedida"])
              ];
              array_push($aLineas, $linea);
            }
            $datos["StockTransferLines"] = $aLineas;
            $respuesta = $serviceLayer->executePost($datos);
            if (isset($respuesta->DocEntry)) {
              $actualizar = TraspasosCabecera::findOne(["id" => $traspaso["id"]]);
              $actualizar->estado =  4;
              $actualizar->save(false);
              Yii::error("ID-MID:{$traspaso["id"]};DATA-".$respuesta->DocEntry);
            } else {
              if (isset($respuesta->message)){
                Yii::error("ID-MID:{$traspaso["id"]};DATA-".json_encode($respuesta->message->value));
              } else {
                Yii::error("ID-MID:{$traspaso["id"]};DATA-".json_encode($respuesta));
              }
            }
          }
        }
        Yii::error('fin');
      }
}
