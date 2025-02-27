<?php

namespace api\controllers;

use api\traits\Respuestas;
use backend\models\Combos;
use backend\models\Cabeceradocumentos;
use backend\models\Detalledocumentos;
use Carbon\Carbon;
use Yii;
use yii\base\Exception;
use yii\rest\ActiveController;

class EntregaController extends ActiveController
{
    use Respuestas;

    const IT = 3;
    public $modelClass = 'backend\models\User';
/*     public function actionIndex()
{
return $this->render('index');
} */

    protected function action()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionEntregar()
    {
        $datos = Yii::$app->request->post();
        Yii::error('DOCUMENTO Entrega -ENTRANTE' . json_encode($datos));
        if (!count($datos)) {
            return $this->error('Sin datos', 201);
        }
        $fechaSend = date("Y-m-d");
        $datosRespuesta = [];
        try {
          foreach ($datos as $entrega) {
                $cabecera = new Cabeceradocumentos();
                $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $entrega["header"]["fecharegistro"]);
                $cabecera->DocNum = $entrega["header"]["DocNum"];
                $cabecera->DocType = $entrega["header"]["DocType"];
                $cabecera->DocDate = $entrega["header"]["DocDate"];
                $cabecera->DocDueDate = $entrega["header"]["DocDueDate"];
                $cabecera->CardCode = $entrega["header"]["CardCode"];
                $cabecera->CardName = $entrega["header"]["CardName"];
                $cabecera->TaxDate = isset($entrega["header"]["TaxDate"]) ? $entrega["header"]["TaxDate"] : date("Y-m-d");
                $cabecera->Address = isset($entrega["header"]["Address"]) ? $entrega["header"]["Address"] : '';
                $cabecera->fecharegistro = $fecha;
                $cabecera->fechasend = $fechaSend;
                $cabecera->idDocPedido = $entrega["header"]["idDocPedido"];
                $cabecera->idUser = $entrega["usuariodataid"];
                $cabecera->gestion = date("Y", date_timestamp_get($fecha));
                $cabecera->mes = date("m", date_timestamp_get($fecha));
                $cabecera->correlativo = $entrega["header"]["id"];
                $cabecera->rowNum = isset($entrega["header"]["rowNum"]) ? $entrega["header"]["rowNum"] : count($entrega["detalles"]);
                $cabecera->DocTotal = $entrega["header"]["DocTotal"];
                $cabecera->DocTotalPay = $entrega["header"]["DocTotalPay"];
                $cabecera->DiscPrcnt = $entrega["header"]["DiscPrcnt"];
                $cabecera->DiscSum = $entrega["header"]["DiscSum"];
                $cabecera->U_4RAZON_SOCIAL = $entrega["header"]["U_4RAZON_SOCIAL"];
                $cabecera->U_4NIT = $entrega["header"]["U_4NIT"];
                $cabecera->PayTermsGrpCode = -1; //($entrega["header"]["PayTermsGrpCode"] == 0) ? -1 : $entrega["header"]["PayTermsGrpCode"];
                $cabecera->TotalDiscPrcnt = isset($entrega["header"]["TotalDiscPrcnt"]) ? $entrega["header"]["TotalDiscPrcnt"] : 0;
                $cabecera->TotalDiscMonetary = isset($entrega["header"]["TotalDiscMonetary"]) ? $entrega["header"]["TotalDiscMonetary"] : 0;
                $cabecera->U_LATITUD = $entrega["header"]["U_LATITUD"];
                $cabecera->U_LONGITUD = $entrega["header"]["U_LONGITUD"];
                $cabecera->U_4MOTIVOCANCELADO = $entrega["header"]["U_4MOTIVOCANCELADO"];
                $cabecera->U_4DOCUMENTOORIGEN = $entrega["header"]["U_4DOCUMENTOORIGEN"];
                $cabecera->ControlCode = isset($entrega["header"]["ControlCode"]) ? $entrega["header"]["ControlCode"] : 0;
                $cabecera->UNumFactura = isset($entrega["header"]["UNumFactura"]) ? $entrega["header"]["UNumFactura"] : 0;
                $cabecera->SlpCode = isset($entrega["header"]["SlpCode"]) ? $entrega["header"]["SlpCode"] : -1;
                $cabecera->DocCur = isset($entrega["header"]["DocCur"]) ? $entrega["header"]["DocCur"] : null;
                $cabecera->Reserve = isset($entrega["header"]["Reserve"]) ? $entrega["header"]["Reserve"] : 0;
                /* $cabecera->clone = isset($entrega["header"]["origenclone"]) ? $entrega["header"]["origenclone"] : 0; */
                $cabecera->Indicator = isset($entrega["header"]["Indicator"]) ? $entrega["header"]["Indicator"] : '4';
                $cabecera->ShipToCode = isset($entrega["header"]["ShipToCode"]) ? $entrega["header"]["ShipToCode"] : '';
                $cabecera->ControlAccount = isset($entrega["header"]["ControlAccount"]) ? $entrega["header"]["ControlAccount"] : '0';
                $cabecera->U_LB_NumeroFactura = isset($entrega["header"]["U_LB_NumeroFactura"]) ? $entrega["header"]["U_LB_NumeroFactura"] : '1';
                $cabecera->U_LB_EstadoFactura = isset($entrega["header"]["U_LB_EstadoFactura"]) ? $entrega["header"]["U_LB_EstadoFactura"] : 'V';
                $cabecera->U_LB_NumeroAutorizac = isset($entrega["header"]["U_LB_NumeroAutorizac"]) ? $entrega["header"]["U_LB_NumeroAutorizac"] : null;
                $cabecera->U_LB_TipoFactura = isset($entrega["header"]["U_LB_TipoFactura"]) ? $entrega["header"]["U_LB_TipoFactura"] : 0;
                $cabecera->U_LB_TotalNCND = isset($entrega["header"]["U_LB_TotalNCND"]) ? $entrega["header"]["U_LB_TotalNCND"] : 0;
                if (isset($entrega["header"]["estado"])) {
                    $cabecera->estado = $entrega["header"]["estado"] != 6 ? 3 : $entrega["header"]["estado"];
                } else {
                    $cabecera->estado = 3;
                }
                if ($cabecera->save(false)) {
                    $flags = [];
                    foreach ($entrega["detalles"] as $lineaPedido) {
                        $linea = new Detalledocumentos();
                        $linea->DocNum = isset($lineaPedido["DocNum"]) ? $lineaPedido["DocNum"] : 0;
                        $linea->LineNum = isset($lineaPedido["LineNum"]) ? $lineaPedido["LineNum"] : 0;
                        $linea->BaseType = isset($lineaPedido["BaseType"]) ? $lineaPedido["BaseType"] : 13;
                        $linea->BaseEntry = isset($lineaPedido["BaseEntry"]) ? $lineaPedido["BaseEntry"] : 0;
                        $linea->BaseLine = isset($lineaPedido["BaseLine"]) ? $lineaPedido["BaseLine"] : 0;
                        $linea->LineStatus = isset($lineaPedido["LineStatus"]) ? $lineaPedido["LineStatus"] : 0;
                        $linea->ItemCode = isset($lineaPedido["ItemCode"]) ? $lineaPedido["ItemCode"] : 0;
                        $linea->Dscription = isset($lineaPedido["Dscription"]) ? $lineaPedido["Dscription"] : 0;
                        $linea->Quantity = isset($lineaPedido["Quantity"]) ? $lineaPedido["Quantity"] : 0;
                        $linea->OpenQty = isset($lineaPedido["OpenQty"]) ? $lineaPedido["OpenQty"] : 0;
                        $linea->Price = isset($lineaPedido["Price"]) ? $lineaPedido["Price"] : 0;
                        $linea->Currency = 'BS'; //isset($lineaPedido["Currency"])?$lineaPedido["Currency"]:0;
                        $linea->DiscPrcnt = isset($lineaPedido["DiscPrcnt"]) ? $lineaPedido["DiscPrcnt"] : 0;
                        $linea->LineTotal = isset($lineaPedido["LineTotal"]) ? $lineaPedido["LineTotal"] : 0;
                        $linea->LineTotalPay = isset($lineaPedido["LineTotalPay"]) ? $lineaPedido["LineTotalPay"] : 0;
                        $linea->WhsCode = isset($lineaPedido["WhsCode"]) ? $lineaPedido["WhsCode"] : 0;
                        $linea->CodeBars = isset($lineaPedido["CodeBars"]) ? $lineaPedido["CodeBars"] : 0;
                        $linea->PriceAfVAT = isset($lineaPedido["PriceAfVAT"]) ? $lineaPedido["PriceAfVAT"] : 0;
                        $linea->TaxCode = isset($lineaPedido["TaxCode"]) ? $lineaPedido["TaxCode"] : 0;
                        $linea->U_4DESCUENTO = isset($lineaPedido["U_4DESCUENTO"]) ? $lineaPedido["U_4DESCUENTO"] : 0;
                        $linea->U_4LOTE = isset($lineaPedido["U_4LOTE"]) ? $lineaPedido["U_4LOTE"] : 0;
                        $linea->GrossBase = isset($lineaPedido["GrossBase"]) ? $lineaPedido["GrossBase"] : 0;
                        $linea->idDocumento = $entrega["usuariodataid"] . date("y", date_timestamp_get($fecha)) . date("m", date_timestamp_get($fecha)) . $this->numPedido($entrega["header"]["DocNum"]);
                        $linea->fechaAdd = isset($lineaPedido["fechaAdd"]) ? $lineaPedido["fechaAdd"] : 0;
                        $linea->unidadid = isset($lineaPedido["unidadid"]) ? $lineaPedido["unidadid"] : 0;
                        $linea->tc = isset($lineaPedido["tc"]) ? $lineaPedido["tc"] : 0;
                        $linea->idCabecera = $cabecera->id;
                        $linea->DiscMonetary = isset($lineaPedido['descuentoM']) ? $lineaPedido['descuentoM'] : 0;
                        $linea->LineTotalPay = isset($lineaPedido['LineTotalPay']) ? $lineaPedido['LineTotalPay'] : 0;
                        $linea->SalesUnitLength = isset($lineaPedido['SalesUnitLength']) ? $lineaPedido['SalesUnitLength'] : 0;
                        $linea->SalesUnitWidth = isset($lineaPedido['SalesUnitWidth']) ? $lineaPedido['SalesUnitWidth'] : 0;
                        $linea->SalesUnitHeight = isset($lineaPedido['SalesUnitHeight']) ? $lineaPedido['SalesUnitHeight'] : 0;
                        $linea->SalesUnitVolume = isset($lineaPedido['SalesUnitVolume']) ? $lineaPedido['SalesUnitVolume'] : 0;
                        $linea->DiscTotalPrcnt = $lineaPedido['DiscTotalPrcnt'];
                        $linea->DiscTotalMonetary = $lineaPedido['DiscTotalMonetary'];
                        $linea->ICET = isset($lineaPedido['icet']) ? $lineaPedido['icet'] : 0;
                        $linea->ICEE = isset($lineaPedido['icee']) ? $lineaPedido['icee'] : 0;
                        $linea->ICEP = isset($lineaPedido['icep']) ? $lineaPedido['icep'] : 0;
                        $linea->TreeType = isset($lineaPedido['TreeType']) ? $lineaPedido['TreeType'] : $this->tipoItem($lineaPedido["ItemCode"]);
                        array_push($flags, $linea->save(false));
                    }
                    array_push($datosRespuesta, [
                        "idPedidoUsr" => $entrega["header"]["DocNum"],
                        "idPedidoServicio" => $cabecera->id,
                    ]);
                } else {
                    return $this->error('Error al registrar Entrega');
                }
            }
            return $this->correcto($datosRespuesta, "Documentos de Entrega Registrados");
        } catch (Exception $e) {
            Yii::error('ERROR-ENTREGA:' . $e->getMessage());
            return $this->error($e->getMessage(), 100);
        }
    }

    private function numPedido($numero)
    {
        if ($numero < 10) {
            $codigo = "000{$numero}";
        } else if($numero < 100){
            $codigo = "00{$numero}";
        } else if ($numero < 1000) {
            $codigo = "0{$numero}";
        } else{
            $codigo = "{$numero}";
        }
        return $codigo;
    }

    public function actionCreate()
    {
        $datos = Yii::$app->request->post();

        Yii::error('DOCUMENTO Pedido -ENTRANTE' . json_encode($datos));
        if (!count($datos)) {
            return $this->error('Sin datos', 201);
        }
        
    }
	
	private function tipoItem($itemCode){
      $item = Combos::find()->where(['TreeCode' => $itemCode])->one();
      if ($item){
        return 'iSalesTree';
      }
      return 'iNotATree';
    }

    public function actionFacturarentregalista(){
        $datos = Yii::$app->request->post();
        $cliente = $datos["clienteid"];
        $sql = "SELECT  *, '' AS Marcado FROM vi_entregasfacturas WHERE CardCode='".$cliente."'";
        $resultado = Yii::$app->db->createCommand($sql)->queryAll();
        $resp2 = [];
        foreach($resultado as $elem) {
            $sql2 = "SELECT OcrCode2 FROM sapentregasdetalle WHERE DocEntry = '".$elem["DocNum"]."'";
            $arrayCentros = [];
            $centros = Yii::$app->db->createCommand($sql2)->queryAll();
            foreach($centros as $centro){
                if (!in_array($centro["OcrCode2"], $arrayCentros)) {
                    array_push($arrayCentros, $centro["OcrCode2"]);
                }
            }
            $elem["UnidadNegocio"] = implode( ",", $arrayCentros );
            array_push($resp2, $elem);
        }
        if (count($resp2) > 0) {
            return $this->correcto($resp2, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }

    
}
