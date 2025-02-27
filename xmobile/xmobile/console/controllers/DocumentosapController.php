<?php

namespace console\controllers;

use backend\models\Cabeceradocumentos;
use backend\models\Clientes;
use backend\models\Configlayer;
use backend\models\Lotes;
use backend\models\Seriesproductos;
use backend\models\Seriesmarketing;
use backend\models\Servislayer;
use backend\models\Unidadesmedida;
use backend\models\TraspasosCabecera;
use backend\models\TraspasosDetalle;
use backend\models\TraspasosLote;
use backend\models\TraspasosSerie;
use backend\models\Productosalmacenes;
use backend\models\Sincronizar;
use backend\models\Pagos;
use backend\models\Contactos;
use backend\models\Clientessucursales;
use backend\models\Anulaciondocmovil;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Exception;
use stdClass;
use Yii;
use yii\console\Controller;
use backend\models\Sapenvio;
use backend\models\Sapenviodoc;

class DocumentosapController extends Controller {

    const IT = 3;

    public $id;
    private $cliente;
    private $conf;
    private $serviceLayer;
    private $codeIT;
    private $codeITGasto;
    private $fecha;

    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);
        $this->fecha = Carbon::today('America/La_Paz');
        $this->conf = new Configlayer();
        $this->cliente = new Client([
            'base_uri' => $this->conf->path,
            'timeout' => 0,
            'verify' => false,
            'cookies' => true
        ]);
        $this->serviceLayer = new Servislayer();
        $this->codeIT = Yii::$app->db->createCommand('select parametro,valor from configuracion where parametro = \'CODE_IT\'')->queryOne();
        $this->codeITGasto = Yii::$app->db->createCommand('select parametro,valor from configuracion where parametro = \'CODE_IT_GASTO\'')->queryOne();
    }

    public function actionSicronizarsap() {
        set_time_limit(0);
        Yii::error("DOCUMENTOS SAP");
        Sapenvio::cliente();

        Sapenviodoc::pedido();
        Sapenviodoc::facturas(); 
        Sapenviodoc::oferta();         
        Sapenviodoc::entrega();

        Sapenviodoc::solicitudDeTraspaso();
        Sapenviodoc::crearTraspaso();
        $this->anulaciones(); 
        //$this->obtenerConciliaciones();
    }
 /**
     * 1  Creado en cliente
     * 2  Enviado Cliente->Middleware
     * 3  Recibido por Middleware
     * 4  Enviado a SAP
     * 5  Reservado para
     * 6  Solicitud Anulado
     * 7  Confirmado Anulado
     */
    private function anulaciones() {

        Sapenviodoc::pedidoCancelar();      
        Sapenviodoc::facturaCancelar();
        Sapenviodoc::ofertaCancelar();  
        Sapenviodoc::entregaCancelar();
        
        Sapenviodoc::pagoCancelar(); 
        Sapenviodoc::pedidoCancelarExterno();
              
    }
}
