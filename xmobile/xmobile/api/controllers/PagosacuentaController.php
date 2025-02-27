<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use yii\base\Exception;
use backend\models\Pagosacuenta;

class PagosacuentaController extends ActiveController {

    public $modelClass = 'backend\models\Pagosacuenta';

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

    public function actionIndex(){
        $datos = Yii::$app->request->post();
        try{
            $resultado = Pagosacuenta::find()->asArray()->all();
            return $this->correcto($resultado, 'OK');
        }catch (\Exception $e) {			
            $arr[] = [
                "id" => $val['id'],
                "xid" => 0
            ];
        }
        return $arr;
    }

    public function actionCreate() {
    }

    public function actionFindbyclient(){
        $data = Yii::$app->request->post();
        $cliente = Yii::$app->request->post('cliente');

        $sqlp = 'SELECT * FROM pagosacuenta WHERE CardCode = :cliente AND DocTotal > 0';
        $resultado = [
            "pagos" => [],
            "facturas" => [],
            "pagos_todo" => [],
        ];

        $pagos = Yii::$app->db->createCommand($sqlp)->bindValue(':cliente' , $cliente)->queryAll();
        $rpagos = [];
        foreach($pagos as $p){
            $pago = [
                "id" => $p["id"],
                "DocDate" => $p["DocDate"],
                "DocTotal" => $p["DocTotal"],
                "DocEntry" => $p["DocEntry"],
                'TransId' => $p["TransId"],
                'ccost' => $p["ccost"],
                "MPago" => 0,
                "Marcado" => false
            ];
            array_push($rpagos, $pago);
        }
        $resultado["pagos"] = $rpagos;
        //$resultado["pagos_todo"] = $pagos;

        $facturas = Yii::$app->db->createCommand("CALL `pa_obtenerFacturaSap`(:usuario, :tipo, :texto)")
                ->bindValue(':usuario', $data['usuario'])->bindValue(':tipo', 1)->bindValue(':texto', $cliente)->queryAll();
        $resp2 = [];
        // if (count($facturas)>0){
            foreach($facturas as $elem) {
                $sql2 = "SELECT OcrCode2 FROM facturasproductos WHERE DocNum = '".$elem["DocNum"]."'";
                $arrayCentros = [];
                $centros = Yii::$app->db->createCommand($sql2)->queryAll();
                foreach($centros as $centro){
                    if (!in_array($centro["OcrCode2"], $arrayCentros)) {
                        array_push($arrayCentros, $centro["OcrCode2"]);
                    }
                }
                $elem["UnidadNegocio"] = implode( ", ", $arrayCentros );
                array_push($resp2, $elem);
            }
            //$facturas = $resp2;
        // }
        
        $resultado["facturas"] = $resp2;// $facturas;

        return $this->correcto($resultado, 'OK');
    }

    public function actionReconciliar(){
		try{
			$data = Yii::$app->request->post();
			$pagospos = Yii::$app->request->post('pagos');
			$facturaspos = Yii::$app->request->post('facturas');
        
			$pagos = explode('|', $pagospos);
			foreach($pagos as $pago){
				if ($pago != ''){
					$p = explode('*', $pago);
					$docentry = $p[1];
					$doctotal = $p[2];
					$sql = "UPDATE pagosacuenta SET DocTotal = DocTotal - :MPago WHERE DocEntry = :DocEntry";
					Yii::$app->db->createCommand($sql)
						->bindValue(':MPago', $doctotal)
						->bindValue(':DocEntry', $docentry)
						->execute();
				}
			}

			$facturas = explode('|', $facturaspos);
			foreach($facturas as $factura){
				if ($factura != ''){
					$f = explode('*', $factura);
					$docentry = $f[1];
					$sql = "UPDATE facturas SET Saldo = 0 WHERE DocEntry = :DocEntry";
					Yii::$app->db->createCommand($sql)
						->bindValue(':DocEntry', $docentry)
						->execute();
				}
			}
		} catch (\Exception $e) {
				Yii::error('PAGOS-ERROR'.$e->getMessage());
        }

        return $this->correcto([], 'OK');
    }

}
