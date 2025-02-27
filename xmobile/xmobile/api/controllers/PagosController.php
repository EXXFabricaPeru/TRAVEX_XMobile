<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use yii\base\Exception;

class PagosController extends ActiveController {

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

    public function actionCreate() {
        $datos = Yii::$app->request->post();
		Yii::error("PAGO REALIZADO: " . json_encode($datos));
        $arr = [];
        foreach ($datos as $key => $val) {
            try {
                if(isset($val['centro'])){
                    $centro=$val['centro'];
                }else{
                    $centro="0";
                }
                if(isset($val['cuota'])){
                    $cuota=$val['cuota'];
                }else{
                    $cuota=1;
                }
                if(isset($val['equipoId'])){
                    $equipo=$val['equipoId'];
                }else{
                    $equipo=1;
                }
                if($val['otpp']==3){
                    $sqlvar = "'{$val['clienteId']}', '{$val['clienteId']}', '{$val['formaPago']}', {$val['tipoCambioDolar']}, '{$val['moneda']}', "
                    . "{$val['monto']}, '{$val['numCheque']}', '{$val['numComprobante']}', '{$val['numTarjeta']}', '{$val['numAhorro']}', "
                    . "'{$val['numAutorizacion']}', '{$val['bancoCode']}', '{$val['ci']}', '{$val['fecha']}', '{$val['hora']}', "
                    . " {$val['cambio']}, {$val['monedaDolar']},{$val['otpp']},'{$val['recibo']}',{$val['usuario']},'{$val['cardCreditNumber']}','{$val['baucher']}', '{$centro}', '{$val['dbtCode']}', {$cuota}, '{$equipo}'"; 
                }else{
                    $sqlvar = "'{$val['documentoId']}', '{$val['clienteId']}', '{$val['formaPago']}', {$val['tipoCambioDolar']}, '{$val['moneda']}', "
                  . "{$val['monto']}, '{$val['numCheque']}', '{$val['numComprobante']}', '{$val['numTarjeta']}', '{$val['numAhorro']}', "
                  . "'{$val['numAutorizacion']}', '{$val['bancoCode']}', '{$val['ci']}', '{$val['fecha']}', '{$val['hora']}', "
                  . " {$val['cambio']}, {$val['monedaDolar']},{$val['otpp']},'{$val['recibo']}',{$val['usuario']},'{$val['cardCreditNumber']}','{$val['baucher']}', '{$centro}', '{$val['dbtCode']}', {$cuota}, '{$equipo}'"; 
                }
               /* $sqlvar = "'{$val['documentoId']}', '{$val['clienteId']}', '{$val['formaPago']}', {$val['tipoCambioDolar']}, {$val['moneda']}, "
                  . "{$val['monto']}, '{$val['numCheque']}', '{$val['numComprobante']}', '{$val['numTarjeta']}', '{$val['numAhorro']}', "
                  . "'{$val['numAutorizacion']}', '{$val['bancoCode']}', '{$val['ci']}', '{$val['fecha']}', '{$val['hora']}', "
                  . " {$val['cambio']}, {$val['monedaDolar']},{$val['otpp']},'{$val['recibo']}',{$val['usuario']},'{$val['cardCreditNumber']}','{$val['baucher']}', '{$centro}', {$cuota}, '{$equipo}'"; 
                */
                $sql = "SELECT insertarPagos($sqlvar) estado";
                $resp = Yii::$app->db->createCommand($sql)->queryOne();
                $arr[] = [
                    "id" => $val['id'],
                    "xid" => (int) $resp['estado']
                ];
            } catch (\Exception $e) {
				Yii::error('PAGOS-ERROR'.$e->getMessage());
                $arr[] = [
                    "id" => $val['id'],
                    "xid" => 0,
                    "sql" => $sql
                ];
            }
        }
        return $arr;
    }

    public function actionListapagos() {
        $usuario = Yii::$app->request->post('usuario');
        $tipo = Yii::$app->request->post('tipo');
        $inicio = Yii::$app->request->post('inicio');
        $fin = Yii::$app->request->post('fin');
        $busqueda = Yii::$app->request->post('clienteCardCode');
        $sql = "SELECT * FROM pagos cd WHERE cd.formaPago LIKE '".$tipo."' AND cd.usuario = '".$usuario."'";
        if ($inicio && $fin){
            $sql .= " AND cd.fecha BETWEEN '".$inicio."' AND '".$fin."'";
        }
        if ($busqueda) {
        $sql .= " AND cd.clienteId LIKE '%".$busqueda."%'";
        }
        $sql .= ";";
        $respuesta = Yii::$app->db->createCommand($sql)
            ->queryAll();
        if (count($respuesta)){
        return $this->correcto($respuesta);
        }
        return $this->error('Sin datos',201);
    }

}
