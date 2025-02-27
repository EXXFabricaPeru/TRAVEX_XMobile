<?php

namespace api\controllers;

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
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $pagos=array(); 

        $fechaConsulta=Carbon::today('America/La_Paz')->format('Y-m-d');
        $fechaConsulta=date("Y-m-d",strtotime($fechaConsulta."- 30 days"));
        $pagosData = Historialpagos::find()
        ->where("usuario=".$usuario." and fecha>='".$fechaConsulta."' and otpp!=1")
        ->orderby('fechaHora asc')
        ->all();
        
       // $pagosData= Yii::$app->db->createCommand("select * from vi_obtenerpagos where fecha>='".$fechaConsulta."' and otpp!=1 and usuario=".$usuario)->queryAll();
        //$pagosData= Yii::$app->db->createCommand("select * from m_vi_obtenerpagos where fecha>='2022-02-15' and otpp!=1 and usuario=".$usuario)->queryAll();
        foreach ($pagosData as $key => $value) {
            Yii::error("PAgos77");
            Yii::error($value);
             # code...
            $cadenaPago=json_decode($value['cadenaPago']);

           /* $pagos = Pagos::find()
           // ->select('estadoEnviado')
            ->where("recibo='".$cadenaPago[0]->documentoPagoId."'")
            ->limit(1)
            ->orderby('id desc')
            ->one();*/
            $pagosCabecera= Yii::$app->db->createCommand("select estadoEnviado from pagos where recibo='".$cadenaPago[0]->documentoPagoId."'")->queryOne();

            //Yii::error("Pagos documentos 10");
            // Yii::error($cabecera[0]->cod);
            //Yii::error($value['idDocumento']);

            if(isset($pagosCabecera['estadoEnviado'])){
                $cadenaPago[0]->estado=$pagosCabecera['estadoEnviado'];
            }
            else{
               $cadenaPago[0]->estado=0;  
            } 

            $pagos[$key]["pagos"] = $cadenaPago;
           
            if($value['otpp']=='2'){// OTPP PUEDE TENER 1 O N DETALLES DE PAGOS
                $pagos[$key]["facturas"] = json_decode($value["cadenaFacturas"]);
            }
            $pagos[$key]["usuario"]=$usuario;
            $pagos[$key]["sucursal"]=0;
            
            
        }
       
       

        if (count($pagos)){
            return $this->correcto($pagos);
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
        ->where("usuario=".$usuario." and fecha>='".$fechaConsulta."' and otpp!=1")
        ->all();
        $documento=["contador"=>count($pagos)];
        return $this->correcto($documento,OK);
       
    }
	
}
