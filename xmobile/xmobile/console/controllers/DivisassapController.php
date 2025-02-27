<?php

namespace console\controllers;

use backend\models\Configlayer;
use backend\models\Servislayer;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Yii;
use yii\console\Controller;

class DivisassapController extends Controller {

    public $id;
    private $cliente;
    private $conf;
    private $serviceLayer;

    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);
        $this->conf = new Configlayer();
        $this->cliente = new Client([
            'base_uri' => $this->conf->path,
            'timeout' => 30,
            'verify' => false,
            'cookies' => true
        ]);
        $this->serviceLayer = new Servislayer();
    }

    public function actionSicronizarsap() {
        set_time_limit(0);
        //$asiento = $this->AsientoCambioDivisa();
    }

    private function AsientoCambioDivisa() {
        $serviceLayer = new Servislayer();
        $serviceLayer->actiondir = "JournalEntries";
        $cuentas = $this->obtenerCuentas();
        $tipocambio = $this->obtenertipocambio();
        Yii::error(json_encode($tipocambio));
        //$transacciones = Yii::$app->db->createCommand("Select * from divisadocumentos where monto>0 and sap is null limit 50 ")->queryAll();
        $transacciones = Yii::$app->db->createCommand("Select * from pagos where monedaDolar>0 and estadocdivisa=0 and TipoCambioDolar<>'".$tipocambio["tc"]."' limit 50 ")->queryAll();
        $aLineas = [];

        if (count($transacciones)) {
            if (count($tipocambio)) {
                foreach ($transacciones as $divisa) {
                    $valor1= round(($divisa["monedaDolar"]* $divisa["tipoCambioDolar"]), 2);
                    $valor2=round(($divisa["monedaDolar"] * $tipocambio["tc"]) - ($divisa["monedaDolar"] * $divisa["tipoCambioDolar"]), 2);
                    $asientoSAP = [
                        "ReferenceDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
                        "Memo" => "Cambio divisa Documento " . $divisa["documentoId"],
                        "TaxDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
                        "JdtNum" => 102,
                        "Series" => 23,
                        "DueDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
                        "JournalEntryLines" => [
                            [
                                "Line_ID" => 0,
                                "AccountCode" => $cuentas["deb"],
                                "Debit" => $divisa["monedaDolar"] * $tipocambio["tc"],
                                "Credit" => 0.0
                            ],
                            [
                                "Line_ID" => 1,
                                "AccountCode" => $cuentas["cred"],
                                "Debit" => 0.0,
                                "Credit" => $valor1
                            ],
                            [
                                "Line_ID" => 2,
                                "AccountCode" => $cuentas["dif"],
                                "Debit" => 0.0,
                                "Credit" =>$valor2 
                            ]
                        ]
                    ];
                    Yii::error(json_encode($asientoSAP));
                    $respuesta = $serviceLayer->executePost($asientoSAP); //$serviceLayer->executePost(json_encode($datos));
                    Yii::error($divisa["id"]);
                    Yii::error(json_encode($respuesta));
                    if (isset($respuesta->Number)) {

                        $actualizacion = Yii::$app->db->createCommand("Update pagos set estadocdivisa='" . $respuesta->Number . "' where id=" . $divisa["id"] . " ")->execute();
                    } else {
                        Yii::error(json_encode($respuesta));
                    }
                }
            }
        }
    }

    private function obtenerCuentas() {
        $cuentadeb = Yii::$app->db->createCommand("Select valor2 from configuracion where parametro='divisadeb' ")->queryOne();
        $cuentacred = Yii::$app->db->createCommand("Select valor2 from configuracion where parametro='divisacred' ")->queryOne();
        $cuentadif = Yii::$app->db->createCommand("Select valor2 from configuracion where parametro='divisadif' ")->queryOne();
        return array("deb" => $cuentadeb["valor2"], "cred" => $cuentacred["valor2"], "dif" => $cuentadif["valor2"]);
    }

    private function obtenertipocambio() {
        $tc = Yii::$app->db->createCommand("Select ExchangeRate from tiposcambio where ExchangeRateDate=CURDATE() and ExchangeRate>0 ")->queryOne();

        return array("tc" => $tc["ExchangeRate"]);
    }

}
