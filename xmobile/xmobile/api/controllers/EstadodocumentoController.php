<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Almacenes;
use Carbon\Carbon;

class EstadodocumentoController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Usuario';


    protected function verbs() {
        return [
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

    public function actionIndex() {
        $recibo=Yii::$app->request->post('recibo');
        $usuario=Yii::$app->request->post('usuario');
        $fechaConsulta=Carbon::today('America/La_Paz')->format('Y-m-d');
        $fechaConsulta=date("Y-m-d",strtotime($fechaConsulta."- 30 days"));

       $DataEstado = Yii::$app->db->createCommand("select estado ,CardCode,idDocPedido from cabeceradocumentos where DocDate>='$fechaConsulta' and idUser=$usuario")
                ->queryAll();
        $arrayEstado = array();
        foreach ($DataEstado as $key => $value) {
            switch ($value['estado']) {
                case '3':
                    array_push($arrayEstado,["idDocPedido"=>$value['idDocPedido'],"estado"=>$value['estado'],"descripcion"=>"MID"]);
                    break;
                case '4':
                array_push($arrayEstado,["idDocPedido"=>$value['idDocPedido'],"estado"=>$value['estado'],"descripcion"=>"SAP"]);
                    break;
                case '6':
                   // array_push($arrayEstado,["recibo"=>$value['recibo'],"estado"=>$value['estadoEnviado'],"descripcion"=>"SOLICITUD DE ANULACION"]);
                   array_push($arrayEstado,["idDocPedido"=>$value['idDocPedido'],"estado"=>$value['estado'],"descripcion"=>"SOLICITUD DE ANULACION"]);
                   break;
                case '7':
                   // $arrayEstado[$value['recibo']]="ANULADO";
                array_push($arrayEstado,["idDocPedido"=>$value['idDocPedido'],"estado"=>$value['estado'],"descripcion"=>"ANULADO"]);
                    break;
                case '8':
                //array_push($arrayEstado,["idDocPedido"=>$value['idDocPedido'],"estado"=>$value['estado'],"descripcion"=>"MID - ERROR DE ENVIO A SAP"]);
                    break;
                case '9':
               // array_push($arrayEstado,["idDocPedido"=>$value['idDocPedido'],"estado"=>$value['estado'],"descripcion"=>"ANULADO"]);
                    break;
                default:
                    // $arrayEstado[$value['recibo']]="MOVIL";
                    break;
            }
        }
        return $this->correcto($arrayEstado, "ok");
    }

   

}
