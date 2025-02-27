<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Pagos;
use yii\base\Exception;
use backend\models\LogIngreso;
use backend\models\Historialpagos;
use Carbon\Carbon;

class PagosmovilesController extends ActiveController {

    public $modelClass = 'backend\models\Pagos';

    use Respuestas;

    protected function verbs() {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

	/*public function verificador($cod) {
        // SIN LIMIT PREVIAMENTE
        $sql = 'SELECT * FROM pagos WHERE recibo = "' . $cod . '" LIMIT 1;';
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
    public function verificador2($cod,$cliente,$monto,$formapago,$fecha) {
        // SIN LIMIT PREVIAMENTE
        $sql = "SELECT * FROM pagos WHERE recibo = '". $cod . "' and clienteId = '". $cliente . "' and monto = '". $monto . "' and formaPago = '". $formapago . "' and fecha = '". $fecha . "' LIMIT 1 ";
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
	*/
	public function updatePago($recibo) { // ACTUALIZA PAGOS PARA QUE SE ANULEN 
		$sql = "UPDATE pagos SET estadoEnviado = 6 WHERE recibo = '" . $recibo . "';";
		$pagos = Yii::$app->db->createCommand($sql)->execute();
        return $pagos;
    }

    public function actionCreate() {
        $datos = Yii::$app->request->post();
		//return $datos;
		Yii::error(" llega PAGO de movil " .json_encode($datos));
        $this->guardarlog($datos);
		$datPago= Pagos::registrarPago($datos);
		return $datPago;
    
    }
    private function guardarlog($documento){
        $logIngreso=new LogIngreso();
        $aux_env=json_encode($documento);        
        $aux_hoy=Carbon::now('America/La_Paz')->format('Y-m-d H:m:s');
        $iddocumento=$documento["documentoPagoId"];       
        $logIngreso->proceso='Ingreso PAGOS';
        $logIngreso->envio=$aux_env;
        $logIngreso->respuesta=$aux_env;
        $logIngreso->fecha=$aux_hoy;
        $logIngreso->documento=$iddocumento;
        $logIngreso->cabecera=$aux_env;
        $logIngreso->save();
      /*  $log_aux= "INSERT INTO `log_ingreso`(`proceso`, `envio`, `respuesta`,  `fecha`, `documento`,`cabecera`) VALUES (";
            $log_aux .=  "'Ingreso Docs','{$aux_env}','{$detalle}','{$aux_hoy}','{$iddocumento}','{$cabecera}');";                        
            $db = Yii::$app->db;
            $db->createCommand($log_aux)->execute(); */
            return $logIngreso;
    }
    public function registrarHistorialPago($val){
        Yii::error("Inserta HistorialPagos");
        Yii::error($val);
        date_default_timezone_set('America/La_Paz');
        $historialpagos = new Historialpagos();
        $historialpagos->id = 0;
        $historialpagos->fecha = date('Y-m-d');
        $historialpagos->fechaHora = date('Y-m-d H:i:s');
        $historialpagos->usuario = $val['usuario'];
        $historialpagos->recibo = $val['recibo'];
        $historialpagos->otpp = $val['otpp'];
        $historialpagos->cadenaPago = $val['cadenaPago'];
        $historialpagos->cadenaFacturas = $val['cadenaFacturas'];
   
        if($historialpagos->save(false)){
            Yii::error("Registro Correcto");
        }
        else{
            Yii::error("Error al registrar historial pagos");
        }
    }
}
