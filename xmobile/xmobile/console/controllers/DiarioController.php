<?php

namespace console\controllers;

use backend\models\Configlayer;
use backend\models\Sincronizar;
use backend\models\Servislayer;
use backend\models\Sap;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Yii;
use yii\console\Controller;


class DiarioController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSicronizarsap() {
        set_time_limit(0);
        Yii::error('SINCRONIZACION DIARIA ACTIVADA');
        $sap = new Sap();
        
         /*$this->obtenerContactosClientes();
            $this->obtenerSucursalClientes();
            $this->obtenerProductos();
            $this->ObtenerProductosAlmacenes();
            $this->ObtenerProductosPrecios();
            $this->ObtenerSeriesProductos();
            $this->ObtenerLotesProductos();
         */

        $sap->ObtenrPedidosCabecera();
        $sap->ObtenrPedidosDetalle();
		
		$sap->ObtenrFacturasCabecera();       
        $sap->ObtenrFacturasDetalle();

        $sap->obtenerProductosAlmacenesODBC();
        $sap->obtenerProductosLotesODBC();

        $sap->Clientes();
        //$sap->obtenerProductosLotesODBC();
       // $this->ObtenerSapOfertasCabecera();
        //$this->ObtenerSapOfertasDetalles();  

        //$this->ObtenerSapEntregasCabecera();
        //$this->ObtenerSapEntregasDetalles();
    }


    


}
