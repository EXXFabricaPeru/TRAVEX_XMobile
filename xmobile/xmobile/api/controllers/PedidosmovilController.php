<?php

namespace api\controllers;

use backend\models\Combos;
use backend\models\Unidadesmedida;
use Carbon\Carbon;
use Yii;
use yii\base\Exception;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use backend\models\Cabeceradocumentos;
use backend\models\Detalledocumentos;
use api\traits\Respuestas;
use backend\models\Pagos;
use backend\models\Historialdocumentos;
use api\controllers\Pagosmoviles;
use backend\models\Sapenviodoc;
use backend\models\Sapenviopagos;
use backend\models\Sap;

class PedidosmovilController extends ActiveController {

    use Respuestas;

    const IT = 3;
    Public $aux_estado=3;
    public $modelClass = 'backend\models\User';

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

    private function estadosDoc($cod) {
        $sql = 'SELECT * FROM cabeceradocumentos WHERE idDocPedido = "' . $cod . '";';
        $resp = Yii::$app->db->createCommand($sql)->queryAll();
        $doctype = 0;
        $DocEntry = 0;
        if (count($resp) > 0) {
            $DocEntry = $resp[0]['DocEntry'];
            switch ($resp[0]['DocType']) {
                case "DOF":
                    $doctype = 23;
                    break;
                case "DOP":
                    $doctype = 17;
                    break;
                case "DFA":
                    $doctype = 13;
                    break;
            }
        } else {
            $doctype = 0;
            $DocEntry = 0;
        }
        return ['doctype' => $doctype, 'docentry' => $DocEntry];
    }

    public function verificador($cod) {
        // SIN LIMIT PREVIAMENTE
        $sql = 'SELECT * FROM cabeceradocumentos WHERE idDocPedido = "' . $cod . '" LIMIT 1;';
        return Yii::$app->db->createCommand($sql)->queryAll();

        //$sql = 'SELECT COUNT(*) AS CANTIDAD FROM cabeceradocumentos WHERE idDocPedido = "' . $cod . '" LIMIT 1;';
        //$cantidad=Yii::$app->db->createCommand($sql)->queryOne();
        //return $cantidad['CANTIDAD'];
    }

    public function actionCreate() {
        $datos = Yii::$app->request->post();
        Yii::error(" llega documento de movil" .json_encode($datos));
        $arr = [];
        try {
            foreach ($datos as $pedido) {
                $this->guardarlog($pedido);
                $this->registraHistorialDoc($pedido);
                $id = 0;
                $x = $this->verificador($pedido["header"]["idDocPedido"]);
                $idaux = $pedido["header"]["id"];
                if (count($x) > 0) {
                   
                    $id = $this->updateHeader($pedido);
                    $estadoDocumento = $this->aux_estado;
                    //SE CANCELA EL DOCUMENTO EN LINEA//
                    switch ($pedido["header"]["DocType"]) {
                        
                        case 'DOP':
                            $respuestaEnvio=Sapenviodoc::pedidoCancelar($pedido["header"]["idDocPedido"]);
                            break;
                        case 'DOF':
                            $respuestaEnvio=Sapenviodoc::ofertaCancelar($pedido["header"]["idDocPedido"]);
                            break;
                        case 'DOE':
                            $respuestaEnvio=Sapenviodoc::entregaCancelar($pedido["header"]["idDocPedido"]);
                            break;
                        case 'DFA':
                            $respuestaEnvio=Sapenviodoc::facturaCancelar($pedido["header"]["idDocPedido"]);
                            break;
                    }                    

                } else {
                    Yii::error("PAGO DOCUMENTO 10");

                    $id = $this->registerHeader($pedido);
                    $respuestaDetalle=$this->registerDetalles($id, $pedido["detalles"], $pedido["header"]["origenclone"], $pedido,$pedido["header"]["PriceListNum"]);
                    //EL DOCUMENTO SE ENVIA A SAP SI EL DETALLE DEL REGISTRO FUE CORRECTO 
                    if($respuestaDetalle){
                        switch ($pedido["header"]["DocType"]) {
                            case 'DOP':
                                $respuestaEnvio=Sapenviodoc::pedido($pedido["header"]["idDocPedido"]);
                                //if($respuestaEnvio) $estadoDocumento=4;
                                
                                break;
                            case 'DOF':
                                $respuestaEnvio=Sapenviodoc::oferta($pedido["header"]["idDocPedido"]);
                                //$estadoDocumento=4;
                                break;
                            case 'DOE':
                                $respuestaEnvio=Sapenviodoc::entrega($pedido["header"]["idDocPedido"]);
                               //$estadoDocumento=4;
                                break;
                            case 'DFA':
                                Yii::error("PAGO DOCUMENTO");
                                Yii::error($pedido["header"]["DocType"]);
                                $aux2_sql = 'UPDATE  lbcc  set  U_NumeroSiguiente = U_NumeroSiguiente + 1 WHERE U_NumeroAutorizacion = "' . $pedido["header"]["U_LB_NumeroAutorizac"] . '";';
                                Yii::$app->db->createCommand($aux2_sql)->execute();
                                $pagosdata= Pagos::registrarPago($pedido["pagos"][0],$id);
                                //ENVIO FACTURA SAP//
                                $respuestaEnvio=Sapenviodoc::facturas($pedido["header"]["idDocPedido"]);
                                //$sap= new Sap();
                                //Yii::error("LLAMA A exportInvoice".$id);
                                //$respuestaEnvio = $sap->exportInvoice($id);
                                //ENVIO PAGO A SAP//
                                if($respuestaEnvio){
                                    $recibo=$pedido["pagos"][0]['recibo'];
                                    $equipoId=$pagosdata[0]['equipo'];
                                    Yii::error("Pago envio a sap: ".$recibo." Equipo: ".$equipoId);
                                    switch ($pedido["pagos"][0]['formaPago']) {
                                        case 'PEF':
                                            Sapenviopagos::efectivo($recibo,$equipoId);
                                            break;
                                        case 'PCH':
                                            Sapenviopagos::cheque($recibo,$equipoId);
                                            break;
                                        case 'PBT':
                                            Sapenviopagos::transferencia($recibo,$equipoId);
                                            break;
                                        case 'PCC':
                                            Sapenviopagos::tarjeta($recibo,$equipoId);
                                            break;
                                    } 

                                }
                                //$estadoDocumento=4;
                                break;
                        }
                    }
                    $estadoDocumento=3;// es estado se envia quemado por que falta ajustes en el movil
                }

                $this->actualizaProductoAlmacen($pedido["header"]["DocType"],$pedido["header"]["Reserve"],$pedido["detalles"]);
                
                array_push($arr, [
                    //"idPedidoUsr" => $pedido["header"]["DocNum"],
                    "idPedidoUsr" => $idaux,
                    "idPedidoServicio" => $id,
                    "estado" => $estadoDocumento,
                    "pagos" => $pagosdata,
                    "mensaje"=>$respuestaEnvio,
                ]);
            }
            return $this->correcto($arr, "Documentos de Pedidos Registrados");
        } catch (\Exception $err) {
            Yii::error("try error datos de registro (documento): ".$err->getMessage());
            Yii::error("try error datos de registro (documento): ".json_encode($datos));
            $response = Yii::$app->response;
            $response->statusCode=400;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = ['message' => 'Algo salio mal, sus datos no se guardaron ',"error_trace"=>json_encode($err)];
            return  $response;
            //throw $th;
        }
    }

    public function updatenumeracion($id, $tipo) {
        $sql = '';
        switch ($tipo) {
            case('DOP'):
                $sql = ' UPDATE numeracion SET numdop = (numdop + 1) WHERE iduser = ' . $id;
                break;
            case('DOF'):
                $sql = ' UPDATE numeracion SET numdof = (numdof + 1) WHERE iduser = ' . $id;
                break;
            case('DFA'):
                $sql = ' UPDATE numeracion SET numdfa = (numdfa + 1) WHERE iduser = ' . $id;
                break;
            case('DOE'):
                $sql = ' UPDATE numeracion SET numdoe = (numdoe + 1) WHERE iduser = ' . $id;
                break;
        }
        return Yii::$app->db->createCommand($sql)->execute();
    }

    public function registerDetalles($id, $detalles, $clone, $pedido,$listaPrecio) {
        if (count($detalles) > 0) {
            $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $pedido["header"]["fecharegistro"]);
            Yii::error(" DEVD  detalles " .json_encode($detalles));
            foreach ($detalles as $lineaPedido) {
                if ($lineaPedido["LineTotal"] >= 0) {
                    $doctype = 0;
                    $docentry = 0;
                    if ($clone != '0') {
                        $data = $this->estadosDoc($clone);
                        $doctype = $data['doctype'];

                        $docentry = $data['docentry'];
                    }
                    if ($lineaPedido["unidadid"]==""){
                        $qauxuom="Select Code from vi_productounidad where ItemCode='{$lineaPedido["ItemCode"]}'";
                        $res_qauxuom=Yii::$app->db->createCommand($qauxuom)->queryOne();
                        $lineaPedido["unidadid"]=$res_qauxuom["Code"];
                    }
                    if($lineaPedido["Currency"]=="undefined"){
                        $lineaPedido["Currency"]="BS";
                        }
                    $linea = new Detalledocumentos();
                    $linea->DocNum = isset($lineaPedido["DocNum"]) ? $lineaPedido["DocNum"] : 0;
                    $linea->LineNum = isset($lineaPedido["LineNum"]) ? $lineaPedido["LineNum"] : 0;
                    $linea->BaseType = isset($lineaPedido["BaseType"]) ? $lineaPedido["BaseType"] : $doctype;
                    $linea->BaseEntry = isset($lineaPedido["BaseEntry"]) ? $lineaPedido["BaseEntry"] : $docentry;
                    $linea->BaseLine = isset($lineaPedido["BaseLine"]) ? $lineaPedido["BaseLine"] : 0;
                    $linea->LineStatus = isset($lineaPedido["LineStatus"]) ? $lineaPedido["LineStatus"] : 0;
                    $linea->ItemCode = isset($lineaPedido["ItemCode"]) ? $lineaPedido["ItemCode"] : 0;
                    $linea->Dscription = isset($lineaPedido["Dscription"]) ? $lineaPedido["Dscription"] : 0;
                    $linea->Quantity = isset($lineaPedido["Quantity"]) ? $lineaPedido["Quantity"] : 0;
                    $linea->OpenQty = isset($lineaPedido["OpenQty"]) ? $lineaPedido["OpenQty"] : 0;
                    $linea->Price = isset($lineaPedido["Price"]) ? $lineaPedido["Price"] : 0;
                    $linea->Currency = 'SOL'; //isset($lineaPedido["Currency"])?$lineaPedido["Currency"]:0;
                    $linea->DiscPrcnt = isset($lineaPedido["DiscPrcnt"]) ? $lineaPedido["DiscPrcnt"] : 0;
                    $linea->LineTotal = isset($lineaPedido["LineTotal"]) ? $lineaPedido["LineTotal"] : 0;
                    $linea->WhsCode = isset($lineaPedido["WhsCode"]) ? $lineaPedido["WhsCode"] : 0;
                    $linea->CodeBars = isset($lineaPedido["CodeBars"]) ? $lineaPedido["CodeBars"] : 0;
                    $linea->PriceAfVAT = isset($lineaPedido["PriceAfVAT"]) ? $lineaPedido["PriceAfVAT"] : 0;
                    $linea->TaxCode = isset($lineaPedido["TaxCode"]) ? $lineaPedido["TaxCode"] : 0;
                    $linea->U_4DESCUENTO = isset($lineaPedido["U_4DESCUENTO"]) ? $lineaPedido["U_4DESCUENTO"] : 0;
                    $linea->U_4LOTE = isset($lineaPedido["U_4LOTE"]) ? $lineaPedido["U_4LOTE"] : 0;
                    $linea->GrossBase = isset($lineaPedido["GrossBase"]) ? $lineaPedido["GrossBase"] : 0;
                    $linea->idDocumento = $pedido["usuariodataid"] . date("y", date_timestamp_get($fecha)) . date("m", date_timestamp_get($fecha)) . $this->numPedido($pedido["header"]["DocNum"]);
                    $linea->fechaAdd = isset($lineaPedido["fechaAdd"]) ? $lineaPedido["fechaAdd"] : 0;
                    $linea->unidadid = isset($lineaPedido["unidadid"]) ? $lineaPedido["unidadid"] : 0;
                    $linea->tc = isset($lineaPedido["tc"]) ? $lineaPedido["tc"] : 0;
                    $linea->idCabecera = $id;
                    $linea->DiscMonetary = isset($lineaPedido['descuentoM']) ? $lineaPedido['descuentoM'] : 0;
                    $linea->LineTotalPay = isset($lineaPedido['LineTotalPay']) ? $lineaPedido['LineTotalPay'] : 0;
                    $linea->SalesUnitLength = 0; //isset($lineaPedido['SalesUnitLength']) ? $lineaPedido['SalesUnitLength'] : 0;
                    $linea->SalesUnitWidth = isset($lineaPedido['SalesUnitWidth']) ? $lineaPedido['SalesUnitWidth'] : 0;
                    $linea->SalesUnitHeight = isset($lineaPedido['SalesUnitHeight']) ? $lineaPedido['SalesUnitHeight'] : 0;
                    $linea->SalesUnitVolume = isset($lineaPedido['SalesUnitVolume']) ? $lineaPedido['SalesUnitVolume'] : 0;
                    $linea->DiscTotalPrcnt = $lineaPedido['DiscTotalPrcnt'];
                    $linea->DiscTotalMonetary = $lineaPedido['DiscTotalMonetary'];
                    $linea->ICET = isset($lineaPedido['ICEt']) ? $lineaPedido['ICEt'] : 0;
                    $linea->ICEE = isset($lineaPedido['ICEe']) ? $lineaPedido['ICEe'] : 0;
                    $linea->ICEP = isset($lineaPedido['ICEp']) ? $lineaPedido['ICEp'] : 0;
                    $linea->xMOB_Venta1 = isset($lineaPedido["xMOB_Venta1"])?$lineaPedido["xMOB_Venta1"]:0 ;
                    $linea->xMOB_Venta2 = isset($lineaPedido["xMOB_Venta2"])? $lineaPedido["xMOB_Venta2"]:0;
                    $linea->xMOB_Venta3 = 0; //informacion de combo
                    $linea->xMOB_Venta4 = isset($lineaPedido["xMOB_Venta4"])?$lineaPedido["xMOB_Venta4"]:0 ;
                    $linea->xMOB_Venta5 = isset($lineaPedido["xMOB_Venta5"])? $lineaPedido["xMOB_Venta5"]:0;
                    $linea->bonificacion = isset($lineaPedido['bonificacion']) ? $lineaPedido['bonificacion'] : 0;
                  //  $linea->codeBonificacionUse = isset($lineaPedido['codeBonificacionUse']) ? $lineaPedido['codeBonificacionUse'] : 0;
                    $linea->TreeType = isset($lineaPedido['TreeType']) ? $lineaPedido['TreeType'] : $this->tipoItem($lineaPedido["ItemCode"]);
                    $linea->listaPrecio= $listaPrecio;

                    $linea->save(false);
                    $this->registerLotes($pedido["header"]["idDocPedido"], $lineaPedido["lotes"], $lineaPedido["ItemCode"], $pedido["usuariodataid"], $lineaPedido["LineNum"]);
					$this->registerSeries($pedido["header"]["idDocPedido"], $lineaPedido["series"], $lineaPedido["ItemCode"], $pedido["usuariodataid"], $lineaPedido["LineNum"]);
				}
            }
            return true;
        }
        else{
            return false;
        }
    }

    public function registerSeries($id,$series,$item,$usr,$linea){
        $contador=count($series);
        $hoy=date('Y-m-d');
        if($contador>0){
            $db = Yii::$app->db;
            foreach ($series as $serie){
                $sqlSub = "";
                $sqlSub .= "INSERT INTO seriesmarketing (DocumentId,SystemNumber,SerialNumber,ItemCode,Status,User,DateUpdate,linea) VALUES (";
                $sqlSub .= "'{$id}','{$serie["SystemNumber"]}','{$serie["SerialNumber"]}','{$item}','1','{$usr}','{$hoy}','{$linea}')";
                $db->createCommand($sqlSub)->execute();
                $sqlSub0 = "";
                $sqlSub0= "INSERT INTO seriesusadas (ItemCode,DistNumber) VALUES (";
                $sqlSub0.= "'{$item}','{$serie["SystemNumber"]}')";
                $db->createCommand($sqlSub0)->execute();
                $sqlSub1 = " Update seriesproductos set Status=0 where ItemCode='" . $item . "' and SerialNumber='" . $serie["SerialNumber"] . "' ";
                $db->createCommand($sqlSub1)->execute();
            }
        }
    }

    public function registerLotes($id, $lotes, $item, $usr, $linea) {
        $contador = count($lotes);
        $hoy = date('Y-m-d');
        if ($contador > 0) {
            $db = Yii::$app->db;
            foreach ($lotes as $lote) {
                $sqlSub = "";
                $sqlSub .= "INSERT INTO lotesmarketing (DocumentId,BatchNum,Quantity,ItemCode,linea,User,DateUpdate) VALUES (";
                $sqlSub .= "'{$id}','{$lote["BatchNum"]}','{$lote["Quantity"]}','{$item}','{$linea}','{$usr}','{$hoy}')";
                Yii::error(" insertar lotes " . $sqlSub);
                $db->createCommand($sqlSub)->execute();
            }
        }
    }

    public function updateHeader($pedido) {/// esto ver mau
        $pedData = $this->verificador($pedido["header"]["idDocPedido"]);
        $id = $pedData[0]['id'];
        $cabecera = Cabeceradocumentos::findOne($id);
        Yii::error('Estado enviado por el movil ======> ' . json_encode($pedido));
        //if (isset($pedido["header"]["estado"]) && $pedido["header"]["estado"] == 6)
        if (isset($pedido["header"]["estadosend"]) && $pedido["header"]["estadosend"] == 7){
             Yii::error('Actualiza Estado ======> ' );
            $cabecera->estado = 6;
            $cabecera->eliminado = 1;
            $cabecera->U_4MOTIVOCANCELADO = $pedido["header"]["U_4MOTIVOCANCELADO"];
            $cabecera->U_4MOTIVOCANCELADOCABEZERA = $pedido["header"]["U_4MOTIVOCANCELADOCABEZERA"];
            $this->aux_estado=6;
            $cabecera->save(false);
        }

        return $cabecera->id;
    }

    public function registerHeader($pedido) {
        $fechaSend = date("Y-m-d");
        $aux_vendedor_q="SELECT codEmpleadoVenta from usuarioconfiguracion where IdUser=".$pedido["usuariodataid"];
        $res_aux_vendedor_q=Yii::$app->db->createCommand($aux_vendedor_q)->queryOne();
        $pedido["header"]["SlpCode"]=$res_aux_vendedor_q["codEmpleadoVenta"];
        /**
         * obtener cardcode
         *  */
        $auxCardCode="SELECT CardCode from clientes where (CardCode='".$pedido["header"]["CardCode"]."' or Mobilecod='".$pedido["header"]["CardCode"]."');";
        $resClientCardCode=Yii::$app->db->createCommand($auxCardCode)->queryOne();
        Yii::error('cardcode encontrado ======> '.json_encode($resClientCardCode) );
        $cabecera = new Cabeceradocumentos();
        $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $pedido["header"]["fecharegistro"]);
        $cabecera->DocNum = $pedido["header"]["DocNum"];
        $cabecera->canceled = '0';
        $cabecera->DocType = $pedido["header"]["DocType"];
        $cabecera->DocDate = $pedido["header"]["DocDate"];
        $cabecera->DocDueDate = $pedido["header"]["DocDueDate"];
        $cabecera->CardCode =  $resClientCardCode &&  $resClientCardCode["CardCode"]?$resClientCardCode["CardCode"]: $pedido["header"]["CardCode"];
        $cabecera->CardName = $pedido["header"]["CardName"];
        $cabecera->TaxDate = isset($pedido["header"]["TaxDate"]) ? $pedido["header"]["TaxDate"] : date("Y-m-d");
        $cabecera->Address = isset($pedido["header"]["idSucursalMobile"][0]["LineNum"])?$pedido["header"]["idSucursalMobile"][0]["LineNum"]:'';//isset($pedido["header"]["Address"]) ? $pedido["header"]["Address"] : '';
        $cabecera->fecharegistro = $fecha;
        $cabecera->fechasend = $fechaSend;
        $cabecera->idDocPedido = $pedido["header"]["idDocPedido"];
        $cabecera->idUser = $pedido["usuariodataid"];
        $cabecera->gestion = date("Y", date_timestamp_get($fecha));
        $cabecera->mes = date("m", date_timestamp_get($fecha));
        $cabecera->correlativo = substr($pedido["header"]["idDocPedido"],-5);
        $cabecera->rowNum = isset($pedido["header"]["rowNum"]) ? $pedido["header"]["rowNum"] : count($pedido["detalles"]);
        $cabecera->DocTotal = $pedido["header"]["DocTotal"];
        $cabecera->DocTotalPay = $pedido["header"]["DocTotalPay"];
        $cabecera->DiscPrcnt = $pedido["header"]["DiscPrcnt"];
        $cabecera->DiscSum = $pedido["header"]["DiscSum"];
        $cabecera->U_4RAZON_SOCIAL = $pedido["header"]["U_4RAZON_SOCIAL"];
        $cabecera->U_4NIT = $pedido["header"]["U_4NIT"];
        $cabecera->PayTermsGrpCode = ($pedido["header"]["PayTermsGrpCode"] == 0) ? -1 : $pedido["header"]["PayTermsGrpCode"];
        $cabecera->TotalDiscPrcnt = isset($pedido["header"]["TotalDiscPrcnt"]) ? $pedido["header"]["TotalDiscPrcnt"] : 0;
        $cabecera->TotalDiscMonetary = isset($pedido["header"]["TotalDiscMonetary"]) ? $pedido["header"]["TotalDiscMonetary"] : 0;
        $cabecera->U_LATITUD = $pedido["header"]["U_LATITUD"];
        $cabecera->U_LONGITUD = $pedido["header"]["U_LONGITUD"];
        $cabecera->U_4MOTIVOCANCELADO = $pedido["header"]["U_4MOTIVOCANCELADO"];
      //  $cabecera->U_4MOTIVOCANCELADOCABEZERA = $pedido["header"]["U_4MOTIVOCANCELADOCABEZERA"];
        $cabecera->U_4DOCUMENTOORIGEN = $pedido["header"]["U_4DOCUMENTOORIGEN"];
        $cabecera->U_LB_NumeroAutorizac = isset($pedido["header"]["U_LB_NumeroAutorizac"]) ? $pedido["header"]["U_LB_NumeroAutorizac"] : 0;
        $cabecera->ControlCode = isset($pedido["header"]["U_LB_CodigoControl"]) ? $pedido["header"]["U_LB_CodigoControl"] : 0;
        $cabecera->UNumFactura = isset($pedido["header"]["U_LB_NumeroFactura"]) ? $pedido["header"]["U_LB_NumeroFactura"] : 0;
        $cabecera->U_LB_NumeroFactura = isset($pedido["header"]["U_LB_NumeroFactura"]) ? $pedido["header"]["U_LB_NumeroFactura"] : 0;
        $cabecera->SlpCode = isset($pedido["header"]["SlpCode"]) ? $pedido["header"]["SlpCode"] : -1;
        $cabecera->DocCur = isset($pedido["header"]["DocCur"]) ? $pedido["header"]["DocCur"] : null;
        $cabecera->Reserve = isset($pedido["header"]["Reserve"]) ? $pedido["header"]["Reserve"] : 0;
        $cabecera->clone = isset($pedido["header"]["origenclone"]) ? $pedido["header"]["origenclone"] : 0;
        $cabecera->giftcard = isset($pedido["header"]["giftcard"]) ? $pedido["header"]["giftcard"] : 0;
        $cabecera->estado = 3;
        $cabecera->sucursalxId = $pedido["header"]["sucursalId"];
        $cabecera->equipoId = $pedido["header"]["equipoId"];
        $cabecera->papelId = $pedido["header"]["papelId"];
        $cabecera->Comments = $pedido["header"]["comentario"];
        $cabecera->grupoproductoscode = $pedido["header"]["grupoproductoscode"];
        $cabecera->tipotransaccion = $pedido["header"]["tipotransaccion"];
        $cabecera->tipoestado = $pedido["header"]["tipoestado"];
        $cabecera->tipocambio = $pedido["header"]["tipocambio"];
        $cabecera->version = $pedido["version"];
      //  $cabecera->campania = $pedido["header"]["U_CodigoCampania"];
        //$cabecera->monto = $pedido["header"]["U_ValorSaldo"];
        $cabecera->save(false);
        $this->updatenumeracion($pedido["usuariodataid"], $pedido["header"]["DocType"]);
        return $cabecera->id;
    }

    public function registerCuotas($cuotas, $usuario, $cabecera) {
        $iddocpedido = $cabecera["idDocPedido"];
        $fecharegistro = $cabecera["DocDate"];
        $cliente = $cabecera["CardCode"];
        $total = $cabecera["DocTotalPay"];
        $control = count($cuotas);
        Yii::error(" insertar cuotas" . $control);
        $i = 0;
        $sum_por = 0;
        foreach ($cuotas as $cuota) {
            if ($control == $i) {
                $porcentage = 100 - $sum_por;
            } else {
                $porcentage = ($cuota['Total'] / $total) * 100;
                $sum_por = $sum_por + $porcentage;
            }
            $sqlSub = "";
            $sqlSub .= "INSERT INTO cuotasfactura (iddocpedido,DueDate,Percentage,Total,InstallmentId,usuario,fecharegistro,idcliente) VALUES (";
            $sqlSub .= "'{$iddocpedido}','{$cuota['DueDate']}','{$porcentage}',{$cuota['Total']},'{$cuota['InstallmentId']}','{$usuario}','{$fecharegistro}','{$cliente}');";
            Yii::error(" insertar cuotas" . $sqlSub);
            $db = Yii::$app->db;
            $db->createCommand($sqlSub)->execute();
        }
    }

    public function actionCancel() {
        $documento = Cabeceradocumentos::find()
                ->where([
                    'idDocPedido' => Yii::$app->request->post('documentId'),
                    'DocType' => Yii::$app->request->post('doctype')
                ])
                ->one();
        if (!is_null($documento)) {
            $documento->estado = 6;
            if ($documento->save(false)) {
                return $this->correcto($documento->toArray());
            }
            return $this->error('Error al actualizar documento', 101);
        }
        return $this->error('Documento no encontrado', 201);
    }

    private function numPedido($numero) {
        if ($numero < 10) {
            $codigo = "000{$numero}";
        } else if ($numero < 100) {
            $codigo = "00{$numero}";
        } else if ($numero < 1000) {
            $codigo = "0{$numero}";
        } else {
            $codigo = "{$numero}";
        }
        return $codigo;
    }

    public function actionFinddetalledocumentobydocentry($docEntry) {
        $oDetalle = Detalledocumentos::find()->where(['DocEntry' => $docEntry])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionFindcabeceradocumentobydocentry($docEntry) {
        $oDetalle = Cabeceradocumentos::find()->where(['DocEntry' => $docEntry])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionFinddetalledocumentobyid($id) {
        $oDetalle = Detalledocumentos::find()->where(['id' => $id])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionFindcabeceradocumentobyid($id) {
        $oDetalle = Cabeceradocumentos::find()->where(['id' => $id])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionClonedocumentopedido() {

        $IdDocumento = Yii::$app->request->post('doc');

        $oPedido = Cabeceradocumentos::find()->where(['id' => $IdDocumento])->one();

        if (is_object($oPedido)) {

            $cardCode = Yii::$app->request->post('cardcode');
            $direccion = Yii::$app->request->post('direccion');
            $listaPrecios = Yii::$app->request->post('listaPrecios');
            $currency = Yii::$app->request->post('currency');
            $descuentos = Yii::$app->request->post('descuentos');

            $data = Yii::$app->db->createCommand("CALL pa_clonarDoc(:Doc, :cliente, :address, :listaPrecio, :moneda, :descuentos, :usuario)")
                    ->bindValue(':Doc', $IdDocumento)
                    ->bindValue(':cliente', $cardCode)
                    ->bindValue(':address', $direccion)
                    ->bindValue(':listaPrecio', $listaPrecios)
                    ->bindValue(':moneda', $currency)
                    ->bindValue(':descuentos', $descuentos)
                    ->bindValue(':usuario', Yii::$app->user->identity->getId())
                    ->queryAll();

            return $this->correcto($data);
        }
        return $this->error();
    }

    private function unidadEntry($unidad) {
        $entry = Unidadesmedida::find()->where(['Code' => $unidad])->one();
        return $entry->getAttribute('AbsEntry');
    }

    private function usuarioId($usuario) {
        if ($usuario < 10) {
            return "000{$usuario}";
        } else if ($usuario < 100) {
            return "00{$usuario}";
        } else if ($usuario < 1000) {
            return "0{$usuario}";
        } else {
            return "{$usuario}";
        }
    }

    private function tipoItem($itemCode) {
        $item = Combos::find()->where(['TreeCode' => $itemCode])->one();
        if ($item) {
            return 'iSalesTree';
        }
        return 'iNotATree';
    }

    private function actualizaProductoAlmacen($docType,$reserve,$detalles){

        Yii::error("ACTUALIZA PRODUCTO ALMACEN");
        Yii::error($docType);
        Yii::error($reserve);
        Yii::error(json_encode($detalle));
        if($docType=='DOP' OR ($docType=='DFA' AND $reserve==1)){

            foreach ($detalles as $lineaPedido) {
                Yii::error("ACTUALIZA COMMITTED");
                $sql = 'UPDATE  productosalmacenes set  Committed = Committed + '.$lineaPedido["Quantity"].'  WHERE WareHouseCode = "'.$lineaPedido["WhsCode"].'" AND ItemCode = "'.$lineaPedido["ItemCode"].'" ';
                Yii::$app->db->createCommand($sql)->execute();
            }

        }
        else if($docType=='DOE' OR ($docType=='DFA' AND $reserve==0)){
            foreach ($detalles as $lineaPedido) {
                Yii::error("ACTUALIZA INSTOCK");
                $sql = 'UPDATE  productosalmacenes set  InStock = InStock - '.$lineaPedido["Quantity"].'  WHERE WareHouseCode = "'.$lineaPedido["WhsCode"].'" AND ItemCode = "'.$lineaPedido["ItemCode"].'" ';
                Yii::$app->db->createCommand($sql)->execute();
            }
        }

    }

    private function guardarlog($documento){
        $aux_env=json_encode($documento);
        $aux_hoy=Carbon::now('America/La_Paz')->format('Y-m-d H:m:s');
        $iddocumento=$documento["header"]["idDocPedido"];
        $detalle=json_encode($documento["detalles"]);
        $cabecera=json_encode($documento["header"]);
            $log_aux= "INSERT INTO `log_ingreso`(`proceso`, `envio`, `respuesta`,  `fecha`, `documento`,`cabecera`) VALUES (";
            $log_aux .=  "'Ingreso Docs','{$aux_env}','{$detalle}','{$aux_hoy}','{$iddocumento}','{$cabecera}');";
            $db = Yii::$app->db;
            $db->createCommand($log_aux)->execute();
    }

    private function registraHistorialDoc($pedido){
        Yii::error("Inserta HistorialDoc");
        date_default_timezone_set('America/La_Paz');
        $historialdocumentos = new Historialdocumentos();
        $historialdocumentos->id = 0;
        $historialdocumentos->fecha = date('Y-m-d');
        $historialdocumentos->fechaHora = date('Y-m-d H:i:s');
        $historialdocumentos->usuario = $pedido['usuariodataid'];
        $historialdocumentos->otpp = $pedido['pagos'][0]['otpp'];
        $historialdocumentos->idDocumento = $pedido["header"]["idDocPedido"];
        $historialdocumentos->cadenaCabecera = $pedido['cadenaCabezera'];
        $historialdocumentos->cadenaDetalle = $pedido['cadenaDetalle'];
        $historialdocumentos->cadenaPago = $pedido['cadenaPago'];
   
        if($historialdocumentos->save(false)){
            Yii::error("Registro Correcto");
        }
        else{
            Yii::error("Error al registrar historial documentos");
        }
    }
}
