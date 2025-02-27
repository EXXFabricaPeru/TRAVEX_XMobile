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


class SincmanualdiarioController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    

    public function actionSicronizarsap() {
        set_time_limit(0);
        Yii::error('SINCRONIZACION MANUAL DIARIA ACTIVADA');
        $sap = new Sap();   
        $sap->sincronizacionDiariaManual();       
    }
}
