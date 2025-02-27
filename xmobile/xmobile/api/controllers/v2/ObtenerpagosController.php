<?php

namespace api\controllers\v2;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use backend\models\Usuariosincronizamovil;
use backend\models\Pagos;
use backend\models\Historialpagos;
use api\traits\Respuestas;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;

class ObtenerpagosController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\User';

    protected function verbs() {
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

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

	public function actionIndex(){
        Yii::error("Datos de ingreso: ".json_encode(Yii::$app->request->post()));
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $pagos=array(); 
        $salida=array(); 

        $fechaConsulta=Carbon::today('America/La_Paz')->format('Y-m-d');
        $fechaConsulta=date("Y-m-d",strtotime($fechaConsulta."- 30 days"));
        $pagosData = Historialpagos::find()
        ->where("usuario=".$usuario." and fecha>='".$fechaConsulta."'  ")
        //->groupby('recibo,otpp')
        ->orderby('fechaHora asc')
        ->all();
        
       // $pagosData= Yii::$app->db->createCommand("select * from vi_obtenerpagos where fecha>='".$fechaConsulta."' and otpp!=1 and usuario=".$usuario)->queryAll();
        //$pagosData= Yii::$app->db->createCommand("select * from m_vi_obtenerpagos where fecha>='2022-02-15' and otpp!=1 and usuario=".$usuario)->queryAll();
        foreach ($pagosData as $key => $value) {
            Yii::error("PAgos77");
            Yii::error($value);
             # code...
            $cadenaPago=json_decode($value['cadenaPago']);
            //Yii::error($cadenaPago);
            $pagosCabecera= Yii::$app->db->createCommand("select estado,cancelado from xmfcabezerapagos where nro_recibo='".$cadenaPago->nro_recibo."' and  estado=3  and otpp='".$cadenaPago->otpp."' and idHistorial=".$value['id']." ORDER BY id DESC limit 1 ")->queryOne();
            if(isset($pagosCabecera['estado'])){
                Yii::error("NRO RECIBO: ".$cadenaPago->nro_recibo);
				Yii::error(json_encode($pagosCabecera));
                $cadenaPago->estado=$pagosCabecera['estado'];
				$cadenaPago->cancelado=$pagosCabecera['cancelado'];
                if(!is_array($cadenaPago->mediosPago)){
                    Yii::error("NO ES ARRAY");
                    $cadenaPago->mediosPago=json_decode($cadenaPago->mediosPago,true);
                }
                if(!is_array($cadenaPago->facturaspago)){
                    Yii::error("NO ES ARRAY");
                    $cadenaPago->facturaspago=json_decode($cadenaPago->facturaspago,true);
                }

                //se codifica en formato json la cadena de medios pagos 


                array_push($pagos,["pagos"=>$cadenaPago,"usuario"=>$usuario,"sucursal"=>0]);
                /*$pagos[$key]["pagos"] = $cadenaPago;
                $pagos[$key]["usuario"]=$usuario;
                $pagos[$key]["sucursal"]=0;*/     
            } 
                       
        }
        
        if (count($pagos)){
            return $this->correcto($pagos,'OK');
        }
        return $this->error('Sin datos',201);
        
	}

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $documento=array();
        $fechaConsulta=Carbon::today('America/La_Paz')->format('Y-m-d');
        $fechaConsulta=date("Y-m-d",strtotime($fechaConsulta."- 30 days"));
        $pagos = Historialpagos::find()
        //->select("count(*) as contador")
        ->where("usuario=".$usuario." and fecha>='".$fechaConsulta."' ")
        ->all();
        $documento=["contador"=>count($pagos)];
        return $this->correcto($documento,OK);
       
    }
	
}
