<?php

namespace console\controllers;

use backend\models\Cabeceradocumentos;
use backend\models\Clientes;
use backend\models\Configlayer;
use backend\models\Pagos;
use backend\models\Servislayer;
use backend\models\Unidadesmedida;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Yii;
use yii\console\Controller;
use backend\models\Sapenviopagos;

class PagossapController extends Controller
{
  public $id;
  private $cliente;
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
  }

  public function actionSicronizarsap()
  {
    set_time_limit(0);
	  Yii::error('here');
    Sapenviopagos::efectivo();
    Sapenviopagos::transferencia();
    Sapenviopagos::cheque();
    Sapenviopagos::tarjeta();
  }
}
