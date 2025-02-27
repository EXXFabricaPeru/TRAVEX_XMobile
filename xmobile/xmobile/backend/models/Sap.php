<?php

namespace backend\models;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\Configlayer;
use backend\models\Servislayer;
use backend\models\Sincronizar;
use backend\models\Gestionbancos;
use backend\models\hana;
use Error;

class Sap extends Model {

    /**
     * @var Servislayer $model
     */
    private $model;
    private $model_odbc;

    public function __construct() {
        $this->model = new Servislayer();
        $this->model_odbc = new Sincronizar();
    }

     /**
     * migracion a sap la factura
     */
    public function exportInvoice($iddocpedido) {
            
        $serviceLayer = new Servislayer();
        $serviceLayer->actiondir = "Invoices";
        $facturas = Cabeceradocumentos::find()
                ->where("DocEntry is null and DocTotal > 0 AND DocType = 'DFA' AND id='$iddocpedido'")
                ->with('detallesincombos')
                ->limit(100)
                ->all();
                Yii::error('data factura a mandarse: ' .json_encode($facturas));
        $this->codeIT = Yii::$app->db->createCommand('select parametro,valor from configuracion where parametro = \'CODE_IT\'')->queryOne();
        $this->codeITGasto = Yii::$app->db->createCommand('select parametro,valor from configuracion where parametro = \'CODE_IT_GASTO\'')->queryOne();
        if (count($facturas)) {
            Yii::error('llega1 rafael');
            foreach ($facturas as $key => $factura) {
                Yii::error('factura a mandarse: ' . ($factura->idDocPedido));
                $aLineas = [];
                $lineNumber = 0;
                $totalIT = 0;
                $empleadoCiudad = Yii::$app->db->createCommand("select regional,sucursal from vendedores where  SalesEmployeeCode = '{$factura->SlpCode}'")->queryOne();
                $tipofactura=Yii::$app->db->createCommand("select U_series from lbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}' and U_Estado=1")->queryOne();
                $auxClone = $factura->clone;
                $montobancarizacion=$factura->DocTotalPay;
                if($montobancarizacion>=50000){
                    $auxbanc1="Y";                    
                    if($factura->PayTermsGrpCode=="-1")
                    $auxbanc2="1";
                    else
                    $auxbanc2="2";

                    $auxbanc3="1";
                }else{
                    $auxbanc1="";
                    $auxbanc2="";
                    $auxbanc3="";
                }
                $usuarioxm = Yii::$app->db->createCommand("select nombreusuario from vi_cabeceradocumentos where idCabecera = '{$factura["id"]}'")->queryOne();
                $equipoxm = Yii::$app->db->createCommand("select equipo from vi_cabeceradocumentos where idCabecera = '{$factura["id"]}'")->queryOne();
                $sucursalxm = Yii::$app->db->createCommand("select nombresucursal from vi_cabeceradocumentos where idCabecera = '{$factura["id"]}'")->queryOne();
                $plataforma=Yii::$app->db->createCommand("select plataforma from vi_cabeceradocumentos where idCabecera = '{$factura["id"]}'")->queryOne();
                $limiteEmision=Yii::$app->db->createCommand("select U_FechaLimiteEmision from lbcc where U_NumeroAutorizacion = '{$factura->U_LB_NumeroAutorizac}'")->queryOne();
                
                $cgfcard=$factura->giftcard;
                Yii::error('factura a mandarse - $cgfcard : ' .$cgfcard);
                if(($cgfcard=="")){
                    $monto_giftcard=0;
                }else{
                    
                    $monto_giftcard=explode("+",$factura["giftcard"]);
                    $monto_giftcard=$monto_giftcard[2];
                }
                $vencimiento = Yii::$app->db->createCommand("select (NumberOfAdditionalDays+(30*NumberOfAdditionalMonths)) as dias  from condicionespagos  where GroupNumber = '{$factura->PayTermsGrpCode}'")->queryOne();
                $vencimiento =$vencimiento["dias"];
                
                if($vencimiento>0){
                    $aux_vencimiento=Carbon::today('America/La_Paz')->addDays($vencimiento)->format('Y-m-d');
                }else{
                    $aux_vencimiento=$factura->DocDate;
                }
                
                $aux_descuento=$factura->TotalDiscPrcnt;
                $lbcc_equipo_aux=$factura->equipoId;
                // datos FEX  
                // FEX?
                // $q_uso_fex="SELECT * FROM configuracion WHERE parametro ='FEX'";
                $q_uso_fex = "SELECT fex FROM equipox WHERE id =".$factura->equipoId; 
                $resp_uso_fex = Yii::$app->db->createCommand($q_uso_fex)->queryOne();
                $uso_fex= $resp_uso_fex["fex"];

                if($uso_fex==1){
                    $q_cuf="SELECT * FROM fex_cufd WHERE codigocontrol ='".$factura->U_LB_NumeroAutorizac."'";
                    $resp_cuf= Yii::$app->db->createCommand($q_cuf)->queryOne();
                    $fex_sucursal= $resp_cuf["sucursal"];
                    $fex_puntoventa= $resp_cuf["puntoventa"]; 
                    $fex_metodoPago=$this->metodoPagoFEx($factura->id,$factura->PayTermsGrpCode);
                    $fex_tipoDocumento=$this->obtenertipodocumentofex($factura);
                    //$fex_sucursal= 2;
                    //$fex_puntoventa= 2;                    
                }else{
                    $fex_sucursal= 0;
                    $fex_puntoventa= 0; 
                    $fex_metodoPago=1;
                }

                $facturaSAP = [
                    "DocDate" => $factura->DocDate,
                    "DocDueDate" => $aux_vencimiento, // vencimiento
                    "CardCode" => $factura->CardCode,
                    "CardName" => $factura->CardName,
                    "DocTotal" => $factura->DocTotalPay,
                    "DocCurrency" => $factura->DocCur,
                    "PaymentGroupCode" => $factura->PayTermsGrpCode,
                    "SalesPersonCode" => $factura->SlpCode,
                    "TaxDate" => $factura->DocDate,
                    "TaxCode" => "EXO",
                    "Series"=>$tipofactura['U_series'], //tipo de factura
                    "Indicator" => "4",
                    "FederalTaxID" => $factura->U_4NIT,
                    //"U_LB_FechaLimiteEmis" =>$limiteEmision["U_FechaLimiteEmision"],
                    //"U_LB_NumeroFactura" => $factura->UNumFactura,
                    //"U_LB_CodigoControl" => $factura->ControlCode,
                    // "U_LB_NumeroAutorizac" => $factura->U_LB_NumeroAutorizac,
                    "U_LB_NIT" => $factura->U_4NIT,
                    "U_LB_RazonSocial" => $factura->U_4RAZON_SOCIAL,
                    "U_LB_ObjType"=>13,
                    "BaseAmount" => round(($factura->DocTotalPay - ($factura->DocTotalPay * 13) / 100), 2),
                    "DiscountPercent" => $factura->TotalDiscPrcnt,
                    "WareHouseUpdateType" => $this->facturaReserva($factura->DocDueDate, $factura->Reserve) ? "dwh_CustomerOrders" : "dwh_Stock",
                    "ReserveInvoice" => $this->facturaReserva($factura->DocDueDate, $factura->Reserve) ? "tYES" : "tNO",
                    "U_LB_Bancarizacion" => $auxbanc1,
                    "U_LB_ModalidadTransa"=> $auxbanc2,
                    "U_LB_TipoTransaccion"=>$auxbanc3,
                    // "U_xMOB_Usuario"=> $factura["idUser"],
                    "U_xMOB_Equipo"=> $factura["equipoId"],
                    "U_xMOB_GCardSerie"=> $factura["giftcard"],
                    "U_xMOB_GCardMonto"=> $monto_giftcard,
                    "U_xMOB_Comentario"=> $factura["Comments"],
                    //"U_xMOB_Venta5"=>$factura["xMOB_Venta5"],
                    "U_xMOB_Codigo"=> $factura["idDocPedido"],
                    "U_xMOB_Usuario" => $usuarioxm["nombreusuario"],
                    "U_xMOB_Equipo" => $equipoxm["equipo"],
                    "U_xMOB_Sucursal" => $sucursalxm["nombresucursal"],                    
                    "JournalMemo"=>"Xmobile Inv. ".$factura["idDocPedido"],
                    "U_xMOB_Plataforma"=> $plataforma["plataforma"],
                    "U_xMOB_Venta1" => $factura->xMOB_Venta1,
                    "U_xMOB_Venta2" => $factura->xMOB_Venta2,
                    "U_EXX_FE_Sucursal" =>$fex_sucursal,
                    "U_EXX_FE_PuntoVenta" =>$fex_puntoventa,
                    //"U_EXX_FE_CodDocIden"=> $fex_tipoDocumento,
                    "U_EXX_FE_CodigoMetodoPago" =>$fex_metodoPago,
                    "U_EXX_FE_NumeroTarjeta" => $factura["codigotarjeta"],
                    "U_EXX_FE_Email" => $factura["xMOB_Venta6"] //"U_EXX_FE_Email" => $factura->xMOB_Venta6
                ];
                if($uso_fex==0){
                    $facturaSAP["U_LB_FechaLimiteEmis"]=$limiteEmision["U_FechaLimiteEmision"];
                    $facturaSAP["U_LB_NumeroFactura"]= $factura->UNumFactura;
                    $facturaSAP["U_LB_CodigoControl"]= $factura->ControlCode;
                    $facturaSAP["U_LB_NumeroAutorizac"] = $factura->U_LB_NumeroAutorizac;
                }
                Yii::error('campos USUARIO ******:'. json_encode($facturaSAP));
                foreach ($factura->detallesincombos as $lineaP) {
                    //if(intval($lineaP->Price) != 0){
                    
                    $lote = [];
                    //$lote=$this->LoteCode_old($lineaP->ItemCode,$lineaP->Quantity, $factura->idDocPedido,$lineaP->LineNum);
                    $lote=$this->LoteCode($lineaP->ItemCode,$lineaP->Quantity, $factura->idDocPedido,$lineaP->unidadid,$lineaP->WhsCode);
                    /*
                    if ($this->LoteCode($lineaP->ItemCode)) {
                        array_push($lote, [
                            "BatchNumber" => $this->LoteCode($lineaP->ItemCode),
                            "Quantity" => $lineaP->Quantity,
                        ]);
                    }*/
                    $unidadNegociox = $this->unidadNegocio($lineaP->ItemCode);
                    //Yii::error("centro de costo unidad de negocio:".json_encode($this->NumeroSerie($lineaP->ItemCode, $lineaP->Quantity, $factura->idDocPedido)));
                    //Yii::error("serie:".$unidadNegociox);
                    $tru_cc=0;
                    if ($aux_descuento>0){
                        $tru_cc=Round((($lineaP->LineTotalPay)-($aux_descuento* $lineaP->LineTotalPay/100)),2);
                        Yii::error("IT : ".$aux_descuento." ".$lineaP->LineTotalPay." ".$tru_cc);
                    }else{
                        $tru_cc=($lineaP->LineTotalPay);
                    }
                    if ($lineaP->bonificacion==0){
                        $lineaP->bonificacion=2;
                    }else{
                        $lineaP->bonificacion=1;
                    }
                    $series_aux=$this->NumeroSerie($lineaP->ItemCode, $lineaP->Quantity, $factura->idDocPedido);
                    Yii::error("--> Serie ". json_encode($series_aux) );
                    $linea = [
                        "BatchNumbers" => $lote,
                        "SerialNumbers" => $series_aux,
                        "ItemCode" => $lineaP->ItemCode,
                        "ItemDescription" => $lineaP->Dscription,
                        "Quantity" => $lineaP->Quantity,
                        //"Price"=> 16.290,�                        
                        "Currency" =>$lineaP->Currency,
                        "DiscountPercent" => $this->ObtenerPorcentaje($lineaP->LineTotalPay, $lineaP->LineTotal),
                        "WarehouseCode" => $lineaP->WhsCode,
                        "SalesPersonCode" => $factura->SlpCode,
                        "TreeType" => $lineaP->TreeType,
                        "TaxCode" => "IVA",
                        "LineTotal" => round($lineaP->LineTotalPay - (($lineaP->LineTotalPay * 13) / 100), 2),
                        "TaxPercentagePerRow" => 13.0,
                        "LineStatus" => "bost_Open",
                        "OpenAmount" => round($lineaP->LineTotalPay - (($lineaP->LineTotalPay * 13) / 100), 2),
                        "UoMEntry" => $this->unidadEntry($lineaP->unidadid),
                        //"UoMCode" => $lineaP->unidadid,
                        "GrossPrice" => $lineaP->Price,
                        "GrossTotal" => $lineaP->LineTotalPay,
                        "ShipDate" => $this->facturaReserva($factura->DocDueDate, $factura->Reserve) ? $factura->DocDueDate : null,
                        "BackOrder" => $this->facturaReserva($factura->DocDueDate, $factura->Reserve) ? "tYES" : "tNO",
                        "ActualDeliveryDate" => $this->facturaReserva($factura->DocDueDate, $factura->Reserve) ? null : Carbon::today('America/La_Paz')->format('Y-m-d'),
                        //"COGSCostingCode" => $empleadoCiudad['HomeState'],
                        //"CostingCode2" => $unidadNegociox,
                        "CostingCode" => $empleadoCiudad['regional'],
                        "COGSCostingCode" => $empleadoCiudad['regional'],
                        "COGSCostingCode2" => $unidadNegociox,
                        "COGSCostingCode3" => $empleadoCiudad['sucursal'],
                        "UnitsOfMeasurment"=> "1.0",
                        //"AccountCode"=> "40101001", // MODIFICAR VALOR !
                        //"CostingCode2"=> $unidadNegociox,
                        "U_Descuento" => $lineaP->DiscTotalMonetary,
                        "U_Bonificacion"=> $lineaP->bonificacion ,
                        "U_AListaPrecio"=> $lineaP->listaprecio,
                        "U_CodCombo"=> $lineaP->cabeceracombo,
                        "LineTaxJurisdictions" => [
                            [
                                "JurisdictionCode" => "IVA",
                                "TaxRate" => 13.0
                            ]
                        ],
                        "DocumentLineAdditionalExpenses" => [
                            [
                                "ExpenseCode" => $this->codeIT['valor'],
                                "TaxCode" => "IVA",
                                
                                
                                "LineTotal" => round(($tru_cc * self::IT) / 100, 2),
                                "LineExpenseTaxJurisdictions" => [
                                    [
                                        "JurisdictionCode" => "EXE",
                                        "JurisdictionType" => 1,
                                        "LineNumber" => 0
                                    ]
                                ]
                            ],
                            [
                                "ExpenseCode" => $this->codeITGasto['valor'],
                                "TaxCode" => "IVA",
                            
                                
                                "LineTotal" => (round(($tru_cc * self::IT) / 100, 2)) * -1,
                                "DistributionRule"=> $empleadoCiudad['regional'],                                
                                "DistributionRule2"=> $unidadNegociox,
                                "DistributionRule3"=> $empleadoCiudad['sucursal'],
                                "LineExpenseTaxJurisdictions" => [
                                    [
                                        "JurisdictionCode" => "EXE",
                                        "JurisdictionType" => 1,
                                        "LineNumber" => 1
                                    ]
                                ]
                            ]

                        ]
                    ];
                    if($fex_tipoDocumento=="14"){
                        $sql_aux_prod=" select producto_std3,producto_std4 from productos where itemcode='".$lineaP->ItemCode."'";
                        $miaux_prod= Yii::$app->db->createCommand($sql_aux_prod)->queryOne();
                        $lineaP["EXX_FE_AlicuotaPorc"]=$miaux_prod["producto_std3"];
                        $lineaP["EXX_FE_AlicuotaEsp"]=$miaux_prod["producto_std4"];
                    }
                    if (intval($lineaP["BaseEntry"])!=0){
                        $linea["BaseEntry"]=intval($lineaP["BaseEntry"]);
                        $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                        $linea["BaseType"]=intval($lineaP["BaseType"]);
                    }else{
                        $linea["BaseType"]=-1;
                    }                   
                    $pos_dfo = strpos($auxClone,"DFO");
                    $pos_dfa = strpos($auxClone,"DFA");
                    $pos_dop = strpos($auxClone,"DOP");
                    $pos_doe = strpos($auxClone,"DOE");
                    Yii::error('objeto entrega linea cont: clon DFA '.$pos_dfo.' dfa '.$pos_dfa.' dop '.$pos_dop.' doe '.$pos_doe);
                    if(($auxClone != '0') and (($pos_dfo!=0) OR ($pos_dfa!=0) OR ($pos_dop!=0)  OR ($pos_doe!=0))) {
                        $auxClone='0' ;
                        //$linea["BaseType"]=13;
                        //$auxClone=$auxClone;
                    }else{
                        $auxClone=$auxClone;
                    } 
                    Yii::error('objeto entrega linea: clon '.$auxClone);  
                    if ($auxClone != '0'){
                        Yii::error('objeto pedido: clon '.$auxClone);
                        /*
                        if(is_null($lineaP["BaseEntry"])){
                        $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne(); 
                        $aux_doc_org = $aux_doc_org["DocEntry"]; 
                        }else{
                            $aux_doc_org=intval($lineaP["BaseEntry"]);
                        }*/
                        $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne(); 
                        $aux_doc_org = $aux_doc_org["DocEntry"]; 
                        
                        $aux_doctipo= Yii::$app->db->createCommand("select DocType from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne();
                        $aux_doctipo  = $aux_doctipo["DocType"];
                            switch ($aux_doctipo){
                                case 'DOF':
                                    $aux_doc_base="23";
                                break;
                                case 'DOP':
                                    $aux_doc_base="17";
                                break;
                                case 'DFA':
                                    $aux_doc_base="13";
                                break;
                                case 'DOE':
                                    $aux_doc_base="15";
                                break;
                            }
                        if (!is_null($lineaP["BaseLine"])){    
                            $linea["BaseEntry"]=$aux_doc_org;
                            $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                            $linea["BaseType"]=$aux_doc_base;
                        }else{
                            $linea["BaseType"]=-1;
                        }
                    } 
                    // "UnitsOfMeasurment"=> 1,
                    // $linea["UnitsOfMeasurment"]=1;
                    $totalIT += round(($lineaP->LineTotalPay * self::IT) / 100, 2);
                    //if($lineaP->LineTotal>0)
                        array_push($aLineas, $linea);

                    $lineNumber++;
                    //}
                }
                $auxCuotas=$this->obtenercuotasFactura($factura->PayTermsGrpCode,$factura->DocTotalPay,$factura->DocDueDate);
                $facturaSAP["DocumentLines"] = $aLineas;
                $facturaSAP["DocumentInstallments"] =$auxCuotas;
                /*
                $facturaSAP["DocumentInstallments"] = [
                    [
                        "InstallmentId" => 1,
                        "DueDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
                        "Percentage" => 100,
                        "Total" => $factura->DocTotalPay,
                    ]
                ];
                
                $facturaSAP["WithholdingTaxDataCollection"] = [
                [
                "WTCode" => "IT",
                "WTAmount" => round(($factura->DocTotalPay * self::IT)/100,2),
                ]
                ]; */
                $facturaSAP["DocumentAdditionalExpenses"] = [
                /* [
                        "ExpenseCode" => $this->codeIT['valor'],
                        "TaxCode" => "IVA",
                        "LineTotal" => round(($factura->DocTotalPay * self::IT) / 100, 2),
                        "DocExpenseTaxJurisdictions" => [
                            [
                                "JurisdictionCode" => "EXE",
                                "JurisdictionType" => 1,
                                "LineNumber" => 0
                            ]
                        ]
                    ],
                    [
                        "ExpenseCode" => $this->codeITGasto['valor'],
                        "TaxCode" => "IVA",
                        "LineTotal" => (round(($factura->DocTotalPay * self::IT) / 100, 2)) * -1,
                        "DocExpenseTaxJurisdictions" => [
                            [
                                "JurisdictionCode" => "EXE",
                                "JurisdictionType" => 1,
                                "LineNumber" => 1
                            ]
                        ]
                    ]*/
                ];

                Yii::error('FACTURA-ENVIO: ' . json_encode($facturaSAP));
                //echo 'FACTURA-ENVIO';
                //print_r($facturaSAP);
                $respuesta = $serviceLayer->executePost($facturaSAP);
                $this->aux_respuesta = json_encode($respuesta);
                Yii::error('llego hasta aqui');
                //$modeloError = new SlError();
                
                Yii::error('FACTURA-RESPUESTA' . json_encode($respuesta));

                if (isset($respuesta->DocEntry)) {
                    $factura->DocEntry = $respuesta->DocEntry;
                // $factura->estado = $factura->estado != 3 ? $factura->estado : 4;
                    $factura->estado = 4;

                    $factura->fechaupdate = Carbon::today('America/La_Paz');
                    $factura->DocNumSAP = $respuesta->DocNum;
                    $factura->save(false);
                    // FEX?                   
                    if($uso_fex==1){
                        $aux2_sql = "Update  lbcc  set  U_NumeroSiguiente= U_NumeroSiguiente+1 WHERE fex_sucursal = '" .$fex_sucursal."' and fex_puntoventa= '" .$fex_puntoventa."' and equipoId='".$lbcc_equipo_aux."'";
                    }else{
                        $aux2_sql = "Update  lbcc  set  U_NumeroSiguiente= U_NumeroSiguiente+1 WHERE U_NumeroAutorizacion = '" .$factura->U_LB_NumeroAutorizac."'";
                    }                    
                    $respuestaGuardado = Yii::$app->db->createCommand($aux2_sql)->execute();
                    Yii::error("CUF:".$respuesta->U_EXX_FE_Cuf);
                    
                    /* $this->obtenerClientesODBC($factura->CardCode);
                    foreach ($factura->detallesincombos as $lineaP) {
                        $this->obtenerProductosODBC($lineaP->ItemCode);
                    } */
                /*  $this->ObtenrFacturasCabecera();
                    $this->ObtenrFacturasDetalle();
                    $this->obtenerActualizacionDcumento($factura->id); */
                    return true;
                } else {
                    $mensajeFinal = $this->Obtenererrortraducido($respuesta);
                    $this->aux_respuesta = json_encode($mensajeFinal);
                    $factura->estado = 0;
                    $factura->save(false);
                    $this->guardarlog($facturaSAP,$respuesta,'Factura',$factura["idDocPedido"]);
                    return false;
                }
            }
        }
    }
    private function metodoPagoFEx($id,$tipopago){
        // se tiene que terminar
        $sql=" select CodigoSIN from vi_fexcodpago  where id=".$id;        
        Yii::error("entra a metodo de pago con: ".$sql);
        
        if($tipopago=="-1"){  
            $db = Yii::$app->db;
            $aux_pagos=$db->createCommand($sql)->queryOne();         
            return  $aux_pagos["CodigoSIN"];           
        }
        else
            return 6;
    }
    private function obtenertipodocumentofex($factura){
        $sql_ice= "select count(*) as contador from detalledocumentos where  idcabecera=".$factura->id." and ICET!='N'";
        $sql_bonificacion="select count(*) as contador from detalledocumentos where  idcabecera=".$factura->id." and bonificacion=1";
        Yii::error("Consultas tipo documento : ");
        Yii::error($sql_ice);
        Yii::error($sql_bonificacion);

        $miaux_ice= Yii::$app->db->createCommand($sql_ice)->queryOne();
        $miaux_bonificacion= Yii::$app->db->createCommand($sql_bonificacion)->queryOne();
        $miaux_ice=$miaux_ice["contador"];
        $miaux_bonificacion=$miaux_bonificacion["contador"];
        if($miaux_ice>0){
            $sql_tp= "select codigoSIN from fex_tipodocumento where  descripcion='ice'";
            $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
            return $tp["codigoSIN"];
        } else 
        if($miaux_bonificacion>0){
            $sql_tp= "select codigoSIN from fex_tipodocumento where  descripcion='bonificacion'";
            $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
            return $tp["codigoSIN"];
        } 
        else{
            $sql_tp= "select codigoSIN from fex_tipodocumento where  descripcion='normal'";
            $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
            return $tp["codigoSIN"];
        }

    }

    private function facturaReserva($fechaEntrega, $reserva) {
        $fechaActual = Carbon::today('America/La_Paz');
        if ($reserva == 1) {
            return true;
        }else{
            return false;
        }
    }

    public function ObtenerPorcentaje($precioDescuento, $precioTotal) {
        $descuento = (($precioTotal - $precioDescuento) * 100) / $precioTotal;
        if ($descuento == 0 || $precioTotal == 0) {
            return 0;
        }
        return round($descuento, 6);
    }

    public function LoteCode_old($ItemCode,$cantidad,$documento,$linea) {
        
        $salida_lote=[];
        $q='select BatchNum,Quantity from lotesmarketing where ItemCode="'.$ItemCode.'" and DocumentId="'.$documento.'" and linea="'.$linea.'"';
        Yii::error("lotes de producto: ".$q);
        $miauxlotes= Yii::$app->db->createCommand($q)->queryAll();  
        Yii::error("lotes de producto: ".count($miauxlotes));    
        if (count($miauxlotes)>0) {
            foreach ($miauxlotes as $xlote){ 
                        if($xlote["Quantity"]<0){
                            $xlote["Quantity"]=$xlote["Quantity"]*-1;
                        }             
                        array_push($salida_lote, [
                            "BatchNumber" =>$xlote["BatchNum"],
                            "Quantity" => $xlote["Quantity"],
                        ]);                        
                }        
            
        
        } 
        return $salida_lote;
    }
    private function LoteCode($ItemCode,$cantidad,$documento,$unidad,$almacen) {
        
        $salida_lote=[];
        $q='select BatchNum,Quantity from lotesproductos where ItemCode="'.$ItemCode.'" and WhsCode="'.$almacen.'" order by InDate';
        $q2='SELECT BaseQty from unidadmedidaxgrupo where unidadmedidaxgrupo.UomEntry=(SELECT AbsEntry from unidadesmedida where Code="'.$unidad.'") and unidadmedidaxgrupo.UgpEntry=(SELECT UoMGroupEntry from productos where productos.ItemCode="'.$ItemCode.'" )';
        Yii::error("lotes de producto: ".$q);
		Yii::error("$q2 : ".$q);
        $miauxlotes= Yii::$app->db->createCommand($q)->queryAll(); 
        /*
        $data = json_encode(array("accion" => 308, "Item"=>$ItemCode,"almacen"=>$almacen));
        $serviceLayer = new Sincronizar();
        $respuesta = $serviceLayer->executex($data);               
        $miauxlotes = json_decode($respuesta);
        Yii::error("LOTE CODE:::: ".json_encode($miauxlotes));
        */
        $BaseQty= Yii::$app->db->createCommand($q2)->queryone(); 
        $BaseQty = $BaseQty["BaseQty"]; 
        Yii::error("lotes de producto: ".count($miauxlotes));    
        if (count($miauxlotes)>0) {
            $ncantidad=$cantidad*$BaseQty;
            foreach ($miauxlotes as $xlote){
                if($ncantidad>0){
                    if($xlote["Quantity"]<$ncantidad){
                        array_push($salida_lote, [
                            "BatchNumber" =>$xlote["BatchNum"],
                            "Quantity" => $xlote["Quantity"],
                        ]);
                        $ncantidad=$ncantidad-$xlote["Quantity"];                    
                    } else{
                        array_push($salida_lote, [
                            "BatchNumber" =>$xlote["BatchNum"],
                            "Quantity" => $ncantidad,
                        ]); 
                        $ncantidad=0;
                    }
                }
                                   
                                               
            }        
            
           
        } 
        Yii::error("lotes de producto: ".json_encode($salida_lote));  
        return $salida_lote;
    }
    public function unidadNegocio($itemCode) {
        $centroCosto = Yii::$app->db->createCommand("SELECT CenterCode FROM `vi_unidadnegocio` WHERE ItemCode = :item;")
                ->bindValue(':item', $itemCode)
                ->queryOne();
        Yii::error('Negocio2' . $centroCosto['CenterCode']);
        return $centroCosto['CenterCode'];
    }

    public function NumeroSerie($itemCode, $cantidad, $documentId) {
        Yii::error(" serie de :".$itemCode." ".$cantidad." ".$documentId);
        $itemSeries=[];

        $q='select * from seriesmarketing where ItemCode="'.$itemCode.'" and DocumentId="'.$documentId.'"';
        Yii::error("series de producto 0: ".$q);
        $series= Yii::$app->db->createCommand($q)->queryAll();  
        Yii::error("series de producto: ".count($series));
        Yii::error("series de producto 1: ".json_encode($series));
        if (count($series)>0) {
            
            foreach ($series as $value) {
                Yii::error("series de producto 2: ".$value["SerialNumber"]);
                $serie = [
                    "InternalSerialNumber" => $value["SerialNumber"]
                    //"SystemNumber"=>$value->SystemNumber
                ];
                array_push($itemSeries, $serie);
            // $value->Status = 0;
            // $value->save(false);
            }
        }
        Yii::error(" serie de p2 :".count($series));
        return $itemSeries;
    }

    public function unidadEntry($unidad) {
        $entry = Unidadesmedida::find()->where(['Code' => $unidad])->one();
        return $entry->getAttribute('AbsEntry');
    }

    public function obtenercuotasFactura($grupo,$total,$vencimiento){
        
        $cuotas=Yii::$app->db->createCommand("Select * from condicionespagocuotas where GroupNum='".$grupo."'")->queryALl();
        $salida=[];
        if(count($cuotas)>0){
            foreach ($cuotas as $cuota) {
                $cuota_total=round(($total*floatval($cuota["InstPrcnt"])/100),2);
                $vencimiento= Carbon::today('America/La_Paz')->format('Y-m-d');
                Yii::error("fecha cuotas factura :".$vencimiento);
                $vencimiento=date('Y-m-d', strtotime($vencimiento. ' + '.$cuota["InstDays"].' day'));
                array_push($salida, [
                    "InstallmentId" => $cuota["IntsNo"],
                    "DueDate" => $vencimiento,
                    "Percentage" => $cuota["InstPrcnt"]
                    
                ]);
            }
        }else{
            array_push($salida, [
                "InstallmentId" => 1,
                "DueDate" => $vencimiento,
                "Percentage" => 100,
                "Total" => $total,
            ]);
        }
    
        return $salida;

    }
    public function Obtenererrortraducido($respuesta){

        //$decodificado = json_decode($respuesta);
        $capturarError = $respuesta->error;
        $errorCode = $capturarError->code;
        $mensajeError = $capturarError->message;
        $valorMensajeError = $mensajeError->value;

        $resultado =  Yii::$app->db->createCommand("SELECT * FROM sl_error WHERE error='".$errorCode."'")->queryOne();
        Yii::error(json_encode($resultado));
        if ($resultado == null) return  $valorMensajeError; //$respuesta;
        else{
            return  $resultado['mensajePos'];
        } 
    }

    public function guardarlog($documento){
        $logIngreso=new LogIngreso();
        $aux_env=json_encode($documento);        
        $aux_hoy=Carbon::now('America/La_Paz')->format('Y-m-d H:m:s');
        $iddocumento=$documento["header"]["idDocPedido"];
        $detalle=json_encode($documento["detalles"]);
        $cabecera=json_encode($documento["header"]);
        $logIngreso->proceso='Ingreso Docs';
        $logIngreso->envio=$aux_env;
        $logIngreso->respuesta=$detalle;
        $logIngreso->fecha=$aux_hoy;
        $logIngreso->documento=$iddocumento;
        $logIngreso->cabecera=$cabecera;
        $logIngreso->save();
        /*  $log_aux= "INSERT INTO `log_ingreso`(`proceso`, `envio`, `respuesta`,  `fecha`, `documento`,`cabecera`) VALUES (";
            $log_aux .=  "'Ingreso Docs','{$aux_env}','{$detalle}','{$aux_hoy}','{$iddocumento}','{$cabecera}');";                        
            $db = Yii::$app->db;
            $db->createCommand($log_aux)->execute(); */
            return $logIngreso;
    }


    public function almacenes() {
        Yii::error("Almacenes");
        $this->model->actiondir = 'Warehouses?$select=Street,WarehouseCode,State,Country,City,WarehouseName';
        $almacenes = $this->model->executex();
        $almacenes = $almacenes->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE almacenes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $ids = '';
        foreach ($almacenes as $puntero) {
            if (!is_null($puntero->WarehouseCode)) {
                $almacen = new Almacenes();
                $almacen->Street = $this->remplaceString($puntero->Street);
                $almacen->WarehouseCode = $puntero->WarehouseCode;
                $almacen->State = $this->remplaceString($puntero->State);
                $almacen->Country = $this->remplaceString($puntero->Country);
                $almacen->City = $this->remplaceString($puntero->City);
                $almacen->WarehouseName = $this->remplaceString($puntero->WarehouseName);
                $almacen->User = 1;
                $almacen->Status = 1;
                $almacen->DateUpdate = date('Y-m-d');
                $almacen->save();
                $ids .= $almacen->id;
            }
        }
        $count = Yii::$app->db->createCommand('select count(*) from almacenes')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function listasPrecios() {
        Yii::error("Listas de precios");
        $this->model->actiondir = 'PriceLists?$select=GroupNum,BasePriceList,PriceListNo,PriceListName,DefaultPrimeCurrency,IsGrossPrice,Active';
        $listaprecios = $this->model->executex();
        $listaprecios = $listaprecios->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE listaprecios;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $ids = '';
        foreach ($listaprecios as $puntero) {
            if (!is_null($puntero->PriceListNo)) {
                $xlistaprecios = new Listaprecios();
                $xlistaprecios->GroupNum = $puntero->GroupNum;
                $xlistaprecios->BasePriceList = $puntero->BasePriceList;
                $xlistaprecios->PriceListNo = $puntero->PriceListNo;
                $xlistaprecios->PriceListName = $puntero->PriceListName;
                $xlistaprecios->DefaultPrimeCurrency = $puntero->DefaultPrimeCurrency;
                $xlistaprecios->IsGrossPrice = $puntero->IsGrossPrice;
                $xlistaprecios->Active = $puntero->Active;
                $xlistaprecios->User = 1;
                $xlistaprecios->Status = 1;
                $xlistaprecios->DateUpdate = date('Y-m-d');
                $xlistaprecios->save();
                $ids .= $xlistaprecios->id;
            }
        }
        $count = Yii::$app->db->createCommand('select count(*) from listaprecios')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function unidadesMedida() {
        Yii::error("Unidades de medida");
        $this->model->actiondir = 'UnitOfMeasurements?$select=AbsEntry,Code,Name';
        $unidadesMedidas = $this->model->executex();
        $unidadesMedidas = $unidadesMedidas->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE unidadesmedida;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $ids = '';
        foreach ($unidadesMedidas as $puntero) {
            $unidadMedida = new Unidadesmedida();
            $unidadMedida->AbsEntry = $puntero->AbsEntry;
            $unidadMedida->Code = $puntero->Code;
            $unidadMedida->Name = $puntero->Name;
            $unidadMedida->User = 1;
            $unidadMedida->Status = 1;
            $unidadMedida->DateTime = date('Y-m-d');
            $unidadMedida->save();
            $ids .= $unidadMedida->id;
        }
        $count = Yii::$app->db->createCommand('select count(*) from unidadesmedida')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function vendedores() {
        Yii::error("Vendedores");
        //$this->model->actiondir = 'SalesPersons?$select=SalesEmployeeCode,SalesEmployeeName,EmployeeID,U_Regional,U_Area';//CONSULTA DE COMPANEX
        // $this->model->actiondir = 'SalesPersons?$select=SalesEmployeeCode,SalesEmployeeName,EmployeeID,U_Regional,U_Area';
        $this->model->actiondir = 'SalesPersons?$select=SalesEmployeeCode,SalesEmployeeName,EmployeeID';
        $vendedores = $this->model->executex();
        $vendedores = $vendedores->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE vendedores;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $ids = '';
        foreach ($vendedores as $puntero) {
            $vendedores = new Vendedores();
            $vendedores->SalesEmployeeCode = $puntero->SalesEmployeeCode;
            $vendedores->SalesEmployeeName = $this->remplaceString($puntero->SalesEmployeeName);
            $vendedores->EmployeeId = $puntero->EmployeeID;
            $vendedores->User = 1;
            $vendedores->Status = 1;
            $vendedores->U_Regional = $puntero->U_Regional?$puntero->U_Regional:'';//CAMPOS DE COMPANEX
            $vendedores->U_Area = $puntero->U_Area?$puntero->U_Area:''; //CAMPOS DE COMPANEX
            $vendedores->DateUpdate = date('Y-m-d');

            $vendedores->save();
            $ids .= $vendedores->id;
        }
        $count = Yii::$app->db->createCommand('select count(*) from vendedores')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function monedas() {
        Yii::error("Monedas");
        $this->model->actiondir = 'Currencies?$select=Code,Name,DocumentsCode';
        $monedas = $this->model->executex();
        $monedas = $monedas->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE monedas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $ids = '';
        $monedasSistema = Monedassistema::find()->one();
        foreach ($monedas as $puntero) {
            if (!is_null($puntero->Code)) {
                $moneda = new Monedas();
                $moneda->Code = $puntero->Code;
                $moneda->Name = $puntero->Name;
                $moneda->DocumentsCode = $puntero->DocumentsCode;
                if ($puntero->Code == $monedasSistema->CurrencyLocal) {
                    $moneda->Type = 'L';
                } else if ($puntero->Code == $monedasSistema->CurrencySystem) {
                    $moneda->Type = 'S';
                } else {
                    $moneda->Type = 'O';
                }
                $moneda->User = 1;
                $moneda->Status = 1;
                $moneda->DateUpdate = date('Y-m-d');
                $moneda->save();
                $ids .= $moneda->id;
            }
        }
        $count = Yii::$app->db->createCommand('select count(*) from monedas')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function Lotes() {
        Yii::error("Lotes");
        $this->model->actiondir = 'BatchNumberDetails?$select=ItemCode,ItemDescription,Status,Batch,AdmissionDate,ExpirationDate';
        $lotes = $this->model->executex();
        $lotes = $lotes->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE lotes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $ids = '';
        foreach ($lotes as $puntero) {
            $lote = new Lotes();
            $lote->ItemCode = $puntero->ItemCode;
            $lote->ItemDescription = $puntero->ItemDescription;
            $lote->ItemStatus = $puntero->Status;
            $lote->Batch = $puntero->Batch;
            $lote->AdmissionDate = ($puntero->AdmissionDate != null) ? $puntero->AdmissionDate : null;
            $lote->ExpirationDate = ($puntero->ExpirationDate != null) ? $puntero->ExpirationDate : null;
            $lote->Stock = 0;
            $lote->User = 1;
            $lote->Status = 1;
            $lote->DateUpdate = date('Y-m-d');
            $lote->save(false);
            $ids .= $lote->id;
        }
        $count = Yii::$app->db->createCommand('select count(*) from lotes')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function productos() {
      $this->obtenerProductosODBC();
      //$this->obtenerProductosAlmacenesODBC();
     //  $this->obtenerProductosPreciosODBC();
      //$this->obtenerProductosSeriesODBC();
      //$this->obtenerProductosLotesODBC();
    }

    public function productosSL() {
        Yii::error("Productos");
        $this->model->actiondir = 'Items/$count?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Manufacturer,NoDiscounts&$filter=   TreeType ne \'iNotATree\'  or (QuantityOnStock gt 0) or (InventoryItem eq \'tNO\') and (SalesItem eq \'tYES\')';
        $cantidad = $this->model->countRowsok(0);
        //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosalmacenes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosprecios;SET FOREIGN_KEY_CHECKS = 1;')->execute();
       /* */
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productos;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosalmacenes;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosprecios;')->execute();
        $textoProducto = '';
        $insertProducto = '';
        $sql = "SELECT * FROM configuracion WHERE parametro LIKE 'producto_std%' AND estado=1 ORDER BY parametro";
        $parametrosProducto = Yii::$app->db->createCommand($sql)->queryAll();
        $cantidadProducto = count($parametrosProducto);
        if (count($parametrosProducto)){
            for ($c = 0; $c < $cantidadProducto; $c++){
                if ($textoProducto == ''){
                    $textoProducto = $parametrosProducto[$c]["valor2"];
                    $insertProducto = $parametrosProducto[$c]["parametro"];
                }
                else {
                    $textoProducto = $textoProducto.','.$parametrosProducto[$c]["valor2"];
                    $insertProducto = $insertProducto.','.$parametrosProducto[$c]["parametro"];
                }
            }
        }
        $productos = '';
        $iteracion = 10;
        //$cantidad=3556;

        for ($i = 0; $i < $cantidad; $i += $iteracion) {
            clearstatcache();
            //$this->model->actiondir = 'Items?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,Properties8,Properties9,Properties10,Properties11,Properties12,Properties13,Properties14,Properties15,Properties16,Properties17,Properties18,Properties19,Properties20,Properties21,Properties22,Properties23,Properties24,Properties25,Properties26,Properties27,Properties28,Properties29,Properties30,Properties31,Properties32,Properties33,Properties34,Properties35,Properties36,Properties37,Properties38,Properties39,Properties40,Properties41,Properties42,Properties43,Properties44,Properties45,Properties46,Properties47,Properties48,Properties49,Properties50,Properties51,Properties52,Properties53,Properties54,Properties55,Properties56,Properties57,Properties58,Properties59,Properties60,Properties61,Properties62,Properties63,Properties64,Manufacturer,NoDiscounts,U_Centro,TreeType,'.$textoProducto.'&$filter= TreeType ne \'iNotATree\'  or (QuantityOnStock gt 0)&$skip=' . $i;
			$this->model->actiondir = 'Items?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,Properties8,Properties9,Properties10,Properties11,Properties12,Properties13,Properties14,Properties15,Properties16,Properties17,Properties18,Properties19,Properties20,Properties21,Properties22,Properties23,Properties24,Properties25,Properties26,Properties27,Properties28,Properties29,Properties30,Properties31,Properties32,Properties33,Properties34,Properties35,Properties36,Properties37,Properties38,Properties39,Properties40,Properties41,Properties42,Properties43,Properties44,Properties45,Properties46,Properties47,Properties48,Properties49,Properties50,Properties51,Properties52,Properties53,Properties54,Properties55,Properties56,Properties57,Properties58,Properties59,Properties60,Properties61,Properties62,Properties63,Properties64,Manufacturer,NoDiscounts,TreeType,'.$textoProducto.'&$filter=TreeType ne \'iNotATree\'  or (QuantityOnStock gt 0) or (InventoryItem eq \'tNO\') and (SalesItem eq \'tYES\') &$orderby=ItemCode&$skip=' . $i;
            //Yii::error("Productos".$this->model->actiondir);
            $productos = $this->model->executex($iteracion);
            //Yii::error("Productos ".$productos);
            $productos = $productos->value;
           
            if($productos){
                $fecha = date("Y-m-d");
                foreach ($productos as $puntero) {
                    if($puntero->TreeType=="iTemplateTree"){
                        $miaux_combo=1;
                        //$this->combos(); 
                    }                
                    else
                    $miaux_combo=0;
    
                    if($puntero->ManageSerialNumbers=="tYES")
                    $miaux_series=1;
                    else
                    $miaux_series=0;
                    if($puntero->ManageBatchNumbers=="tYES")
                    $miaux_lotes=1;
                    else
                    $miaux_lotes=0;
    
                    $producto = new Productos();
                    $producto->ItemCode = $puntero->ItemCode;
                    $producto->ItemName = $this->remplaceString($puntero->ItemName);
                    $producto->ItemsGroupCode = $puntero->ItemsGroupCode;
                    $producto->ForeignName = $this->remplaceString($puntero->ForeignName);
                    $producto->CustomsGroupCode = $puntero->CustomsGroupCode;
                    $producto->BarCode = $puntero->BarCode;
                    $producto->PurchaseItem = $puntero->PurchaseItem;
                    $producto->SalesItem = $puntero->SalesItem;
                    $producto->InventoryItem = $puntero->InventoryItem;
                    $producto->UserText = $this->remplaceString($puntero->User_Text);
                    $producto->SerialNum = $puntero->SerialNum;
                    $producto->QuantityOnStock = $puntero->QuantityOnStock;
                    $producto->QuantityOrderedFromVendors = $puntero->QuantityOrderedFromVendors;
                    $producto->QuantityOrderedByCustomers = $puntero->QuantityOrderedByCustomers;
                    $producto->ManageSerialNumbers = $miaux_series;
                    $producto->ManageBatchNumbers = $miaux_lotes;
                    $producto->SalesUnit = $puntero->SalesUnit;
                    $producto->SalesUnitLength = $puntero->SalesUnitLength;
                    $producto->SalesUnitWidth = $puntero->SalesUnitWidth;
                    $producto->SalesUnitHeight = $puntero->SalesUnitHeight;
                    $producto->SalesUnitVolume = $puntero->SalesUnitVolume;
                    $producto->PurchaseUnit = $puntero->PurchaseUnit;
                    $producto->DefaultWarehouse = $puntero->DefaultWarehouse;
                    $producto->ManageStockByWarehouse = $puntero->ManageStockByWarehouse;
                    $producto->ForceSelectionOfSerialNumber = $puntero->ForceSelectionOfSerialNumber;
                    $producto->Series = $puntero->Series;
                    $producto->UoMGroupEntry = $puntero->UoMGroupEntry;
                    $producto->DefaultSalesUoMEntry = $puntero->DefaultSalesUoMEntry;
                    $producto->User = 1;
                    $producto->Status = 1;
                    $producto->DateUpdate = date("Y-m-d");
                    $this->productoPropiedad($puntero);
                    $producto->Manufacturer = $puntero->Manufacturer;
                    $producto->NoDiscounts = $puntero->NoDiscounts;
                    //$producto->U_XM_ICEtipo = "N";
                    //$producto->U_XM_ICEPorcentual = 0;
                    //$producto->U_XM_ICEEspecifico = 0;
                    //$producto->U_Centro = $puntero->U_Centro;
                    $producto->combo = $miaux_combo;
                    if (count($parametrosProducto)){
                        for ($c = 0; $c < $cantidadProducto; $c++){
                            $campoNombre = $parametrosProducto[$c]["parametro"];		
                            $campoValor = $parametrosProducto[$c]["valor2"];
                            $producto->$campoNombre = $puntero->$campoValor;
                        }
                    }
                   // Yii::error("ITEMS --->>> ".json_encode($producto));
                    if (!$producto->save(false))
                        Yii::error(json_encode($producto));
                    $db = Yii::$app->db;
                    $sqlPA = '';
                    try {
                        //$transaction = $db->beginTransaction();
                        foreach ($puntero->ItemWarehouseInfoCollection as $almacenProducto) {
                            if(($almacenProducto->InStock>0)or($puntero->TreeType=="iTemplateTree")){
                                $sqlPA = "insert into productosalmacenes (ItemCode,WarehouseCode,InStock,Committed,Ordered,Locked,User,Status,DateUpdate) values('{$puntero->ItemCode}','{$almacenProducto->WarehouseCode}','{$almacenProducto->InStock}','{$almacenProducto->Committed}','{$almacenProducto->Ordered}','{$almacenProducto->Locked}',1,1,'{$fecha}');";
                                $db->createCommand($sqlPA)->execute();
                            }
                            

                        }
                        //$db->createCommand($sqlPA)->execute();
                       // $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        throw $e;
                    }
                    foreach ($puntero->ItemPrices as $listaPrecio) {
                        if (count($listaPrecio->UoMPrices) > 0) {
                            foreach ($listaPrecio->UoMPrices as $unidadPrecio) {
                                $oListaPrecio = new Productosprecios();
                                $oListaPrecio->ItemCode = $puntero->ItemCode;
                                $oListaPrecio->IdListaPrecios = $this->idListaPrecio($unidadPrecio->PriceList);
                                $oListaPrecio->IdUnidadMedida = $this->idUnidadNumero($unidadPrecio->UoMEntry);
                                $oListaPrecio->Price = $unidadPrecio->Price;
                                $oListaPrecio->Currency = $unidadPrecio->Currency;
                                $oListaPrecio->User = 1;
                                $oListaPrecio->Status = 1;
                                $oListaPrecio->DateUpdate = date("Y-m-d");
                                if(($oListaPrecio->Price >0 )or($puntero->TreeType=="iTemplateTree")){
                                    $oListaPrecio->save();
                                }
                                    

                                if (intval($unidadPrecio->AdditionalPrice1) != 0 && !is_null($unidadPrecio->AdditionalCurrency1)) {
                                    $oListaPrecioAd = new Productosprecios();
                                    $oListaPrecioAd->ItemCode = $puntero->ItemCode;
                                    $oListaPrecioAd->IdListaPrecios = $this->idListaPrecio($unidadPrecio->PriceList);
                                    $oListaPrecioAd->IdUnidadMedida = $this->idUnidadNumero($unidadPrecio->UoMEntry);
                                    $oListaPrecioAd->Price = $unidadPrecio->AdditionalPrice1;
                                    $oListaPrecioAd->Currency = $unidadPrecio->AdditionalCurrency1;
                                    $oListaPrecioAd->User = 1;
                                    $oListaPrecioAd->Status = 1;
                                    $oListaPrecioAd->DateUpdate = date("Y-m-d");
                                    $oListaPrecioAd->save();
                                }
                                $db = Yii::$app->db;
                                $sqlPA = '';
                                try {
                                    //$transaction = $db->beginTransaction();
                                    foreach ($puntero->ItemWarehouseInfoCollection as $almacenProducto) {
                                        if(($almacenProducto->InStock>0)or($puntero->TreeType=="iTemplateTree")){
                                            $sqlPA = "insert into productosalmacenes (ItemCode,WarehouseCode,InStock,Committed,Ordered,Locked,User,Status,DateUpdate) values('{$puntero->ItemCode}','{$almacenProducto->WarehouseCode}','{$almacenProducto->InStock}','{$almacenProducto->Committed}','{$almacenProducto->Ordered}','{$almacenProducto->Locked}',1,1,'{$fecha}');";
                                            $db->createCommand($sqlPA)->execute();
                                        }
                                    }
                                   
                                   // $transaction->commit();
                                } catch (\Exception $e) {
                                    $transaction->rollBack();
                                    throw $e;
                                } catch (\Throwable $e) {
                                    $transaction->rollBack();
                                    throw $e;
                                }
                            }
                        } else {
                            $oListaPrecio = new Productosprecios();
                            $oListaPrecio->ItemCode = $puntero->ItemCode;
                            $oListaPrecio->IdListaPrecios = $this->idListaPrecio($listaPrecio->PriceList);
                            $unidadMedida = is_null($puntero->SalesUnit) ? $puntero->InventoryUOM : $puntero->SalesUnit;
                            $oListaPrecio->IdUnidadMedida = $this->idUnidad($unidadMedida);
                            $oListaPrecio->Price = $listaPrecio->Price;
                            $oListaPrecio->Currency = $listaPrecio->Currency;
                            $oListaPrecio->User = 1;
                            $oListaPrecio->Status = 1;
                            $oListaPrecio->DateUpdate = date("Y-m-d");
                            if(($oListaPrecio->Price >0 )or($puntero->TreeType=="iTemplateTree")){
                                $oListaPrecio->save();
                            }
                            
                            if (intval($listaPrecio->AdditionalPrice1) != 0 && !is_null($listaPrecio->AdditionalCurrency1)) {
                                $oListaPrecio = new Productosprecios();
                                $oListaPrecio->ItemCode = $puntero->ItemCode;
                                $oListaPrecio->IdListaPrecios = $this->idListaPrecio($listaPrecio->PriceList);
                                $unidadMedida = is_null($puntero->SalesUnit) ? $puntero->InventoryUOM : $puntero->SalesUnit;
                                $oListaPrecio->IdUnidadMedida = $this->idUnidad($unidadMedida);
                                $oListaPrecio->Price = $listaPrecio->AdditionalPrice1;
                                $oListaPrecio->Currency = $listaPrecio->AdditionalCurrency1;
                                $oListaPrecio->User = 1;
                                $oListaPrecio->Status = 1;
                                $oListaPrecio->DateUpdate = date("Y-m-d");
                                $oListaPrecio->save();
                            }
                            if (intval($listaPrecio->AdditionalPrice2) != 0 && !is_null($listaPrecio->AdditionalCurrency2)) {
                                $oListaPrecio = new Productosprecios();
                                $oListaPrecio->ItemCode = $puntero->ItemCode;
                                $oListaPrecio->IdListaPrecios = $this->idListaPrecio($listaPrecio->PriceList);
                                $unidadMedida = is_null($puntero->SalesUnit) ? $puntero->InventoryUOM : $puntero->SalesUnit;
                                $oListaPrecio->IdUnidadMedida = $this->idUnidad($unidadMedida);
                                $oListaPrecio->Price = $listaPrecio->AdditionalPrice2;
                                $oListaPrecio->Currency = $listaPrecio->AdditionalCurrency2;
                                $oListaPrecio->User = 1;
                                $oListaPrecio->Status = 1;
                                $oListaPrecio->DateUpdate = date("Y-m-d");
                                $oListaPrecio->save();
                            }
                        }
                    }
                }
            }
            
        }
        //$this->combos();
        return true;
    }

    public function productos2() {
        Yii::error("Prodictos 2");
        //$this->model->actiondir = 'Items?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,Properties8,Properties9,Properties10,Properties11,Properties12,Properties13,Properties14,Properties15,Properties16,Properties17,Properties18,Properties19,Properties20,Properties21,Properties22,Properties23,Properties24,Properties25,Properties26,Properties27,Properties28,Properties29,Properties30,Properties31,Properties32,Properties33,Properties34,Properties35,Properties36,Properties37,Properties38,Properties39,Properties40,Properties41,Properties42,Properties43,Properties44,Properties45,Properties46,Properties47,Properties48,Properties49,Properties50,Properties51,Properties52,Properties53,Properties54,Properties55,Properties56,Properties57,Properties58,Properties59,Properties60,Properties61,Properties62,Properties63,Properties64,Manufacturer,NoDiscounts&$filter=contains(ItemCode, \'COMB\') ';
        $this->model->actiondir = 'Items?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,Properties8,Properties9,Properties10,Properties11,Properties12,Properties13,Properties14,Properties15,Properties16,Properties17,Properties18,Properties19,Properties20,Properties21,Properties22,Properties23,Properties24,Properties25,Properties26,Properties27,Properties28,Properties29,Properties30,Properties31,Properties32,Properties33,Properties34,Properties35,Properties36,Properties37,Properties38,Properties39,Properties40,Properties41,Properties42,Properties43,Properties44,Properties45,Properties46,Properties47,Properties48,Properties49,Properties50,Properties51,Properties52,Properties53,Properties54,Properties55,Properties56,Properties57,Properties58,Properties59,Properties60,Properties61,Properties62,Properties63,Properties64,Manufacturer,NoDiscounts&$filter=contains(ItemCode, \'ITM-000000\')';

        $productos = $this->model->executex(30);
        $productos = $productos->value;
        //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosalmacenes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosprecios;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $fecha = date("Y-m-d");
        foreach ($productos as $puntero) {
            $producto = new Productos();
            $producto->ItemCode = $puntero->ItemCode;
            $producto->ItemName = $this->remplaceString($puntero->ItemName);
            $producto->ItemsGroupCode = $puntero->ItemsGroupCode;
            $producto->ForeignName = $this->remplaceString($puntero->ForeignName);
            $producto->CustomsGroupCode = $puntero->CustomsGroupCode;
            $producto->BarCode = $puntero->BarCode;
            $producto->PurchaseItem = $puntero->PurchaseItem;
            $producto->SalesItem = $puntero->SalesItem;
            $producto->InventoryItem = $puntero->InventoryItem;
            $producto->UserText = $this->remplaceString($puntero->User_Text);
            $producto->SerialNum = $puntero->SerialNum;
            $producto->QuantityOnStock = $puntero->QuantityOnStock;
            $producto->QuantityOrderedFromVendors = $puntero->QuantityOrderedFromVendors;
            $producto->QuantityOrderedByCustomers = $puntero->QuantityOrderedByCustomers;
            $producto->ManageSerialNumbers = $puntero->ManageSerialNumbers;
            $producto->ManageBatchNumbers = $puntero->ManageBatchNumbers;
            $producto->SalesUnit = $puntero->SalesUnit;
            $producto->SalesUnitLength = $puntero->SalesUnitLength;
            $producto->SalesUnitWidth = $puntero->SalesUnitWidth;
            $producto->SalesUnitHeight = $puntero->SalesUnitHeight;
            $producto->SalesUnitVolume = $puntero->SalesUnitVolume;
            $producto->PurchaseUnit = $puntero->PurchaseUnit;
            $producto->DefaultWarehouse = $puntero->DefaultWarehouse;
            $producto->ManageStockByWarehouse = $puntero->ManageStockByWarehouse;
            $producto->ForceSelectionOfSerialNumber = $puntero->ForceSelectionOfSerialNumber;
            $producto->Series = $puntero->Series;
            $producto->UoMGroupEntry = $puntero->UoMGroupEntry;
            $producto->DefaultSalesUoMEntry = $puntero->DefaultSalesUoMEntry;
            $producto->User = 1;
            $producto->Status = 1;
            $producto->DateUpdate = date("Y-m-d");
            $this->productoPropiedad($puntero);
            $producto->Manufacturer = $puntero->Manufacturer;
            $producto->NoDiscounts = $puntero->NoDiscounts;
            $producto->U_XM_ICEtipo = "N";
            $producto->U_XM_ICEPorcentual = 0;
            $producto->U_XM_ICEEspecifico = 0;
            if (!$producto->save(false)) {
                Yii::error(json_encode($producto));
            }
            $db = Yii::$app->db;
            $sqlPA = '';
            try {
                $transaction = $db->beginTransaction();
                foreach ($puntero->ItemWarehouseInfoCollection as $almacenProducto) {
                    $sqlPA .= "insert into productosalmacenes (ItemCode,WarehouseCode,InStock,Committed,Ordered,Locked,User,Status,DateUpdate) values('{$puntero->ItemCode}','{$almacenProducto->WarehouseCode}','{$almacenProducto->InStock}','{$almacenProducto->Committed}','{$almacenProducto->Ordered}','{$almacenProducto->Locked}',1,1,'{$fecha}');";
                }
                $db->createCommand($sqlPA)->execute();
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
            foreach ($puntero->ItemPrices as $listaPrecio) {
                if (count($listaPrecio->UoMPrices) > 0) {
                    foreach ($listaPrecio->UoMPrices as $unidadPrecio) {
                        $oListaPrecio = new Productosprecios();
                        $oListaPrecio->ItemCode = $puntero->ItemCode;
                        $oListaPrecio->IdListaPrecios = $this->idListaPrecio($unidadPrecio->PriceList);
                        $oListaPrecio->IdUnidadMedida = $this->idUnidadNumero($unidadPrecio->UoMEntry);
                        $oListaPrecio->Price = $unidadPrecio->Price;
                        $oListaPrecio->Currency = $unidadPrecio->Currency;
                        $oListaPrecio->User = 1;
                        $oListaPrecio->Status = 1;
                        $oListaPrecio->DateUpdate = date("Y-m-d");
                        $oListaPrecio->save();
                        if (intval($unidadPrecio->AdditionalPrice1) != 0 && !is_null($unidadPrecio->AdditionalCurrency1)) {
                            $oListaPrecioAd = new Productosprecios();
                            $oListaPrecioAd->ItemCode = $puntero->ItemCode;
                            $oListaPrecioAd->IdListaPrecios = $this->idListaPrecio($unidadPrecio->PriceList);
                            $oListaPrecioAd->IdUnidadMedida = $this->idUnidadNumero($unidadPrecio->UoMEntry);
                            $oListaPrecioAd->Price = $unidadPrecio->AdditionalPrice1;
                            $oListaPrecioAd->Currency = $unidadPrecio->AdditionalCurrency1;
                            $oListaPrecioAd->User = 1;
                            $oListaPrecioAd->Status = 1;
                            $oListaPrecioAd->DateUpdate = date("Y-m-d");
                            $oListaPrecioAd->save();
                        }
                        if (intval($unidadPrecio->AdditionalPrice2) != 0 && !is_null($unidadPrecio->AdditionalCurrency2)) {
                            $oListaPrecio = new Productosprecios();
                            $oListaPrecio->ItemCode = $puntero->ItemCode;
                            $oListaPrecio->IdListaPrecios = $this->idListaPrecio($unidadPrecio->PriceList);
                            $oListaPrecio->IdUnidadMedida = $this->idUnidadNumero($unidadPrecio->UoMEntry);
                            $oListaPrecio->Price = $unidadPrecio->AdditionalPrice2;
                            $oListaPrecio->Currency = $unidadPrecio->AdditionalCurrency2;
                            $oListaPrecio->User = 1;
                            $oListaPrecio->Status = 1;
                            $oListaPrecio->DateUpdate = date("Y-m-d");
                            $oListaPrecio->save();
                        }
                    }
                } else {
                    $oListaPrecio = new Productosprecios();
                    $oListaPrecio->ItemCode = $puntero->ItemCode;
                    $oListaPrecio->IdListaPrecios = $this->idListaPrecio($listaPrecio->PriceList);
                    $unidadMedida = is_null($puntero->SalesUnit) ? $puntero->InventoryUOM : $puntero->SalesUnit;
                    $oListaPrecio->IdUnidadMedida = $this->idUnidad($unidadMedida);
                    $oListaPrecio->Price = $listaPrecio->Price;
                    $oListaPrecio->Currency = $listaPrecio->Currency;
                    $oListaPrecio->User = 1;
                    $oListaPrecio->Status = 1;
                    $oListaPrecio->DateUpdate = date("Y-m-d");
                    $oListaPrecio->save();
                    if (intval($listaPrecio->AdditionalPrice1) != 0 && !is_null($listaPrecio->AdditionalCurrency1)) {
                        $oListaPrecio = new Productosprecios();
                        $oListaPrecio->ItemCode = $puntero->ItemCode;
                        $oListaPrecio->IdListaPrecios = $this->idListaPrecio($listaPrecio->PriceList);
                        $unidadMedida = is_null($puntero->SalesUnit) ? $puntero->InventoryUOM : $puntero->SalesUnit;
                        $oListaPrecio->IdUnidadMedida = $this->idUnidad($unidadMedida);
                        $oListaPrecio->Price = $listaPrecio->AdditionalPrice1;
                        $oListaPrecio->Currency = $listaPrecio->AdditionalCurrency1;
                        $oListaPrecio->User = 1;
                        $oListaPrecio->Status = 1;
                        $oListaPrecio->DateUpdate = date("Y-m-d");
                        $oListaPrecio->save();
                    }
                    if (intval($listaPrecio->AdditionalPrice2) != 0 && !is_null($listaPrecio->AdditionalCurrency2)) {
                        $oListaPrecio = new Productosprecios();
                        $oListaPrecio->ItemCode = $puntero->ItemCode;
                        $oListaPrecio->IdListaPrecios = $this->idListaPrecio($listaPrecio->PriceList);
                        $unidadMedida = is_null($puntero->SalesUnit) ? $puntero->InventoryUOM : $puntero->SalesUnit;
                        $oListaPrecio->IdUnidadMedida = $this->idUnidad($unidadMedida);
                        $oListaPrecio->Price = $listaPrecio->AdditionalPrice2;
                        $oListaPrecio->Currency = $listaPrecio->AdditionalCurrency2;
                        $oListaPrecio->User = 1;
                        $oListaPrecio->Status = 1;
                        $oListaPrecio->DateUpdate = date("Y-m-d");
                        $oListaPrecio->save();
                    }
                }
            }
        }
    }

    private function idUnidad($unidad) {
        //Yii::error("idunidad");
        $unidadMedida = Unidadesmedida::find()->where(['Name' => $unidad])->one();
        if (is_null($unidadMedida)) {
            $unidadMedida = Unidadesmedida::find()->where(['like', 'Name', 'UNI%'])->one();
        }
        return (is_null($unidadMedida)) ? $this->nuevaUnidad() : $unidadMedida->id;
    }

    private function idUnidadNumero($unidad) {
        //Yii::error("id unidadnumero");
        $unidad = Unidadesmedida::find()->where(['AbsEntry' => $unidad])->one();
        if (is_null($unidad)) {
            $unidad = Unidadesmedida::find()->where(['like', 'Name', 'UNI%'])->one();
        }
        try {
            return $unidad->id;
        } catch (Exception $e) {
            echo $unidad;
        }
    }

    private function idListaPrecio($lista) {
        //Yii::error("idlistade precio");
        $listaPrecio = Listaprecios::find()->where(['PriceListNo' => $lista])->one();
        return $listaPrecio->id;
    }

    public function clientesGrupos() {
        Yii::error("clientes grupos");
        $this->model->actiondir = 'BusinessPartnerGroups?$select=Code,Name,Type&$filter=Type eq \'bbpgt_CustomerGroup\'';
		//$this->model->actiondir = "BusinessPartnerGroups?$select=Code,Name,Type&$filter= Type eq 'bbpgt_CustomerGroup' ";
        Yii::error("clientes grupos ".$this->model->actiondir);
        $clientesgrupos = $this->model->executex();
        $clientesgrupos = $clientesgrupos->value;
        Yii::$app->db->createCommand('TRUNCATE TABLE clientesgrupo;')->execute();
        $ids = '';
        foreach ($clientesgrupos as $puntero) {
            $model = new Clientesgrupo();
            $model->Code = $puntero->Code;
            $model->Name = $puntero->Name;
            $model->Type = $puntero->Type;
            $model->User = 1;
            $model->Status = 1;
            $model->DateUpdate = date('Y-m-d');
            $model->save();
            $ids .= $model->id;
        }
        $count = Yii::$app->db->createCommand('select count(*) from clientesgrupo')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function clientes() {
        Yii::error("clientes");
        $this->obtenerClientesODBC();
        // $this->Metodocualquiera();
        // $this->obtenerClientesDirecODBC();

    }

    public function tipoCambio() {
        $i=0;
        Yii::error("tipo cambio");
        $fecha = Carbon::today();
        $model = new Servislayer();
        $monedaLocal = Monedassistema::find()->one();
        $monedaLocal = $monedaLocal->getAttribute('CurrencyLocal');
        $fechas = true;
        $tiposCambios = [];
        $monedaLocal = Monedas::find()->where(['Code' => strtoupper($monedaLocal)])->one();
        $monedas = Monedas::find()->select('id,Code')->all();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE tiposcambio;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        foreach ($monedas as $moneda) {

            $fechas = true;
            $fecha = Carbon::today('America/La_Paz');
            if ($monedaLocal->getAttribute('Code') != $moneda->Code) {
                while ($fechas) {

                    $model->actiondir = 'SBOBobService_GetCurrencyRate';
                    $parametros = [
                        "Currency" => $moneda->Code,
                        "Date" => $fecha->format('Ymd')
                    ];
                    Yii::error("ERROR 731: ".json_encode($parametros));
                    
                    $respuesta = $model->executePost($parametros);

                    Yii::error("REspuesta Moneda(".$moneda->Code."): " .json_encode($respuesta));
                    if (!$respuesta) {
                        $fechas = !$fechas;
                        break;
                    }
                    if($respuesta>0 ){
                        Yii::error("Respuesta");
                        Yii::error($respuesta);
                        if(!isset($respuesta->error)){
                            $tipoCambio = new Tiposcambio();
                            $tipoCambio->ExchangeRateFrom = $moneda->id;
                            $tipoCambio->ExchangeRateTo = $monedaLocal->id;
                            $tipoCambio->ExchangeRateDate = $fecha->format("Y-m-d");
                            $tipoCambio->ExchangeRate = $respuesta;
                            $tipoCambio->User = 1;
                            $tipoCambio->Status = 1;
                            $tipoCambio->DateUpdate = Carbon::today();
                            $tipoCambio->save(false);
                        }
                    }                    
                    $fecha->addDay(1);
                    $i=$i+1;
                    if($i>10){
                        $fechas=false;
                        $i=0;
                    }
                }
            }
        }
        $count = Yii::$app->db->createCommand('select count(*) from tiposcambio')->queryAll();
        Yii::error($count);
        $this->bancos();
    }

    public function bancos(){
        Yii::error("GESTION DE BANCOS");
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE gestionbancos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $this->model->actiondir = 'Banks?$filter=CountryCode eq \'BO\'';
        $bancos = $this->model->executex();
        $bancos = $bancos->value;
        foreach ($bancos as $banco) {
            $gestionBanco = new Gestionbancos();
            $gestionBanco->BankCode = $banco->BankCode;
            $gestionBanco->BankName = $banco->BankName;
            $gestionBanco->CountryCode = $banco->CountryCode;
            $gestionBanco->save(false);
        }
        $count = Yii::$app->db->createCommand('select count(*) from gestionbancos')->queryAll();
        Yii::error('CANTIDAD GESTION BANCOS' . $count);
    }

    public function condicionesPagos() {
        Yii::error("condiciones pago");
        $this->model->actiondir = 'PaymentTermsTypes';
        $condicionesPagos = $this->model->executex();
        $condicionesPagos = $condicionesPagos->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE condicionespagos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = '';
        foreach ($condicionesPagos as $puntero) {
            $condicionPago = new Condicionespagos();
            $condicionPago->GroupNumber = $puntero->GroupNumber;
            $condicionPago->PaymentTermsGroupName = $puntero->PaymentTermsGroupName;
            $condicionPago->StartFrom = $puntero->StartFrom;
            $condicionPago->NumberOfAdditionalMonths = $puntero->NumberOfAdditionalMonths;
            $condicionPago->NumberOfAdditionalDays = $puntero->NumberOfAdditionalDays;
            $condicionPago->CreditLimit = $puntero->CreditLimit;
            $condicionPago->GeneralDiscount = $puntero->GeneralDiscount;
            $condicionPago->InterestOnArrears = $puntero->InterestOnArrears;
            $condicionPago->PriceListNo = $puntero->PriceListNo;
            $condicionPago->LoadLimit = $puntero->LoadLimit;
            $condicionPago->OpenReceipt = $puntero->OpenReceipt;
            $condicionPago->DiscountCode = $puntero->DiscountCode;
            $condicionPago->DunningCode = $puntero->DunningCode;
            $condicionPago->BaselineDate = $puntero->BaselineDate;
            $condicionPago->NumberOfInstallments = $puntero->NumberOfInstallments;
            $condicionPago->NumberOfToleranceDays = $puntero->NumberOfToleranceDays;
            $condicionPago->U_UsaLc = isset($puntero->U_UsaLc) ? $puntero->U_UsaLc : 0;
            $condicionPago->User = Yii::$app->user->identity->getId();
            $condicionPago->DateUpdated = Carbon::today();
            $condicionPago->Status = 1;
            $condicionPago->save(false);
        }
        $count = Yii::$app->db->createCommand('select count(*) from condicionespagos')->queryAll();
        Yii::error($count);
    }

    public function territorios() {
        Yii::error("terrritorios");
        $this->model->actiondir = 'Territories';
        $territorios = $this->model->executex();
        $territorios = $territorios->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE territorios;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = '';
        foreach ($territorios as $puntero) {
            $sql .= "INSERT INTO territorios (id,TerritoryID,Description,LocationIndex,Inactive,Parent,User,Status,DateUpdate) VALUES(DEFAULT,";
            $sql .= "{$puntero->TerritoryID},'{$puntero->Description}',{$puntero->LocationIndex},'{$puntero->Inactive}',{$puntero->Parent},";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from territorios')->queryAll();
        Yii::error($count);
    }

    public function empleadosRoles() {
        Yii::error("empleados roles");
        $this->model->actiondir = 'EmployeeRolesSetup';
        $empleadosRoles = $this->model->executex();
        $empleadosRoles = $empleadosRoles->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE empleadosroles;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = '';
        foreach ($empleadosRoles as $puntero) {
            $sql .= "INSERT INTO empleadosroles (id,TypeID,Name,Description,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "{$puntero->TypeID},'{$puntero->Name}','{$puntero->Description}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from empleadosroles')->queryAll();
        Yii::error($count);
    }

    public function empleadosInfo() {
        Yii::error("empleados info");
        
        $this->model->actiondir = 'EmployeesInfo?$select=EmployeeID,LastName,FirstName,MiddleName,EmployeeRolesInfoLines,SalesPersonCode,HomeState,CostCenterCode,WorkStateCode';
        $empleadosInfo = $this->model->executex();
        $empleadosInfo = $empleadosInfo->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE empleadosinfo;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE empleadosrolesinfo;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlSub = "";
        foreach ($empleadosInfo as $puntero) {
            If ($puntero->MiddleName == null) {
                $puntero->MiddleName = " ";
            }
            If ($puntero->HomeState == null) {
                $puntero->HomeState =  $puntero->CostCenterCode;
            }
            If ($puntero->SalesPersonCode!= null) {
               
                $sql .= "INSERT INTO empleadosinfo (id,EmployeeID,LastName,FirstName,MiddleName,SalesPersonCode,HomeState,User,Status,DateUpdate) VALUES (DEFAULT,";

                $sql .= "{$puntero->EmployeeID},
                '{$this->remplaceString($puntero->LastName)}',
                '{$this->remplaceString($puntero->FirstName)}',
                '{$this->remplaceString($puntero->MiddleName)}',
                {$puntero->SalesPersonCode},'{$puntero->WorkStateCode}',";
                $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
                // Yii::error("registro de empleados Info".$sql);
                foreach ($puntero->EmployeeRolesInfoLines as $punteroSub) {
                    $sqlSub .= "INSERT INTO empleadosrolesinfo (id,EmployeeID,LineNum,RoleID,User,Status,DateUpdate) VALUES (DEFAULT,";
                    $sqlSub .= "{$punteroSub->EmployeeID},{$punteroSub->LineNum},{$punteroSub->RoleID},";
                    $sqlSub .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
                }
            }

            
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $db->createCommand($sqlSub)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from empleadosinfo')->queryAll();
        Yii::error($count);
        $count = Yii::$app->db->createCommand('select count(*) from empleadosrolesinfo')->queryAll();
        Yii::error($count);
        
    }

    private function nuevaUnidad() {
        Yii::error("unidad nueva");
        $unidadMedida = Unidadesmedida::find()
                ->where('Code = :unidad', [':unidad' => 'UNIDAD'])
                ->one();
        if (!is_null($unidadMedida)) {
            return $unidadMedida->getAttribute('id');
        }
        $unidadMedidaN = new Unidadesmedida();
        $unidadMedidaN->AbsEntry = Unidadesmedida::find()->count() + 1;
        $unidadMedidaN->Code = 'UNIDAD';
        $unidadMedidaN->Name = 'UNIDAD';
        $unidadMedidaN->User = Yii::$app->user->identity->getId();
        $unidadMedidaN->Status = 1;
        $unidadMedidaN->DateTime = Carbon::today();
        $unidadMedidaN->save(false);
        return $unidadMedidaN->getAttribute('id');
    }

    public function productosGrupo() {
        Yii::error("productos grupo");
        $this->model->actiondir = 'ItemGroups?$select=PriceDifferencesAccount,StockInflationAdjustAccount,ExchangeRateDifferencesAccount,IncreasingAccount,StockInflationOffsetAccount,PurchaseOffsetAccount,WIPMaterialVarianceAccount,PurchaseAccount,ReturningAccount,CostInflationAccount,ExpensesAccount,RevenuesAccount,TransfersAccount,CostInflationOffsetAccount,InventoryAccount,DecreaseGLAccount,Number,GoodsClearingAccount,IncreaseGLAccount,ForeignRevenuesAccount,WIPMaterialAccount,ShippedGoodsAccount,ExemptRevenuesAccount,DecreasingAccount,VATInRevenueAccount,VarianceAccount,EUExpensesAccount,ForeignExpensesAccount,GroupName,NegativeInventoryAdjustmentAccount,WHIncomingCenvatAccount,WHOutgoingCenvatAccount,StockInTransitAccount,WipOffsetProfitAndLossAccount,InventoryOffsetProfitAndLossAccount,PurchaseBalanceAccount';
        $empleadosInfo = $this->model->executex();
        $empleadosInfo = $empleadosInfo->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosgrupo;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        foreach ($empleadosInfo as $puntero) {
            $sql .= "INSERT INTO productosgrupo (id,PriceDifferencesAccount,StockInflationAdjustAccount,ExchangeRateDifferencesAccount,IncreasingAccount,StockInflationOffsetAccount,PurchaseOffsetAccount,WIPMaterialVarianceAccount,PurchaseAccount,ReturningAccount,CostInflationAccount,ExpensesAccount,RevenuesAccount,TransfersAccount,CostInflationOffsetAccount,InventoryAccount,DecreaseGLAccount,Number,GoodsClearingAccount,IncreaseGLAccount,ForeignRevenuesAccount,WIPMaterialAccount,ShippedGoodsAccount,ExemptRevenuesAccount,DecreasingAccount,VATInRevenueAccount,VarianceAccount,EUExpensesAccount,ForeignExpensesAccount,GroupName,NegativeInventoryAdjustmentAccount,WHIncomingCenvatAccount,WHOutgoingCenvatAccount,StockInTransitAccount,WipOffsetProfitAndLossAccount,InventoryOffsetProfitAndLossAccount,PurchaseBalanceAccount,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "'{$puntero->PriceDifferencesAccount}','{$puntero->StockInflationAdjustAccount}','{$puntero->ExchangeRateDifferencesAccount}','{$puntero->IncreasingAccount}','{$puntero->StockInflationOffsetAccount}','{$puntero->PurchaseOffsetAccount}','{$puntero->WIPMaterialVarianceAccount}','{$puntero->PurchaseAccount}','{$puntero->ReturningAccount}','{$puntero->CostInflationAccount}','{$puntero->ExpensesAccount}','{$puntero->RevenuesAccount}','{$puntero->TransfersAccount}','{$puntero->CostInflationOffsetAccount}','{$puntero->InventoryAccount}','{$puntero->DecreaseGLAccount}',{$puntero->Number},'{$puntero->GoodsClearingAccount}','{$puntero->IncreaseGLAccount}','{$puntero->ForeignRevenuesAccount}','{$puntero->WIPMaterialAccount}','{$puntero->ShippedGoodsAccount}','{$puntero->ExemptRevenuesAccount}','{$puntero->DecreasingAccount}','{$puntero->VATInRevenueAccount}','{$puntero->VarianceAccount}','{$puntero->EUExpensesAccount}','{$puntero->ForeignExpensesAccount}','{$puntero->GroupName}','{$puntero->NegativeInventoryAdjustmentAccount}','{$puntero->WHIncomingCenvatAccount}','{$puntero->WHOutgoingCenvatAccount}','{$puntero->StockInTransitAccount}','{$puntero->WipOffsetProfitAndLossAccount}','{$puntero->InventoryOffsetProfitAndLossAccount}','{$puntero->PurchaseBalanceAccount}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from productosgrupo')->queryAll();
        Yii::error($count);
    }

    public function lbcc() {
        Yii::error("lbcc");
        $this->model->actiondir = 'LBCC';
        $lbcc = $this->model->executex();
        $lbcc = $lbcc->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE lbcc;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        foreach ($lbcc as $puntero) {
            $sql .= "INSERT INTO lbcc (id,Code,Name,DocEntry,Canceled,Object,LogInst,UserSign,Transfered,CreateDate,CreateTime,UpdateDate,UpdateTime,DataSource,U_NumeroAutorizacion,U_ObjType,U_Estado,U_PrimerNumero,U_NumeroSiguiente,U_UltimoNumero,U_Series,U_SeriesName,U_FechaLimiteEmision,U_LlaveDosificacion,U_Leyenda,U_Leyenda2,U_TipoDosificacion,U_Sucursal,U_EmpleadoVentas,U_GrupoCliente,U_Actividad,User,Status,DateUpdate) VALUES (DEFAULT,";
            //$sql .= "'{$puntero->Code}','{$puntero->Name}',{$puntero->DocEntry},'{$puntero->Canceled}','{$puntero->Object}','{$puntero->LogInst}',{$puntero->UserSign},'{$puntero->Transfered}','{$puntero->CreateDate}','{$puntero->CreateTime}','{$puntero->UpdateDate}','{$puntero->UpdateTime}','{$puntero->DataSource}','{$puntero->U_NumeroAutorizacion}',{$puntero->U_ObjType},'{$puntero->U_Estado}',{$puntero->U_PrimerNumero},{$puntero->U_NumeroSiguiente},{$puntero->U_UltimoNumero},{$puntero->U_Series},'{$puntero->U_SeriesName}','{$puntero->U_FechaLimiteEmision}','{$puntero->U_LlaveDosificacion}','{$puntero->U_Leyenda}','{$puntero->U_Leyenda2}',";
            $sql .= "'{$puntero->Code}','{$puntero->Name}',{$puntero->DocEntry},'{$puntero->Canceled}','{$puntero->Object}','{$puntero->LogInst}',{$puntero->UserSign},'{$puntero->Transfered}','{$puntero->CreateDate}','{$puntero->CreateTime}','{$puntero->UpdateDate}','{$puntero->UpdateTime}','{$puntero->DataSource}','{$puntero->U_NumeroAutorizacion}',{$puntero->U_ObjType},'{$puntero->U_Estado}',{$puntero->U_PrimerNumero},{$puntero->U_NumeroSiguiente},{$puntero->U_UltimoNumero},{$puntero->U_Series},'{$puntero->U_SeriesName}','{$puntero->U_FechaLimiteEmision}','{$puntero->U_LlaveDosificacion}','{$puntero->U_Leyenda}','',";
            $sql .= "0,"; // $puntero->U_TipoDosificacion ? $puntero->U_TipoDosificacion."," : 0 .",";
            $sql .= "'','',"; //"'{$puntero->U_Sucursal}','{$puntero->U_EmpleadoVentas}',";
            $sql .= "0,"; //$puntero->U_GrupoCliente ? $puntero->U_GrupoCliente.",":0 .",";
            $sql .= "0,"; //$puntero->U_Actividad ? $puntero->U_Actividad."," : 0 .",";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from lbcc')->queryAll();
        Yii::error($count);
    }

    public function descuentosEspeciales() {
        Yii::error("descuentos especiales");
        $this->model->actiondir = 'SpecialPrices';
        $descuentosEspeciales = $this->model->executex();
        $descuentosEspeciales = $descuentosEspeciales->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE descuentosespeciales;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE descuentoscantidad;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE descuentosperiodo;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlSub = "";
        $sqlSubO = "";
        foreach ($descuentosEspeciales as $puntero) {
            $puntero->PriceListNum = $puntero->PriceListNum?$puntero->PriceListNum:0;

            $sql .= "INSERT INTO descuentosespeciales (id,PriceListNum,ItemCode,AutoUpdate,ValidFrom,Currency,Price,DiscountPercent,SourcePrice,CardCode,ValidTo,Valid,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "{$puntero->PriceListNum},'{$puntero->ItemCode}','{$puntero->AutoUpdate}','{$puntero->ValidFrom}','{$puntero->Currency}',{$puntero->Price},{$puntero->DiscountPercent},'{$puntero->SourcePrice}','{$puntero->CardCode}','{$puntero->ValidTo}','{$puntero->Valid}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
            if (count($puntero->SpecialPriceDataAreas)) {
                foreach ($puntero->SpecialPriceDataAreas as $subPuntero) {
                    $sqlSub .= "INSERT INTO descuentosperiodo (id,PriceCurrency,AutoUpdate,Dateto,Discount,SpecialPrice,DateFrom,BPCode,PriceListNo,ItemNo,RowNumber,User,Status,DateUpdate,Valid) VALUES (DEFAULT,";
                    $sqlSub .= "'{$subPuntero->PriceCurrency}','{$subPuntero->AutoUpdate}','{$subPuntero->Dateto}',{$subPuntero->Discount},{$subPuntero->SpecialPrice},'{$subPuntero->DateFrom}','{$subPuntero->BPCode}',{$subPuntero->PriceListNo},'{$subPuntero->ItemNo}',{$subPuntero->RowNumber},";
                    $sqlSub .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "','{$puntero->Valid}');";
                    if (count($subPuntero->SpecialPriceQuantityAreas)) {
                        foreach ($subPuntero->SpecialPriceQuantityAreas as $punteroObjeto) {
                            $sqlSubO .= "INSERT INTO descuentoscantidad (id,Quantity,SPDARowNumber,SpecialPrice,ItemNo,RowNumber,BPCode,PriceCurrency,Discountin,UoMEntry,ListPriceNo,Dateto,Datefrom,User,Status,DateUpdate,Valid) VALUES (DEFAULT,";
                            $sqlSubO .= "{$punteroObjeto->Quantity},{$punteroObjeto->SPDARowNumber},{$punteroObjeto->SpecialPrice},'{$punteroObjeto->ItemNo}',{$punteroObjeto->RowNumber},'{$punteroObjeto->BPCode}','{$punteroObjeto->PriceCurrency}',{$punteroObjeto->Discountin},{$punteroObjeto->UoMEntry},";
                            $sqlSubO .= "{$subPuntero->PriceListNo},'{$subPuntero->Dateto}','{$subPuntero->DateFrom}',";
                            $sqlSubO .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "','{$puntero->Valid}');";
                        }
                    }
                }
            }
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $db->createCommand($sqlSub)->execute();
            $db->createCommand($sqlSubO)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from descuentosespeciales')->queryAll();
        Yii::error($count);
        $count = Yii::$app->db->createCommand('select count(*) from descuentoscantidad')->queryAll();
        Yii::error($count);
        $count = Yii::$app->db->createCommand('select count(*) from descuentosperiodo')->queryAll();
        Yii::error($count);
    }

    public function descuentosGrupo() {
        Yii::error("descuentos de grupo");
        $this->model->actiondir = 'EnhancedDiscountGroups';
        $descuentosEspeciales = $this->model->executex();
        $descuentosEspeciales = $descuentosEspeciales->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE grupodescuentos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE grupodescuentoslinea;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlSub = "";
        foreach ($descuentosEspeciales as $puntero) {
            $validFrom = empty($puntero->ValidFrom) ? '0000-00-00' : $puntero->ValidFrom;
            $validTo = empty($puntero->ValidTo) ? '0000-00-00' : $puntero->ValidTo;
            $sql .= "INSERT INTO grupodescuentos (id,AbsEntry,Type,ObjectCode,DiscountRelations,Active,ValidFrom,ValidTo,User,Status,DateUpdate,Priority) VALUES (DEFAULT,";
            $sql .= "{$puntero->AbsEntry},'{$puntero->Type}','{$puntero->ObjectCode}','{$puntero->DiscountRelations}','{$puntero->Active}','{$validFrom}','{$validTo}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "',";
            if ($puntero->Type == 'dgt_SpecificBP') {
                $sql .= 1;
            } else if ($puntero->Type == 'dgt_VendorGroup') {
                $sql .= 2;
            } else if ($puntero->Type == 'dgt_CustomerGroup') {
                $sql .= 3;
            } else if ($puntero->Type == 'dgt_AllBPs') {
                $sql .= 4;
            }
            $sql .= ");";
            if (count($puntero->DiscountGroupLineCollection)) {
                foreach ($puntero->DiscountGroupLineCollection as $subPuntero) {
                    $sqlSub .= "INSERT INTO grupodescuentoslinea (id,AbsEntry,ObjectType,ObjectCode,DiscountType,Discount,PaidQuantity,FreeQuantity,MaximumFreeQuantity,User,Status,DateUpdate,ObjectTypeClient,ValidFrom,ValidTo) VALUES (DEFAULT,";
                    $sqlSub .= "{$subPuntero->AbsEntry},'{$subPuntero->ObjectType}','{$subPuntero->ObjectCode}','{$subPuntero->DiscountType}',{$subPuntero->Discount},{$subPuntero->PaidQuantity},{$subPuntero->FreeQuantity},{$subPuntero->MaximumFreeQuantity},";
                    $sqlSub .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "','{$puntero->Type}','{$validFrom}','{$validTo}');";
                }
            }
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $db->createCommand($sqlSub)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from grupodescuentos')->queryAll();
        Yii::error($count);
        $count = Yii::$app->db->createCommand('select count(*) from grupodescuentoslinea')->queryAll();
        Yii::error($count);
    }

    private function productoPropiedad($producto) {
       // Yii::error("productos propiedades");
        foreach ($producto as $clave => $valor) {
            if (!is_array($valor)) {
                if (strpos($clave, 'Properties') !== false) {
                    if ($valor == 'tYES') {
                        Yii::error($clave . "=>" . $valor);
                        $propiedad = new Propiedadesproductos();
                        $propiedad->ItemCode = $producto->ItemCode;
                        $propiedad->propiedad = $clave;
                        $propiedad->valor = $valor;
                        $propiedad->User = Yii::$app->user->identity->getId();
                        $propiedad->DateUpdate = Carbon::today();
                        $propiedad->save(false);
                    }
                }
            }
        }
    }

    public function leyendas() {
        Yii::error("leyendas");
        $this->model->actiondir = 'LEYENDA';
        $leyendas = $this->model->executex();
        $leyendas = $leyendas->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE leyendas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlSub = "";
        foreach ($leyendas as $puntero) {
            $sql .= "INSERT INTO leyendas (id,Code,Name,DocEntry,Canceled,Object,LogInst,UserSign,Transfered,CreateDate,CreateTime,UpdateDate,UpdateTime,DataSource,U_Tipo,U_Descripcion,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "'{$puntero->Code}','{$puntero->Name}',{$puntero->DocEntry},'{$puntero->Canceled}','{$puntero->Object}','{$puntero->LogInst}',{$puntero->UserSign},'{$puntero->Transfered}','{$puntero->CreateDate}','{$puntero->CreateTime}','{$puntero->UpdateDate}','{$puntero->UpdateTime}','{$puntero->DataSource}','{$puntero->U_Tipo}','{$puntero->U_Descripcion}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "'";
            $sql .= ");";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from leyendas')->queryAll();
        Yii::error($count);
    }

    public function motivosAnulacion() {
        Yii::error("motivos de anulacion");
        $this->model->actiondir = 'Anulacion';
        $motivosAnulacion = $this->model->executex();
        $motivosAnulacion = $motivosAnulacion->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE motivosanulacion;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        foreach ($motivosAnulacion as $puntero) {
            $sql .= "INSERT INTO motivosanulacion (id,Code,Name,U_TipoAnulacion,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "'{$puntero->Code}','{$puntero->Name}','{$puntero->U_TipoAnulacion}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "'";
            $sql .= ");";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from motivosanulacion')->queryAll();
        Yii::error($count);
    }

    public function objetivoVentas() {
        Yii::error("objetivos de venta");
        $this->model->actiondir = 'ANULACIÓN';
        $motivosAnulacion = $this->model->executex();
        $motivosAnulacion = $motivosAnulacion->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE motivosanulacion;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        foreach ($motivosAnulacion as $puntero) {
            $sql .= "INSERT INTO leyendas (id,name,kind,url,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "'{$puntero->name}','{$puntero->kind}','{$puntero->url}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "'";
            $sql .= ");";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function facturas() {
        Yii::error("facturas");
        $this->model->actiondir = 'Invoices?$filter=DocumentStatus ne \'bost_Close\'';
        $facturas = $this->model->executex();
        $facturas = $facturas->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE facturas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE facturasproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlSub = "";
        foreach ($facturas as $puntero) {
            $sql .= "INSERT INTO facturas (id,DocEntry,DocNum,DocDate,DocDueDate,CardCode,CardName,DocTotal,DocCurrency,JournalMemo,PaymentGroupCode,DocTime,Series,TaxDate,CreationDate,UpdateDate,FinancialPeriod,UpdateTime,U_LB_NumeroFactura,U_LB_NumeroAutorizac,U_LB_FechaLimiteEmis,U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode,ReserveInvoice,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}','{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocCurrency}','{$puntero->JournalMemo}',{$puntero->PaymentGroupCode},'{$puntero->DocTime}',{$puntero->Series},'{$puntero->TaxDate}','{$puntero->CreationDate}','{$puntero->UpdateDate}',{$puntero->FinancialPeriod},'{$puntero->UpdateTime}','{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}','{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}',{$puntero->U_LB_TipoFactura},{$puntero->SalesPersonCode},'{$puntero->ReserveInvoice}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "'";
            $sql .= ");";
            if (count($puntero->DocumentLines)) {
                foreach ($puntero->DocumentLines as $punteroSub) {
                    $sqlSub .= "INSERT INTO facturasproductos (id,LineNum,ItemCode,ItemDescription,Quantity,Price,PriceAfterVAT,Currency,Rate,LineTotal,TaxTotal,UnitPrice,DocEntry,User,Status,DateUpdate) VALUES (DEFAULT,";
                    $sqlSub .= "{$punteroSub->LineNum},'{$punteroSub->ItemCode}','{$punteroSub->ItemDescription}',{$punteroSub->Quantity},{$punteroSub->Price},{$punteroSub->PriceAfterVAT},'{$punteroSub->Currency}',{$punteroSub->Rate},{$punteroSub->LineTotal},{$punteroSub->TaxTotal},{$punteroSub->UnitPrice},{$punteroSub->DocEntry},";
                    $sqlSub .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "'";
                    $sqlSub .= ");";
                }
            }
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $db->createCommand($sqlSub)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function indicadoresImpuestosODBC() {
        Yii::error("indicadores de impuestos");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1403));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE indicadoresimpuestos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            $campos="(Code, Rate, RowNumber, STCCode, STACode, EffectiveRate, User, Status, DateUpdate)";
            $valores = "";
            foreach ($respuesta as $punteroSub) {
                $valores .= "('{$punteroSub->Code}','{$punteroSub->Rate}','{$punteroSub->RowNumber}','{$punteroSub->STCCode}','{$punteroSub->STACode}','{$punteroSub->EffectiveRate}','{$punteroSub->User}','{$punteroSub->Status}','{$punteroSub->DateUpdate}'),";
            }
            $cadena = substr($valores, 0, -1); // quitar comita final
            $sql="INSERT INTO indicadoresimpuestos {$campos}  VALUES {$cadena};";
            Yii::error("SQL =>" . $sql);
            $db = Yii::$app->db;
            $db->createCommand($sql)->execute();

            $count = Yii::$app->db->createCommand('select count(*) from indicadoresimpuestos')->queryAll();
            Yii::error($count);

        } catch (\Exception $e) {
            Yii::error('sincroniza companex canal error', $e);
        }
        
    }

    public function indicadoresImpuestos() {
        Yii::error("indicadores de impuestos");
        $this->model->actiondir = 'SalesTaxCodes';
        $indicadoresImpuestos = $this->model->executex();
        $indicadoresImpuestos = $indicadoresImpuestos->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE indicadoresimpuestos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        foreach ($indicadoresImpuestos as $puntero) {
            foreach ($puntero->SalesTaxCodes_Lines as $punteroSub) {
                $sql .= "INSERT INTO indicadoresimpuestos (id,Code,Rate,RowNumber,STCCode,STACode,EffectiveRate,User,Status,DateUpdate ) VALUES (DEFAULT,";
                $sql .= "'{$puntero->Code}',{$puntero->Rate},{$punteroSub->RowNumber},'{$punteroSub->STCCode}','{$punteroSub->STACode}',{$punteroSub->EffectiveRate},";
                $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "'";
                $sql .= ");";
            }
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from indicadoresimpuestos')->queryAll();
        Yii::error($count);
    }

    public function gruposUMedida() {
        Yii::error("grupos unidades de medida");
        $this->model->actiondir = 'UnitOfMeasurementGroups?$select=AbsEntry,Code,Name,UoMGroupDefinitionCollection';
        $grupoUMedida = $this->model->executex();
        $grupoUMedida = $grupoUMedida->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE grupounidadesmedidas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        foreach ($grupoUMedida as $puntero) {
            foreach ($puntero->UoMGroupDefinitionCollection as $punteroSub) {
                $sql .= "INSERT INTO grupounidadesmedidas (id,AbsEntry,Code,Name,BaseQuantity,User,Status,DateUpdate) VALUES (DEFAULT,";
                $sql .= "{$puntero->AbsEntry},'{$puntero->Code}','{$puntero->Name}',{$punteroSub->BaseQuantity},";
                $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "'";
                $sql .= ");";
            }
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from grupounidadesmedidas')->queryAll();
        Yii::error($count);
        $this->ObtenerRelUnidMedidaGrupo();
    }

    public function combos() {
        Yii::error("combos");
        $this->model->actiondir = 'ProductTrees';
        $combos = $this->model->executex();
        $combos = $combos->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE combos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE combosdetalle;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlD = "";
        $db = Yii::$app->db;
        $aux_q1="Select valor from configuracion where parametro='combos_descuento'";
        $configuracion=$db->createCommand($aux_q1)->queryOne();
        $configuracion=$configuracion['valor'];
        $aux_q2="Select valor from configuracion where parametro='combos_descuento_lista_precios'";
        $listabase=$db->createCommand($aux_q2)->queryOne();
        $listabase=$listabase['valor'];

        foreach ($combos as $puntero) {
            $sql .= "INSERT INTO combos(id,TreeCode,TreeType,Quantity,PriceList,Warehouse,PlanAvgProdSize,HideBOMComponentsInPrintout,ProductDescription,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "'{$puntero->TreeCode}','{$puntero->TreeType}',{$puntero->Quantity},{$puntero->PriceList},'{$puntero->Warehouse}',{$puntero->PlanAvgProdSize},'{$puntero->HideBOMComponentsInPrintout}','{$puntero->ProductDescription}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
            $sum=0;
            $sql_auxpc="Select Price from productosprecios where ItemCode='{$puntero->TreeCode}' and IdListaPrecios={$puntero->PriceList}";
            $preciocombo=$db->createCommand($sql_auxpc)->queryOne();
            $preciocombo=$preciocombo['Price'];
            $codigocombo=$preciocombo['ItemCode'];
            foreach ($puntero->ProductTreeLines as $punteroSub) {
                $sum=($punteroSub->Price * $punteroSub->Quantity)+$sum;
            }
            Yii::error("conf :".$configuracion." total prods combo: ".$sum);
            foreach ($puntero->ProductTreeLines as $punteroSub) {
                
                if($configuracion=="1"){
                    $por=($punteroSub->Price/$sum);
                    $precio=($por*$preciocombo);
                    $descuento=($punteroSub->Price - $precio)*$punteroSub->Quantity;
                    $descuento = round($descuento,2);
                    Yii::error("precio combo :".$preciocombo." total prods combo:(".$codigocombo.") ".$sum. " % ".$por." desc: " . $descuento);
                }else{
                    $descuento=0;
                    $precio=$punteroSub->Price;
                }
                
                $sqlD .= "INSERT INTO combosdetalle (id,ItemCode,Quantity,Warehouse,Price,Currency,IssueMethod,ParentItem,PriceList,ItemType,AdditionalQuantity,ChildNum,VisualOrder,User,Status,DateUpdate,ItemComboPrice,Descuento) VALUES (DEFAULT,";
                $sqlD .= "'{$punteroSub->ItemCode}',{$punteroSub->Quantity},'{$punteroSub->Warehouse}',{$punteroSub->Price},'{$punteroSub->Currency}','{$punteroSub->IssueMethod}','{$punteroSub->ParentItem}',{$punteroSub->PriceList},'{$punteroSub->ItemType}',{$punteroSub->AdditionalQuantity},{$punteroSub->ChildNum},{$punteroSub->VisualOrder},";
                $sqlD .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "','{$precio}','{$descuento}'";
                $sqlD .= ");";
            }
        }        
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $db->createCommand($sqlD)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from combos')->queryAll();
        Yii::error($count);
        $count = Yii::$app->db->createCommand('select count(*) from combosdetalle')->queryAll();
        Yii::error($count);
    }



    public function pagosRecibidos() {
        Yii::error("pagos recibidos");
        $this->model->actiondir = 'IncomingPayments';
        $pagosRecibidos = $this->model->executex();
        $pagosRecibidos = $pagosRecibidos->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE pagosrecibidos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlD = "";
        foreach ($pagosRecibidos as $puntero) {
            $sql .= "INSERT INTO pagosrecibidos(id,DocNum,DocType,HandWritten,Printed,DocDate,CardCode,CardName,Address,DocCurrency,CashSum,TransferSum,JournalRemarks,TaxDate,DocEntry,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "{$puntero->DocNum},'{$puntero->DocType}','{$puntero->HandWritten}','{$puntero->Printed}','{$puntero->DocDate}','{$puntero->CardCode}','{$puntero->CardName}','{$puntero->Address}','{$puntero->DocCurrency}',{$puntero->CashSum},{$puntero->TransferSum},'{$puntero->JournalRemarks}','{$puntero->TaxDate}',{$puntero->DocEntry},";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from pagosrecibidos')->queryAll();
        Yii::error($count);
    }

    public function cuentasContables() {
        Yii::error("cuentas contables");
        $this->model->actiondir = 'ChartOfAccounts?$select=Code,Name,Balance,AccountLevel,FatherAccountKey,AcctCurrency,FormatCode&$filter=ActiveAccount eq \'tYES\'';
        //$this->model->actiondir = 'ChartOfAccounts?$select=Code,Name,Balance,AccountLevel,FatherAccountKey,AcctCurrency,FormatCode&$filter=U_POS_visible ne \'0\'';
        Yii::error("cuentas contables", $this->model->actiondir );
	   $cuentasContables = $this->model->executex();
        $cuentasContables = $cuentasContables->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE cuentascontables;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = "";
        $sqlD = "";
        foreach ($cuentasContables as $puntero) {
            $sql .= "INSERT INTO cuentascontables(id,Code,Name,Balance,AccountLevel,FatherAccountKey,AcctCurrency,FormatCode,User,Status,DateUpdate) VALUES (DEFAULT,";
            $sql .= "'{$puntero->Code}','{$this->remplaceString($puntero->Name)}',{$puntero->Balance},{$puntero->AccountLevel},'{$puntero->FatherAccountKey}','{$puntero->AcctCurrency}','{$puntero->FormatCode}',";
            $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
        }
		 Yii::error("cuentas contables ".$sql );
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from cuentascontables')->queryAll();
        Yii::error($count);
    }
    /*    
    private function remplaceString($string) {
        if (!is_null($string)) {
            $string=str_replace('\'', '`', $string);
            $string=addslashes($string);
            return $string;
        }
        return $string;
    }
    */
    public function industrias() {
        Yii::error("industrias");
        $this->model->actiondir = 'Industries';
        $industrias = $this->model->executex();
        $industrias = $industrias->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE industrias;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql = '';
        foreach ($industrias as $puntero) {
			$sql .= "INSERT INTO industrias (id,nombre,Descripcion,User,Status,DateUpdate) VALUES(";
            $sql .= "{$puntero->IndustryCode},'{$puntero->IndustryName}','{$puntero->IndustryDescription}',1,1,'{$fecha}');";
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        $count = Yii::$app->db->createCommand('select count(*) from industrias')->queryAll();
        Yii::error($count);
    }

    public function seriesProductos() {
        Yii::error("series productos");
        $this->Series();
        $this->model->actiondir = 'SerialNumberDetails';
        $seriesx = $this->model->executex();
        $series = $seriesx->value;
        if($series){
            $sqlt = 'SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE seriesproductos;SET FOREIGN_KEY_CHECKS = 1;';
            Yii::$app->db->createCommand($sqlt)->execute();
    
            $sql = ' INSERT INTO seriesproductos VALUES';
            $fecha = Carbon::today();
            foreach ($series as $val)
                $sql .= " (NULL,'{$val->DocEntry}','{$val->ItemCode}','{$val->SerialNumber}','{$val->SystemNumber}','{$val->AdmissionDate}',1,1,'{$fecha}','0'),";
            $sqlx = substr($sql, 0, -1);
            $sqlx . ";";
            return Yii::$app->db->createCommand($sqlx)->execute();

        }
       
    }

    //  return print_r($series);

    /* Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE seriesproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
      $sql = '';
      foreach ($series as $puntero) {
      $sql .= "INSERT INTO seriesproductos (DocEntry,ItemCode,SerialNumber,SystemNumber,AdmissionDate,User,Status,Date) VALUES(";
      $sql .= "{$puntero->DocEntry},'{$puntero->ItemCode}','{$puntero->SerialNumber}',{$puntero->SystemNumber},'{$puntero->AdmissionDate}',";
      $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
      }
      $db = Yii::$app->db;
      $transaction = $db->beginTransaction();
      try {
      $db->createCommand($sql)->execute();
      $transaction->commit();
      } catch (\Exception $e) {
      $transaction->rollBack();
      throw $e;
      } catch (\Throwable $e) {
      $transaction->rollBack();
      throw $e;
      }
      $count = Yii::$app->db->createCommand('select count(*) from industrias')->queryAll();
      Yii::error($count); */
    public function Series() {
        Yii::error("series");
        Yii::error('Serie Sincronizacion RN: ');
        $series = array();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE series;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $sql_hana='SELECT * from "NNM1" ';
        $hana=New hana;
        $respuesta=$hana->ejecutarconsultaAll($sql_hana);
        //$this->model->actiondir = 'SeriesService_GetSeries';
        foreach($respuesta as $response){
        //for ($i = 1; $i<=1000; $i++) 
           // $response = $this->model->executePost(array("SeriesParams" => array("Series" => strval($i))));
           // if ($response) {
                $serie = new Series();
                $serie->id = 0;
                $serie->Document = $response["ObjectCode"];
                $serie->DocumentSubType = $response["DocSubType"];
                $serie->InitialNumber = $response["InitialNum"];
                $serie->LastNumber = $response["LastNum"];
                $serie->NextNumber = $response["NextNumber"];
                $serie->Prefix = $response["BeginStr"];
                $serie->Suffix = $response["EndStr"];
                $serie->Remarks = $response["Remark"];
                $serie->GroupCode = $response["GroupCode"];
                $serie->Locked = $response["Locked"];
                $serie->PeriodIndicator = $response["Indicator"];
                $serie->Name = $response["SeriesName"];
                $serie->Series = $response["Series"];
                $serie->IsDigitalSeries = $response["IsDigSerie"];
                $serie->DigitNumber = isset($response["DigitNumber"])?$response["DigitNumber"]:null;
                $serie->SeriesType = isset($response["SeriesType"]) ? $response["SeriesType"] : null;
                $serie->IsManual = $response["IsManual"];
                $serie->BPLID = $response["BPLId"];
                $serie->ATDocumentType = $response["AtDocType"];
                $serie->IsElectronicCommEnabled = isset($response["IsElAuth"])?$response["IsElAuth"]:null;
                $serie->CostAccountOnly = $response["CoAccount"];
                $serie->save();
            //}
            /*else {
                if (!($this->model->executePost(array("SeriesParams" => array("Series" => strval($i + 1)))))) {
                    break;
                }
            }*/
        }
    }

    public function empresa() {
        Yii::error("empresa");
        $hoy = date("Y/m/d");
        //$this->ObtenerRelUnidMedidaGrupo();
        //$this->CargaFexCufd($fecha);
        //$this->obtenerNroFactutas();
        
        $this->model->actiondir = 'CompanyService_GetAdminInfo';
        Yii::error("AAR-POR EJECUTAR");
        $datos=array("datos"=>1);
        $enterprise = $this->model->executePost($datos);
		Yii::error("AAR-EJECUTO");
        if (isset($enterprise)) {
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE empresa;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        }
        $sql = "";
        $phone1 = isset($enterprise->PhoneNumber1) ? $enterprise->PhoneNumber1 : 0;
        $phone2 = isset($enterprise->PhoneNumber2) ? $enterprise->PhoneNumber2 : 0;
        $pais = $this->obtenerPais($enterprise->Country);
        $ciudad = $this->obtenerCiudad($enterprise->State);
        $actividad = 'Actividad';
        $sql .= "INSERT INTO empresa (id,nombre,direccion,telefono1,telefono2,nit,pais,ciudad,actividad,usuario,Status,DateUpdate) VALUES (DEFAULT,";
        $sql .= "'{$enterprise->CompanyName}','{$this->escapeJsonString($enterprise->Address)}','{$phone1}','{$phone2}','{$enterprise->FederalTaxID}','{$pais}','{$ciudad}','{$actividad}',";
        $sql .= Yii::$app->user->identity->getId() . ",1,'" . Carbon::today() . "');";
        Yii::error("SQL.-" . $sql);
        $db = Yii::$app->db;
        $db->createCommand($sql)->execute();
        /*
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        */
        $count = Yii::$app->db->createCommand('select count(*) from lbcc')->queryAll();
        Yii::error($count);
    }

    private function escapeJsonString($value) {
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

    private function obtenerPais($pais) {
        $resultado = '';
        switch ($pais) {
            case 'BO': $resultado = 'BOLIVIA';
                break;
            default: $resultado = '';
                break;
        }
        return $resultado;
    }

    private function obtenerCiudad($ciudad) {
        $resultado = '';
        switch ($ciudad) {
            case 'CB': $resultado = 'COCHABAMBA';
                break;
            case 'SC': $resultado = 'SANTA CRUZ';
                break;
            case 'LP': $resultado = 'LA PAZ';
                break;
            default: $resultado = '';
                break;
        }
        return $resultado;
    }
    /**!**/

    // actualizacion de datos ODBC
    private function obtenerClientesODBC(){
        Yii::error("SINCRONIZA ODBC clientes : ");
        
        try {
            $texto = '';
            $insert = '';
            $std='';
            $sql = "SELECT * FROM configuracion WHERE parametro LIKE 'cliente_std%' AND estado=1 ORDER BY parametro";
            $parametrosClientes = Yii::$app->db->createCommand($sql)->queryAll();
            $cantidadCliente = count($parametrosClientes);

            if (count($cantidadCliente)){
                for ($c = 0; $c < $cantidadCliente; $c++){
                        $insert .= ','.$parametrosClientes[$c]["parametro"];
                        $std .= ',"'.$parametrosClientes[$c]["valor2"].'"';
                }
            }


            $serviceLayer = new Sincronizar();
            
            $data = json_encode(array("accion" => 53));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("SINCRONIZA ODBC clientes cantidad: ".$respuesta);
            $respuesta = json_decode($respuesta);
            
            $contador=$respuesta[0]->CANTIDAD;
            Yii::error("SINCRONIZA ODBC clientes cantidad contador: ".$contador);
            $campos='CardCode,CardName,CardType,Address,CreditLimit,MaxCommitment,DiscountPercent,PriceListNum,SalesPersonCode,Currency,County,Country,CurrentAccountBalance,NoDiscounts,PriceMode,FederalTaxId,PhoneNumber,ContactPerson,PayTermsGrpCode,Latitude,Longitude,GroupCode,User,Status,DateUpdate,GroupName,U_XM_DosificacionSocio,Territory,DiscountRelations,Mobilecod,StatusSend,CardForeignName,Phone2,Cellular,EmailAddress,MailAdress,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,FreeText,img,Industry,codecanal,codesubcanal,codetipotienda,cadena,cadenatxt,activo'.$insert;
           
            for($reg= 0; $reg < $contador; $reg+=1000){
                
                $data = json_encode(array("accion" => 50, "std"=>$std,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);               
                $respuesta = json_decode($respuesta);
                Yii::error("RESPUESTA: ");                             
                $valores="";
                foreach ($respuesta as $p) { 
                   //Yii::error("SINCRONIZA ODBC cliente ======>: ".json_encode($p));
                    if($p->CardType=='C'){
                       // Yii::error("ADRESS77: ".$p->Address);
                        $texto="";
                        $xCardCode = $p->CardCode;
                        $xCardName = $this->remplaceString($p->CardName);
                        $xAddress = $this->remplaceString($p->Address);
                        $xCreditLimit = $p->CreditLimit;
                        $xMaxCommitment = $p->MaxCommitment;
                        $xDiscountPercent = $p->DiscountPercent;
                        $xPriceListNum = $p->PriceListNum;
                        $xSalesPersonCode = $p->SalesPersonCode;
                        $xCurrency = $p->Currency;
                        $xCounty = $p->County;
                        $xCountry = $this->remplaceString($p->Country);
                        $xCurrentAccountBalance = $p->CurrentAccountBalance;
                        $xNoDiscounts = $p->NoDiscounts;
                        $xFederalTaxId = $p->FederalTaxID;
                        $xPhoneNumber = $p->Phone1;
                        $xContactPerson = $p->ContactPerson;
                        $xPayTermsGrpCode = $p->PayTermsGrpCode;
                        $xLatitude = $p->U_XM_Latitud;
                        $xLongitude = $p->U_XM_Longitud;
                        $xMobilecod = 0;
                        $xGroupCode = $p->GroupCode;
                        $xUser = 1;
                        $xStatus = 3;
                        $xDateUpdate = date('Y-m-d');
                        $xU_XM_DosificacionSocio = 0;
                        $xTerritory = $p->Territory;
                        $xCardType = $p->CardType;
                        $xDiscountRelations = $p->DiscountRelations;
                        $xCardForeignName = $this->remplaceString($p->CardForeignName);
                        $xPhone2 = $p->Phone2;
                        $xCellular = $p->Cellular;
                        $xEmailAddress = $p->EmailAddress;
                        $xMailAdress = $this->remplaceString($p->MailAddress);
                        $xProperties1 = $this->cambiaFormato($p->Properties1);
                        $xProperties2 = $this->cambiaFormato($p->Properties2);
                        $xProperties3 = $this->cambiaFormato($p->Properties3);
                        $xProperties4 = $this->cambiaFormato($p->Properties4);
                        $xProperties5 = $this->cambiaFormato($p->Properties5);
                        $xProperties6 = $this->cambiaFormato($p->Properties6);
                        $xProperties7 = $this->cambiaFormato($p->Properties7);
                        $xFreeText = $this->remplaceString($p->FreeText);
                        $xIndustry = $this->remplaceString($p->Industry);
                        $xcanal=$p->U_XM_Canal;
                        $xsubcanal=$p->U_XM_Subcanal;
                        $xtipotienda=$p->U_XM_TipoTienda;
                        $xcadena=$p->U_XM_Cadena;
                        $xcadenadesc=$p->U_XM_CadenaDesc;
                        $xactivo=$p->activo;
                        if (count($parametrosClientes)){
                            for ($c = 0; $c < $cantidadCliente; $c++){
                                $campoNombre = $parametrosClientes[$c]["parametro"];        
                                $campoValor = $parametrosClientes[$c]["valor2"];
                                $texto .=",'".$p->$campoValor."'";
                            }
                        }
                        
                        $valores.="('{$xCardCode}','{$xCardName}','{$xCardType}','{$xAddress}','{$xCreditLimit}','{$xMaxCommitment}','{$xDiscountPercent}','{$xPriceListNum}','{$xSalesPersonCode}','{$xCurrency}','{$xCounty}','{$xCountry}','{$xCurrentAccountBalance}','{$xNoDiscounts}','{$xPriceMode}','{$xFederalTaxId}','{$xPhoneNumber}','{$xContactPerson}','{$xPayTermsGrpCode}','{$xLatitude}','{$xLongitude}','{$xGroupCode}','{$xUser}','{$xStatus}','{$xDateUpdate}','{$xGroupName}','{$xU_XM_DosificacionSocio}','{$xTerritory}','{$xDiscountRelations}','{$xMobilecod}','{$xStatusSend}','{$xCardForeignName}','{$xPhone2}','{$xCellular}','{$xEmailAddress}','{$xMailAdress}','{$xProperties1}','{$xProperties2}','{$xProperties3}','{$xProperties4}','{$xProperties5}','{$xProperties6}','{$xProperties7}','{$xFreeText}','{$ximg}','{$xIndustry}','{$xcanal}','{$xsubcanal}','{$xtipotienda}','{$xcadena}','{$xcadenadesc}','{$xactivo}'".$texto;
                        $valores.="),";
                    }
                    
                }
                $cadena = substr($valores, 0, -1);
                $sql="INSERT INTO sinc_clientes ({$campos} ) VALUES {$cadena};";
                $db = Yii::$app->db;
                //Yii::error("SINCRONIZA ODBC clientes query: ".json_encode($sql));
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_clientes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $db->createCommand($sql)->execute();
                
             //gc_collect_cycles();
            }
       
            Yii::$app->db->createCommand('CALL pa_sincronizarClientes()')->execute();
 
        } catch (\Exception $e) {
           Yii::error("Error en sincronizacion de clientes: ",$e);
        }
     
        $this->obtenerClientesContactosODBC();
        $this->obtenerClientesDirecODBC();
        
       
      
        

    }
    private function obtenerClientesContactosODBC(){
        Yii::error("SINCRONIZA ODBC clientesContactos : ");
        try{
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 55));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);            
            $contador=$respuesta[0]->CANTIDAD;
            Yii::error("SINCRONIZA ODBC clientes contactos cantidad: ".$contador);
            $campos='InternalCode,cardCode,nombre,direccion,telefono1,telefono2,celular,tipo,comentarios,User,Status,DateUpdate,correo,titulo,idsap';
            for($reg= 0; $reg < $contador; $reg+=1000){
                $data = json_encode(array("accion" => 51,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data); 
                $respuesta = json_decode($respuesta);
                $valores="";
                foreach ($respuesta as $con) {                    
                    $xnombre =  $this->remplaceString($con->Name);
                    $xcardCode =  $this->remplaceString($con->CardCode);
                    $xdireccion =  $this->remplaceString($con->Address);
                    $xtelefono1 = $con->Phone1;
                    $xtelefono2 = $con->Phone1;
                    $xcelular = $con->MobilePhone;
                    $xtipo = $con->Position;
                    $xcomentarios = $con->Comment;
                    $xUser = 1;
                    $xStatus = 1;
                    $xDateUpdate = date('Y-m-d');
                    $xcorreo=$con->Mail;
                    $xtitulo=$con->Title;
                    $xicsap=$con->Phone1->CntctCode;
                    $xInternalCode=$con->InternalCode;
                    $valores.="('{$xInternalCode}','{$xcardCode}','{$xnombre}','{$xdireccion}','{$xtelefono1}','{$xtelefono2}','{$xcelular}','{$xtipo}','{$xcomentarios}','{$xUser}','{$xStatus}','{$xDateUpdate}','{$xcorreo}','{$xtitulo}','{$xicsap}'";
                    $valores.="),";
                }
                $cadena = substr($valores, 0, -1);
                $sql="INSERT INTO sinc_contactos ({$campos} ) VALUES {$cadena};";
                $db = Yii::$app->db;
                //Yii::error("SINCRONIZA ODBC clientes contactos query: ".json_encode($sql));
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_contactos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $db->createCommand($sql)->execute();

            }
        }catch(\Exception $e){
            Yii::error("Error en sincronizacion de clientesSincContactos: ",$e);
        } 
        Yii::$app->db->createCommand('CALL pa_sincronizarSincContactos()')->execute();     
    }
    //funcion "OBTENER CLIENTES SUCURSALES"
    private function obtenerClientesDirecODBC(){
        Yii::error("SINCRONIZANDO SUCURSAL  CLIENTES");
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 54));
        $respuesta = $serviceLayer->executex($data);
        $respuesta = json_decode($respuesta);
        $contador=$respuesta[0]->CANTIDAD;
        Yii::error("Direcciones de clientes contador: ".$contador);
        $campos='RowNum,AdresType,u_lat,u_lon,u_territorio, AddresName,Street,State,FederalTaxId,CreditLimit,CardCode,User,Status,DateUpdate,TaxCode';
        
        for($reg= 0; $reg < $contador; $reg+=1000){
            $data = json_encode(array("accion" => 52,"salto"=>$reg));
            $respuesta = $serviceLayer->executex($data);
            Yii::error($respuesta);          
            $respuesta = json_decode($respuesta);
            // Yii::error("Respuesta 77");   
            // Yii::error($respuesta);       

            $valores="";           

            foreach ($respuesta as $val) {
                
            //    if($val->TERRITORIO=="Territorio A"){
            //     $val->TERRITORIO=1;
            //    } else if($val->TERRITORIO=="Territorio B"){
            //     $val->TERRITORIO=2;
            //    }else{
            //     $val->TERRITORIO=-2;
            //    }
                if($val->Street==""){
                    $xStreet = $this->remplaceString($val->Block);
                }
                else{
                    $xStreet = $this->remplaceString($val->Street);
                }
                
                $xAddresName = $this->remplaceString($val->AddressName);
                //$xStreet = $this->remplaceString($val->Street);
                $xState = $val->State;
                $xFederalTaxId = $val->FederalTaxID;
                $xCreditLimit = 0;
                $xTaxCode = $val->TaxCode;
                $xUser = 1;
                $xStatus = 1;
                $xDateUpdate = date('Y-m-d');
                $xCardCode = $val->CardCode;
                //$xU_EXX_FE_ZonaDesp =$val->U_EXX_FE_ZonaDesp;
                //xU_EXX_Flete =$val->U_EXX_Flete;
                $xAddressType =$val->AdresType;
                $xzona =$val->U_ZONA;
                $xlat =$val->U_LAT;
                $xlon =$val->U_LON;
                $xterritorio =$val->TERRITORIO;
                $xvendedor =$val->VENDEDOR;
                //$xterritorio ="2";
               $valores.="('{$val->LineNum}','{$val->AdresType}','{$val->U_XM_Latitud}','{$val->U_XM_Longitud}','{$val->U_Territorio}','{$xAddresName}','{$xStreet}','{$xState}','{$xFederalTaxId}','{$xCreditLimit}','{$xCardCode}','{$xUser}','{$xStatus}','{$xDateUpdate}','{$xTaxCode}'";
               $valores.="),";
             
            }
            
            $cadena = substr($valores, 0, -1);
            $sql="INSERT INTO sinc_clientessucursales ({$campos} ) VALUES {$cadena};";
            //Yii::error($sql);

            $db = Yii::$app->db;
            //Yii::error("SINCRONIZA ODBC clientes sucursales  contactos query: ".json_encode($sql));
            if($reg==0)
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_clientessucursales;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
            $db->createCommand($sql)->execute(); 
            
        }
        
        Yii::$app->db->createCommand('CALL pa_sincronizarClientesSucursales()')->execute();
    }
    
    public function obtenerProductosODBC(){
        Yii::error("SINCRONIZA ODBC productos : ");

        try {
            $texto = '';
            $insert = '';
            $std='';
            $sql = "SELECT * FROM configuracion WHERE parametro LIKE 'producto_std%' AND estado=1 ORDER BY parametro";
            $parametrosProductos = Yii::$app->db->createCommand($sql)->queryAll();
            $cantidadParametrosProductos = count($parametrosProductos);

            if (count($cantidadParametrosProductos)){
                for ($c = 0; $c < $cantidadParametrosProductos; $c++){
                        $insert .= ','.$parametrosProductos[$c]["parametro"];
                        $std .= ',"'.$parametrosProductos[$c]["valor2"].'"';
                }
            }

            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 41));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador=$respuesta[0]->CANTIDAD;
            Yii::error("SINCRONIZA ODBC PRODUCTOS cantidad: ".$contador . " campos STD: " . $std . " de " . $insert);
            
            $campos='ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,User,Status,DateUpdate,Manufacturer,NoDiscounts,created_at,updated_at,combo,SalesPersonCode,UserText'.$insert;
            
            for($reg= 0; $reg < $contador; $reg+=1000){
                $data = json_encode(array("accion" => 42, "std"=>$std,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);
              //  Yii::error("Respuesta JSON: " . $respuesta);
                $respuesta = json_decode($respuesta);
                $valores="";

                foreach ($respuesta as $producto) {
                    if ($producto->QuantityOnStock >= 0) {
                        //Yii::error("---> Item VÁLIDO: " . json_encode($producto));
                        if($producto->combo=="N"){
                            $producto->combo=0;
                        }else{
                            $producto->combo=1;
                        }
                        if($producto->ManageSerialNumbers=="N"){
                            $producto->ManageSerialNumbers=0;
                        }else{
                            $producto->ManageSerialNumbers=1;
                        }
                        if($producto->ManageBatchNumbers=="N"){
                            $producto->ManageBatchNumbers=0;
                        }else{
                            $producto->ManageBatchNumbers=1;
                        }
                        $texto="";
                        $xItemCode = $producto->ItemCode;
                        $xItemName = $this->remplaceString($producto->ItemName);
                        $xItemsGroupCode = $producto->ItemsGroupCode;
                        $xForeignName = $this->remplaceString($producto->ForeignName);
                        $xCustomsGroupCode = $producto->CustomsGroupCode;
                        $xBarCode = $producto->BarCode;
                        $xPurchaseItem = $producto->PurchaseItem;
                        $xSalesItem = $producto->SalesItem;
                        $xInventoryItem = $producto->InventoryItem;
                        $xSerialNum = $producto->SerialNum;
                        $xQuantityOnStock = $producto->QuantityOnStock;
                        $xQuantityOrderedFromVendors = $producto->QuantityOrderedFromVendors;
                        $xQuantityOrderedByCustomers = $producto->QuantityOrderedByCustomers;
                        $xManageSerialNumbers = $producto->ManageSerialNumbers;
                        $xManageBatchNumbers = $producto->ManageBatchNumbers;
                        $xSalesUnit = $producto->SalesUnit;
                        $xSalesUnitLength = $producto->SalesUnitLength;
                        $xSalesUnitWidth = $producto->SalesUnitWidth;
                        $xSalesUnitHeight = $producto->SalesUnitHeight;
                        $xSalesUnitVolume = $producto->SalesUnitVolume;
                        $xPurchaseUnit = $producto->PurchaseUnit;
                        $xDefaultWarehouse = $producto->DefaultWarehouse;
                        $xManageStockByWarehouse = $producto->ManageStockByWarehouse;
                        $xForceSelectionOfSerialNumber = $producto->ForceSelectionOfSerialNumber;
                        $xSeries = $producto->Series;
                        $xUoMGroupEntry = $producto->UoMGroupEntry;
                        $xDefaultSalesUoMEntry = $producto->DefaultSalesUoMEntry;
                        $xUser = $producto->User;
                        $xStatus = $producto->Status;
                        $xDateUpdate = $producto->DateUpdate;
                        $xManufacturer = $producto->Manufacturer;
                        $xNoDiscounts = $producto->NoDiscounts;
                        $xcreated_at = $producto->created_at;
                        $xupdated_at = $producto->updated_at;
                        $xcombo = $producto->combo;
                        $xSalesPersonCode = $producto->SalesPersonCode;
                        $xUserText = $producto->UserText;
                        if (count($parametrosProductos)){
                            for ($c = 0; $c < $cantidadParametrosProductos; $c++){
                                $campoNombre = $parametrosProductos[$c]["parametro"];       
                                $campoValor = $parametrosProductos[$c]["valor2"];
                                $texto .=",'".$producto->$campoValor."'";
                            }
                        }
                        $queryValores = "";
                        $queryValores = "('{$xItemCode}','{$xItemName}','{$xItemsGroupCode}','{$xForeignName}','{$xCustomsGroupCode}','{$xBarCode}','{$xPurchaseItem}','{$xSalesItem}','{$xInventoryItem}','{$xSerialNum}','{$xQuantityOnStock}','{$xQuantityOrderedFromVendors}','{$xQuantityOrderedByCustomers}','{$xManageSerialNumbers}','{$xManageBatchNumbers}','{$xSalesUnit}','{$xSalesUnitLength}','{$xSalesUnitWidth}','{$xSalesUnitHeight}','{$xSalesUnitVolume}','{$xPurchaseUnit}','{$xDefaultWarehouse}','{$xManageStockByWarehouse}','{$xForceSelectionOfSerialNumber}','{$xSeries}','{$xUoMGroupEntry}','{$xDefaultSalesUoMEntry}','{$xUser}','{$xStatus}','{$xDateUpdate}','{$xManufacturer}','{$xNoDiscounts}','{$xcreated_at}','{$xupdated_at}','{$xcombo}','{$xSalesPersonCode}','{$xUserText}'".$texto;
                        $queryValores .= "),";
                        $valores .= $queryValores;
                    }
                }
                $cadena = substr($valores, 0, -1); // quitar comita final
                $sql="INSERT INTO sinc_productos ({$campos} ) VALUES {$cadena};";
               //Yii::error("SQL =>" . $sql);
                $db = Yii::$app->db;
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_productos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $resultQuery = $db->createCommand($sql)->execute();
                //Yii::error("RESULT-SQL =>" . $resultQuery);                
            }
            //$this->obtenerProductosAlmacenesODBC();
            
        } catch (\Exception $e) {
            Yii::error("Error en sincronizacion de productos x ODBC: ",$e);
        }
        Yii::$app->db->createCommand('SELECT func_actualizacionProductos();')->execute();
       // Yii::$app->db->createCommand('CALL pa_sincronizarProductos()')->execute();
    }
    public function obtenerProductosAlmacenesODBC(){
        Yii::error("SINCRONIZA ODBC productos ALMACENES: ");
        $sqlError = '';
        try {
            $serviceLayer = new Sap();
            $data = json_encode(array("accion" => 24));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador=$respuesta[0]->CANTIDAD;
            Yii::error("SINCRONIZA ODBC PRODUCTOS ALMACENES cantidad: ".$contador);
            
            $campos='ItemCode,WarehouseCode,InStock,Committed,Locked,Ordered,User,Status,DateUpdate';

            for($reg= 0; $reg < $contador; $reg+=1000){
                $data = json_encode(array("accion" => 21, "limite"=>1000,"inicio"=>$reg));
                $respuesta = $serviceLayer->executex($data);             
                //Yii::error("SINCRONIZA ODBC PRODUCTOS ALMACENES contador: ".$reg);
                //Yii::error("SINCRONIZA ODBC PRODUCTOS ALMACENES respuesta: ".$respuesta);
                $respuesta = json_decode($respuesta);
                $valores = "";
                $fecha = date('Y-m-d');
                //Yii::error("SINCRONIZA ODBC PRODUCTOS ALMACENES contador: ".$reg);
                foreach ($respuesta as $productowharehose) {
                    
                    //if ((int)($productowharehose->OnHand) > 0) {
                        $valores .= "('{$productowharehose->ItemCode}','{$productowharehose->WhsCode}','{$productowharehose->OnHand}','{$productowharehose->IsCommited}','{$productowharehose->Locked}','{$productowharehose->OnOrder}','1','1','{$fecha}'),";
                    //}
                }
                $cadena = substr($valores, 0, -1); // quitar comita final
				//Yii::error("SINCRONIZA ODBC PRODUCTOS ALMACENES sql: ".$cadena);
                $sql="INSERT INTO sinc_productosalmacenes ({$campos} ) VALUES {$cadena};";
                //Yii::error("SINCRONIZA ODBC PRODUCTOS ALMACENES sql: ".$sql);
                $sqlError = $sql;
                $db = Yii::$app->db;
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_productosalmacenes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $resultQuery = $db->createCommand($sql)->execute();
            }
        } catch (\Exception $th) {
            Yii::error("Error en sincronizacion de Productos Almacenes x ODBC: ",$th);
            Yii::error($sqlError);
        }
        Yii::$app->db->createCommand('SELECT func_actualizacionAlmacenes();')->execute();
        //$this->obtenerProductosPreciosODBC();
            
    }

    public function obtenerProductosPreciosODBC(){
        Yii::error("SINCRONIZA ODBC productos PRECIOS: ");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 28));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("SINCRONIZA ODBC PRODUCTOS PRECIOS respuesta: ".json_encode($respuesta));
            $respuesta = json_decode($respuesta);
            $contador=$respuesta[0]->CANTIDAD;
            $fecha = date('Y-m-d');
            Yii::error("SINCRONIZA ODBC PRODUCTOS PRECIOS cantidad: ".$contador);
            
            $campos='ItemCode,IdListaPrecios,IdUnidadMedida,Price,Currency,User,Status,DateUpdate,PriceListName,codigo,Name,BaseQty';

            for($reg= 0; $reg < $contador; $reg+=1000){   
                Yii::error("SINCRONIZA ODBC PRODUCTOS PRECIOS EACH: ".$reg);
                $data = json_encode(array("accion" => 22, "limite"=>1000,"inicio"=>$reg));
                $respuesta = $serviceLayer->executex($data);  
                Yii::error("SINCRONIZA ODBC PRODUCTOS PRECIOS EACH: ".$respuesta);             
                $respuesta = json_decode($respuesta);
                $valores="";

                foreach ($respuesta as $productoPrecio) {
                    $valores .= "('{$productoPrecio->ItemCode}','{$productoPrecio->PriceList}','{$productoPrecio->UomEntry}','{$productoPrecio->Price}','{$productoPrecio->Currency}','1','1','{$fecha}','{$productoPrecio->ListName}','{$productoPrecio->UomCode}','{$productoPrecio->UomName}','{$productoPrecio->BaseQty}'),";
                    if (((float)$productoPrecio->AddPrice1 > 0) && isset($productoPrecio->Currency1)) {
                        $valores .= "('{$productoPrecio->ItemCode}','{$productoPrecio->PriceList}','{$productoPrecio->UomEntry}','{$productoPrecio->AddPrice1}','{$productoPrecio->Currency1}','1','1','{$fecha}','{$productoPrecio->ListName}','{$productoPrecio->UomCode}','{$productoPrecio->UomName}','{$productoPrecio->BaseQty}'),";
                        // se tiene precios de producto adicional a la lista de precios
                        // Yii::error("1 ---->" . $productoPrecio->ItemCode . " - " . $productoPrecio->AddPrice1 );
                    }
                    if (((float)$productoPrecio->AddPrice2 > 0) && isset($productoPrecio->Currency2)) {
                        $valores .= "('{$productoPrecio->ItemCode}','{$productoPrecio->PriceList}','{$productoPrecio->UomEntry}','{$productoPrecio->AddPrice2}','{$productoPrecio->Currency2}','1','1','{$fecha}','{$productoPrecio->ListName}','{$productoPrecio->UomCode}','{$productoPrecio->UomName}','{$productoPrecio->BaseQty}'),";
                        // Yii::error("2 ---->" . $productoPrecio->ItemCode . " - " . $productoPrecio->AddPrice2 );
                    }
                     /*
                    $item = Productos::find()
                        ->where(['ItemCode' => $productoPrecio->ItemCode])
                        ->one();
                    if ((float)$productoPrecio->Price > 0 && isset($item)) {
                        $valores .= "('{$productoPrecio->ItemCode}','{$productoPrecio->PriceList}','{$productoPrecio->UomEntry}','{$productoPrecio->Price}','{$productoPrecio->Currency}','1','1','{$fecha}'),";
                        // Yii::error("---->" . "('{$productoPrecio->ItemCode}','{$productoPrecio->PriceList}','{$productoPrecio->UomEntry}','{$productoPrecio->Price}','{$productoPrecio->Currency}','1','1','{$fecha}'),");
                        
                    }*/
                }
                $cadena = substr($valores, 0, -1); // quitar comita final
                if( $cadena !=""){
                    $sql="INSERT INTO sinc_productosprecios ({$campos} ) VALUES {$cadena};";
                    //Yii::error("SQL =>" . $sql);
                    $db = Yii::$app->db;
                    if($reg==0)
                        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_productosprecios;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                    $resultQuery = $db->createCommand($sql)->execute();
                }else{
                    Yii::error("SQL   CON ERROR =>" );
                }

            }


        } catch (\Exception $e) {
            Yii::error("Error en sincronizacion de Productos Almacenes x ODBC: ",$e);
        }
      Yii::$app->db->createCommand('SELECT func_actualizacionPrecios();')->execute();
      //Yii::$app->db->createCommand('CALL pa_sincronizarProductos()')->execute();
      //$this->combos();
      //$this->obtenerProductosLotesODBC();
      //$this->obtenerProductosSeriesODBC();
    }

    public function obtenerProductosSeriesODBC(){
        Yii::error("SINCRONIZA ODBC productos SERIES: ");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 33));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador=$respuesta[0]->CANTIDAD;
            $fecha = date('Y-m-d');
            Yii::error("SINCRONIZA ODBC PRODUCTOS SERIES cantidad: ".$contador);
            $campos='(DocEntry,ItemCode,SerialNumber,SystemNumber,AdmissionDate,User,Status,Date,WsCode)';
            for($reg= 0; $reg < $contador; $reg+=1000){
                $data = json_encode(array("accion" => 32, "limite"=>1000,"inicio"=>$reg));
                $respuesta = $serviceLayer->executex($data);               
                $respuesta = json_decode($respuesta);
                $valores="";

                foreach ($respuesta as $serie) {
                    // Yii::error("--------> " . json_encode($serie));
                   
                        $valores .= "('{$serie->AbsEntry}','{$serie->ItemCode}','{$serie->DistNumber}','{$serie->SysNumber}','{$serie->InDate}','{$serie->UserSign}',1,'{$fecha}','0'),";
                   
                }
                $cadena = substr($valores, 0, -1); // quitar comita final
                $sql="INSERT INTO sinc_seriesproductos {$campos}  VALUES {$cadena};";
                // Yii::error("SQL =>" . $sql);
                $db = Yii::$app->db;
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_seriesproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $resultQuery = $db->createCommand($sql)->execute();
            }
        } catch (\Exception $th) {
            Yii::error("Error en sincronizacion de Productos Almacenes x ODBC: ",$e);
        }
         Yii::$app->db->createCommand('SELECT func_actualizacionSeries();')->execute();
    }
    public function obtenerProductosLotesODBC(){
        Yii::error("SINCRONIZA ODBC productos LOTES: ");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 35));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador=$respuesta[0]->CANTIDAD;
            Yii::error("SINCRONIZA ODBC PRODUCTOS LOTES cantidad: ".$contador);
            
            $campos='(ItemCode, BatchNum, WhsCode,Quantity,ExpDate,InDate,BaseType,BaseEntry,BaseNum,BaseLinNum,DataSource,Transfered)';

            for($reg= 0; $reg < $contador; $reg+=1000){
                $data = json_encode(array("accion" => 34, "limite"=>1000,"inicio"=>$reg));
                $respuesta = $serviceLayer->executex($data);               
                $respuesta = json_decode($respuesta);
                $valores="";

                foreach ($respuesta as $lote) {
                    //Yii::error("----> " . json_encode($lote));
                        if($lote->Expira==""){
                          $lote->Expira='2020-12-31';  
                        }
                        $valores .= "('{$lote->ItemCode}','{$lote->BatchNum}','{$lote->WhsCode}','{$lote->Quantity}','{$lote->Expira}','{$lote->Ingreso}','{$lote->BaseType}','{$lote->BaseEntry}','{$lote->BaseNum}','{$lote->BaseLinNum}','{$lote->DataSource}','{$lote->Transfered}'),";
                    
                }
                $cadena = substr($valores, 0, -1); // quitar comita final
                $sql="INSERT INTO sinc_lotesproductos {$campos}  VALUES {$cadena};";
                //Yii::error("SQL =>" . $sql);
                $db = Yii::$app->db;
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_lotesproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $resultQuery = $db->createCommand($sql)->execute();

            }


        } catch (\Exception $th) {
            Yii::error("Error en sincronizacion de Productos Lotes x ODBC: ",$e);
        }
         Yii::$app->db->createCommand('SELECT func_actualizacionLotes();')->execute();
    }

    public function Bonificacion(){
		Yii::error("SINCRONIZA ODBC: BonificacionesSemiAutomaticas BONIFICACION");
        //$this->obtenerBonificacionesSemiautomaticasCA();
        //$this->obtenerBonificacionesSemiautomaticasDE1();
        //$this->obtenerBonificacionesSemiautomaticasDE2();
    }    

    private function obtenerBonificacionesSemiautomaticasCA() {
        Yii::error("SINCRONIZA ODBC: BonificacionesSemiAutomaticas BONIFICACION_CA");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 200));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE bonificacion_ca;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";

                $sqlSub .= "INSERT INTO `bonificacion_ca`(`Code`,`Name`,`U_tipo`,`U_cliente`,`U_fecha`,`U_fecha_inicio`,`U_fecha_fin`,`U_estado`,`U_entrega`,`U_cantidadbonificacion`,`U_observacion`,`U_reglatipo`,`U_reglaunidad`,`U_reglacantidad`,`U_bonificaciontipo`,`U_bonificacionunidad`,`U_bonificacioncantidad`) VALUES (";
                
                $sqlSub .= "'{$punteroSub->Code}', '{$punteroSub->Name}', '{$punteroSub->U_tipo}', '{$punteroSub->U_cliente}', '{$punteroSub->U_fecha}', '{$punteroSub->U_fecha_inicio}', '{$punteroSub->U_fecha_fin}', '{$punteroSub->U_estado}', '{$punteroSub->U_entrega}', '{$punteroSub->U_cantidadbonificacion}', '{$punteroSub->U_observacion}', '{$punteroSub->U_reglatipo}', '{$punteroSub->U_reglaunidad}', '{$punteroSub->U_reglacantidad}', '{$punteroSub->U_bonificaciontipo}', '{$punteroSub->U_bonificacionunidad}', '{$punteroSub->U_bonificacioncantidad}')";
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' bonificacion_ca', $e);
        }
    }

    private function obtenerBonificacionesSemiautomaticasDE1() {
        Yii::error("SINCRONIZA ODBC: BonificacionesSemiAutomaticas bonificacion_de1");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 201));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE bonificacion_de1;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";

                $sqlSub .= "INSERT INTO `bonificacion_de1`(`Code`,`Name`,`U_ID_bonificacion`,`U_regla`) VALUES (";
                $sqlSub .= "'{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->U_ID_bonificacion}','{$punteroSub->U_regla}')";
                //Yii::error("SINCRONIZA bonificacion_de1: ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' bonificacion_de1', $e);
        }
    }

    private function obtenerBonificacionesSemiautomaticasDE2() {
        Yii::error("SINCRONIZA ODBC: BonificacionesSemiAutomaticas bonificacion_de2");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 202));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE bonificacion_de2;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";
                
                $sqlSub .= "INSERT INTO `bonificacion_de2`(`Code`,`Name`,`U_ID_bonificacion`,`U_bonificacion`) VALUES (";
                $sqlSub .= "'{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->U_ID_bonificacion}','{$punteroSub->U_bonificacion}')";
                // Yii::error("SINCRONIZA bonificacion_de2: ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' bonificacion_de2', $e);
        }
    }
    private function sincronizarCanal() {
        Yii::error("SINCRONIZA ODBC: Companex Canal");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 300));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE companex_canal;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            $campos="(docEntry,code,name,canceled,objeto)";
            foreach ($respuesta as $punteroSub) {
                $valores .= "('{$punteroSub->DocEntry}','{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->Canceled}','{$punteroSub->Object}'),";
            }
            $cadena = substr($valores, 0, -1); // quitar comita final
            $sql="INSERT INTO companex_canal {$campos}  VALUES {$cadena};";
            Yii::error("SQL =>" . $sql);
            $db = Yii::$app->db;
            $db->createCommand($sql)->execute();

        } catch (\Exception $e) {
            Yii::error('sincroniza companex canal error', $e);
        }
    }
    private function sincronizarSubCanal() {
        Yii::error("SINCRONIZA ODBC: Companex subCanal");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 301));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE companex_subcanal;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            $campos="(docEntry,canal,code,name,canceled,objeto)";
            foreach ($respuesta as $punteroSub) {
                $valores .= "('{$punteroSub->DocEntry}','{$punteroSub->U_Canal}','{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->Canceled}','{$punteroSub->Object}'),";
            }
            $cadena = substr($valores, 0, -1); // quitar comita final
            $sql="INSERT INTO companex_subcanal {$campos}  VALUES {$cadena};";
            // Yii::error("SQL =>" . $sql);
            $db = Yii::$app->db;
            $db->createCommand($sql)->execute();

        } catch (\Exception $e) {
            Yii::error('sincroniza companex subcanal error', $e);
        }
    }
    private function sincronizartipoTienda() {
        Yii::error("SINCRONIZA ODBC: Companex tipo tienda");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 302));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE companex_tipotienda;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            $campos="(docEntry,subcanal,code,name,canceled,objeto)";
            foreach ($respuesta as $punteroSub) {
                $valores .= "('{$punteroSub->DocEntry}','{$punteroSub->U_SubCanal}','{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->Canceled}','{$punteroSub->Object}'),";
            }
            $cadena = substr($valores, 0, -1); // quitar comita final
            $sql="INSERT INTO companex_tipotienda {$campos}  VALUES {$cadena};";
            // Yii::error("SQL =>" . $sql);
            $db = Yii::$app->db;
            $db->createCommand($sql)->execute();

        } catch (\Exception $e) {
            Yii::error('sincroniza companex tipotienda error', $e);
        }
    }
    private function sincronizarCadena() {
        Yii::error("SINCRONIZA ODBC: Companex cadena");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 303));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE companex_cadena;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            $campos="(docEntry,tipotienda,code,name,canceled,objeto,tipodato)";
            foreach ($respuesta as $punteroSub) {
                $valores .= "('{$punteroSub->DocEntry}','{$punteroSub->U_TipoTienda}','{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->Canceled}','{$punteroSub->Object}','{$punteroSub->U_TipoDato}'),";
            }
            $cadena = substr($valores, 0, -1); // quitar comita final
            $sql="INSERT INTO companex_cadena {$campos}  VALUES {$cadena};";
            Yii::error("SQL =>" . $sql);
            $db = Yii::$app->db;
            $db->createCommand($sql)->execute();

        } catch (\Exception $e) {
            Yii::error('sincroniza companex cadena error', $e);
        }
    }
    private function sincronizarSocioConsolidador() {
        Yii::error("SINCRONIZA ODBC: Companex Socio consolidador");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 304));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE companex_consolidador;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            $campos="(docentry,code,name)";
            foreach ($respuesta as $punteroSub) {
                $valores .= "('{$punteroSub->DocEntry}','{$punteroSub->Code}','{$punteroSub->Name}'),";
            }
            $cadena = substr($valores, 0, -1); // quitar comita final
            $sql="INSERT INTO companex_consolidador {$campos}  VALUES {$cadena};";
            Yii::error("SQL =>" . $sql);
            $db = Yii::$app->db;
            $db->createCommand($sql)->execute();

        } catch (\Exception $e) {
            Yii::error('sincroniza companex consolidador error', $e);
        }
    }
    public function Canal() {
        Yii::error("SINCRONIZA ODBC: canales");
        $this->sincronizarCadena();
        $this->sincronizartipoTienda();
        $this->sincronizarSubCanal();
        $this->sincronizarCanal();
        $this->sincronizarSocioConsolidador();
    }

    public function ObtenrFacturasCabecera() {
        Yii::error("SINCRONIZA ODBC: Facturas Cabecera");
        try
        {
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 101));
        $respuesta = $serviceLayer->executex($data);
        $respuesta = json_decode($respuesta);
        $contador=$respuesta[0]->REGISTROS;
        //$contador=10000;
        Yii::error("SINCRONIZA ODBC: registros".$contador);
        $campos="id,DocEntry,DocNum,DocDate,DocDueDate,CardCode,CardName,DocTotal,DocCurrency,JournalMemo";
        $campos .= ",PaymentGroupCode,DocTime,Series,TaxDate,CreationDate,UpdateDate";
        $campos .= ",FinancialPeriod,UpdateTime,U_LB_NumeroFactura,U_LB_NumeroAutorizac,U_LB_FechaLimiteEmis";
        $campos .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
        $campos .= ",ReserveInvoice,PaidtoDate,Saldo,TransId,DocStatus,InvStatus,User,Status,DateUpdate,U_LB_NIT,U_xMOB_Codigo,FolioNum,descuento,U_XMB_repartidor,U_XMB_AUX1,Address,Address2,U_XMB_Latitud,U_XMB_Longitud,U_XMB_Territorio";
        for($reg= 0;  $reg < $contador; $reg+=1000){
            $datos=" ";
            $data = json_encode(array("accion" => 1,"salto"=>$reg));            
            $respuesta = $serviceLayer->executex($data); 
            $respuesta = json_decode($respuesta);
            foreach ($respuesta as $puntero) {
                $pagado = round($puntero->PaidToDate, 2);
                $saldo = round($puntero->Saldo, 2);
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $datos .= "(DEFAULT,";
                $datos .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                $datos .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocCurrency}','{$this->remplaceString($puntero->JournalMemo)}'";
                $datos .= ",{$puntero->PaymentGroupCode},'{$puntero->DocTime}',{$puntero->Series},'{$puntero->TaxDate}','{$puntero->CreationDate}','{$puntero->UpdateDate}'";
                $datos .= ",'{$puntero->FinancialPeriod}','{$puntero->UpdateTime}','{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                $datos .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}','{$puntero->U_LB_TipoFactura}',{$puntero->SalesPersonCode}";
                $datos .= ",'{$puntero->ReserveInvoice}',{$pagado},{$saldo},'{$puntero->TransId}','{$puntero->Status}','{$puntero->pedienteEntrega}',";
                $datos .= "1,1,'" . Carbon::today() . "','{$puntero->U_LB_NIT}','{$puntero->U_xMOB_Codigo}','{$puntero->FolioNum}','{$puntero->descuento}','{$puntero->Repartidor}','{$puntero->AUX1}','{$puntero->Address}','{$puntero->Address2}','{$puntero->LatitudDest}','{$puntero->LongitudDest}','{$puntero->TerritorioDest}'";
                $datos .= "),";
            } 
            $cadena = substr($datos, 0, -1);
            $sql="INSERT INTO facturas ({$campos} ) VALUES {$cadena};";   
            //Yii::error("SINCRONIZA ODBC: sql ".$sql);         
            $db = Yii::$app->db; 
            if($reg==0)
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE facturas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
            $db->createCommand($sql)->execute();  
                  
        }
        } catch (\Throwable $e) {
            $this->insertLog2('sincroniza', 'Facturas Cabecera', $e);
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public function ObtenrFacturasDetalle() {
        Yii::error("SINCRONIZA ODBC: Facturas Cuerpo");
        try{
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 105));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador = $respuesta[0]->REGISTROS;
            Yii::error("SINCRONIZAR ODBC: registros: ".$contador);
            $campos = "(id,LineNum,ItemCode,ItemDescription,Quantity,Price,PriceAfterVAT,Currency,Rate,LineTotal,TaxTotal,UnitPrice,DocEntry,DocNum,Entregado,OpenQty,User,Status,DateUpdate,WhsCode,OcrCode,OcrCode2,Linestatus,InvStatus,OpenSum,UomCode,descuento,U_XMB_CANTREP,U_XMB_ALMREP,U_XMB_LOTEREP,U_XMB_SERIEREP,LineTotalPay,DiscPrcnt,impuesto)";
            for($reg= 0;  $reg < $contador; $reg+=1000){
                $sql = " ";
                $data = json_encode(array("accion" => 2,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);            
                $respuesta = json_decode($respuesta);
                foreach ($respuesta as $punteroSub) {
                    If ($punteroSub->Rate == null) {
                        $punteroSub->Rate = 1;
                    }
                    $punteroSub->ItemDescription = str_replace("'"," ",$punteroSub->ItemDescription);
                    $sql .= "(DEFAULT, '{$punteroSub->LineNum}','{$punteroSub->ItemCode}','{$punteroSub->ItemDescription}','{$punteroSub->Quantity}','{$punteroSub->Price}','{$punteroSub->PriceAfterVAT}','{$punteroSub->Currency}','{$punteroSub->Rate}','{$punteroSub->LineTotal}','{$punteroSub->TaxTotal}','{$punteroSub->UnitPrice}','{$punteroSub->DocEntry}','{$punteroSub->DocNum}','{$punteroSub->Entregado}','{$punteroSub->OpenQty}',";
                    $sql .= "1,1,'" . Carbon::today() . "','{$punteroSub->WhsCode}','{$punteroSub->OcrCode}','{$punteroSub->OcrCode2}','{$punteroSub->LineStatus}','{$punteroSub->InvntSttus}','{$punteroSub->OpenSum}','{$punteroSub->UomCode}','{$punteroSub->U_Descuento}','{$punteroSub->CantidadRepartida}','{$punteroSub->AlmacenReparto}','{$punteroSub->LoteReparto}','{$punteroSub->SerieReparto}','{$punteroSub->LineTotalPay}','{$punteroSub->DiscPrcnt}','{$punteroSub->Impuesto}'";
                    $sql .= "),";
                }                
                $cadena = substr($sql, 0, -1);
                $sql = "INSERT INTO facturasproductos {$campos} VALUES {$cadena};";
              //  Yii::error("SINCRONIZA ODBC: detalle facturas ",  $sql);
                $db = Yii::$app->db; 
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE facturasproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $db->createCommand($sql)->execute(); 
            }
        } catch (\Throwable $e) {
            $this->insertLog2('sincroniza', 'Facturas Cuerpo', $e);
            $transaction->rollBack();
            throw $e;
        }
    }

    public function ObtenerSapOfertasCabecera() {
        Yii::error("SINCRONIZA ODBC: ofertas Cabecera");
        try{
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 102));
        $respuesta = $serviceLayer->executex($data);
        $respuesta = json_decode($respuesta);
        $contador=$respuesta[0]->REGISTROS;
        Yii::error("SINCRONIZA ODBC: registros: ".$contador);
        $campos = "";
        $campos .= "(id,";
        $campos .= "DocEntry,  DocNum,  DocDate,  DocDueDate,  CardCode,  CardName,  DocTotal, ";
        $campos .= "DocTime, Series, TaxDate, UpdateDate, U_LB_NumeroFactura, U_LB_NumeroAutorizac, U_LB_FechaLimiteEmis, ";
        $campos .= "U_LB_CodigoControl, U_LB_EstadoFactura, U_LB_RazonSocial, U_LB_TipoFactura, User, Status, ";
        $campos .= "DateUpdate, ReserveInvoice, SalesPersonCode, PaidtoDate, Saldo,DocStatus,InvStatus,U_LB_NIT,U_xMOB_Codigo,descuento,U_XMB_repartidor,U_XMB_AUX1,Address,Address2,";
        $campos .= "U_XMB_Latitud,U_XMB_Longitud,U_XMB_Territorio ";
        $campos .= ")";
        for($reg= 0;  $reg < $contador; $reg+=1000){
            $datos=" ";
            $data = json_encode(array("accion" => 14,"salto"=>$reg));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $datos .= "(DEFAULT,";
                $datos .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                $datos .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocTime}','{$puntero->Series}','{$puntero->TaxDate}'";
                $datos .= ",'{$puntero->UpdateDate}'";
                $datos .= ",'{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                $datos .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}','{$puntero->U_LB_TipoFactura}','{$puntero->User}','{$puntero->Status}','','{$puntero->ReserveInvoice}','{$puntero->SalesPersonCode}','{$puntero->PaidtoDate}','{$puntero->Saldo}','{$puntero->Status}','{$puntero->pedienteEntrega}','{$puntero->U_LB_NIT}','{$puntero->U_xMOB_Codigo}','{$puntero->descuento}','{$puntero->Repartidor}','{$puntero->AUX1}','{$puntero->Address}','{$puntero->Address2}','{$puntero->LatitudDest}','{$puntero->LongitudDest}','{$puntero->TerritorioDest}'";
                $datos .= "),";
            } 
            $cadena = substr($datos, 0, -1);
            $sql="INSERT INTO sapofertas {$campos} VALUES {$cadena};";
            $db = Yii::$app->db; 
            if($reg==0)
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapofertas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
            $db->createCommand($sql)->execute(); 
                  
        }            
        } catch (\Throwable $e) {
            $this->insertLog2('sincroniza', 'Ofertas Cuerpo', $e);
            $transaction->rollBack();
            throw $e;
        }
    }

    public function ObtenerSapOfertasDetalles() {
        Yii::error("SINCRONIZA ODBC: oferta detalle");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 106));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador = $respuesta[0]->REGISTROS;
            Yii::error("SINCRONIZAR ODBC: registros: ".$contador);
            $campos = '';
            $campos .= "(id,";
            $campos .= "DocEntry, LineNum, ItemCode, ItemDescription, Price, Quantity, ";
            $campos .= "Currency, Rate, LineTotal, OpenQty, IdCabecera, Usuario,";
            $campos .= "Status, DateUpdate,UomCode,PriceAfVAT, OcrCode,";
            $campos .= "OcrCode2,WhsCode,GTotal,LineStatus,descuento,U_XMB_CANTREP,U_XMB_ALMREP,U_XMB_LOTEREP,U_XMB_SERIEREP,LineTotalPay,DiscPrcnt,TaxTotal";
            $campos .= ")";            
            for($reg= 0;  $reg < $contador; $reg+=1000){
                $sql = " ";
                $data = json_encode(array("accion" => 15,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);            
                $respuesta = json_decode($respuesta);
                foreach ($respuesta as $puntero) {
                    If ($puntero->JournalMemo == null) {
                        $puntero->JournalMemo = "nn";
                    }
                    $puntero->ItemDescription=str_replace("'"," ",$puntero->ItemDescription);                    
                    $sql .= "(DEFAULT,{$puntero->DocEntry},'{$puntero->LineNum}','{$puntero->ItemCode}','{$puntero->ItemDescription}','{$puntero->Price}','{$puntero->Quantity}'";
                    $sql .= ",'{$puntero->Currency}','{$puntero->Rate}','{$puntero->LineTotal}','{$puntero->OpenQty}'";
                    $sql .= ",'{$puntero->IdCabecera}','{$puntero->Usuario}','{$puntero->Status}'";
                    $sql .= ",'{$puntero->DateUpdate}','{$puntero->UomCode}','{$puntero->PriceAfVAT}','{$punteroSub->OcrCode}','{$punteroSub->OcrCode2}','{$puntero->WhsCode}','{$puntero->GTotal}','{$puntero->LineStatus}','{$puntero->U_Descuento}'";
                    $sql .= ",'{$puntero->CantidadRepartida}','{$puntero->AlmacenReparto}','{$puntero->LoteReparto}','{$puntero->SerieReparto}',{$puntero->LineTotalPay},'{$puntero->DiscPrcnt}','{$puntero->TaxTotal}'";
                    $sql .= "),";
                }
                $cadena = substr($sql, 0, -1);
                $sql="INSERT INTO sapofertasdetalle {$campos} VALUES {$cadena};";
                $db = Yii::$app->db; 
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapofertasdetalle;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $db->createCommand($sql)->execute(); 
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'oferta detalle', $e);
            $transaction->rollBack();
            throw $e;
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    public function ObtenrPedidosCabecera($idRepartidor=0) {
        //DOCUMENTOS IMPORTADOS PARA PICKING//
        Yii::error("SINCRONIZA ODBC: pedidos Cabecera update: ".$idRepartidor);
       
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 103,"Repartidor"=>$idRepartidor));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador = $respuesta[0]->REGISTROS;
            Yii::error("SINCRONIZAR ODBC: registros de pedido update--->: ".$contador);
            $campos  = '';
            $campos .= "(id,";
            $campos .= "DocEntry,DocNum,DocDate,DocDueDate";
            $campos .= ",CardCode,CardName,DocTotal,DocCurrency,JournalMemo";
            $campos .= ",PaymentGroupCode,DocTime,Series,TaxDate,CreationDate,UpdateDate";
            $campos .= ",FinancialPeriod,UpdateTime,U_LB_NumeroFactura,U_LB_NumeroAutorizac,U_LB_FechaLimiteEmis";
            $campos .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
            $campos .= ",ReserveInvoice,DocStatus,InvStatus,User,Status,DateUpdate,U_LB_NIT,U_xMOB_Codigo,descuento,U_XMB_repartidor";
            $campos .= ",U_XMB_AUX1,Address,Address2,U_XMB_Latitud,U_XMB_Longitud,U_XMB_Territorio,PickDate,AbsEntry,DirCobro,DirEntrega";
            $campos .= ")";
            for($reg= 0;  $reg < $contador; $reg+=1000){
            $sql=" ";
            $data = json_encode(array("accion" => 3,"salto"=>$reg,"Repartidor"=>$idRepartidor));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            Yii::error("SINCRONIZAR ODBC: registros de pedido update 0--->: ".json_encode($respuesta));
            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
               // Yii::error("SINCRONIZAR ODBC: registros de pedido update--->: ".json_encode($puntero));
              ;
              if($puntero->DirCobro=="")$puntero->DirCobro=null;
              if($puntero->DirEntrega=="")$puntero->DirEntrega=null;
                $sql .= "(DEFAULT,";
                $sql .= "'{$puntero->DocEntry}','{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                $sql .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}','{$puntero->DocTotal}','{$puntero->DocCurrency}','{$puntero->JournalMemo}'";
                $sql .= ",'{$puntero->PaymentGroupCode}','{$puntero->DocTime}','{$puntero->Series}','{$puntero->TaxDate}','{$puntero->CreationDate}','{$puntero->UpdateDate}'";
                $sql .= ",'{$puntero->FinancialPeriod}','{$puntero->UpdateTime}','{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                $sql .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}','{$puntero->U_LB_TipoFactura}','{$puntero->SalesPersonCode}'";
                $sql .= ",'{$puntero->ReserveInvoice}','{$puntero->Status}','{$puntero->pedienteEntrega}',";
                $sql .= "1,1,'{$puntero->Status}','{$puntero->U_LB_NIT}','{$puntero->U_xMOB_Codigo}','{$puntero->descuento}','{$puntero->Repartidor}','{$puntero->AUX1}','{$puntero->Address}','{$puntero->Address2}','{$puntero->U_Latitud}','{$puntero->U_Longitud}','{$puntero->U_Territorio}','{$puntero->PickDate}','{$puntero->AbsEntry}','{$puntero->DirCobro}','{$puntero->DirEntrega}'";
                $sql .= "),";
                Yii::error("INGRESA 77");
                
            }
            
            $cadena = substr($sql, 0, -1);
            $sql="INSERT INTO pedidos {$campos} VALUES {$cadena};";
            Yii::error("ERROR update 0--->: ".$sql);
            //Yii::error("ERROR 1: ");
            $db = Yii::$app->db; 
            if($contador>0){
                Yii::error("Se elimina pedido segun vendedor de sap: ");
                Yii::$app->db->createCommand('DELETE FROM pedidos where U_XMB_repartidor='.$idRepartidor)->execute();
                //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE pedidos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                  //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                 $db->createCommand($sql)->execute(); 
            }

          
        }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'pedidos Cabecera', $e);
        }
        
    }
    
    public function ObtenrPedidosDetalle($idRepartidor=0) {
          //DOCUMENTOS IMPORTADOS PARA PICKING//
        Yii::error("SINCRONIZA ODBC: pedidos detalle  Cuerpo: ".$idRepartidor);
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 107,"Repartidor"=>$idRepartidor));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador = $respuesta[0]->REGISTROS;
            Yii::error("SINCRONIZAR ODBC: registros: ".$contador);
            $campos = '';
            $campos .= "(id,LineNum,ItemCode,ItemDescription,Quantity,Price,PriceAfterVAT,Currency,Rate,LineTotal,TaxTotal,UnitPrice,DocEntry,DocNum,Entregado,OpenQty,User, OcrCode, OcrCode2,Status,DateUpdate,WhsCode,Linestatus,InvStatus,OpenSum,UomCode,descuento,U_XMB_CANTREP,U_XMB_ALMREP,U_XMB_LOTEREP,U_XMB_SERIEREP,LineTotalPay,DiscPrcnt,impuesto,bonif,codebonif,ICEE,ICEP,BaseQty,ListaPrecio,U_XMB_repartidor,iceorpor,iceoresp)";            
            for($reg= 0;  $reg < $contador; $reg+=1000){
                $sql = " ";
                $data = json_encode(array("accion" => 4,"salto"=>$reg,"Repartidor"=>$idRepartidor));
                $respuesta = $serviceLayer->executex($data);            
                $respuesta = json_decode($respuesta);  

                foreach ($respuesta as $punteroSub) {
                   If ($punteroSub->Rate == null) {
                        $punteroSub->Rate = 1;
                    }
                    $punteroSub->LineTotalPay= $punteroSub->LineTotalPay?$punteroSub->LineTotalPay:0;
                    $punteroSub->SerieReparto=$punteroSub->SerieReparto?$punteroSub->SerieReparto:"";
                    $punteroSub->ItemDescription=str_replace("'"," ",$punteroSub->ItemDescription);
                    $sql .= "(DEFAULT,'{$punteroSub->LineNum}','{$punteroSub->ItemCode}','{$punteroSub->ItemDescription}','{$punteroSub->Quantity}','{$punteroSub->Price}','{$punteroSub->PriceAfterVAT}','{$punteroSub->Currency}','{$punteroSub->Rate}','{$punteroSub->LineTotal}','{$punteroSub->TaxTotal}','{$punteroSub->UnitPrice}','{$punteroSub->DocEntry}','{$punteroSub->DocNum}','{$punteroSub->Entregado}','{$punteroSub->OpenQty}',";
                    $sql .= "1,'{$punteroSub->OcrCode}','{$punteroSub->OcrCode2}',1,'" . Carbon::today() . "','{$punteroSub->WhsCode}','{$punteroSub->LineStatus}','{$punteroSub->InvntSttus}','{$punteroSub->OpenSum}','{$punteroSub->UomCode}','{$punteroSub->U_Descuento}','{$punteroSub->U_XMB_CANTREP}','{$punteroSub->AlmacenReparto}','{$punteroSub->LoteReparto}','{$punteroSub->SerieReparto}',{$punteroSub->LineTotalPay},'{$punteroSub->DiscPrcnt}','{$punteroSub->Impuesto}'";
                    $sql .= ",'{$punteroSub->Bonif}','{$punteroSub->CodeBonif}','{$punteroSub->ICEE}','{$punteroSub->ICEP}','{$punteroSub->BaseQty}','{$punteroSub->ListaPrecio}','{$punteroSub->Repartidor}','{$punteroSub->U_XM_ICEPorcentual}','{$punteroSub->U_XM_ICEEspecifico}'";
                    $sql .= "),";
                }
                $cadena = substr($sql, 0, -1);
                $sql="INSERT INTO pedidosproductos {$campos} VALUES {$cadena};";
                //Yii::error("SINCRONIZA detalle--->",$sql);
                $db = Yii::$app->db; 
                
                if($contador>0){
                    Yii::error("Se elimina pedido segun vendedor de sap: ");
                    Yii::$app->db->createCommand('DELETE FROM pedidosproductos where U_XMB_repartidor='.$idRepartidor)->execute();
                    //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE pedidosproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                    $db->createCommand($sql)->execute();
                }

                //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('sincroniza', 'pedidos Cuerpo', $e);
            throw $e;
        }
    }

    public function ObtenerSapEntregasCabecera() {
        Yii::error("SINCRONIZA ODBC: entregas Cabecera");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 104));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador = $respuesta[0]->REGISTROS;
            Yii::error("SINCRONIZAR ODBC: registros: ".$contador);
            $campos = "";
            $campos .= "(id,";
            $campos .= "DocEntry,  DocNum,  DocDate,  DocDueDate,  CardCode,  CardName,  DocTotal, ";
            $campos .= "DocTime, Series, TaxDate, UpdateDate, U_LB_NumeroFactura, U_LB_NumeroAutorizac, U_LB_FechaLimiteEmis, ";
            $campos .= "U_LB_CodigoControl, U_LB_EstadoFactura, U_LB_RazonSocial, U_LB_TipoFactura, User, Status, ";
            $campos .= "DateUpdate, ReserveInvoice, SalesPersonCode, PaidtoDate, Saldo,DocStatus,InvStatus,U_LB_NIT,U_xMOB_Codigo,descuento,U_XMB_repartidor,U_XMB_AUX1,Address,Address2,";
            $campos .= "U_XMB_Latitud,U_XMB_Longitud,U_XMB_Territorio)";
            for($reg= 0;  $reg < $contador; $reg+=1000){
                $sql = " ";
                $data = json_encode(array("accion" => 16,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);
                $respuesta = json_decode($respuesta);
                foreach ($respuesta as $puntero) {
                    If ($puntero->JournalMemo == null) {
                        $puntero->JournalMemo = "nn";
                    }                
                    $sql .= "(DEFAULT,";
                    $sql .= "'{$puntero->DocEntry}','{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                    $sql .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}','{$puntero->DocTotal}','{$puntero->DocTime}','{$puntero->Series}','{$puntero->TaxDate}'";
                    $sql .= ",'{$puntero->UpdateDate}'";
                    $sql .= ",'{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                    $sql .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}','{$puntero->U_LB_TipoFactura}','{$puntero->User}','{$puntero->Status}','','{$puntero->ReserveInvoice}','{$puntero->SalesPersonCode}','{$puntero->PaidtoDate}','{$puntero->Saldo}','{$puntero->Status}','{$puntero->pedienteEntrega}','{$puntero->U_LB_NIT}','{$puntero->U_xMOB_Codigo}','{$puntero->descuento}'";
                    $sql .= ",'{$puntero->Repartidor}','{$puntero->AUX1}','{$puntero->Address}','{$puntero->Address2}','{$puntero->LatitudDest}','{$puntero->LongitudDest}','{$puntero->TerritorioDest}'";
                    $sql .= "),";
                }
                $cadena = substr($sql, 0, -1);
                $sql="INSERT INTO sapentregas {$campos} VALUES {$cadena};";
                $db = Yii::$app->db; 
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapentregas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $db->createCommand($sql)->execute(); 
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'sapentregas Cabecera', $e);
            $transaction->rollBack();
            throw $e;
        }
    }

    public function ObtenerSapEntregasDetalles() {
        Yii::error("SINCRONIZA ODBC: entregas detalle");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 108));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador = $respuesta[0]->REGISTROS;
            Yii::error("SINCRONIZAR ODBC: registros: ".$contador);
            $campos = '';
            $campos .= "(id,";
            $campos .= "DocEntry, LineNum, ItemCode, ItemDescription, Price, Quantity, ";
            $campos .= "Currency, Rate, LineTotal, OpenQty, IdCabecera, Usuario,";
            $campos .= "Status, DateUpdate,UomCode,PriceAfVAT, OcrCode, OcrCode2,WhsCode,GTotal,LineStatus,descuento,";
            $campos .= "U_XMB_CANTREP, U_XMB_ALMREP, U_XMB_LOTEREP, U_XMB_SERIEREP,LineTotalPay,DiscPrcnt,impuesto,TaxTotal";
            $campos .= ")";
            for($reg= 0;  $reg < $contador; $reg+=1000){
                $sql = " ";
                $data = json_encode(array("accion" => 17,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);            
                $respuesta = json_decode($respuesta);
                foreach ($respuesta as $puntero) {
                    If ($puntero->JournalMemo == null) {
                        $puntero->JournalMemo = "nn";
                    }
                    $puntero->LineTotalPay=$puntero->LineTotalPay?$puntero->LineTotalPay:0;
                    $puntero->ItemDescription=str_replace("'"," ",$puntero->ItemDescription);
                    $sql .= "(DEFAULT,";
                    $sql .= "'{$puntero->DocEntry}','{$puntero->LineNum}','{$puntero->ItemCode}','{$puntero->ItemDescription}','{$puntero->Price}','{$puntero->Quantity}'";
                    $sql .= ",'{$puntero->Currency}','{$puntero->Rate}','{$puntero->LineTotal}','{$puntero->OpenQty}'";
                    $sql .= ",'{$puntero->IdCabecera}','{$puntero->Usuario}','{$puntero->Status}'";
                    $sql .= ",'{$puntero->DateUpdate}','{$puntero->UomCode}','{$puntero->PriceAfVAT}', '{$puntero->OcrCode}','{$puntero->OcrCode2}' ,'{$puntero->WhsCode}','{$puntero->GTotal}','{$puntero->LineStatus}','{$puntero->U_Descuento}'";
                    $sql .= ",'{$puntero->CantidadRepartida}','{$puntero->AlmacenReparto}','{$puntero->LoteReparto}','{$puntero->SerieReparto}',{$puntero->LineTotalPay},'{$puntero->DiscPrcnt}','{$puntero->Impuesto}','{$puntero->TaxTotal}'";
                    $sql .= "),";
                }
                $cadena = substr($sql, 0, -1);
                $sql="INSERT INTO sapentregasdetalle {$campos} VALUES {$cadena};";
                $db = Yii::$app->db; 
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapentregasdetalle;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $db->createCommand($sql)->execute(); 
                
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'sapentregas Cabecera', $e);
            $transaction->rollBack();
            throw $e;
        }
    }    
    
    private function insertLog2($action, $parametros, $error) {
        $sql = "INSERT INTO log_envio(idlog, proceso, envio, respuesta, fecha, ultimo, endpoint) VALUES 
                (DEFAULT,'','" . $parametros . "','" . htmlentities($error, ENT_QUOTES) . "','" . Carbon::now() . "','','" . $action . "')";
        Yii::$app->db->createCommand($sql)->execute();
    }

    private function remplaceString($string) {
        if (!is_null($string)) {
            $string=str_replace('\'', '`', $string);
            $string=str_replace("'", '`', $string);
            $string=str_replace('?', ' ', $string);
            return $string;
        }
        return $string;
    } 

    public function promociones() {
        Yii::error("SINCRONIZA ODBC: Promociones");
        try {

            $serviceLayer = new Sincronizar();            
            $data = json_encode(array("accion" => 307));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("SINCRONIZA ODBC Promociones cantidad: ".$respuesta);
            $respuesta = json_decode($respuesta);            
            $contador=$respuesta[0]->CANTIDAD;


            for($reg= 0; $reg < $contador; $reg+=1000){
                $data = json_encode(array("accion" => 306,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);
                Yii::error("Resultado bonificacion: ".$respuesta);
                $respuesta = json_decode($respuesta);

                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE promociones; SET FOREIGN_KEY_CHECKS = 1;')->execute();

                $campos="(Code,Name,U_CardCode,U_CodigoCampania,U_ValorGanado,U_FechaInicio,U_FechaFinal,U_FechaMaximoCobro,U_Saldo,U_Meta,U_Acumulado)";
                foreach ($respuesta as $punteroSub) {
                   // if($punteroSub->Acumulado >= $punteroSub->U_Meta){
                        $valorganado=$punteroSub->U_ValorGanado - $punteroSub->Usado;
                        if($valorganado>0){
                            $valores .= "('{$punteroSub->CpnNo}','{$punteroSub->Name}','{$punteroSub->BpCode}','{$punteroSub->CpnNo}','{$punteroSub->U_ValorGanado}','{$punteroSub->StartDate}','{$punteroSub->FinishDate}','{$punteroSub->U_CobroPremio}','{$valorganado}','{$punteroSub->U_Meta}','{$punteroSub->Acumulado}'),";
                        }

                    //}
                    

                    
                }
                $cadena = substr($valores, 0, -1); // quitar comita final
                $sql="INSERT INTO promociones {$campos}  VALUES {$cadena};";
                Yii::error("SQL =>" . $sql);
                $db = Yii::$app->db;
                $db->createCommand($sql)->execute();


            }


           

        } catch (\Exception $e) {
            Yii::error('sincroniza promociones error', $e);
        }
     
    }

    public function CamposUsuario(){
        Yii::error("Campos de Usuario");
        $this->model->actiondir = 'UserFieldsMD?$filter=(startswith(Name, \'LB_\'))or (startswith(Name, \'XM_\'))or (startswith(Name, \'xMOB_\'))';
        $campos_usuario = $this->model->executex();
        $campos_usuario = $campos_usuario->value;
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE cnfcamposusuario;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $ids = '';
        $campos="id,Name,Type,Size,Description,SubType,LinkedTable,DefaultValue,TableName,FieldID,EditSize, Mandatory,LinkedUDO,LinkedSystemObject";
        foreach ($campos_usuario as $puntero) {
            $cu->Name=$puntero->Name;
            $cu->Type=$puntero->Type;
            $cu->Size=$puntero->Size;
            $cu->Description=$puntero->Description;
            $cu->SubType=$puntero->SubType;
            $cu->LinkedTable=$puntero->LinkedTable;
            $cu->DefaultValue=$puntero->DefaultValue;
            $cu->TableName=$puntero->TableName;
            $cu->FieldID=$puntero->FieldID;
            $cu->EditSize=$puntero->EditSize;
            $cu->Mandatory=$puntero-> Mandatory;
            $cu->LinkedUDO=$puntero->LinkedUDO;
            $cu->LinkedSystemObject=$puntero->LinkedSystemObject;

            $sql .= "(DEFAULT,";
            $sql .= "'{$cu->Name}','{$cu->Type}','{$cu->Size}','{$cu->Description}','{$cu->SubType}','{$cu->LinkedTable}'";
            $sql .= ",'{$cu->DefaultValue}','{$cu->TableName}','{$cu->FieldID}','{$cu->EditSize}'";
            $sql .= ",'{$cu->Mandatory}','{$cu->LinkedUDO}','{$cu->LinkedSystemObject}'";
            $sql .= "),";
        }
        $cadena = substr($sql, 0, -1);
        $sql="INSERT INTO cnfcamposusuario ({$campos}) VALUES {$cadena};";
        Yii::error("SQL =>" . $sql);
        $db = Yii::$app->db;
        $db->createCommand($sql)->execute();

        $count = Yii::$app->db->createCommand('select count(*) from cnfcamposusuario')->queryAll();
        Yii::error($count);
        return $ids;
    }

    public function sincronizacionDiariaManual() {
        Yii::error("SINC MANUAL");
        $this->tipoCambio();
        $this->obtenerClientesODBC();
        $this->obtenerProductosODBC();
    }
    public function Consulta_saldo($codigo) {
        try {
            ///Yii::error("EEEE");
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1114,"codigo"=>$codigo));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("consulta cliente:");
            Yii::error($respuesta);
            $respuesta = json_decode($respuesta);
            
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'consulta saldo cliente', $e);
            throw $e;
        }
    }
    public function Productos_por_almacen($codigo,$almacen) {
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1115,"codigo"=>$codigo,"almacen"=>$almacen));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('sincroniza', 'pedidos Cuerpo', $e);
            throw $e;
        }
    }

    public function Consulta_rut($rut,$codigo=0) {
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1116,"rut"=>$rut,"CardCode"=>$codigo));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'consulta cliente', $e);
            throw $e;
        }
    }
    public function obtenerNroFactutas(){
        // se usara una unica vez al pasar a productivo el xmobile
        Yii::error("SINCRONIZA ODBC: numero de facturas");
        try{
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 101));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador=$respuesta[0]->REGISTROS;
            Yii::error("SINCRONIZA ODBC: registros: ".$contador);
           
            for($reg= 0;  $reg < $contador; $reg+=1000){
                $datos=" ";
                $data = json_encode(array("accion" => 1,"salto"=>$reg));
                $respuesta = $serviceLayer->executex($data);
                Yii::error($respuesta);
                $respuesta = json_decode($respuesta);
             
                foreach ($respuesta as $puntero) {

                   
                    $datos .= "UPDATE `pagosfacturas` SET `pagosfacturas`.nrofactura='{$puntero->U_LB_NumeroFactura}' ,`pagosfacturas`.CardName='{$puntero->CardName}',`pagosfacturas`.DocTotal='{$puntero->DocTotal}',`pagosfacturas`.saldo='".($puntero->DocTotal-$puntero->PaidToDate)."'  WHERE `pagosfacturas`.docEntry='{$puntero->DocEntry}' and `pagosfacturas`.nroFactura is null ;";
                    
                } 
                $db = Yii::$app->db; 
                Yii::error("Actualiza pagos factura:");
                Yii::error($datos);
                $db->createCommand($datos)->execute(); 
                    
            }            
        } catch (\Throwable $e) {
            $this->insertLog2('sincroniza', 'SINCRONIZA NUMERACION FACTURA EN LA TABLA PAGOSFACTURAS', $e);
            $transaction->rollBack();
            throw $e;
        }
    }

    public function VerificaPedido($codigo) {
        //verifica si un pedido esta cerrado 
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1117,"codigo"=>$codigo));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'verifica pedido si esta cerrado', $e);
            throw $e;
        }
    }

    public function VerificaFactura($codigo) {
        //verifica si una factura esta cerrada 
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1118,"codigo"=>$codigo));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'verifica factura si esta cerrada', $e);
            throw $e;
        }
    }

    public function VerificaOferta($codigo) {
        //verifica si una factura esta cerrada 
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1119,"codigo"=>$codigo));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'verifica Oferta si esta cerrada', $e);
            throw $e;
        }
    }

    function cambiaFormato($valor){
      if($valor=='Y')return'tYES';
      elseif($valor=='N')return'tNO';
      else return $valor;
   }

    public function DocumentosImportadosContador($vendedor) {
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 2000,"vendedor"=>$vendedor));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("Respuesta modelo: ");
            Yii::error($respuesta);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'consulta cliente', $e);
            throw $e;
        }
    }

    public function DocumentosImportados($vendedor,$salto) {
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 2001,"vendedor"=>$vendedor,"salto"=>$salto));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("Respuesta modelo: ");
            Yii::error($respuesta);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'consulta cliente', $e);
            throw $e;
        }
    }

    public function DocumentosImportadosCliente($cliente) {
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 2002,"cliente"=>$cliente));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("Respuesta modelo Doc Importado Cliente: ");
            Yii::error($respuesta);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'consulta cliente', $e);
            throw $e;
        }
    }

    /* Implementación del método para obtener los tipos de tarjestas a través del ODBC */
    public function obtenerTipoTarjetasODBC(){  
         Yii::error("SINCRONIZA ODBC tipotarjetas ");
        try
        {   $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 1401));
            $respuesta = $serviceLayer->executex($data);
            if($respuesta[0]!='[')
                $respuesta=substr($respuesta,3,-1);
            //$respuesta=$respuesta.']';
            //Yii::error($respuesta);
            $respuesta = json_decode($respuesta);
            $valores="";
            $campos='(CreditCard, CardName, AcctCode)';
            $sw=0;
            foreach ($respuesta as $con)
            {   $valores.="('{$con->CreditCard}','{$con->CardName}','{$con->AcctCode}'";
                $valores.="),";
                $sw=1;
            }
            $cadena = substr($valores, 0, -1);
            $sql="INSERT INTO tipotarjetas {$campos} VALUES {$cadena};";
            Yii::error($sql);
            $db = Yii::$app->db;
            Yii::error("SINCRONIZA ODBC tipotarjetasquery: ".json_encode($sql));
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE tipotarjetas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
            if($sw==1){
                $db->createCommand($sql)->execute();
            }
           
        }
        catch(\Exception $e)
        {   Yii::error("Error en sincronizacion de tipo tarjetas: ",$e);
        }
    }

    public function ObtenerCamposUsuarioAux(){
        Yii::error('inicio  creacion Campos de usuario Aux');

        $serviceLayer = new Sincronizar();
        // CONSULTA A LA TABLA AUX CAMPOS USUARIOS
        $aux_camposusuario_data = Yii::$app->db->createCommand('select * from aux_camposusuario where estado=1')->queryAll();
        Yii::error($aux_camposusuario_data);
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE aux_camposusuariodata;SET FOREIGN_KEY_CHECKS = 1;')->execute();

        foreach ($aux_camposusuario_data as $key => $value) {
            $id=$value['id'];
            $campos=' "'.$value['code'].'" As code, "'.$value['name'].'" As name ';
            $tabla= $value['tabla'];
            $condicion= $value['cond'];
            $data = json_encode(array("accion" => 2006,"campos"=>$campos,"tabla"=>$tabla,"condicion"=>$condicion));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            Yii::error("Respuesta Campos usuario Aux: ");
            Yii::error($respuesta);
            $sql = '';
           
            foreach ($respuesta as $puntero) {
                $name=utf8_decode($puntero->NAME);
                $sql .= "INSERT INTO aux_camposusuariodata (id,id_aux_camposusuario,code,name,estado) VALUES (DEFAULT,";
                $sql .= "'{$id}','{$puntero->CODE}','{$name}','1');";
            }
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $db->createCommand($sql)->execute();
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
   
        } 
        $count = Yii::$app->db->createCommand('select count(*) from aux_camposusuariodata')->queryAll();
        Yii::error($count);
    }

    public function CerrarPedidos(){
        Yii::error("SINCRONIZA ODBC Cerrar pedidos : ");
        $odbc = new Sincronizar();
        $serviceLayer = new Servislayer();
        $data = json_encode(array("accion" => 1107));
        $respuesta = $odbc->executex($data);
        //Yii::error("respuesta: ".$respuesta);
        //$respuesta="1_ - > x y z  [ []  ] 21 _ a b c ";
        // $pos=strpos($respuesta,'[',0);
        // //Yii::error("Posición: ".$respusta_aux);
        // if($respuesta[0]!='['):
        //     $respuesta=substr($respuesta,$pos); // quitar comita final
        // endif;
        // Yii::error("Posición caracter encontrado desde atras: ".strrpos($respuesta,']',0));
        // Yii::error("Cadena final: ".substr($respuesta,0,strrpos($respuesta,']',0)+1));
        // Yii::error("Ultimo caracter: ".substr($respuesta,-1,1));
        // Yii::error("Longitud cadena: ".strlen($respuesta)-1);
        //Yii::error("Contenido respuesta: ".var_dump($respuesta));
        //Yii::error("Caracteres especiales HTML: ".htmlspecialchars($respuesta,ENT_COMPAT,'ISO-8859-1',true));
        //Yii::error("Entidades HTML: ".htmlentities($respuesta,ENT_COMPAT,'ISO-8859-1',true));
        //$respuesta=preg_replace($respuesta[0],'',$respuesta);
        Yii::error("Respuesta CerrarPedidos(): ".$respuesta);

        $respuesta = json_decode($respuesta);
        foreach ($respuesta as $pedido) {
            $aux_docEntry=$pedido->DocEntry;
            $serviceLayer->actiondir = "Orders({$aux_docEntry})/Close";
            $respuestaClose = $serviceLayer->executePost([]);
            Yii::error("Respuesta Close:{$aux_docEntry}; DATA-" .json_encode($respuestaClose));
        }
    }

    public function DocumentosImportadosDetalleContador($vendedor) {
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 2003,"vendedor"=>$vendedor));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("Respuesta modelo: ");
            Yii::error($respuesta);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'consulta cliente', $e);
            throw $e;
        }
    }

    public function DocumentosImportadosDetalle($vendedor,$salto) {
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 2004,"vendedor"=>$vendedor,"salto"=>$salto));
            $respuesta = $serviceLayer->executex($data);
            Yii::error("Respuesta modelo: ");
            Yii::error($respuesta);
            $respuesta = json_decode($respuesta);
            return $respuesta;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'consulta cliente', $e);
            throw $e;
        }
    }
    /// facturacion electronica
    public function CargaFexCufd(){//formato de fecha AAAA/MM/DD
        date_default_timezone_set('America/La_Paz');
        
        $fecha=date('Y-m-d');
        Yii::error("SINCRONIZA FEX_BO_CUFD : ");

        $sql = "SELECT valor FROM configuracion WHERE parametro = 'FEX_Empresa' AND estado=1";
        $empresa = Yii::$app->db->createCommand($sql)->queryOne();
        //$emp = 6;
        Yii::error("FEX EMPRESA : ",$empresa["valor"]);
        $emp=$empresa["valor"];
        $odbc = new Sincronizar();
        $serviceLayer = new Servislayer();
        Yii::error("FECHA CON FORMATO : ".date("j-n-Y", strtotime($fecha)));
        $form = date("j/n/Y", strtotime($fecha));
        $data = json_encode(array("accion" => 1300,"empresafex"=> $emp,"fecha"=>$form));
        $respuesta = $odbc->executex($data);
        Yii::error("respuesta : ",$respuesta);
        $respuesta = json_decode($respuesta);
        $db = Yii::$app->db;

        
        //Yii::error("EMPRESA : ",$emp);
        $sql = "UPDATE fex_cufd SET estado = 0 where fecha = '{$fecha}' and fexcompany = '$emp'";
        //Yii::error("UPDATE PRUEBA : ".$sql);
        $db->createCommand($sql)->execute();
        
        try {
            $sql = '';
            $sql2 = '';
            $hoy = date("Y-m-d");
            //$hoy = date("d-m-Y",strtotime($hoy."- 1 days")); 
            Yii::error("fecha de hoy: ".$hoy);
            foreach ($respuesta as $puntero) {

                $fechax = '';
                $fechax = substr($puntero->FechaCreacion, 0, -10);
                $sql .= "INSERT INTO fex_cufd (fexcompany,CUIS,sucursal,puntoventa,CUFD,fecha,codigocontrol,estado) VALUES (";
                $sql .= "{$puntero->IdFexCompany},'{$puntero->CodigoCUIS}','{$puntero->Sucursal}','{$puntero->CodigoPuntoVenta}','{$puntero->CodigoCUFD}','{$hoy}','{$puntero->CodigoControl}','1');";

                if($hoy == $fecha){
                    $sql2 .= "UPDATE lbcc SET U_NumeroAutorizacion = '{$puntero->CodigoCUFD}' WHERE fex_sucursal = '{$puntero->Sucursal}' and fex_puntoventa = '{$puntero->CodigoPuntoVenta}';";
                }
                Yii::error("INSERT PRUEBA : ".$sql);
            }
            //Yii::error("INSERT PRUEBA : ".$sql);
            //Yii::error("INSERT PRUEBA : ".$sql2);
            $db->createCommand($sql)->execute();
            //$db->createCommand($sql2)->execute();
        } catch (\Exception $e) {
            Yii::error("Error en sincronizacion de FEX_BO_CUFD ",$e);
        }
        try{
            $this->CargaFexPuntoventa($emp);
            $this->CargaFexSucursal($emp);
        }catch (\Exception $e) {
            Yii::error("Error en sincronizacion de punto venta y sucursal ",$e);
        } 
        //$this->CargaFexPuntoventa($emp);
        //$this->CargaFexSucursal($emp);

    }

    public function CargaFexPuntoventa($empresa){
        Yii::error("SINCRONIZA FEX_BO_TIPOPUNTOVENTA : ");
        $odbc = new Sincronizar();
        $serviceLayer = new Servislayer();
        $data = json_encode(array("accion" => 1301,"empresafex"=> $empresa));
        $respuesta = $odbc->executex($data);
        Yii::error("respuesta : ".$respuesta);
        $respuesta = json_decode($respuesta);
        $db = Yii::$app->db;

        //$sql = "DELETE FROM fex_puntoventa where fexcompany = '2'";
        //Yii::error("UPDATE PRUEBA : ".$sql);
        //$db->createCommand($sql)->execute();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE fex_puntoventa;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        try {
            $sql = '';
            foreach ($respuesta as $puntero) {
                $sql .= "INSERT INTO fex_puntoventa (fexcompany,idpuntoventa,descripcion,idsucursal) VALUES (";
                $sql .= "'{$pempresa}','{$puntero->U_CodPuntoVenta}','{$puntero->U_NombrePtoVenta}','{$puntero->U_CodSucursal_SIN}');";
            }
            $db->createCommand($sql)->execute();
        } catch (\Exception $e) {
            Yii::error("Error en sincronizacion de TIPOPUNTOVENTA ",$e);
        }

    }

    public function CargaFexSucursal($empresa){
        Yii::error("SINCRONIZA @SUCURSALES : ");
        $odbc = new Sincronizar();
        $serviceLayer = new Servislayer();
        $data = json_encode(array("accion" => 1302,"empresafex"=> $empresa));
        $respuesta = $odbc->executex($data);
        Yii::error("respuesta : ".$respuesta);
        $respuesta = json_decode($respuesta);
        $db = Yii::$app->db;

        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE fex_sucursal;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        
        try {
            $sql = '';
            foreach ($respuesta as $puntero) {
                $sql .= "INSERT INTO fex_sucursal (DocEntry,Code,NumSucursal,NombreSucursal,Ubicacion,direccion,telefono) VALUES (";
                $sql .= "{$puntero->Code},'{$puntero->U_CodSucursal_SIN}','{$puntero->U_CodSucursal_SIN}','{$puntero->U_Municipio}','{$puntero->U_Municipio}','{$puntero->U_Direccion}','{$puntero->U_NIT}');";
            }
            //Yii::error("INSERT PRUEBA : ".$sql);
            $db->createCommand($sql)->execute();
        } catch (\Exception $e) {
            Yii::error("Error en sincronizacion de @SUCURSALES",$e);
        }

    }
    public function Consulta_cuf_sap($iddoc,$nit,$accion) {
        try {
            $serviceLayer = new Sincronizar();
            Yii::error("envia CONSULTA CUF EN SAP ".$iddoc." ".$nit." ".$accion);
            $data = json_encode(array("accion" => 1400,"iddoc"=>$iddoc,"nit"=>$nit,"acc"=>$accion));
            
            $respuesta = $serviceLayer->executex($data);
            Yii::error("respuesta : ".$respuesta);
            $respuesta = json_decode($respuesta);
            
            return $respuesta;
        } catch (\Throwable $e) {
            //$transaction->rollBack();
            //$this->insertLog2('sincroniza', 'pedidos Cuerpo', $e);
            throw $e;
        }
    }

    // fin facturacion electronica
    private function ObtenerRelUnidMedidaGrupo(){
        Yii::error("SINCRONIZA ODBC UnidMedidaGrupo : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 40));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE unidadmedidaxgrupo; SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $respuesta = json_decode($respuesta);
            foreach ($respuesta as $punteroSub) {
                $grupo = $punteroSub->UgpEntry;
                $unidad = $punteroSub->UomEntry;
                $unidadbase = $punteroSub->AltQty;
                $unidadcantidad = $punteroSub->BaseQty;
                $sqlSub = "";
                $sql = " Insert into unidadmedidaxgrupo(UgpEntry,UomEntry,AltQty,BaseQty) values('" . $grupo . "','" . $unidad . "','" . $unidadbase . "','".$unidadcantidad."')";
                Yii::$app->db->createCommand($sql)->execute();
                //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', 'bancos', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'UnidadMedidaGrupo', $e);
        }
    }

    public function Metodocualquiera(){
        Yii::error("Entra aqui ....!!!");
        $this->obtenerClientesODBC();
    }    
}
