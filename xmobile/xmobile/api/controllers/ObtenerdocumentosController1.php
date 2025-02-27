<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use backend\models\Usuariosincronizamovil;
use backend\models\Cabeceradocumentos;
use backend\models\Historialdocumentos;
use api\traits\Respuestas;
use yii\data\ActiveDataProvider;
use Carbon\Carbon;

class ObtenerdocumentosController extends ActiveController {

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
        $documento=array();
        //$cabecera= Yii::$app->db->createCommand("select *,id as idCabecera from cabeceradocumentos where idUser=".$usuario." order by id limit 1000 OFFSET {$salto}")->queryAll();
        $fechaConsulta=Carbon::today('America/La_Paz')->format('Y-m-d');
        $fechaConsulta=date("Y-m-d",strtotime($fechaConsulta."- 30 days"));
        /*$cabecera = Cabeceradocumentos::find()
        //->select(" *, (3) As estadosend ")
        ->where("idUser=".$usuario." and DocDate>='".$fechaConsulta."'")
        ->with('detalledocumentos')
        ->groupBy('idDocPedido')
        ->having('count(*)=1')
        ->limit(1000)
        ->offset($salto)
        ->all();
        */
        $cabecera = Historialdocumentos::find()
        //->select(" *, (3) As estadosend ")
        ->where("usuario=".$usuario." and fecha>='".$fechaConsulta."'")
        ->limit(1000)
        ->offset($salto)
        ->groupby('idDocumento')
        ->orderby('fechaHora asc')
        ->all();
        Yii::error("HDARIODEV DATA HEADER: " . json_encode($cabecera));
        foreach ($cabecera as $key => $value) {
    
            $cabecera=json_decode($value['cadenaCabecera']);
            if(is_object($cabecera)){
                Yii::error("ES OBJETO:");
                //array_map( 'objectToArray', (array) $cabecera );
                $cabeceraAux=[];
                //$cabeceraAux=json_decode( json_encode( $cabecera ), false );
                array_push($cabeceraAux,$cabecera);
                $cabecera=$cabeceraAux;
            }

            Yii::error("DOC LOCAL: ".json_encode($cabecera));
            Yii::error($cabecera[0]->cod);
            $cabeceradocumentos = Cabeceradocumentos::find()
            ->select('estado')
            ->where("idDocPedido='".$cabecera[0]->cod."' and estado=3")
            ->limit(1)
            ->orderby('id desc')
            ->one();
            Yii::error("Cabecera documentos 10");
            //Yii::error($cabecera[0]->cod);
            //Yii::error($value['idDocumento']);

            if(isset($cabeceradocumentos['estado'])){
                $cabecera[0]->estado=$cabeceradocumentos['estado'];
                $documento[$key]["usuariodataid"]=$usuario;
                $documento[$key]["cantidadDetalle"]=0;//count($value["detalledocumentos"]);
                $documento[$key]["header"]=$cabecera;
                $documento[$key]["detalles"] =json_decode($value["cadenaDetalle"]);//$resultadoDetalle;
                //$documento[$key]["pagos"] = json_decode($value["cadenaPago"]);
            }
        }

        if (count($documento)){
            return $this->correcto($documento,'OK');
        }
        return $this->error('Sin datos',201);
	}

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $documento=array();
        //$cabecera= Yii::$app->db->createCommand("select *,id as idCabecera from cabeceradocumentos where idUser=".$usuario." order by id limit 1000 OFFSET {$salto}")->queryAll();
        $fechaConsulta=Carbon::today('America/La_Paz')->format('Y-m-d');
        $fechaConsulta=date("Y-m-d",strtotime($fechaConsulta."- 30 days"));
        $cabecera = Historialdocumentos::find()
        //->select("count(*) as contador")
        ->where("usuario=".$usuario." and fecha>='".$fechaConsulta."'")
        ->all();
        $documento=["contador"=>count($cabecera)];
        return $this->correcto($documento,OK);
       
    }
	
}
