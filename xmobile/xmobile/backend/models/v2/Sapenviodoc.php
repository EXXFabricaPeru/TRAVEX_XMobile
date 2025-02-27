<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\Configlayer;
use backend\models\Servislayer;
use backend\models\Sincronizar;
use backend\models\Gestionbancos;
use backend\models\Cabeceradocumentos;
use backend\models\Clientes as modelcli;
use backend\models\Lotes;
use backend\models\Seriesproductos;
use backend\models\Seriesmarketing;
use backend\models\Unidadesmedida;
use backend\models\TraspasosCabecera;
use backend\models\TraspasosDetalle;
use backend\models\TraspasosLote;
use backend\models\TraspasosSerie;
use backend\models\Productosalmacenes;
use backend\models\Contactos;
use backend\models\Clientessucursales;
use backend\models\Anulaciondocmovil;
use backend\models\v2\Productos;
use backend\models\v2\Documentos;
use backend\models\hana;

class Sapenviodoc extends Model {

    /**
     * @var Servislayer $model
     */
    private $model;
    private $model_odbc;
    //////////////
    //public $IT = 3;
    public $id;

    public function __construct() {
        $this->model = new Servislayer();
        $this->model_odbc = new Sincronizar();
    }

   /*modelo pago de varias facturas tipo otpp=2 */

/*public function updateDoc($id, $est) {
  $sql = "UPDATE cabeceradocumentos SET estado = '" . $est . "' WHERE id = " . $id . ";";
  Yii::$app->db->createCommand($sql)->execute();
}
*/
public function documento($idDocPedido='') {

    Yii::error('inicio  documento');
    $serviceLayer = new Servislayer();
    $miODBC=new Sincronizar();

    if($idDocPedido==''){
        $pedidos = Cabeceradocumentos::find()
            ->where("DocEntry is null and DocTotal > 0 AND DocType != 'DFA' AND (estado = 2 OR estado = 6)")
            ->with('detalledocumentos')
            ->limit(150)
            ->asArray()
            ->all();
    }
    else{
        $pedidos = Cabeceradocumentos::find()
            ->where("DocEntry is null and DocTotal > 0 AND id = '".$idDocPedido."' AND (estado = 2 OR estado = 6)")
            ->with('detalledocumentos')
            //->limit(150)
            ->asArray()
            ->all();
    }
    if (count($pedidos)>0) {
        foreach ($pedidos as $pedido) {
            switch ($pedido["DocType"]) {
                case 'DOF':
                    $serviceLayer->actiondir = "Quotations";
                    break;
                case 'DOP':
                    $serviceLayer->actiondir = "Orders";
                    break;

                default:
                    $mensaje="Error! no existe tipo de documento";
                    Yii::error($mensaje);
                    $arr = [
                        "estado" =>0,
                        "anulado" => 0,
                        "codigoDoc" =>$pedido["idDocPedido"] ,
                        "numeracion"=>0,
                        "registro"=>false,
                        "mensaje"=>$mensaje
                    ];
                    return $arr;
                    break;
            }
            // consulta si el usuario esta chekeado en zona franca
            $dataZonaFranca = Yii::$app->db->createCommand("SELECT zonaFranca FROM usuarioconfiguracion WHERE idUser=". $pedido["idUser"])->queryOne();

            if($dataZonaFranca['zonaFranca']==1) $IT=0;
            else $IT=3;

            //$empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadempleado where idUser = {$pedido["idUser"]}")->queryOne();
            $auxq="select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '".$pedido["SlpCode"]."'";
            Yii::error('inicio  pedido:'.$pedido["CardCode"]." ".$auxq);
            $empleadoCiudad = Yii::$app->db->createCommand($auxq)->queryOne();

            $usuarioxm = Yii::$app->db->createCommand("select nombreusuario from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
            $equipoxm = Yii::$app->db->createCommand("select equipo from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
            $sucursalxm = Yii::$app->db->createCommand("select nombresucursal from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();

            //direccion de facturacion- desde aqui busca datos
            $billToAddress="select AddresName,Street,State,u_territorio,u_zona,u_vendedor,SalesEmployeeName from clientessucursales
            left join vendedores on SalesEmployeeCode = u_vendedor where CardCode='".$pedido['CardCode']."' and AdresType='B' and RowNum='".$pedido['Address']."' order by RowNum desc limit 1 ;";
            $dataBillToAddress=Yii::$app->db->createCommand($billToAddress)->queryOne();
            Yii::error("Consulta de direccion Cobro: ".$billToAddress);
            Yii::error("data bill toaddres". json_encode($dataBillToAddress));
            // direccion de envio -- campos no definidos en companex ,City,County
            $shipToAddress="select AddresName,Street,State,u_territorio,u_zona,u_vendedor,SalesEmployeeName from clientessucursales
            left join vendedores on SalesEmployeeCode = u_vendedor  where CardCode='".$pedido['CardCode']."' and AdresType='S' and RowNum='".$pedido['Address']."' order by RowNum desc limit 1 ;";
            $dataShipToAddress=Yii::$app->db->createCommand($shipToAddress)->queryOne();
            Yii::error("Consulta de direccion Envio: ".$shipToAddress);
            Yii::error("data ship toaddres". json_encode($dataShipToAddress));
            ////fin de consulta facturacion

            // FACTURACION ELECTRONICA PARA PEDIDOS * CASO COMPANEX

            $q_uso_fex = "SELECT fex FROM equipox WHERE id =".$pedido["equipoId"];
            $resp_uso_fex = Yii::$app->db->createCommand($q_uso_fex)->queryOne();
            $uso_fex= $resp_uso_fex["fex"];

            if($uso_fex==1){
                $q_cuf="SELECT fex_sucursal,fex_puntoventa FROM lbcc WHERE equipoId ='".$pedido["equipoId"]."' and status=1 limit 1";
                //$q_cuf="SELECT * FROM fex_cufd WHERE codigocontrol ='".$factura->U_LB_NumeroAutorizac."'";
                $resp_cuf= Yii::$app->db->createCommand($q_cuf)->queryOne();
                //$fex_sucursal= $resp_cuf["sucursal"];
                //$fex_puntoventa= $resp_cuf["puntoventa"];
                $fex_sucursal= $resp_cuf["fex_sucursal"];
                $fex_puntoventa= $resp_cuf["fex_puntoventa"];
                //$fex_metodoPago=Sapenviodoc::metodoPagoFEx($factura->id,$factura->PayTermsGrpCode);
                $fex_tipoDocumento=Sapenviodoc::obtenertipodocumentofexPedido($pedido);
                //$sqlSeries="select U_series as series from vi_fexlbcc where equipoId = '{$factura->equipoId}'  and codigoSIN='{$fex_tipoDocumento}' ";
                //$xtipofactura = Yii::$app->db->createCommand($sqlSeries)->queryOne();
                //Yii::error("Serie de documento:");
                //Yii::error($xtipofactura['series']);

            }else{
                $fex_sucursal= 0;
                $fex_puntoventa= 0;
                $fex_metodoPago=1;
            }
            // FIN FACTURACION ELECTRONICA PARA PEDIDOS
            Yii::error('objeto pedido envio MID---->: '.json_encode($pedido));

            $datos = [
                "CardCode" => $pedido["CardCode"],
                "DocDate" => $pedido->DocDate,
                "TaxDate" => $pedido->TaxDate,
                "DocDueDate" => $pedido["DocDueDate"],
                "DocType" => $pedido["DocType"],
                "CardName" => $pedido["CardName"],
                "Comments"=> $pedido["Comments"],
                "Address2" =>  $dataShipToAddress && $dataShipToAddress['Street']?$dataShipToAddress['Street']:'',//envio
                "Address" => $dataBillToAddress && $dataBillToAddress['Street']?$dataBillToAddress['Street']:'',//cobro
                // "DocTotal" => $pedido["DocTotalPay"],
                "PaymentGroupCode" => $pedido["PayTermsGrpCode"],
                "SalesPersonCode" => $pedido["SlpCode"],                
                "U_xMOB_Usuario"=> $pedido["idUser"],
                "ShipToCode"=> $dataShipToAddress && $dataShipToAddress['AddresName']?$dataShipToAddress['AddresName']:'',//direccion de envio
                "PayToCode"=>$dataBillToAddress && $dataBillToAddress['AddresName']?$dataBillToAddress['AddresName']:'',//direccion de facturacion - cobro
                "BPL_IDAssignedToInvoice"=> $equipoxm["BPLid"],
                "BPLName"=> $equipoxm["BPLName"],
                //datos nit y razon socila
                "U_LB_NIT" => $pedido['U_4NIT'],
                "U_LB_RazonSocial" => $pedido['U_4RAZON_SOCIAL'],
                /////////////////////////////////////////////
                "U_xMOB_Equipo"=> $pedido["equipoId"],
                "U_xMOB_GCardSerie"=> $pedido["giftcard"],
                "U_xMOB_GCardMonto"=> $pedido["giftcard"],
                "U_xMOB_Comentario"=> $pedido["Comments"],
                "U_xMOB_Codigo"=> $pedido["idDocPedido"],
                "U_xMOB_Usuario" => $usuarioxm["nombreusuario"],
                "U_xMOB_Equipo" => $equipoxm["equipo"],
                "U_xMOB_Sucursal" => $sucursalxm["nombresucursal"],
                // fectura electronica companex
                "U_EXX_FE_Sucursal" => $fex_sucursal,
                "U_EXX_FE_PuntoVenta" => $fex_puntoventa,
                "U_EXX_FE_CodDocSector" => $fex_tipoDocumento,
                // fin factura electronica companex
                // campañas
                "U_XM_CampUsa"=>$pedido["monto"],
                "U_XM_Campana"=>$pedido["campania"],
                // fin campañas
                "U_XM_Autorizacion" => Sapenviodoc::ObtenerAutorizacion($pedido["detalledocumentos"],'pedido')
            ];
            // se verifica en la configuracion si la series de pedido si esta activo y la configuracion de usuario en series sea diferente de null)
            $configGeneral = Yii::$app->db->createCommand("SELECT valor FROM `configuracion` WHERE estado = 1 and parametro = 's_pedido'")->queryOne();

            switch ($pedido["DocType"]) {
                case 'DOF':
                    $configUsuario = Yii::$app->db->createCommand("SELECT seriesOferta FROM usuarioconfiguracion WHERE idUser=". $pedido["idUser"])->queryOne();
                    Yii::error("Configuracion usuario usa series pedido: ".$configUsuario['seriesOferta']);
                    if($configGeneral['valor']==1 and $configUsuario['seriesOferta']!=null){
                       Yii::error(" Usa SERIES de la configuracion del usuario: ");
                       $datos["Series"] = $configUsuario['seriesOferta'];
                    }
                    break;
                case 'DOP':
                    $configUsuario = Yii::$app->db->createCommand("SELECT seriesPedido FROM usuarioconfiguracion WHERE idUser=". $pedido["idUser"])->queryOne();
                    Yii::error("Configuracion usuario usa series pedido: ".$configUsuario['seriesPedido']);
                    if($configGeneral['valor']==1 and $configUsuario['seriesPedido']!=null){
                       Yii::error(" Usa SERIES de la configuracion del usuario: ");
                       $datos["Series"] = $configUsuario['seriesPedido'];
                    }
                    break;
            }

            Yii::error('campos USUARIO ******:'. json_encode($datos));
            $auxClone = $pedido["clone"];
            $aLineas = [];
            
            /// centro de costos mau
            $hana=new hana;
            $aux_cc_c1="SELECT * from vendedores where SalesEmployeeCode=".$pedido["SlpCode"];
            $respuesta_aux_cc_c1 = Yii::$app->db->createCommand($aux_cc_c1)->queryOne();
            $aux_cc1=$respuesta_aux_cc_c1["U_Regional"];
            $aux_cc2=$respuesta_aux_cc_c1["U_Area"];


            // $aux_cc_c3='Select CC."U_Centrodecosto",C."CardCode" from "OCRD" C left join "@SUB_CANAL" CC on C."U_XM_Subcanal"= CC."Code" where C."CardCode"=\''.$pedido["CardCode"].'\';';

            // $respuesta_aux_cc_c3 =$hana->ejecutarconsultaOne($aux_cc_c3);
            // $aux_cc3=$respuesta_aux_cc_c3["U_Centrodecosto"];

            // Yii::error("CENTRO DE COSTO 3  DATOS CLIENTE: ".$pedido["idDocPedido"]." - ".$aux_cc3);

            /// fin centro de costos mau
            $sum_aux_linetotalpay=0;
            foreach ($pedido["detalledocumentos"] as $lineaPedido) {
                $lote = [];
                $lote=Sapenviodoc::LoteCode($lineaPedido["ItemCode"],$lineaPedido["Quantity"], $pedido["idDocPedido"],$lineaPedido["unidadid"],$lineaPedido["WhsCode"]);

                // $unidadNegociox = $this->unidadNegocio($lineaPedido["ItemCode"]);$lineaP->unidadid,$lineaP->WhsCode

                /// inicio datos centros de costo

                /*
                $aux_cc_c3="SELECT * from clientes where CardCode='".$pedido["CardCode"]."'";
                $respuesta_aux_cc_c3 = Yii::$app->db->createCommand($aux_cc_c3)->queryOne();
                $data_aux_cc_c3 = json_encode(array("accion" => 400,"codeSubCanal"=>$respuesta_aux_cc_c3["codesubcanal"]));
                $respuesta_c3 = $miODBC->executex($data_aux_cc_c3);
                $respuesta_c3 = json_decode($respuesta_c3);
                foreach ($respuesta_c3 as $key)  $aux_cc3=$key->U_Centrodecosto;
                */

                // $aux_cc_c4='select I."U_Marca",I."ItemCode",M."U_Centrodecosto"  from "OITM" I left join "@MARCA" M on I."U_Marca"=M."Code" where  I."ItemCode"= \''.$lineaPedido["ItemCode"].'\';';
                // $respuesta_aux_cc_c4 =$hana->ejecutarconsultaOne($aux_cc_c4);
                // $aux_cc4=$respuesta_aux_cc_c4["U_Centrodecosto"];
                // Yii::error("CENTRO DE COSTO 4  DATOS PRODUCTO: ".$pedido["idDocPedido"]." - ".$aux_cc4);
                /// fin obtencion de centros de costo
                $aux_linetotalpay=(($lineaPedido["Quantity"]*$lineaPedido["Price"])-$lineaPedido["U_4DESCUENTO"]);
                $sum_aux_linetotalpay= $sum_aux_linetotalpay+$aux_linetotalpay;
                $gastosAdicionales=Sapenviodoc::gastosAdicionalesLinea($lineaPedido["ICEE"],$lineaPedido["ICEP"],$aux_linetotalpay,$aux_cc1,$aux_cc2,/*$aux_cc3*/"",/*$aux_cc4*/"",$IT);
                // $gastosAdicionales=[];
                if($lineaPedido["U_4DESCUENTO"]>0){
                    $descuento=round(($lineaPedido["U_4DESCUENTO"]*100/($lineaPedido["Quantity"]*$lineaPedido["Price"])),6);
                }else{
                    $descuento=0;
                }
                $aux_precioUnitario=Round((($lineaPedido["Price"]*$lineaPedido["Quantity"])*0.87),4);
                $aux_precioUnitario=Round(($aux_precioUnitario/$lineaPedido["Quantity"]),4);
                Yii::error('campos USUARIO ******:'. json_encode($datos));

                $linea = [
                    "DocNum" => $lineaPedido["DocNum"],
                    "LineNum " => $lineaPedido["LineNum"],
                    "ItemCode" => $lineaPedido["ItemCode"],
                    "Dscription" => $lineaPedido["Dscription"],
                    "Quantity" => $lineaPedido["Quantity"],
                    "SalesPersonCode" => $pedido["SlpCode"],
                    "Price" =>  $lineaPedido["Price"],
                    // "Price" =>  $aux_precioUnitario,
                    "GrossPrice" => $lineaPedido["Price"],
                    "DiscountPercent" =>$descuento,
                    "GrossTotal" => $lineaPedido["LineTotalPay"],
                    "Currency" => $lineaPedido["Currency"],
                    // zona_frnaca "TaxCode" => IVA_EXE
                    "TaxCode" => "IGV",
                            //"LineTotal" => $lineaPedido["LineTotal"],
                    "WarehouseCode" => $lineaPedido["WhsCode"],
                    "UoMEntry" => Sapenviodoc::unidadEntry($lineaPedido["unidadid"]),
                    // "BatchNumbers" => $lote,
                    // centros de costo
                    // "CostingCode"=>  $aux_cc1,
                    // "CostingCode2"=>  $aux_cc2,
                    // "CostingCode3"=>  $aux_cc3,
                    // "CostingCode4"=>  $aux_cc4,
                    // "COGSCostingCode"=>  $aux_cc1,
                    // "COGSCostingCode2"=>  $aux_cc2,
                    // "COGSCostingCode3"=>  $aux_cc3,
                    // "COGSCostingCode4"=>  $aux_cc4,
                    // fin centros de costo
                    // "U_XM_Bonif"=>$lineaPedido["bonificacion"],
                    // "U_XM_CodeBonif"=>$lineaPedido["codeBonificacionUse"],
                    // "U_XM_ListaPrecios"=>$lineaPedido["listaPrecio"],
                    // "DocumentLineAdditionalExpenses" =>$gastosAdicionales
                ];
                //verificando check de zona franca
                // if($dataZonaFranca['zonaFranca']=='1'){
                //     $linea["TaxCode"] = "IVA_EXE";
                // }
                // else{
                //     $linea["TaxCode"] = "IVA";
                // }
                if (intval($lineaPedido["BaseEntry"])!=0){
                    $linea["BaseEntry"]=intval($lineaPedido["BaseEntry"]);
                    $linea["BaseLine"]=intval($lineaPedido["BaseLine"]);
                    $linea["BaseType"]=intval($lineaPedido["BaseType"]);

                }else{
                    Yii::error('objeto entrega linea cont: clon dfo2 '.$auxClone);
                    if ($auxClone != '0'){
                        /*
                        if(is_null($lineaPedido["BaseEntry"])){
                        $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne();
                        $aux_doc_org = $aux_doc_org["DocEntry"];
                        }else{
                            $aux_doc_org=intval($lineaPedido["BaseEntry"]);
                        }
                        */
                        $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}' and DocEntry!='' ")->queryOne();
                        $aux_doc_org = $aux_doc_org["DocEntry"];
                        Yii::error('objeto entrega linea cont: clon dfo2  docentry'.$aux_doc_org);
                        $aux_doctipo= Yii::$app->db->createCommand("select DocType from cabeceradocumentos where idDocPedido = '{$auxClone}' and DocType!='' ")->queryOne();
                        $aux_doctipo  = $aux_doctipo["DocType"];
                        Yii::error('objeto pedido: clon '.$auxClone);
                        //OBTENIENDO EL ID DE UNIDAD DE MEDIDA//
                        $idUnidadM = $lineaPedido["unidadid"];
                        $sqlUnidadMedida = Yii::$app->db->createCommand("SELECT AbsEntry from unidadesmedida where Code = '{$idUnidadM}'")->queryOne();
                        $idUnidadM=$sqlUnidadMedida["AbsEntry"];
                        //FIN CONSULTA ID UNIDAD MEDIDA//
                        $documentosV2= new Documentos;
                            switch ($aux_doctipo){
                                case 'DOF':
                                    $aux_doc_base="23";
                                    Yii::error("LineNum_detalle_oferta");
                                    $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaPedido["ItemCode"],$lineaPedido["Quantity"],$aux_doctipo,$idUnidadM);
                                break;
                                case 'DOP':
                                    $aux_doc_base="17";
                                    Yii::error("LineNum_detalle_pedidos");
                                    $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaPedido["ItemCode"],$lineaPedido["Quantity"],$aux_doctipo,$idUnidadM);
                                break;
                                case 'DFA':
                                    $aux_doc_base="13";
                                    Yii::error("LineNum_detalle_faturas");
                                    $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaPedido["ItemCode"],$lineaPedido["Quantity"],$aux_doctipo,$idUnidadM);
                                break;
                                case 'DOE':
                                    $aux_doc_base="15";
                                    Yii::error("LineNum_detalle_entrga");
                                    $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaPedido["ItemCode"],$lineaPedido["Quantity"],$aux_doctipo,$idUnidadM);
                                    /*
                                        $resultado = $sap->LineNum_detalle_entrga($aux_doc_org,$lineaP["ItemCode"],$lineaP["ItemCode"],$lineaP["Quantity"]);
                                    */
                                break;
                            }
                             //esto reemplaza xm1
                            if (!is_null($lineaPedido["BaseLine"])){
                                $linea["BaseEntry"]=$aux_doc_org;
                               // $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                                if($resultado >= 0){
                                    $linea["BaseLine"]=$resultado;
                                }else{
                                    $linea["BaseLine"]=intval($lineaPedido["BaseLine"]);
                                }

                                $linea["BaseType"]=$aux_doc_base;
                            }else{
                                $linea["BaseType"]=-1;
                            }
                      //xm1
                        /*$linea["BaseEntry"]=$aux_doc_org;
                        $linea["BaseLine"]=intval($lineaPedido["BaseLine"]);
                        $linea["BaseType"]=$aux_doc_base;
                        if(is_null($lineaPedido["BaseLine"])){
                            $linea["BaseEntry"]="";
                            $linea["BaseLine"]="";
                            $linea["BaseType"]="";
                        }*/
                    // fin xm1
                    }
                    /*
                     $aux_precioUnitario=Round((($lineaP->Price*$lineaP->Quantity)*0.87),2);
                     $aux_precioUnitario=Round(($aux_precioUnitario/$lineaP->Quantity),4);
                     $linea["UnitPrice"]= $aux_precioUnitario;
                        // "UnitsOfMeasurment"=> 1,
                        // $linea["UnitsOfMeasurment"]=1;
                     $totalIT += round(($lineaP->LineTotalPay * self::IT) / 100, 2);
                        //if($lineaP->LineTotal>0)
                            array_push($aLineas, $linea);

                        $lineNumber++
                    */
                }
                array_push($aLineas, $linea);
            }
            $datos["DocumentLines"] = $aLineas;
            // $datos["AddressExtension"]=[
            //     "ShipToStreet"=> $dataShipToAddress && $dataShipToAddress['Street']?$dataShipToAddress['Street']:'',
            //     // "ShipToCity"=> $dataShipToAddress && $dataShipToAddress['City']?$dataShipToAddress['City']:'',
            //     "ShipToCounty"=> $dataShipToAddress && $dataShipToAddress['County']?$dataShipToAddress['County']:'',
            //     "ShipToState"=> $dataShipToAddress && $dataShipToAddress['State']?$dataShipToAddress['State']:'',
            //     "ShipToCountry"=>  'BO',


            //     "BillToStreet"=> $dataBillToAddress && $dataBillToAddress['Street']?$dataBillToAddress['Street']:'' ,
            //     // "BillToCity"=> $dataBillToAddress && $dataBillToAddress['City']?$dataBillToAddress['City']:'',
            //     "BillToCounty"=> $dataBillToAddress && $dataBillToAddress['County']?$dataBillToAddress['County']:'',
            //     "BillToState"=> $dataBillToAddress && $dataBillToAddress['State']?$dataBillToAddress['State']:'',
            //     "BillToCountry"=> 'BO',
            // ];

            // $datos["DocumentAdditionalExpenses"]=Sapenviodoc::GastosAdicionalesCab($sum_aux_linetotalpay,$IT);
            Yii::error('objeto pedido envio a sap: '.json_encode($datos));
            $respuesta = $serviceLayer->executePost($datos); //$serviceLayer->executePost(json_encode($datos));
            Yii::error('respuesta pedido: '.json_encode($respuesta));
            try {

                if (isset($respuesta->DocEntry)) {
                    $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
                    $actualizaPedido->DocEntry = $respuesta->DocEntry;
                    $actualizaPedido->estado = 3;
                    $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz')->format('Y-m-d');
                    $actualizaPedido->DocNumSAP = $respuesta->DocNum;
                    $actualizaPedido->save(false);

                    $estado=3;
                    $mensaje="Documento enviado a SAP";
                    $registro=true;

                    //Yii::error("ID-MID:{$pedido["id"]};DATA-" . $respuesta->DocEntry);
                    //if($idDocPedido!='') return true; //repuesta si el pedido es envio desde el xmobile directo

                } else {
                        $estado=2;
                        $mensaje=json_encode($respuesta->error->message->value);
                        $registro=false;
                        Sapenviodoc::guardarlog($datos,$respuesta,'Pedido',$pedido["idDocPedido"]);
                    //if($idDocPedido!='') return $respuesta; //repuesta si el pedido es envio desde el xmobile directo

                }
                $arr = [
                    "estado" =>$estado,
                    "anulado" => 0,
                    "codigoDoc" =>$pedido["idDocPedido"] ,
                    "numeracion"=>0,
                    "registro"=>$registro, //control solo Midd tru=se registro y false no se registro
                    "mensaje"=>$mensaje
                ];
            } catch (\Exception $e) {
                Yii::error('PAGOS-ERROR'.$e->getMessage());
                Sapenviodoc::guardarlog($datos,$respuesta,'Pedido',$pedido["idDocPedido"]);
                $arr = [
                    "estado" =>2,
                    "anulado" => 0,
                    "codigoDoc" =>$pedido["idDocPedido"] ,
                    "numeracion"=>0,
                    "registro"=>false, //control solo Midd tru=se registro y false no se registro
                    "mensaje"=>$e->getMessage()
                ];
            }
        }
    }
    else{
        $arr = [
            "estado" =>0,
            "anulado" => 0,
            "codigoDoc" =>0,
            "numeracion"=>0,
            "registro"=>false, //control solo Midd tru=se registro y false no se registro
            "mensaje"=>"Error! volver a enviar el Documento"
        ];
    }
    return $arr;
    Yii::error('fin');
}

public function facturas($idDocPedido='') {

    Yii::error('inicio  facturas');
    //cancelando documentos duplicados
    Yii::$app->db->createCommand('call cancelarDocumentosDuplicados();')->execute();

    $serviceLayer = new Servislayer();
    $miODBC=new Sincronizar();

    $serviceLayer->actiondir = "Invoices";

    if($idDocPedido==''){
        $facturas = Cabeceradocumentos::find()
        ->where("DocEntry is null and DocTotal > 0 AND DocType = 'DFA' AND (estado=2)")
        ->with('detalledocumentos')
        ->groupBy('idDocPedido')
        ->having('count(*)=1')
        ->limit(100)
        ->all();
    }
    else{
        $facturas = Cabeceradocumentos::find()
        ->where("DocEntry is null and DocTotal > 0 AND id = '".$idDocPedido."' AND (estado = 2)")
        ->with('detalledocumentos')
        //->groupBy('idDocPedido')
        //->having('count(*)=1')
        //->limit(100)
        ->all();
    }

    Yii::error('Listo para el count');

    if (count($facturas)>0) {

        foreach ($facturas as $key => $factura) {

            Yii::error('factura a mandarse: ' . ($factura->idDocPedido) . "=======================================================================");
            $aLineas = [];
            $lineNumber = 0;
            $totalIT = 0;
            // consulta si el usuario esta chekeado en zona franca
            $dataZonaFranca = Yii::$app->db->createCommand("SELECT zonaFranca FROM usuarioconfiguracion WHERE idUser=". $factura["idUser"])->queryOne();

            if($dataZonaFranca['zonaFranca']==1) $IT=0;
            else $IT=3;

            $empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '{$factura->SlpCode}'")->queryOne();
            // se verifica en la configuracion que valor tiene es 1 o 0 si es 1 se consulta a la tabla series (exclusivo para companex)
            $verifica = Yii::$app->db->createCommand("SELECT valor FROM `configuracion` WHERE estado = 1 and parametro = 'lbcc'")->queryOne();
            if($verifica['valor']==0){
              Yii::error(" SERIE LBCC: ");
               $tipofactura=Yii::$app->db->createCommand("select U_series as series from lbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}'")->queryOne();
               //$tipofactura=Yii::$app->db->createCommand("select U_series as series from lbcc where equipoId = '{$factura->equipoId}' ")->queryOne();
            }else{
              Yii::error("SERIE SERIES:");
               $periodoMes=date('Y-m');
               $tipofactura=Yii::$app->db->createCommand("SELECT Series as series from series WHERE series.Document=13 and series.DocumentSubType='--' and series.Remarks='{$periodoMes}'")->queryOne();//series.PeriodIndicator
            }
            Yii::error($tipofactura['series']);

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
            
            $aux_indicador=0;
            $tipoDocCliente=Yii::$app->db->createCommand("select cliente_std3 from clientes where CardCode='{$factura->CardCode}'")->queryOne();
            // //------formas de pago
            // $sqlPayment="select * from condicionespagos where GroupNumber='".$factura->PayTermsGrpCode."'";
            // $dataPaymentAux=Yii::$app->db->createCommand($sqlPayment)->queryOne();
            // Yii::error("payment data".json_encode($dataPaymentAux));

            // // Yii::error("tipo de cliente: ".$tipoDocCliente["cliente_std3"]);
            // // if($tipoDocCliente["cliente_std3"]==6){ //factura
            // //     $aux_indicador="01";
            // //     $tipofactura=Yii::$app->db->createCommand("select U_series from lbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}' and Tipo = 2 ")->queryOne();
            // //     $seriesName=Yii::$app->db->createCommand("select U_SeriesName from lbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}' and Tipo = 2 ")->queryOne();
            // // }else{// boleta
            // //     $aux_indicador="03";
            // //     $tipofactura=Yii::$app->db->createCommand("select U_series  from lbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}' and Tipo = 3")->queryOne();
            // //     $seriesName=Yii::$app->db->createCommand("select  U_SeriesName from lbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}' and Tipo = 3")->queryOne();
            // // }

            $cgfcard=$factura["giftcard"];
            if(($cgfcard!="") OR ($cgfcard!=0)){
                $monto_giftcard=explode("+",$cgfcard);
                $monto_giftcard=$monto_giftcard[2];
            }else{
                $monto_giftcard=0;
            }

            $vencimiento = Yii::$app->db->createCommand("select (NumberOfAdditionalDays+(30*NumberOfAdditionalMonths)) as dias  from condicionespagos  where GroupNumber = '{$factura->PayTermsGrpCode}'")->queryOne();
            $vencimiento =$vencimiento["dias"];
            $aux_vencimiento=Carbon::today('America/La_Paz')->addDays($vencimiento)->format('Y-m-d');

            // datos FEX
            // FEX?
            // $q_uso_fex="SELECT * FROM configuracion WHERE parametro ='FEX'";
            $q_uso_fex = "SELECT fex FROM equipox WHERE id =".$factura->equipoId;
            $resp_uso_fex = Yii::$app->db->createCommand($q_uso_fex)->queryOne();
            $uso_fex= $resp_uso_fex["fex"];

            if($uso_fex==1){
                $q_cuf="SELECT fex_sucursal,fex_puntoventa FROM lbcc WHERE equipoId ='".$factura->equipoId."' and status=1 limit 1";
                //$q_cuf="SELECT * FROM fex_cufd WHERE codigocontrol ='".$factura->U_LB_NumeroAutorizac."'";
                $resp_cuf= Yii::$app->db->createCommand($q_cuf)->queryOne();
                //$fex_sucursal= $resp_cuf["sucursal"];
                //$fex_puntoventa= $resp_cuf["puntoventa"];
                $fex_sucursal= $resp_cuf["fex_sucursal"];
                $fex_puntoventa= $resp_cuf["fex_puntoventa"];
                $fex_metodoPago=Sapenviodoc::metodoPagoFEx($factura->id,$factura->PayTermsGrpCode);
                $fex_tipoDocumento=Sapenviodoc::obtenertipodocumentofex($factura);
                $sqlSeries="select U_series as series from vi_fexlbcc where equipoId = '{$factura->equipoId}'  and codigoSIN='{$fex_tipoDocumento}' ";
                $xtipofactura = Yii::$app->db->createCommand($sqlSeries)->queryOne();
                Yii::error("Serie de documento:");
                Yii::error($xtipofactura['series']);

            }else{
                $fex_sucursal= 0;
                $fex_puntoventa= 0;
                $fex_metodoPago=1;
            }

            $facturaSAP = [
              "DocDate" => $factura->DocDate,
              "TaxDate" => $factura->TaxDate,
              "DocDueDate" => $aux_vencimiento, // vencimiento
              "CardCode" => $factura->CardCode,
              "CardName" => $factura->CardName,
            //   "DocTotal" => $factura->DocTotalPay,
              "DocCurrency" => $factura->DocCur,
              "Comments"=> $factura["Comments"],
              "PaymentGroupCode" => $factura->PayTermsGrpCode,
              "SalesPersonCode" => $factura->SlpCode,              
              // zona_frnaca "TaxCode" => IVA_EXE
              //"TaxCode" => "IVA",
              "Series"=> 80,//$uso_fex==1?$xtipofactura['series']:$tipofactura['series'], //tipo de factura
              "Indicator"=> "01", //$aux_indicador,//"01",
              "FederalTaxID" => $factura->U_4NIT,
              "U_LB_NIT" => $factura['U_4NIT'],
              "U_LB_RazonSocial" => $factura['U_4RAZON_SOCIAL'],
              "U_LB_ObjType"=>13,
              //"BaseAmount" => round(($factura->DocTotalPay, 2), zf
              //"BaseAmount" => round(($factura->DocTotalPay - ($factura->DocTotalPay * 13) / 100), 2),
             // "DiscountPercent" => Sapenviodoc::ObtenerPorcentaje($factura->DocTotalPay, $factura->DocTotal),
              "WareHouseUpdateType" => Sapenviodoc::facturaReserva($factura->DocDueDate, $factura->Reserve) ? "dwh_CustomerOrders" : "dwh_Stock",
              "ReserveInvoice" => Sapenviodoc::facturaReserva($factura->DocDueDate, $factura->Reserve) ? "tYES" : "tNO",
              "U_LB_Bancarizacion" => $auxbanc1,
              "U_LB_ModalidadTransa"=> $auxbanc2,
              "U_LB_TipoTransaccion"=>$auxbanc3,
              "U_xMOB_Equipo" => $equipoxm["equipo"],
              "U_xMOB_GCardSerie"=> $factura["giftcard"],
              "U_xMOB_GCardMonto"=> $factura["giftcard"],
              "U_xMOB_Comentario"=> $factura["Comments"],
              "U_xMOB_Codigo"=> $factura["idDocPedido"],
              "U_xMOB_Usuario" => $usuarioxm["nombreusuario"],
              "U_xMOB_Sucursal" => $sucursalxm["nombresucursal"],
              "JournalMemo" =>"Xmobile Inv. ".$factura["idDocPedido"],
              "U_xMOB_Plataforma" => $plataforma["plataforma"],
              "U_xMOB_Venta1" =>$factura->xMOB_Venta1,
              "U_xMOB_Venta2" => $factura->xMOB_Venta2,
              "U_EXX_FE_Sucursal" => $fex_sucursal,
              "U_EXX_FE_PuntoVenta" => $fex_puntoventa,
              "U_EXX_FE_CodDocIden" =>  $factura["fex_tipodoc"],
              "U_EXX_FE_CodDocSector" => $fex_tipoDocumento,
              "U_EXX_FE_CodigoMetodoPago" => $fex_metodoPago,
              "U_EXX_FE_NumeroTarjeta" => $factura["codigotarjeta"],
              "U_EXX_FE_Email" => $factura["xMOB_Venta6"],
              //campos companex
              //"U_LB_FechaLimiteEmis" => $limiteEmision["U_FechaLimiteEmision"],
              //"U_LB_NumeroFactura" => $factura->UNumFactura,
              //"U_LB_CodigoControl" => $factura->ControlCode,
              //"U_LB_NumeroAutorizac" => $factura->U_LB_NumeroAutorizac,
              "U_XM_Autorizacion" => Sapenviodoc::ObtenerAutorizacion($factura->detalledocumentos,'factura'),
              // campañas
                    "U_XM_CampUsa"=>$factura["monto"],
                    "U_XM_Campana"=>$factura["campania"],
            // fin campañas
            ];
            //verificando check de zona franca
            // if($dataZonaFranca['zonaFranca']=='1'){
            //     $facturaSAP["TaxCode"] = "IVA_EXE";
            //     $facturaSAP["BaseAmount"] = round(($factura->DocTotalPay), 2);
            // }
            // else{
            //     $facturaSAP["TaxCode"] = "IVA";
            //     $facturaSAP["BaseAmount"] = round(($factura->DocTotalPay - ($factura->DocTotalPay * 13) / 100), 2);
            // }
             //Yii::error('campos USUARIO ******:'. json_encode($datos));
            // Yii::error('DETALLE DOCUMENTO =========>'. json_encode($factura->detalledocumentos));

            if($uso_fex==0){
                $facturaSAP["U_LB_FechaLimiteEmis"]=$limiteEmision["U_FechaLimiteEmision"];
                $facturaSAP["U_LB_NumeroFactura"]= $factura->UNumFactura;
                $facturaSAP["U_LB_CodigoControl"]= $factura->ControlCode;
                $facturaSAP["U_LB_NumeroAutorizac"] = $factura->U_LB_NumeroAutorizac;
            }

            /// centro de costos mau
            $hana=new hana;
            $aux_cc_c1="SELECT * from vendedores where SalesEmployeeCode=".$factura["SlpCode"];
            $respuesta_aux_cc_c1 = Yii::$app->db->createCommand($aux_cc_c1)->queryOne();
            $aux_cc1=$respuesta_aux_cc_c1["U_Regional"];
            $aux_cc2=$respuesta_aux_cc_c1["U_Area"];
            // $aux_cc_c3='Select CC."U_Centrodecosto",C."CardCode" from "OCRD" C left join "@SUB_CANAL" CC on C."U_XM_Subcanal"= CC."Code" where C."CardCode"=\''.$factura["CardCode"].'\';';
            // $respuesta_aux_cc_c3 =$hana->ejecutarconsultaOne($aux_cc_c3);
            // $aux_cc3=$respuesta_aux_cc_c3["U_Centrodecosto"];
            // Yii::error("CENTRO DE COSTO 3  DATOS CLIENTE: ".$factura["idDocPedido"]." - ".$aux_cc3);
            /// fin centro de costos mau

            $sum_aux_linetotalpay=0;
            $auxDetalleDoc=[];
            $index=0;
            foreach ($factura->detalledocumentos as $lineaP) {
              //if(intval($lineaP->Price) != 0){

                $lote = [];
                $lote=Sapenviodoc::LoteCode($lineaP->ItemCode,$lineaP->Quantity, $factura->idDocPedido,$lineaP->unidadid,$lineaP->WhsCode);
                //$unidadNegociox = $this->unidadNegocio($lineaP->ItemCode);
                /// inicio datos centros de costo

                // $aux_cc_c4='select I."U_Marca",I."ItemCode",M."U_Centrodecosto"  from "OITM" I left join "@MARCA" M on I."U_Marca"=M."Code" where  I."ItemCode"= \''.$lineaP["ItemCode"].'\';';
                // $respuesta_aux_cc_c4 =$hana->ejecutarconsultaOne($aux_cc_c4);
                // $aux_cc4=$respuesta_aux_cc_c4["U_Centrodecosto"];

                /*
                Yii::error("CENTRO DE COSTO 4  DATOS PRODUCTO: ".$pedido["idDocPedido"]." - ".$aux_cc4);
                $aux_cc_c4="SELECT * from productos where ItemCode='".$lineaP["ItemCode"]."'";
                $respuesta_aux_cc_c4 = Yii::$app->db->createCommand($aux_cc_c4)->queryOne();

                $data_aux_cc_c4 = json_encode(array("accion" => 401,"marca"=>$respuesta_aux_cc_c4["producto_std7"]));
                $respuesta_c4 = $miODBC->executex($data_aux_cc_c4);
                $respuesta_c4 = json_decode($respuesta_c4);

                foreach ($respuesta_c4 as $key)  $aux_cc4=$key->U_Centrodecosto;
                */
                // Yii::error("CENTRO DE COSTO 4  DATOS PRODUCTO: ".$factura["idDocPedido"]." - ".$aux_cc4);
                /// fin obtencion de centros de costo
                $aux_linetotalpay=(($lineaP->Quantity*$lineaP->Price)-$lineaP->U_4DESCUENTO);
                $sum_aux_linetotalpay=$sum_aux_linetotalpay+$aux_linetotalpay;
                $gastosAdicionales=Sapenviodoc::gastosAdicionalesLinea($lineaP->ICEE,$lineaP->ICEP,$aux_linetotalpay,$aux_cc1,$aux_cc2,/*$aux_cc3*/"",/*$aux_cc4*/"",$IT);
                // $gastosAdicionales=[];
                if($lineaP->U_4DESCUENTO > 0){
                    //$descuento=Round(($lineaP->U_4DESCUENTO*100/($lineaP->Quantity * $lineaP->Price)),6);
                    $descuento=($lineaP->U_4DESCUENTO*100/($lineaP->Quantity*$lineaP->Price));
                    $descuento=Round($descuento,6);
                }else{
                    $descuento=0;
                }

                //verificando check de zona franca
                if($dataZonaFranca['zonaFranca']=='1'){
                   $aux_precioUnitario=Round((($lineaP->Quantity*$lineaP->Price)),4);
                }
                else{
                   $aux_precioUnitario=Round((($lineaP->Quantity*$lineaP->Price)*0.87),4);
                }

                // $aux_precioUnitario=Round((($lineaP->Quantity*$lineaP->Price)),4); zf
                //$aux_precioUnitario=Round((($lineaP->Quantity*$lineaP->Price)*0.87),4);
                $aux_precioUnitario=Round(($aux_precioUnitario/$lineaP->Quantity),4);
                $linea = [
                    "BatchNumbers" => $lote,
                    "SerialNumbers" => Sapenviodoc::NumeroSerie($lineaP->ItemCode, $lineaP->Quantity, $factura->idDocPedido),
                    "ItemCode" => $lineaP->ItemCode,
                    "ItemDescription" => $lineaP->Dscription,
                    "Quantity" => $lineaP->Quantity,
                    //"SalesPersonCode" => $lineaP->SlpCode,
                    "Price" =>  $lineaP->Price,
                    // "Price" =>  $aux_precioUnitario,
                    // "GrossPrice" => $lineaP->Price,
                    "DiscountPercent" =>$descuento,
                    "Currency" =>$lineaP->Currency,
                    "TaxCode" => "IGV",
                    "WarehouseCode" => $lineaP->WhsCode,
                    "UoMEntry" => Sapenviodoc::unidadEntry($lineaP->unidadid),
                    "TreeType" => $lineaP->TreeType,
                    //"TaxPercentagePerRow" => 13.0, zf ya no va
                    "LineStatus" => "bost_Open",
                    //"OpenAmount" => round($lineaP->LineTotalPay - (($lineaP->LineTotalPay * 13) / 100), 2),
                    //"UoMEntry" => Sapenviodoc::unidadEntry($lineaP->unidadid),
                    "UoMCode" => $lineaP->unidadid,
                   // "GrossPrice" => $lineaP->Price,
                    //"GrossTotal" => $lineaP->LineTotalPay,
                    //"ShipDate" => Sapenviodoc::facturaReserva($lineaP->DocDueDate, $$lineaP->Reserve) ? $lineaP->DocDueDate : null,
                    //"BackOrder" => Sapenviodoc::facturaReserva($lineaP->DocDueDate, $$lineaP->Reserve) ? "tYES" : "tNO",
                    //"ActualDeliveryDate" => Sapenviodoc::facturaReserva($lineaP->DocDueDate,$lineaP->Reserve) ? null : Carbon::today('America/La_Paz')->format('Y-m-d'),
                    // "COGSCostingCode" => $empleadoCiudad['HomeState'],
                    //"CostingCode2" => $unidadNegociox,
                    "U_XM_ListaPrecios"=>$lineaP->listaPrecio,
                    // centros de costo
                    // "CostingCode"=>  $aux_cc1,
                    // "CostingCode2"=>  $aux_cc2,
                    // "CostingCode3"=>  $aux_cc3,
                    // "CostingCode4"=>  $aux_cc4,
                    // "COGSCostingCode"=>  $aux_cc1,
                    // "COGSCostingCode2"=>  $aux_cc2,
                    // "COGSCostingCode3"=>  $aux_cc3,
                    // "COGSCostingCode4"=>  $aux_cc4,
                    // fin centros de costo
                    "U_XM_Bonif"=>$lineaP->bonificacion,
                    "U_XM_CodeBonif"=>$lineaP->codeBonificacionUse,
                    /*
                    zf
                    "LineTaxJurisdictions" => [
                        [
                            "JurisdictionCode" => "IVA_EXE",
                            "TaxRate" => 0
                        ]
                    ],
                    */
                    // "LineTaxJurisdictions" => [
                    //     [
                    //         "JurisdictionCode" => "IVA",
                    //         "TaxRate" => 13.0
                    //     ]
                    // ],
                    // "DocumentLineAdditionalExpenses" =>$gastosAdicionales
                ];
                //verificando check de zona franca
                // if($dataZonaFranca['zonaFranca']=='1'){
                //     $IT=0;
                //     $linea["TaxCode"] = "IVA_EXE";
                //     $linea["LineTaxJurisdictions"] = [
                //         [
                //             "JurisdictionCode" => "IVA_EXE",
                //             "TaxRate" => 0
                //         ]
                //     ];
                // }
                // else{
                //     $linea["TaxCode"] = "IVA";
                //     $linea["TaxPercentagePerRow"] = 13.0;
                //     $linea["LineTaxJurisdictions"] = [
                //         [
                //             "JurisdictionCode" => "IVA",
                //             "TaxRate" => 0
                //         ]
                //     ];
                // }

                Yii::error("Valor de IT: ".$IT);

                if($fex_tipoDocumento=="14"){
                    // sacar de sap OITM where ItemCode= a itemcode
                    $campos="\"U_XM_ICEPorcentual\",\"U_XM_ICEEspecifico\",\"U_FE_AlicuotaporLitro\" ";
                    $condicion="where \"ItemCode\"='{$lineaP->ItemCode}'";
                    //$miaux_prod= Yii::$app->db->createCommand($sql_aux_prod)->queryOne();
                    Yii::error("OBTIENE CAMPOS PRODUCTOS ESPECIFICOS");
                    $productos=new Productos;
                    $miaux_prod=$productos->obtenerCamposEspecificos($campos,$condicion);
                    Yii::error($miaux_prod);
                    $linea["U_EXX_FE_AlicuotaPorcentual"]=$miaux_prod["U_XM_ICEPorcentual"];
                    $linea["U_EXX_FE_AlicuotaEspecifica"]=$miaux_prod["U_XM_ICEEspecifico"];
                    $linea["U_EXX_FE_AlicuotaLitro"]=$miaux_prod["U_FE_AlicuotaporLitro"];
                }
                if (intval($lineaP["BaseEntry"])!=0){
                    /*$linea["BaseEntry"]=intval($lineaP["BaseEntry"]);
                    $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                    $linea["BaseType"]=intval($lineaP["BaseType"]);
                    *///verificar doc importado clon factura
                    $resultVerificado=Sapenviodoc::verificandoLinea($lineaP["BaseEntry"],$lineaP,$auxDetalleDoc,$index,'DOP');
                    $linea+=$resultVerificado["linea"];
                    $auxDetalleDoc=$resultVerificado["auxDetalleDoc"];
                    $index=$resultVerificado["index"];
                }else{
                    $pos_dfo = strpos($auxClone,"DFO");
                    $pos_dfa = strpos($auxClone,"DFA");
                    $pos_dop = strpos($auxClone,"DOP");
                    $pos_doe = strpos($auxClone,"DOE");
                    Yii::error('objeto entrega linea cont: clon DFA '.$pos_dfo.' dfa '.$pos_dfa.' dop '.$pos_dop.' doe '.$pos_doe);
                    if(($auxClone != '0') and (($pos_dfo!=0) OR ($pos_dfa!=0) OR ($pos_dop!=0)  OR ($pos_doe!=0))) {
                        $auxClone='0' ;
                        //$linea["BaseType"]=13;
                        //$auxClone=$auxClone;//
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
                        $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}' and DocEntry!='' ")->queryOne();
                        $aux_doc_org = $aux_doc_org["DocEntry"];
                        $aux_doctipo= Yii::$app->db->createCommand("select DocType from cabeceradocumentos where idDocPedido = '{$auxClone}' and DocType!='' ")->queryOne();
                        $aux_doctipo  = $aux_doctipo["DocType"];
                        $resultVerificado=Sapenviodoc::verificandoLinea($aux_doc_org,$lineaP,$auxDetalleDoc,$index,$aux_doctipo);
                        $linea+=$resultVerificado["linea"];
                        $auxDetalleDoc=$resultVerificado["auxDetalleDoc"];
                        $index=$resultVerificado["index"];
                            // fin nuevo ajuste para el linenum //

                            //esto reemplaza xm1
                            /*if (!is_null($lineaP["BaseLine"])){
                                $linea["BaseEntry"]=$aux_doc_org;
                               // $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                                if($resultado >= 0){
                                    //$linea["BaseLine"]=$resultado;
                                    $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                                }else{
                                    $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                                }

                                $linea["BaseType"]=$aux_doc_base;
                            }else{
                                $linea["BaseType"]=-1;
                            }*/


                       /* $linea["BaseEntry"]=$aux_doc_org;
                        $linea["BaseLine"]=intval($lineaP["BaseLine"])
                        $linea["BaseType"]=$aux_doc_base;
                        if(is_null($lineaP["BaseLine"])){
                            $linea["BaseEntry"]="";
                            $linea["BaseLine"]="";
                            $linea["BaseType"]="";
                        }*/

                    }
                }

                $totalIT += round(($lineaP->LineTotalPay * $IT) / 100, 2);
                Yii::error("LINEA DE DOCUMENTO =======> " . json_encode($linea));
                array_push($aLineas, $linea);
                $lineNumber++;
              //}
            }

            $auxCuotas=Sapenviodoc::obtenercuotasFactura($factura->PayTermsGrpCode,$factura->DocTotalPay,$factura->DocDueDate);
            $facturaSAP["DocumentLines"] = $aLineas;
            $facturaSAP["DocumentInstallments"] =$auxCuotas;

            // $facturaSAP["DocumentAdditionalExpenses"]=Sapenviodoc::GastosAdicionalesCab($sum_aux_linetotalpay,$IT);
            Yii::error('FACTURA-ENVIO' . json_encode($facturaSAP));
            //echo 'FACTURA-ENVIO';
            //print_r($facturaSAP);
            $respuesta = $serviceLayer->executePost($facturaSAP);

            Yii::error('FACTURA-RESPUESTA' . json_encode($respuesta));
            try {
                if (isset($respuesta->DocEntry)) {
                    $factura->DocEntry = $respuesta->DocEntry;
                    $factura->estado = 3;
                    $factura->fechaupdate = Carbon::today('America/La_Paz');
                    $factura->DocNumSAP = $respuesta->DocNum;
                    $factura->save(false);

                    $estado=3;
                    $mensaje="Factura enviada a SAP";
                    $registro=true;
                    //if($idDocPedido!='') return true; //repuesta si la factura se envio desde el xmobile en linea

                } else {
                    $estado=2;
                    $mensaje=json_encode($respuesta->error->message->value);
                    $registro=false;

                    //Sapenviodoc::guardarlog($facturaSAP,$respuesta,'Factura',$factura["idDocPedido"]);
                    //if($idDocPedido!='') return $respuesta; //repuesta si el factura es envio desde el xmobile directo
                }
                Sapenviodoc::guardarlog($facturaSAP,$respuesta,'Factura',$factura["idDocPedido"]);
                $arr = [
                    "estado" =>$estado,
                    "anulado" => 0,
                    "codigoDoc" =>$factura["idDocPedido"] ,
                    "numeracion"=>0,
                    "registro"=>$registro, //control solo Midd tru=se registro y false no se registro
                    "mensaje"=>$mensaje
                ];
            } catch (\Exception $e) {
                Yii::error('PAGOS-ERROR'.$e->getMessage());
                Sapenviodoc::guardarlog($facturaSAP,$respuesta,'Factura',$factura["idDocPedido"]);
                $arr = [
                    "estado" =>2,
                    "anulado" => 0,
                    "codigoDoc" =>$factura["idDocPedido"] ,
                    "numeracion"=>0,
                    "registro"=>false, //control solo Midd tru=se registro y false no se registro
                    "mensaje"=>$e->getMessage()
                ];
            }
        }
    }
    else{
        $arr = [
            "estado" =>0,
            "anulado" => 0,
            "codigoDoc" =>0,
            "numeracion"=>0,
            "registro"=>false, //control solo Midd tru=se registro y false no se registro
            "mensaje"=>"Error! volver a enviar el Documento"
        ];
    }
    return $arr;
}

public function entrega($idDocPedido=''){
    if($idDocPedido==''){
        $entregas = Cabeceradocumentos::find()
        ->where("DocEntry is null and DocTotal > 0 AND DocType = 'DOE' AND (estado=2)")
        ->with('detalledocumentos')
        ->groupBy('idDocPedido')
        ->having('count(*)=1')
        ->limit(100)
        ->all();
    }
    else{
        $entregas = Cabeceradocumentos::find()
        ->where("DocEntry is null and DocTotal > 0 AND id = '".$idDocPedido."' AND (estado = 2)")
        ->with('detalledocumentos')
        //->groupBy('idDocPedido')
        //->having('count(*)=1')
        //->limit(100)
        ->all();
    }

    Yii::error('inicio entrega');
  $serviceLayer = new Servislayer();
  $serviceLayer->actiondir = "DeliveryNotes";
  
  if (count($entregas)) {
    foreach ($entregas as $entrega){
        $empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '{$entrega["SlpCode"]}'")->queryOne();
        $auxClone = $entrega["clone"];
        $tipofactura=356;//Yii::$app->db->createCommand("select U_series from lbcc where equipoId = '{$entrega["equipoId"]}' and papelId='{$entrega["papelId"]}' and Tipo = 1 ")->queryOne();
        yii::error("data serie boleta entrega".json_encode($tipofactura));
        $seriesName="0035-22";//Yii::$app->db->createCommand("select U_SeriesName from lbcc where equipoId = '{$entrega["equipoId"]}' and papelId='{$entrega["papelId"]}' and Tipo = 1 ")->queryOne();
        $usuarioxm = Yii::$app->db->createCommand("select nombreusuario from vi_cabeceradocumentos where idCabecera = '{$entrega["id"]}'")->queryOne();
        $equipoxm = Yii::$app->db->createCommand("select equipo from vi_cabeceradocumentos where idCabecera = '{$entrega["id"]}'")->queryOne();
        $sucursalxm = Yii::$app->db->createCommand("select nombresucursal from vi_cabeceradocumentos where idCabecera = '{$entrega["id"]}'")->queryOne();
        $plataforma=Yii::$app->db->createCommand("select plataforma from vi_cabeceradocumentos where idCabecera = '{$entrega["id"]}'")->queryOne();
        $FolioPref =  substr($seriesName['U_SeriesName'],0,4);
      $datos = [
        "CardCode" => $entrega["CardCode"],
        "DocDueDate" => $entrega["DocDueDate"],
        "DocType" => $entrega["DocType"],
        "CardName" => $entrega["CardName"],
        "PaymentGroupCode" => $entrega["PayTermsGrpCode"],
        "SalesPersonCode" => $entrega["SlpCode"],
        /* "Address" => $entrega["Address"], */
        "ControlAccount" => $entrega["ControlAccount"],
        //"DocTotal" =>$entrega["DocTotal"],
        "Indicator" => $entrega["Indicator"],
        /* "Reference1" => $entrega["DocNumSAP"], */
        "Series"=>$entrega["Series"] ? $entrega["Series"] : $tipofactura['U_series'], //tipo de factura
        "FolioPrefixString"=>$FolioPref,
        "FolioNumber" => $entrega["UNumFactura"],
        "ShipToCode" => $entrega["ShipToCode"],
        "DocObjectCode" => "DocObjectCode",
        "CreationDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
        "DocDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
        "Comments"=> $entrega["Comments"],
        "Document_ApprovalRequests" => [],
        "DocumentAdditionalExpenses" => [],
        "Indicator" => '09',
        "U_EXX_MOTIVTRA" => '01',
        /* "TransNum" => 0, */
       
        "U_LB_CodigoControl" => $entrega["ControlCode"],
        "U_LB_EstadoFactura" => $entrega["U_LB_EstadoFactura"],
        "U_LB_FechaLimiteEmis" => $entrega["DocDueDate"],
        "U_LB_NumeroAutorizac" => $entrega["U_LB_NumeroAutorizac"],
        "U_LB_NumeroFactura" => $entrega["U_LB_TipoFactura"],
        "U_LB_TipoFactura" => $entrega["U_LB_TipoFactura"],
        "U_LB_TotalNCND" => $entrega["U_LB_TotalNCND"],
        "WithholdingTaxDataCollection" => [],
        "U_xMOB_Usuario"=> $entrega["idUser"],
        "U_xMOB_Equipo"=> $entrega["equipoId"],
        "U_xMOB_GCardSerie"=> $entrega["giftcard"],
        "U_xMOB_GCardMonto"=> $entrega["giftcard"],
        "U_xMOB_Comentario"=> $entrega["Comments"],
        "U_xMOB_Codigo"=> $entrega["idDocPedido"],
        "U_xMOB_Usuario" => $usuarioxm["nombreusuario"],
        "U_xMOB_Equipo" => $equipoxm["equipo"],
        "U_xMOB_Sucursal" => $sucursalxm["nombresucursal"],
        "U_xMOB_Plataforma"=> $plataforma["plataforma"],
        "U_EXX_TIPOOPER" => $entrega["U_EXX_TIPOOPER"] ? $entrega["U_EXX_TIPOOPER"] : "01",
        "U_EXX_CODTRANS" => $entrega["U_EXX_CODTRANS"],
        "U_EXX_NOMTRANS" => $entrega["U_EXX_NOMTRANS"],
        "U_EXX_RUCTRANS" => $entrega["U_EXX_RUCTRANS"],
        "U_EXX_DIRTRANS" => $entrega["U_EXX_DIRTRANS"],
        "U_EXX_NOMCONDU" => $entrega["U_EXX_NOMCONDU"],
        "U_EXX_LICCONDU" => $entrega["U_EXX_LICCONDU"],
        "U_EXX_PLACAVEH" => $entrega["U_EXX_PLACAVEH"],
        "U_EXX_MARCAVEH" => $entrega["U_EXX_MARCAVEH"],
        "U_EXX_PLACATOL" => $entrega["U_EXX_PLACATOL"],
        "U_EXX_MOTIVTRA" => $entrega["U_EXX_FE_MODTRA"]
        ];
        $aLineas = [];
        foreach ($entrega["detalledocumentos"] as $lineaEntrega) {
        $lote = [];
        $lote=Sapenviodoc::LoteCode($lineaEntrega["ItemCode"],$lineaEntrega["Quantity"],$entrega["idDocPedido"],$lineaEntrega["unidadid"], $lineaEntrega["WhsCode"] );
        $unidadNegociox = Sapenviodoc::unidadNegocio($lineaEntrega["ItemCode"]);
        if($lineaEntrega["DiscTotalPrcnt"]>0){
            $descuento=$lineaEntrega["DiscTotalPrcnt"];
        }else{
            if($lineaEntrega["U_4DESCUENTO"]>0){
                $descuento=($lineaEntrega["U_4DESCUENTO"]*100/($lineaEntrega["Quantity"]*$lineaEntrega["Price"]));
            }else{
                $descuento=0;
            }
        }
        //adjuntando campos dinamicos
                $queryCamposUsuario="select * from  campos_dinamicos where tabla='cabeceradocumentos'";
                $dataCampoUser=Yii::$app->db->createCommand($queryCamposUsuario)->queryAll();
                yii::error("datacamposUsusario".json_encode($dataCampoUser));
                foreach ($dataCampoUser as $campouser ) {
                    $datos[$campouser['Campo_Sap']]=$entrega[$campouser['campo_midd']];
                }
        $linea = [
          /* "DocNum"     => $lineaEntrega["DocNum"], */
          "BatchNumbers" => $lote,
          "LineNum"   => intval($lineaEntrega["LineNum"]),
          "ItemCode"   => $lineaEntrega["ItemCode"],
          /* "ItemDescription" => $this->remplaceString($lineaEntrega["Dscription"]), */
          "Quantity"   => $lineaEntrega["Quantity"],
          "Price"      => $lineaEntrega["Price"],
          /* "Currency"   => $lineaEntrega["Currency"], */
          /* "DiscPrcnt"  => $lineaEntrega["DiscPrcnt"], */
          /* "LineTotal"  => $lineaEntrega["LineTotal"], */
           // "WhsCode"    => $lineaEntrega["WhsCode"],
          /* "UoMEntry"   => $this->unidadEntry($lineaEntrega["unidadid"]), */
          /* "BaseDocEntry" => intval($lineaEntrega["BaseDocEntry"]), */
          /* "BaseDocLine" => intval($lineaEntrega["BaseDocLine"]), */
          /* "BaseDocType" => intval($lineaEntrega["BaseDocType"]), */
          /* "BaseDocumentReference" => intval($lineaEntrega["BaseDocumentReference"]), */
          //"GrossPrice" => intval($lineaEntrega["GrossPrice"]),
          "DiscountPercent" => $descuento,
        //   "GrossPrice" =>$lineaEntrega["Price"],
        //   "GrossTotal" =>$lineaEntrega["LineTotal"],
          "TaxCode" => $lineaEntrega["TaxCode"],
          "WarehouseCode" => $lineaEntrega["WhsCode"], // no se esta guardando este dato en la BD t.
          "CorrectionInvoiceItem" => isset($lineaEntrega["CorrectionInvoiceItem"]) ? $lineaEntrega["CorrectionInvoiceItem"] : "ciis_ShouldBe",
          "Status" => isset($lineaEntrega["Status"]) ? $lineaEntrega["Status"] : "bost_Close",
          "Stock" => isset($lineaEntrega["Stock"]) ? $lineaEntrega["Stock"] : "tNO",
          "TargetAbsEntry" => intval($lineaEntrega["TargetAbsEntry"]),
          //revisar estos valores en sap
          "VatPrcnt" => $lineaEntrega["VatPrcnt"],
          "TaxOnly" => $lineaEntrega["TaxOnly"],
          "U_EXX_GRUPODET" => '099',
          "U_EXX_GRUPOPER" => $lineaEntrega["U_EXX_GRUPOPER"],
          "U_EXX_GRUPERMAN" => $lineaEntrega["U_EXX_GRUPERMAN"],
          //"U_EXX_GRUPERMAN"=>"N",
          "U_EXX_PERDGHDCM" => $lineaEntrega["U_EXX_PERDGHDCM"],
          //"U_EXX_PERDGHDCM" =>"N",
          //revisar estos valores en sap
          "BatchNumbers" => $lote,
          "SerialNumbers" => Sapenviodoc::NumeroSerie($lineaEntrega["ItemCode"], $lineaEntrega["Quantity"], $entrega["idDocPedido"]),
          "COGSCostingCode" => $empleadoCiudad['HomeState'],
          //"CostingCode2" => $unidadNegociox,
          //"COGSCostingCode2"=> $unidadNegociox,
          "U_Descuento" => $lineaEntrega["DiscTotalMonetary"],
        //   "LineTaxJurisdictions" => [
        //     [
        //         "JurisdictionCode" => "IVA",
        //         "TaxRate" => 13.0
        //     ]
        //     ],
        ];

        if (intval($lineaEntrega["BaseEntry"])!=0){
            $linea["BaseEntry"]=intval($lineaEntrega["BaseEntry"]);
            $linea["BaseLine"]=intval($lineaEntrega["BaseLine"]);
            $linea["BaseType"]=intval($lineaEntrega["BaseType"]);
        }            

        $pos_dfo = strpos($auxClone,"DFO");
        $pos_dfa = strpos($auxClone,"DFA");
        $pos_dop = strpos($auxClone,"DOP");
        $pos_doe = strpos($auxClone,"DOE");
        if(($auxClone != '0') and (($pos_dfo!=0) OR ($pos_dfa!=0) OR ($pos_dop!=0)  OR ($pos_doe!=0))) {
            $auxClone='0' ;
        }else{
            $auxClone=$auxClone;
        }
        if ($auxClone != '0'){
            $aux_doc_org = Yii::$app->db->createCommand("select * from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne();
             $aux_doc_org = $aux_doc_org["DocEntry"];
             //obtiene los dimenciones
             yii::error("dimenciones del pedido peedido->".$entrega['idDocPedido']);
             if($aux_doc_org){
                $serviceOdbc = new Sincronizar();
               
               yii::error("dimenciones del pedido clone->".$auxClone);

               yii::error("dimenciones del pedido->".json_encode($aux_doc_org));
               $data = json_encode(array("accion" => 7004,"DocEntry"=>$aux_doc_org));
               yii::error("dimenciones del data ->".$data);
               $respuesta = $serviceOdbc->executex($data);
               yii::error("dimenciones del pedido response".$respuesta);
               $respuesta = json_decode($respuesta);
               if($respuesta && is_array($respuesta) ){
                   $linea["OcrCode"]=$respuesta[0]->OcrCode;
                   $linea["OcrCode2"]=$respuesta[0]->OcrCode2;
                   $linea["OcrCode3"]=$respuesta[0]->OcrCode3;
                   $linea["OcrCode4"]=$respuesta[0]->OcrCode4;
                   $linea["OcrCode5"]=$respuesta[0]->OcrCode5;
               }
             }
           
          
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
                if (!is_null($lineaEntrega["BaseLine"])){
                    $linea["BaseEntry"]=$aux_doc_org;
                    $linea["BaseLine"]=intval($lineaEntrega["BaseLine"]);
                    $linea["BaseType"]=$aux_doc_base;
                    }
         }

        array_push($aLineas, $linea);
      }
        $datos["DocumentLines"] = $aLineas;
        Yii::error('Envio entrega:'. json_encode($datos));
        $respuesta = $serviceLayer->executePost($datos);
        Yii::error('Respuesta entrega:'. json_encode($respuesta));
        try {
            if (isset($respuesta->DocEntry)) {
                $entrega->DocEntry = $respuesta->DocEntry;
                $entrega->estado = 3;
                $entrega->fechaupdate = Carbon::today('America/La_Paz');
                $entrega->DocNumSAP = $respuesta->DocNum;
                $entrega->save(false);

                $estado=3;
                $mensaje="Entrega enviada a SAP";
                $registro=true;
                //if($idDocPedido!='') return true; //repuesta si la factura se envio desde el xmobile en linea

            } else {
                $estado=2;
                $mensaje=json_encode($respuesta->error->message->value);
                $registro=false;

            }
            Sapenviodoc::guardarlog($entregas,$respuesta,'Entrega',$entrega["idDocPedido"]);
            $arr = [
                "estado" =>$estado,
                "anulado" => 0,
                "codigoDoc" =>$entrega["idDocPedido"] ,
                "numeracion"=>0,
                "registro"=>$registro, //control solo Midd tru=se registro y false no se registro
                "mensaje"=>$mensaje
            ];
        } catch (\Exception $e) {
            Yii::error('PAGOS-ERROR'.$e->getMessage());
            Sapenviodoc::guardarlog($entregas,$respuesta,'Factura',$entrega["idDocPedido"]);
            $arr = [
                "estado" =>2,
                "anulado" => 0,
                "codigoDoc" =>$entrega["idDocPedido"] ,
                "numeracion"=>0,
                "registro"=>false, //control solo Midd tru=se registro y false no se registro
                "mensaje"=>$e->getMessage()
            ];
        }
    }
    }
    Yii::error('fin');
    return $arr;
}

public function verificandoLinea($aux_doc_org,$lineaP,$auxDetalleDoc,$index,$aux_doctipo){

    //OBTENIENDO EL ID DE UNIDAD DE MEDIDA//
    $idUnidadM = $lineaP["unidadid"];
    $sqlUnidadMedida = Yii::$app->db->createCommand("SELECT AbsEntry from unidadesmedida where Code = '{$idUnidadM}'")->queryOne();
    $idUnidadM=$sqlUnidadMedida["AbsEntry"];
    //FIN CONSULTA ID UNIDAD MEDIDA//

    $documentosV2= new Documentos;
    switch ($aux_doctipo){
        case 'DOF':
            $aux_doc_base="23";
            Yii::error("LineNum_detalle_oferta");
            $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaP["ItemCode"],$lineaP["Quantity"],$aux_doctipo,$idUnidadM);
        break;
        case 'DOP':
            $aux_doc_base="17";
            Yii::error("LineNum_detalle_pedidos");
            $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaP["ItemCode"],$lineaP["Quantity"],$aux_doctipo,$idUnidadM);
        break;
        case 'DFA':
            $aux_doc_base="13";
            Yii::error("LineNum_detalle_faturas");
            $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaP["ItemCode"],$lineaP["Quantity"],$aux_doctipo,$idUnidadM);
        break;
        case 'DOE':
            $aux_doc_base="15";
            Yii::error("LineNum_detalle_entrga");
            $resultado = $documentosV2->LineNum_detalle($aux_doc_org,$lineaP["ItemCode"],$lineaP["Quantity"],$aux_doctipo,$idUnidadM);
            /*
                $resultado = $sap->LineNum_detalle_entrga($aux_doc_org,$lineaP["ItemCode"],$lineaP["ItemCode"],$lineaP["Quantity"]);
            */
        break;
    }
    yii::error("base line document: ".$resultado);
    //nuevo ajuste para el linenum //
    if($lineaP["BaseLine"]==$resultado){
        Yii::error("LineNum igual al resultado");
        $linea=[
            "BaseEntry"=>$aux_doc_org,
            "BaseLine"=>intval($lineaP["BaseLine"]),
            "BaseType"=>$aux_doc_base
        ];

        $auxDetalleDoc[$index]["LineNum"]=$lineaP["BaseLine"];
        $auxDetalleDoc[$index]["ItemCode"]=$lineaP["ItemCode"];
        $auxDetalleDoc[$index]["Cantidad"]=$lineaP["Quantity"];
        $auxDetalleDoc[$index]["UnidadM"]=$idUnidadM;
        $index++;
    }else{
        Yii::error("Por falso consulta lineNum a sap");
        $resultadoLD = $documentosV2->LineNum_detalleTodo($aux_doc_org,$lineaP["ItemCode"],$lineaP["Quantity"],$aux_doctipo,$idUnidadM);
        Yii::error("Resultado linenum 2");
        Yii::error($resultadoLD);
        if(count($resultadoLD)==0){
            Yii::error("Caso lineNum 1");
            $linea=[
                "BaseEntry"=>"",
                "BaseLine"=>"",
                "BaseType"=>""
            ];
        }
        elseif(count($resultadoLD)==1){
            Yii::error("Caso lineNum 2");
            $linea=[
                "BaseEntry"=>$aux_doc_org,
                "BaseLine"=>intval($resultadoLD[0]["LineNum"]),
                "BaseType"=>$aux_doc_base
            ];

            $auxDetalleDoc[$index]["LineNum"]=$resultadoLD[0]["LineNum"];
            $auxDetalleDoc[$index]["ItemCode"]=$lineaP["ItemCode"];
            $auxDetalleDoc[$index]["Cantidad"]=$lineaP["Quantity"];
            $auxDetalleDoc[$index]["UnidadM"]=$idUnidadM;
            $index++;

        }
        elseif(count($resultadoLD)>1){
            Yii::error("Caso lineNum 3");
            $indexD=0;
            //se verifica en el auxDetalleDoc si el linenum esta usada en otro item
            foreach ($auxDetalleDoc as $keyD => $valueD) {
                if($valueD["LineNum"]==$resultadoLD[$indexD]["LineNum"] and $valueD["ItemCode"]==$resultadoLD[$indexD]["ItemCode"] and $valueD["Cantidad"]==$resultadoLD[$indexD]["Quantity"] and $valueD["UnidadM"]==$resultadoLD[$indexD]["UomEntry"]){
                    Yii::error("Se suma el indexD");
                    $indexD++;
                }
                else{
                    Yii::error("No hace nada");
                }
            }
            $linea=[
                "BaseEntry"=>$aux_doc_org,
                "BaseLine"=>intval($resultadoLD[$indexD]["LineNum"]),
                "BaseType"=>$aux_doc_base
            ];

            //se agrega una nueva fila al detalle aux
            $auxDetalleDoc[$index]["LineNum"]=$resultadoLD[$indexD]["LineNum"];
            $auxDetalleDoc[$index]["ItemCode"]=$lineaP["ItemCode"];
            $auxDetalleDoc[$index]["Cantidad"]=$lineaP["Quantity"];
            $auxDetalleDoc[$index]["UnidadM"]=$idUnidadM;
            $index++;

        }

    }
    Yii::error("DETALLE AUX:");
    Yii::error($auxDetalleDoc);
    return ["linea"=>$linea,"auxDetalleDoc"=>$auxDetalleDoc,"index"=>$index];
}

public function documentoCancelar($idDocPedido='') {
  Yii::error('inicio cancelar pedido');
  $serviceLayer = new Servislayer();
  if($idDocPedido==''){
    $pedidos = Cabeceradocumentos::find()
    ->where("DocType = 'DOP' AND (estado = 11 or estado = 6 or eliminado=1)  and DocEntry is not null and estado!=7")
    ->with('detalledocumentos')
    ->limit(100)
    ->asArray()
    ->all();
  }
  else{
    $pedidos = Cabeceradocumentos::find()
    ->where(" idDocPedido = '".$idDocPedido."' AND (estado = 11 or estado = 6 or eliminado=1)  and DocEntry is not null and estado!=7")
    ->with('detalledocumentos')
    ->limit(100)
    ->asArray()
    ->all();

  }

  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          if(Sapenviodoc::actualizaMotivoAnulacion($pedido['DocEntry'],$pedido['U_4MOTIVOCANCELADOCABEZERA'])==1){
              $serviceLayer->actiondir = "Orders({$pedido['DocEntry']})/Cancel";
              $respuesta = $serviceLayer->executePost([]); //$serviceLayer->executePost(json_encode($datos));
              Yii::error("PRUEBA CANCELAR PEDIDO: DOCENTRY -> ".$pedido['DocEntry']." - ". json_encode($respuesta));
              if ($respuesta=='true') {
                  $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
                  $actualizaPedido->estado = 7;
                  $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
                  $actualizaPedido->update(false);
              } else {
                  Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
              }
          }
      }
  }
  Yii::error('fin');
}

public function facturaCancelar($idDocPedido='') {
  Yii::error('inicio cancelar factura');

  $serviceLayer = new Servislayer();
  $serviceLayer2 = new Servislayer();
  if($idDocPedido==''){
    $pedidos = Cabeceradocumentos::find()
          ->where("DocType = 'DFA' AND (estado = 11 or estado = 6 or eliminado=1) and DocEntry is not null and estado!=7")
          ->with('detalledocumentos')
          ->limit(100)
          ->asArray()
          ->all();
  }
  else{
    $pedidos = Cabeceradocumentos::find()
          ->where("idDocPedido = '".$idDocPedido."' AND (estado = 11 or estado = 6 or eliminado=1) and DocEntry is not null and estado!=7")
          ->with('detalledocumentos')
          ->limit(100)
          ->asArray()
          ->all();
  }
  $pedidos = Cabeceradocumentos::find()
          ->where("DocType = 'DFA' AND (estado = 11 or estado = 6 or eliminado=1) and DocEntry is not null and estado!=7")
          ->with('detalledocumentos')
          ->limit(100)
          ->asArray()
          ->all();
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          // Yii::error('inicio cancelar factura paso 1 obtener pagos');
          $pagos =Yii::$app->db->createCommand("Select * from pagos where documentoId='{$pedido['idDocPedido']}'")->queryAll();
          Yii::error('inicio cancelar factura paso 1 obtener pagos'.count($pagos));
          if (count($pagos)){
              foreach ($pagos as $pago) {
                  Yii::error('inicio cancelar factura paso 2 '.$pago['TransId']);
                  $serviceLayer2->actiondir = "IncomingPayments({$pago['TransId']})/Cancel";
                  $respuesta = $serviceLayer2->executePost([]);
                  Yii::error("DATA PAGO CANCELADO ===>" . json_encode($respuesta));
                  if ($respuesta) {
                      $respuestaBD = Yii::$app->db->createCommand('UPDATE pagos SET estadoEnviado = 9 WHERE recibo = ' . $pago['recibo'])->execute();
                      Yii::error("RESPUESTA ACTUALIZACION EN BD ===>" . json_encode($respuestaBD));
                  }
              }
          }
          if(Sapenviodoc::actualizaMotivoAnulacionFactura($pedido['DocEntry'],$pedido['U_4MOTIVOCANCELADOCABEZERA'])==1){
              $serviceLayer->actiondir = "Invoices({$pedido['DocEntry']})/Cancel";
              $respuesta = $serviceLayer->executePost([]); //$serviceLayer->executePost(json_encode($datos));
              Yii::error("RESPUESTA CANCELACION FACTURA EN SL ===>" . $pedido['DocEntry']." : ".json_encode($respuesta));
              if ($respuesta) {
                  $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
                  $actualizaPedido->estado = 7;
                  $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
                  $actualizaPedido->update(false);
              } else {
                  Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
              }
          }
      }
  }
  Yii::error('fin');
}

/*public function ionvoiceCancelarByIdDocpedido($idDocPedido='',$U_4MOTIVOCANCELADOCABEZERA) {
  Yii::error('inicio cancelar factura');

  $serviceLayer = new Servislayer();
  $serviceLayer2 = new Servislayer();
  $response=array("estado"=>1, "mensaje"=>"Documento no encontrado para anular, es posible que se haya anulado anteriormente ");
  $pedidos= Cabeceradocumentos::find()
          ->where("DocType = 'DFA' AND ( estado = 3 && eliminado=0) and DocEntry is not null and idDocPedido='$idDocPedido' and eliminado=0")
          ->with('detalledocumentos')
          ->limit(100)
          ->asArray()
          ->all();
  Yii::error('documento de factura para eliminar'.json_encode($pedidos));
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          // Yii::error('inicio cancelar factura paso 1 obtener pagos');
          $pagos =Yii::$app->db->createCommand("Select * from xmfcabezerapagos where idDocumento='{$pedido['id']}' and cancelado=3")->queryAll();
          Yii::error('verificando que la factura este cancelado para anular el documento de factura'.count($pagos));

          if(count($pagos)>0){

          $response=array("estado"=>1, "mensaje"=>"No se puede anular el documento, primero tiene que anular el pago");
          }else{
              if(Sapenviodoc::actualizaMotivoAnulacionFactura($pedido['DocEntry'],$U_4MOTIVOCANCELADOCABEZERA)==1){
                  $serviceLayer->actiondir = "Invoices({$pedido['DocEntry']})/Cancel";
                  $respuesta = $serviceLayer->executePost([]); //$serviceLayer->executePost(json_encode($datos));
                  Yii::error("RESPUESTA CANCELACION FACTURA EN SL ===>" . $pedido['DocEntry']." : ".json_encode($respuesta));
                  if ($respuesta) {
                      $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
                     // $actualizaPedido->estado = 7;
                      $actualizaPedido->U_4MOTIVOCANCELADOCABEZERA=$U_4MOTIVOCANCELADOCABEZERA;
                      $actualizaPedido->eliminado=3;

                      $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
                      $actualizaPedido->update(false);
                      $response=array("estado"=>3, "mensaje"=>"No se puede anular el documento, primero tiene que anular el pago");
                  } else {
                      Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
                    $response=array("estado"=>2, "mensaje"=>Sapenviodoc::obtenerMensajeError($respuesta));
                  }
              }
              $response=array("estado"=>2, "mensaje"=>"error en la actualizacion del motivo de anulacion");
          }

      }
  }
  return $response;
  Yii::error('fin');
}*/
public function documentCancelarById($actionDir,$idDocPedido='',$U_4MOTIVOCANCELADOCABEZERA,$U_4MOTIVOCANCELADO) {
  Yii::error('inicio cancelar factura');

  $serviceLayer = new Servislayer();
  $serviceLayer2 = new Servislayer();
  $response=array("estado"=>1, "mensaje"=>"Documento no encontrado para anular, es posible que se haya anulado anteriormente ");
  $pedidos= Cabeceradocumentos::find()
          ->where(" estado = 3  and DocEntry is not null and idDocPedido='$idDocPedido' and  canceled='0' ")
          ->with('detalledocumentos')
          ->limit(1)
          ->asArray()
          ->all();
  Yii::error('documento de factura para eliminar'.json_encode($pedidos));
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          // Yii::error('inicio cancelar factura paso 1 obtener pagos');

             Yii::error("RESPUESTA CANCELACION FACTURA EN SL ===>" . $actionDir);
              if(Sapenviodoc::documetoMotivoAnulacion($actionDir,$pedido['DocEntry'],$U_4MOTIVOCANCELADOCABEZERA)==1){

                  $serviceLayer->actiondir = "$actionDir({$pedido['DocEntry']})/Cancel";
                  Yii::error("RESPUESTA CANCELACION FACTURA EN SL ===>" . $serviceLayer->actiondir);

                  $respuesta = $serviceLayer->executePost([]); //$serviceLayer->executePost(json_encode($datos));
                  Yii::error("RESPUESTA CANCELACION FACTURA EN SL ===>" . $pedido['DocEntry']." : ".json_encode($respuesta));
                  if ($respuesta) {
                      $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
                     // $actualizaPedido->estado = 7;
                      $actualizaPedido->U_4MOTIVOCANCELADOCABEZERA=$U_4MOTIVOCANCELADOCABEZERA;
                      $actualizaPedido->U_4MOTIVOCANCELADO=$U_4MOTIVOCANCELADO;
                      //$actualizaPedido->eliminado=3;
                      $actualizaPedido->canceled='3';
                      $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
                      $actualizaPedido->update(false);
                      $response=array("estado"=>3, "mensaje"=>"El documento se anulo exitosamente");
                  } else {
                      Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
                    $response=array("estado"=>2, "mensaje"=>Sapenviodoc::obtenerMensajeError($respuesta));
                  }
              }else{
                  $response=array("estado"=>2, "mensaje"=>"error en la actualizacion del motivo de anulacion");
              }

      }
  }
  Yii::error('response'. json_encode($response));
  return $response;

}

public function validaDocumentoByCod($idDocPedido){
     $document = Cabeceradocumentos::find()
          ->where("idDocPedido = '".$idDocPedido."' AND  estado = 3 and eliminado=0 and DocEntry is not null")
          ->with('detalledocumentos')
          ->limit(100)
          ->asArray()
          ->all();
     return $document;
}

public function cancelDocument($tipoDocument,$idDocPedido='',$motivoAnulacion, $U_4MOTIVOCANCELADO) {
    $response=array("estado"=>1, "mensaje"=>"El documento ya fue anulado anteriormente");
  switch ($tipoDocument) {
      case 'DFA':
          $dataDocument=Sapenviodoc::validaDocumentoByCod($idDocPedido);
          $actionDir='Invoices';
          if(count($dataDocument)){
            //validate facturas
              if($dataDocument[0]['PayTermsGrpCode']=='-1'){
                  $dataPagos =Yii::$app->db->createCommand("Select * from xmfcabezerapagos where documentoId='{$idDocPedido}' and cancelado=3")->queryAll();
                  Yii::error('verificando que el pago este cancelado para anular el documento de factura='.count($dataPagos));

                  if(count($dataPagos)>0){

                     $response= Sapenviodoc::documentCancelarById($actionDir,$idDocPedido,$motivoAnulacion,$U_4MOTIVOCANCELADO);
                  }
                  else{
                     //$response=array("estado"=>1, "mensaje"=>"Error! El documento no se anulo, verifique el pago si esta cancelado.");
                     $response=array("estado"=>1, "mensaje"=>"No se puede anular el documento, primero tiene que anular el pago");
                  }

              }
              else{
                $response= Sapenviodoc::documentCancelarById($actionDir,$idDocPedido,$motivoAnulacion,$U_4MOTIVOCANCELADO);
              }
          }

          break;
      case 'DOP':
          $dataDocument=Sapenviodoc::validaDocumentoByCod($idDocPedido);
           //validate facturas
          if(count($dataDocument)){
            $actionDir='Orders';
            $sqlVerifyDocumentoClone="Select * from cabeceradocumentos where clone='{$idDocPedido}' and eliminado=3";
            yii::error("data query for validate".$sqlVerifyDocumentoClone);
            $dataDoc =Yii::$app->db->createCommand($sqlVerifyDocumentoClone)->queryAll();
                 Yii::error('verificando que la factura este cancelado para anular el documento de factura='.count($dataDoc));

              if(count($dataDoc)>0){

                if($dataDoc && $dataDoc[0]["eliminado"]==3){
                    $response= Sapenviodoc::documentCancelarById($actionDir,$idDocPedido,$motivoAnulacion,$U_4MOTIVOCANCELADO);
                }else{
                    $response=array("estado"=>1, "mensaje"=>"No se puede anular el documento, primero tienes que anular la factura");
                }
              }else{
                //no existe el documento nativoy se manda a cancelar e documento
                $response= Sapenviodoc::documentCancelarById($actionDir,$idDocPedido,$motivoAnulacion,$U_4MOTIVOCANCELADO);

              }

          }
          break;
      case 'DOF':

          $dataDocument=Sapenviodoc::validaDocumentoByCod($idDocPedido);
           //validate pedidos
          if(count($dataDocument)){
            $actionDir='Quotations';
            $response= Sapenviodoc::documentCancelarById($actionDir,$idDocPedido,$motivoAnulacion,$U_4MOTIVOCANCELADO);
          }
          break;
      case 'DOE':
          # code...
          break;

      default:
          # code...
          break;
  }
  return $response;
}

public function pagoCancelar($recibo='',$equipoId='') {

  Yii::error("ENTRA A ANULAR PAGO");
  $serviceLayer = new Servislayer();
  // $anulaciones = Yii::$app->db->CreateCommand("select * from vi_anulacionpagos where transId IS NOT NULL AND estadoEnviado <> 6")->queryAll();
  if($recibo=='' && $equipoId==''){
    $anulaciones = Yii::$app->db->CreateCommand("select * from pagos where transId IS NOT NULL AND estadoEnviado = 6")->queryAll();
  }
  else{
    $anulaciones = Yii::$app->db->CreateCommand("select * from pagos where transId IS NOT NULL AND estadoEnviado = 6 AND recibo='".$recibo."' AND equipoId='".$equipoId."'")->queryAll();
  }
  foreach($anulaciones as $anulacion){
      Yii::error('Cancelar Pago ===>'.$anulacion['TransId']);
      $serviceLayer->actiondir = "IncomingPayments({$anulacion['TransId']})/Cancel";
      $respuesta = $serviceLayer->executePost([]);
      Yii::error("RESPUESTA CANCELAR PAGO-" . json_encode($respuesta));
      //Yii::error("RESPUESTA ERROR: " . $respuesta['error']);
      if (!isset($respuesta['error'])) {
          // $actualizaPago = Pagos::findOne($anulacion['TransId']);
          //$actualizaPago->estadoEnviado = 7;
          // $actualizaPago->fechaupdate = Carbon::today('America/La_Paz');
          try {
            $sql_auxupdatepago = "UPDATE pagos SET estadoEnviado = 7 WHERE TransId = '" .$anulacion['TransId']. "' ";
            Yii::$app->db->createCommand($sql_auxupdatepago)->execute();
            Yii::error("CANCELAR PAGO CORRECTAMENTO");
          } catch (\Throwable $th) {
            //throw $th;
            Yii::error("RESPUESTA ACUTALIZAR CANCELAR PAGO: " . $th);
          }
      } else {
          Yii::error("ID-MID:{$anulaciones["id"]};DATA-" . json_encode($respuesta));
      }
  }

}

public function pagoAnular($recibo='',$equipoId='') {

    Yii::error("ENTRA A ANULAR PAGO");
    $serviceLayer = new Servislayer();
    // $anulaciones = Yii::$app->db->CreateCommand("select * from vi_anulacionpagos where transId IS NOT NULL AND estadoEnviado <> 6")->queryAll();
   /*  if($recibo=='' && $equipoId==''){
      $anulaciones = Yii::$app->db->CreateCommand("select * from xmfcabezerapagos where TransId IS NOT NULL AND estadoEnviado = 3")->queryAll();
    }
    else{ */
      $anulaciones = Yii::$app->db->CreateCommand("select * from xmfcabezerapagos where TransId IS NOT NULL AND estado = 3 AND nro_recibo='".$recibo."' AND equipo='".$equipoId."'")->queryAll();
   // }
   Yii::error('Cancelar Pago ===>'.json_encode($anulaciones));
    foreach($anulaciones as $anulacion){
        Yii::error('Cancelar Pago ===>'.$anulacion['TransId']);
        $serviceLayer->actiondir = "IncomingPayments({$anulacion['TransId']})/Cancel";
        $respuesta = $serviceLayer->executePost([]);
        Yii::error("RESPUESTA CANCELAR PAGO-" . json_encode($respuesta));
        //Yii::error("RESPUESTA ERROR: " . $respuesta['error']);
        if ($respuesta &&  !$respuesta->error) {
            // $actualizaPago = Pa934gos::findOne($anulacion['TransId']);
            //$actualizaPago->estadoEnviado = 7;
            // $actualizaPago->fechaupdate = Carbon::today('America/La_Paz');

              $sql_auxupdatepago = "UPDATE xmfcabezerapagos SET cancelado = 3 WHERE TransId = '" .$anulacion['TransId']. "' ";

              Yii::$app->db->createCommand($sql_auxupdatepago)->execute();
              Yii::error("CANCELAR PAGO CORRECTAMENTO");
              return Array(
                  "estado"=>3,
                  "mensaje"=>"Se anulo el pago exitosamente"
              );


        } else {
            Yii::error("ID-MID:{$anulaciones["id"]};DATA-" . json_encode($respuesta));
            return Array(
                "estado"=>2,
                "mensaje"=>Sapenviodoc::obtenerMensajeError($respuesta)
            );
        }
    }

    return Array(
        "estado"=>1,
        "mensaje"=>"no se pudo enviar el pago"
    );
  }


  public function pedidoCancelarExterno() {
  Yii::error('inicio cancelar pedido externo:');
  $serviceLayer = new Servislayer();
  $pedidos = Anulaciondocmovil::find()
          ->where("docType = 'DOP' AND estado = 6  and docEntry is not null")
          ->asArray()
          ->all();
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {

          if(Sapenviodoc::actualizaMotivoAnulacion($pedido['docEntry'],$pedido['motivoAnulacion'])==1){
              $serviceLayer->actiondir = "Orders({$pedido['docEntry']})/Cancel";
              $respuesta = $serviceLayer->executePost([]);
              Yii::error('Respuesta pedido cancelar externo:'.json_encode($respuesta));
              if ($respuesta) {
                  Yii::error('actualiza cabecera Doc');
                  //if($pedido['origen']=='Externo'){
                    /* $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
                      $actualizaPedido->estado = 7;
                      $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
                      $actualizaPedido->update(false);*/
                      // actualiza en la tabla de documentos externos
                      $db = Yii::$app->db;
                      $db->createCommand("update cabeceradocumentos set estado=7 where DocEntry='".$pedido['docEntry']."' and DocType='DOP'")->execute();

                      $actualizaPedidoE = Anulaciondocmovil::findOne($pedido['id']);
                      $actualizaPedidoE->estado = 7;
                      $actualizaPedidoE->fechaRegistro =date('Y-m-d H:i:s');
                      $actualizaPedidoE->update(false);

                  //}

              } else {
                  Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
              }
          }


      }
  }
  Yii::error('fin');
}

private function obtenercuotasFactura($grupo,$total,$vencimiento){

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
        $sql_tp= "select codigoSIN from fex_tipodocumentosin where  descripcion='ice'";
        $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
        return $tp["codigoSIN"];
    } else
    if($miaux_bonificacion>0){
        $sql_tp= "select codigoSIN from fex_tipodocumentosin where  descripcion='bonificacion'";
        $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
        return $tp["codigoSIN"];
    }
    else{
        $sql_tp= "select codigoSIN from fex_tipodocumentosin where  descripcion='normal'";
        $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
        return $tp["codigoSIN"];
    }

}

private function obtenertipodocumentofexPedido($pedido){
    $sql_ice= "select count(*) as contador from detalledocumentos where  idcabecera=".$pedido['id']." and ICET!='N'";
    $sql_bonificacion="select count(*) as contador from detalledocumentos where  idcabecera=".$pedido['id']." and bonificacion=1";
    Yii::error("Consultas tipo documento : ");
    Yii::error($sql_ice);
    Yii::error($sql_bonificacion);

    $miaux_ice= Yii::$app->db->createCommand($sql_ice)->queryOne();
    $miaux_bonificacion= Yii::$app->db->createCommand($sql_bonificacion)->queryOne();
    $miaux_ice=$miaux_ice["contador"];
    $miaux_bonificacion=$miaux_bonificacion["contador"];
    if($miaux_ice>0){
        $sql_tp= "select codigoSIN from fex_tipodocumentosin where  descripcion='ice'";
        $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
        return $tp["codigoSIN"];
    } else
    if($miaux_bonificacion>0){
        $sql_tp= "select codigoSIN from fex_tipodocumentosin where  descripcion='bonificacion'";
        $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
        return $tp["codigoSIN"];
    }
    else{
        $sql_tp= "select codigoSIN from fex_tipodocumentosin where  descripcion='normal'";
        $tp=Yii::$app->db->createCommand($sql_tp)->queryOne();
        return $tp["codigoSIN"];
    }

}

/**
* @param $unidad
* @return mixed
* Solo para obtener Unidades no realiza tareas con SAP
*/
private function unidadEntry($unidad,$producto="") {

  $entry = Unidadesmedida::find()->where(['Code' => $unidad])->one();
  if ($entry) {
      $entry = $entry->getAttribute('AbsEntry');
  } else {
      $q='select DefaultSalesUoMEntry from productos where ItemCode="'.$producto.'"';
      $entry= Yii::$app->db->createCommand($q)->queryone();
      $entry = $entry["DefaultSalesUoMEntry"];
  }
  return $entry;
}


public function LoteCode($ItemCode,$cantidad,$documento,$unidad,$almacen) {
    //CONSULTA DIRECTA A SAP//
    $campos="\"UgpEntry\" ";
    $condicion="where \"ItemCode\"='{$ItemCode}'";
    $productos=new Productos;
    $UgpEntry=$productos->obtenerCamposEspecificos($campos,$condicion);
    Yii::error("OBTIENE CAMPO UgpEntry: ".$UgpEntry['UgpEntry']);
    $salida_lote=[];
    /*$q='select BatchNum,Quantity from lotesproductos where ItemCode="'.$ItemCode.'" and WhsCode="'.$almacen.'" order by InDate';
    Yii::error("lotes de producto: ".$q);
    $miauxlotes= Yii::$app->db->createCommand($q)->queryAll(); */

    $campos="\"BatchNum\" ,\"Quantity\" ";
    $condicion="where \"ItemCode\"='{$ItemCode}' AND \"WhsCode\"='{$almacen}' ";
    $miauxlotes=$productos->obtenerCamposEspecificosLotesTodos($campos,$condicion);

    // FIN CONSULTAS DIRECTAS A SAP//


  $q2='SELECT BaseQty from unidadmedidaxgrupo where unidadmedidaxgrupo.UgpEntry='.$UgpEntry['UgpEntry'];
  Yii::error("$q2 : ".$q2);

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
  Yii::error("lotes de producto: ----->".json_encode($salida_lote));
  return $salida_lote;
}

private function ObtenerPorcentaje($precioDescuento, $precioTotal) {
  $descuento = (($precioTotal - $precioDescuento) * 100) / $precioTotal;
  if ($descuento == 0 || $precioTotal == 0) {
      return null;
  }
  return round($descuento, 6);
}

private function NumeroSerie($itemCode, $cantidad, $documentId) {
  Yii::error(" serie de :".$itemCode." ".$cantidad." ".$documentId);
  $itemSeries = [];
  $series = Seriesmarketing::find()
          ->where("ItemCode = '{$itemCode}'  AND DocumentId = '{$documentId}'")
          ->all();
  if (count($series)) {
      foreach ($series as $value) {
          $serie = [
              "InternalSerialNumber" => $value->SerialNumber
              //"SystemNumber"=>$value->SystemNumber
          ];
          array_push($itemSeries, $serie);
          $value->Status = 0;
          $value->save(false);
      }
  }
  return $itemSeries;
}

private function GastosAdicionalesLinea($icee,$icep,$totalpagar,$cc1="",$cc2="",$cc3="",$cc4="",$IT){
  $q='select ice,it,itg,icee,icep from  vi_gastosadicionales';
  $resultado= Yii::$app->db->createCommand($q)->queryone();
  $entry = $entry["DefaultSalesUoMEntry"];

  $gicee= [
          "ExpenseCode" => $resultado['icee'],
          "TaxCode" => "IVA",
          "LineTotal" => round($icee, 2),
          "LineExpenseTaxJurisdictions" => [
                                              [
                                                  "JurisdictionCode" => "EXE",
                                                  "JurisdictionType" => 1,
                                                  "LineNumber" => 0
                                              ]
                                          ]
          ];

   $gicep= [
          "ExpenseCode" => $resultado['icep'],
          "TaxCode" => "IVA",
          "LineTotal" => round($icep, 2),
          "LineExpenseTaxJurisdictions" => [
                                              [
                                                  "JurisdictionCode" => "EXE",
                                                  "JurisdictionType" => 1,
                                                  "LineNumber" => 0
                                              ]
                                          ]
          ];

   $git= [
          "ExpenseCode" => $resultado['itg'],
          "TaxCode" => "IVA",
          "DistributionRule"=>$cc1,
          "DistributionRule2"=>$cc2,
          "DistributionRule3"=>$cc3,
          "DistributionRule4"=>$cc4,
          "LineTotal" => round(($totalpagar * $IT) / 100, 2)*-1,
          "LineExpenseTaxJurisdictions" => [
                                              [
                                                  "JurisdictionCode" => "EXE",
                                                  "JurisdictionType" => 1,
                                                  "LineNumber" => 0
                                              ]
                                          ]
          ];

  $salida=[$gicee,$gicep,$git];
  return $salida;

}

private function GastosAdicionalesCab($total,$IT){
    $q='select ice,it,itg,icee,icep from  vi_gastosadicionales';
    $resultado= Yii::$app->db->createCommand($q)->queryone();
    $gitg=[
                  "ExpenseCode" => $resultado['it'],
                  "TaxCode" => "IVA",
                  "LineTotal" => round(($total * $IT) / 100, 2),
                  "DocExpenseTaxJurisdictions" => [
                      [
                          "JurisdictionCode" => "EXE",
                          "JurisdictionType" => 1,
                          "LineNumber" => 0
                      ]
                  ]
          ];
      $salida=[$gitg];
      return $salida;

}

private function facturaReserva($fechaEntrega, $reserva) {
  $fechaActual = Carbon::today('America/La_Paz');
  if ($reserva == 1) {
      return true;
  }else{
      return false;
  }
 /*
  $fechaEntrega =$fechaEntrega->format('Y-m-d');
  $fechaEntrega = Carbon::createFromFormat('Y-m-d', $fechaEntrega, 'America/La_Paz');
  if ($reserva == 1) {
      return true;
  }
      if ($fechaActual->format('Y-m-d') == $fechaEntrega->format('Y-m-d')) {
          Yii::error('FECHAS:' . $fechaActual->format('Y-m-d') . '-' . $fechaEntrega->format('Y-m-d'));
          return false;
      } else if ($fechaEntrega->lessThan($fechaActual)) {
          return false;
      }*/
  //return true;
}

private function unidadNegocio($itemCode){
  $centroCosto = Yii::$app->db->createCommand("SELECT CenterCode FROM `vi_unidadnegocio` WHERE ItemCode = :item;")
          ->bindValue(':item', $itemCode)
          ->queryOne();
  Yii::error('Negocio2' . $centroCosto['CenterCode']);
  return $centroCosto['CenterCode'];
}

private function remplaceString($string) {
  if (!is_null($string)) {
      return str_replace('\'', '`', $string);
  }
  return $string;
}

public function solicitudDeTraspaso(){
  Yii::error("DOCUMENTOSAP-AAR-735-SOLICITUD DE TRASPASO");
  $serviceLayer = new Servislayer();
  $sql = 'SELECT * FROM traspasocabecera WHERE (estado = 3 Or estado = 4) AND (DocEntrySolicitud is null OR DocEntrySolicitud = "")';
  $serviceLayer->actiondir = "InventoryTransferRequests";
  $cabeceras = Yii::$app->db->createCommand($sql)->queryAll();

  if (count($cabeceras) > 0){
      foreach($cabeceras as $cabecera){
          $datos = [
              "DocDate" => $cabecera["fechasolicitud"],
              "Comments" => $cabecera["comentariosolicitud"],
              "SalesPersonCode" => -1,
              "FromWarehouse" => $cabecera["origenWarehouse"],
              "ToWarehouse" => $cabecera["destinoWarehouse"],
              "StockTransferLines" => []
          ];

          $detalles = TraspasosDetalle::find()->where(["idcabecera" => $cabecera["id"]])
                    //->bindValue(':idcabecera', $cabecera["id"])
                    ->limit(50)
                    ->asArray()
                    ->all();
        if (count($detalles) > 0){
          foreach($detalles as $detalle){
              $linea = [
                  "ItemCode" => $detalle["itemCode"],
                  "Quantity" => $detalle["cantidadaprobada"],
                  "WarehouseCode" => $detalle["destinowarehouse"],
                  "FromWarehouseCode" => $detalle["origenwarehouse"],
                  "MeasureUnit" => $detalle["unidadmedida"],
                  "BaseLine" => $detalle["serie"],
                  "BatchNumbers" => [],
                  "SerialNumbers" => []
              ];

              array_push($datos["StockTransferLines"],$linea);
          }
          $tempral = json_encode($datos);
          Yii::error("DOCUMENTOSAP-AAR-735-".$tempral);
          $respuesta = $serviceLayer->executePost($datos);
          if(isset($respuesta->DocEntry)){
            $actualiartraspaso = TraspasosCabecera::findOne($cabecera['id']);
            $actualiartraspaso->DocEntrySolicitud = $respuesta->DocEntry;
            //$actualiartraspaso->MensajeSolicitud = "Creada solo en SAP";
            $actualiartraspaso->save(false);
            Yii::error("ID-MID:{$cabecera["id"]};DATA-".$respuesta->DocEntry);
          }
          else {
              if (isset($respuesta->message)) {
                Yii::error("ID-MID:{$cabecera["id"]};DATA-".json_encode($respuesta->message->value));
              } else {
                Yii::error("ID-MID:{$cabecera["id"]};DATA-".json_encode($respuesta));
              }
              Yii::error('fin');
          }
        }
      }

    }
  }


  public function crearTraspaso(){
    Yii::error("CREAR TRASPASO");
    $serviceLayer = new Servislayer();
    $sql = 'SELECT * FROM traspasocabecera WHERE (estado = 6 OR estado = 2) AND (DocEntryTraspaso is null OR DocEntryTraspaso = "")';
    $serviceLayer->actiondir = "StockTransfers";
    $cabeceras = Yii::$app->db->createCommand($sql)->queryAll();

    if (count($cabeceras) > 0){
        foreach($cabeceras as $cabecera){
            $datos = [
                "DocDate" => $cabecera["fecharecepcion"],
                "Comments" => $cabecera["comentariorecepcion"],
                "SalesPersonCode" => -1,
                "FromWarehouse" => $cabecera["origenWarehouse"],
                "ToWarehouse" => $cabecera["destinoWarehouse"],
                "StockTransferLines" => []
            ];

            $detalles = TraspasosDetalle::find()->where(["idcabecera" => $cabecera["id"]])
                      //->bindValue(':idcabecera', $cabecera["id"])
                      ->limit(50)
                      ->asArray()
                      ->all();
            if (count($detalles) > 0){
                $cantidadAprobada = 0;
                $cantidadRecepcionada = 0;
                foreach($detalles as $detalle){
                    $linea = [
                        "ItemCode" => $detalle["itemCode"],
                        "Quantity" => $detalle["cantidadrecepcionada"],
                        "WarehouseCode" => $detalle["destinowarehouse"],
        "FromWarehouseCode" => $detalle["origenwarehouse"],
                        "MeasureUnit" => $detalle["unidadmedida"],
                        "BaseType" => "InventoryTransferRequest",
                        "BaseLine"=> $detalle["serie"],
                        "BaseEntry" => 0,
                        "BatchNumbers" => [],
                        "SerialNumbers" => []
                    ];
                    $cantidadAprobada = $cantidadAprobada + $detalle["cantidadaprobada"];
                    $cantidadRecepcionada = $cantidadRecepcionada + $detalle["cantidadrecepcionada"];
                    if ($detalle["tipoRegistro"] == "L"){
                        $lotes = TraspasosLote::find()->where(["idDetalle" => $detalle["id"]])
                      //->bindValue(':iddetalle', $detalle["id"])
                      ->limit(50)
                      ->asArray()
                      ->all();
                      if(count($lotes) > 0){
                          foreach($lotes as $lote){
                              $llote = [
                                  "BatchNumber" => $lote["BatchNum"],
                                  "Quantity" => $lote["Quantity"]
                              ];
                              array_push($linea["BatchNumbers"], $llote);
                          }
                      }
                    }

                    if ($detalle["tipoRegistro"] == "S"){
                        $series = TraspasosSerie::find()->where(["idDetalle" => $detalle["id"]])
                      //->bindValue(':iddetalle', $detalle["id"])
                      ->limit(50)
                      ->asArray()
                      ->all();
                      if(count($series) > 0){
                          foreach($series as $serie){
                              $lserie = [
                                  "InternalSerialNumber" => $serie["SerialNumber"],
                                  "SystemSerialNumber" => $serie["SystemNumber"]
                              ];
                              array_push($linea["SerialNumbers"], $lserie);
                          }
                      }
                    }
                    array_push($datos["StockTransferLines"],$linea);
                }

                $solicitud = 0;

                if ($cabecera["DocEntrySolicitud"] <> null && $cabecera["DocEntrySolicitud"] <> "")
                    $solicitud = $cabecera["DocEntrySolicitud"];
                else $solicitud = Sapenviodoc::solicitudDeTraspasoDesdeFinalizado($cabecera['id']);

                if ($solicitud != null) {
                    $nuevaslineas = [];
                    //$baseline = 0;
                    foreach ($datos["StockTransferLines"] as $base){
                        //$base["BaseLine"] = $baseline;
                        $base["BaseEntry"] = $solicitud;
                        //$baseline = $baseline + 1;
                        array_push($nuevaslineas, $base);

                    }
                    $datos["StockTransferLines"] = $nuevaslineas;
                    $t1272 = json_encode($datos);
                    Yii::error("DOCUMENTOSAP-AAR-1273-".$t1272);
                    $respuesta = $serviceLayer->executePost($datos);
                    if (($cabecera["estado"] == "6" || $cabecera["estado"] == 6) && (($cantidadAprobada - $cantidadRecepcionada) != 0)){
                        Sapenviodoc::cerrarTraspaso($solicitud);
                    }
                    if(isset($respuesta->DocEntry)){
                        //actualizar el stock en productosalmcenes
                        //$this->obtenerItemTraspasos();
                        //////////////////////////////////////////
                        $actualiartraspaso = TraspasosCabecera::findOne($cabecera['id']);
                        $actualiartraspaso->DocEntryTraspaso = $respuesta->DocEntry;
                        $actualiartraspaso->MensajeTraspaso = "Solicitud de SAP: ".$solicitud;
                        $actualiartraspaso->update(false);
                        Yii::error("ID-MID:{$cabecera["id"]};DATA-".$respuesta->DocEntry);
                        Sapenviodoc::obtenerItemTraspasos();
                    } else {
                      if (isset($respuesta->message)) {
                          Yii::error("ID-MID:{$cabecera["id"]};DATA-".json_encode($respuesta->message->value));
                      } else {
                        Yii::error("ID-MID:{$cabecera["id"]};DATA-".json_encode($respuesta));
                      }
                      Yii::error('fin');
                    }
                }
                else{
                    Yii::error("DOCUMENTOSAP-AAR-855-NO PUDO REGISTRAR SOLICITUD AUTOMATICA");
                }
            }

        }
    }
}
/********************Conciliacion*************************/
private function obtenerConciliaciones(){
  $serviceLayer = new Servislayer();
  $serviceLayer->actiondir = "InternalReconciliations";
  $conciliaciones=Yii::$app->db->createCommand("Select * from conciliaciones where StatusSend= 0")->queryAll();
  if (count($conciliaciones)) {
      foreach ($conciliaciones as $conm) {
          //$empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadempleado where idUser = {$pedido["idUser"]}")->queryOne();
          //Yii::error('Query oferta'. "select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '{$pedido["SlpCode"]}'");
          $detalles = Yii::$app->db->createCommand("select * from conciliacionesdetalles where idMaestro = '{$conm["id"]}'")->queryAll();
          foreach ($detalles as $lineaCon) {
              $lineas[] = [
                  "CashDiscount" => $lineaCon["CashDiscount"],
                  "CreditOrDebit" => $lineaCon["CreditOrDebit"],
                  "ReconcileAmount" => $lineaCon["Amount"],
                  "Selected" => $lineaCon["Selected"],
                  "ShortName" => $lineaCon["ShortName"],
                 "SrcObjAbs" => $lineaCon["SrcObjAbs"],
                  "SrcObjTyp" => $lineaCon["SrcObjTyp"],
                  "TransId" => $lineaCon["TransId"],
                  "TransRowId" => $lineaCon["TransIdRow"]
              ];
          }
          $arrayCon['CardOrAccount'] = $conm['CardorAccount'];
          $arrayCon['InternalReconciliationOpenTransRows'] = $lineas;
          $arrayCon['ReconDate'] = date('Y-m-d',strtotime($conm['Recondate']));
          Yii::error('inicio conciliación Json: '.json_encode($arrayCon));
          $respuesta = $serviceLayer->executePost($arrayCon); //$serviceLayer->executePost(json_encode($datos));
          Yii::error('respuesta conciliación Json: '.json_encode($respuesta));
          if(isset($respuesta->CardOrAccount)){
              //UPDATE `conciliaciones` SET `StatusSend`= 1 WHERE id =
              $sql = "UPDATE `conciliaciones` SET `StatusSend`= 1 WHERE id =" . $conm['id'] . ";";
              Yii::$app->db->createCommand($sql)->execute();
              Yii::error("ID-MID: {$conm['id']};DATA-" . $sql);
          } else {
              /*if (isset($respuesta->message)) {
                  Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta->message->value));
              } else {
                  Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
              }*/
          }
      }
  }
  Yii::error('fin');

}
/*********************************************************/

private function solicitudDeTraspasoDesdeFinalizado($idCabecera){
          Yii::error("CREAR SOLICITUD TRASPASO AUTOMATICO");
          $serviceLayer = new Servislayer();
          $serviceLayer->actiondir = "InventoryTransferRequests";
          $cabeceras = TraspasosCabecera::find()->where("id = ".$idCabecera)
            ->limit(50)
            ->asArray()
            ->all();

          if (count($cabeceras) > 0){
              foreach($cabeceras as $cabecera){
                  $datos = [
                      "DocDate" => $cabecera["fechasolicitud"],
                      "Comments" => "BASADO EN EL TRASPASO DESDE EL POS ".$idCabecera,
                      "SalesPersonCode" => -1,
                      "FromWarehouse" => $cabecera["origenWarehouse"],
                      "ToWarehouse" => $cabecera["destinoWarehouse"],
                      "StockTransferLines" => []
                  ];

                  $detalles = TraspasosDetalle::find()->where(["idcabecera" => $cabecera["id"]])
                            //->bindValue(':idcabecera', $cabecera["id"])
                            ->limit(50)
                            ->asArray()
                            ->all();
                  if (count($detalles) > 0){
                      foreach($detalles as $detalle){
                          $linea = [
                              "ItemCode" => $detalle["itemCode"],
                              "Quantity" => $detalle["cantidadaprobada"],
                              "WarehouseCode" => $detalle["destinowarehouse"],
                              "FromWarehouseCode" => $detalle["origenwarehouse"],
                              "MeasureUnit" => $detalle["unidadmedida"],
            "BaseLine" => $detalle["serie"],
                              "BatchNumbers" => [],
                              "SerialNumbers" => []
                          ];

                          array_push($datos["StockTransferLines"],$linea);
                      }
                      $tempral = json_encode($datos);
                      Yii::error("DOCUMENTOSAP-AAR-926-".$tempral);

                      $respuesta = $serviceLayer->executePost($datos);
                      if(isset($respuesta->DocEntry)){
                          $actualiartraspaso = TraspasosCabecera::findOne($cabecera['id']);
                          $actualiartraspaso->DocEntrySolicitud = $respuesta->DocEntry;
                          $actualiartraspaso->MensajeSolicitud = "Solicitud automatica";
                          $actualiartraspaso->save(false);
                          Yii::error("ID-MID:{$cabecera["id"]};DATA-".$respuesta->DocEntry);
                          return $respuesta->DocEntry;
                        } else {
                          if (isset($respuesta->message)) {
                            Yii::error("ID-MID:{$cabecera["id"]};DATA-".json_encode($respuesta->message->value));
                          } else {
                            Yii::error("ID-MID:{$cabecera["id"]};DATA-".json_encode($respuesta));
                          }
                          Yii::error('fin');
                          return null;
                          }
                      }
                  }

              }
}

private function cerrarTraspaso($docentry){
              Yii::error("AAR-cerrarTraspaso-Cerrando solicitud 1035");
              $serviceLayer = new Servislayer();
              $serviceLayer->actiondir = "InventoryTransferRequests({$docentry})/Close";
              $respuesta = $serviceLayer->executePost([]);
              Yii::error("AAR-cerrarTraspaso-solicitud cerrada 1039");
}
/****** guardar log */
private function guardarlog($documento,$respuesta,$proceso,$iddocumento){
    $aux_env=json_encode($documento);
    $aux_resp=addslashes(json_encode($respuesta));
    $aux_hoy=Carbon::now('America/La_Paz')->format('Y-m-d H:m:s');
    $log_aux="";
    $log_aux=" SELECT idlog from log_envio where envio='".$aux_env."'";

    $db = Yii::$app->db;
    $aux_control=$db->createCommand($log_aux)->queryOne();
    $log_aux="";
    if( $aux_control["idlog"]){
        $log_aux.= "UPDATE  `log_envio` set ultimo ='".$aux_hoy."' ";
        $log_aux .= " WHERE idlog=".$aux_control["idlog"];
        $db->createCommand($log_aux)->execute();
    }else{
        $log_aux.= "INSERT INTO `log_envio`(`proceso`, `envio`, `respuesta`,  `fecha`, `documento`) VALUES (";
        $log_aux .=  "'{$proceso}','{$aux_env}','{$aux_resp}','{$aux_hoy}','{$iddocumento}');";
        $db->createCommand($log_aux)->execute();
    }
    Yii::error('Documentos a SAP : '.$proceso .' '. $iddocumento. json_encode($respuesta));
    /*
    if (isset($respuesta->message)) {
        Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta->message->value));
        $aux_env=json_encode($datos);
        $aux_resp=json_encode($respuesta);
        $aux_hoy=Carbon::now('America/La_Paz')->format('Y-m-d H:m:s');
        $log_aux="";
        $log_aux=" SELECT idlog from log_envio where envio='".$aux_env."'";

        $db = Yii::$app->db;
        $aux_control=$db->createCommand($log_aux)->queryOne();
        $log_aux="";
        if( $aux_control["idlog"]){
            $log_aux.= "UPDATE  `log_envio` set ultimo ='".$aux_hoy."' ";
            $log_aux .= " WHERE idlog=".$aux_control["idlog"];
            $db->createCommand($log_aux)->execute();
        }else{
            $log_aux.= "INSERT INTO `log_envio`(`proceso`, `envio`, `respuesta`,  `fecha`) VALUES (";
            $log_aux .= "'PEDIDO','{$aux_env}','{$aux_resp}','{$aux_hoy}');";
            $db->createCommand($log_aux)->execute();
        }
    } else {
        //Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
        $aux_codigo=strrpos($respuesta, "-2028");
        $aux_txt=strrpos($respuesta, "No matching re");
        if (($aux_codigo==true) AND ($aux_txt==true)){
            Yii::error("error tipo" .$respuesta->error);
            $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
            $actualizaPedido->estado = 4;
            $actualizaPedido->save(false);

        }else{
            Yii::error("ID-MID:{$pedido["id"]};DATA-");
        }
    }
    */

}

private function obtenerItemTraspasos(){
  Yii::error("items on demand Traspasos");
      $items=Yii::$app->db->createCommand("Select id,ItemCode from traspasodetalle where (status=1) and (actsl=0  or actsl is null) ")->queryALl();
      if (count($items)){
          foreach ($items as $item){
              Sapenviodoc::Actualizarproductostraspaso($item["ItemCode"],$item["id"]);
              //Yii::error(json_encode($item));
          }

      }
}

public function Actualizarproductostraspaso($item,$id){
    Yii::error("items traspasos on demand");
    //$serviceLayer = new Servislayer();
    $odbc = new Sincronizar();
    $data = json_encode(array("accion" => 30,"ItemCode"=>$item));
    $respuesta = $odbc->executex($data);
    $productos = json_decode($respuesta);

    /*$serviceLayer->actiondir = 'Items?$select=ItemCode,ItemName,ItemsGroupCode,ForeignName,CustomsGroupCode,BarCode,PurchaseItem,SalesItem,InventoryItem,User_Text,SerialNum,QuantityOnStock,QuantityOrderedFromVendors,QuantityOrderedByCustomers,ManageSerialNumbers,ManageBatchNumbers,SalesUnit,SalesUnitLength,SalesUnitWidth,SalesUnitHeight,SalesUnitVolume,PurchaseUnit,DefaultWarehouse,ManageStockByWarehouse,ForceSelectionOfSerialNumber,Series,UoMGroupEntry,DefaultSalesUoMEntry,ItemWarehouseInfoCollection,ItemPrices,InventoryUOM,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,Properties8,Properties9,Properties10,Properties11,Properties12,Properties13,Properties14,Properties15,Properties16,Properties17,Properties18,Properties19,Properties20,Properties21,Properties22,Properties23,Properties24,Properties25,Properties26,Properties27,Properties28,Properties29,Properties30,Properties31,Properties32,Properties33,Properties34,Properties35,Properties36,Properties37,Properties38,Properties39,Properties40,Properties41,Properties42,Properties43,Properties44,Properties45,Properties46,Properties47,Properties48,Properties49,Properties50,Properties51,Properties52,Properties53,Properties54,Properties55,Properties56,Properties57,Properties58,Properties59,Properties60,Properties61,Properties62,Properties63,Properties64,Manufacturer,NoDiscounts&$filter=ItemCode eq \''.$item.'\'';
    $productos = $serviceLayer->executex(1);
    $productos = $productos->value;
    $fecha = date("Y-m-d");
    // Yii::error(json_encode($productos));
    $actualizacion=Yii::$app->db->createCommand("Update detalledocumentos set actsl=1 where id='".$id."' ")->execute();
    */
    $db = Yii::$app->db;
    $sumatotal=0;
    foreach ($productos as $puntero) {
        $cantidad = round($puntero->OnHand,0);
        $comprometido=round($puntero->IsCommited,0);
        $almacen=$puntero->WhsCode;
        $sumatotal=$cantidad+$sumatotal;
        $locked = $puntero->Locked;
        $onorder = $puntero->OnOrder;
        $update = Carbon::today('America/La_Paz');
        Yii::error("items traspasos on demand: buscar item");
        $items = Yii::$app->db->createCommand("SELECT * FROM productosalmacenes WHERE ItemCode='".$item."' and  WarehouseCode='".$almacen."'")->queryALl();
        Yii::error("items traspasos on demand: fin buscar item");
        if (count($items)){
            Yii::error("items traspasos on demand: entro a actualizar");
            $sql= "UPDATE productosalmacenes set InStock='{$cantidad}', Committed='{$comprometido}' where ItemCode='".$item."' and  WarehouseCode='".$almacen."'";
            $sql2= "UPDATE traspasodetalle set actsl=1 where id={$id}";
            // Yii::error("items on demand".$sql);
            //$db = Yii::$app->db;
            $db->createCommand($sql)->execute();
            // $db->createCommand($sql2)->execute();
            Yii::error("items traspasos on demand: finalizo actualizar");
        }
        else{
            Yii::error("items traspasos on demand: entro a crear");
            $sql = "INSERT INTO `productosalmacenes`(`id`, `ItemCode`, `WarehouseCode`, `InStock`, `Committed`, `Locked`, `Ordered`, `User`, `Status`, `DateUpdate`) VALUES (DEFAULT,'{$item}','{$almacen}','{$cantidad}','{$comprometido}','{$locked}','{$onorder}','1','1','{$update}')";
            $db->createCommand($sql)->execute();
            Yii::error("items traspasos on demand: finalizo a crear");
        }
    }
    $sql3= "UPDATE productos set QuantityOnStock='{$sumatotal}' where ItemCode='{$item}'";
    $db->createCommand($sql3)->execute();

}

private function ObtenerAutorizacion($lineasPedido,$tipoDoc){
    $autorizacion = 1;
    foreach ($lineasPedido as $lineaPedido)
    {
        Yii::error('eddy inicio  autorizacion: bonificacion'.$lineaPedido["bonificacion"]);
        if ($lineaPedido["bonificacion"] == 3) {
            $auxq="select U_bonificacioncantidad from bonificacion_ca where Code = '".$lineaPedido["codeBonificacionUse"]."'";
            Yii::error('eddy code bonificacion query'.$lineaPedido["codeBonificacionUse"]." ".$auxq);
            $U_LimiteMaxRegalo = Yii::$app->db->createCommand($auxq)->queryOne();
            Yii::error('eddy limitemaximo '.$U_LimiteMaxRegalo["U_bonificacioncantidad"]);
            if ($lineaPedido["DiscTotalPrcnt"] > $U_LimiteMaxRegalo["U_bonificacioncantidad"])
            {
                Yii::error('eddy necesita autorizacion '.$lineaPedido["Quantity"]." > ".$U_LimiteMaxRegalo["U_bonificacioncantidad"]);
                if($tipoDoc == 'pedido'){
                    return 2;
                }else{
                    return 4;
                }
            }
        }
    }
    return $autorizacion;
}

private function ObtenerAutorizacionFactura($lineasPedido){
    $autorizacion = 4;
    foreach ($lineasPedido as $lineaP)
    {
        Yii::error('eddy inicio  autorizacion: bonificacion'.$lineaP->bonificacion);
        if ($lineaP->bonificacion == 2) {
            $auxq="select U_limitemaxregalo from bonificacion_ca where Code = '".$lineaP->codeBonificacionUse."'";
            Yii::error('eddy code bonificacion query'.$lineaP->codeBonificacionUse." ".$auxq);
            $U_LimiteMaxRegalo = Yii::$app->db->createCommand($auxq)->queryOne();
            Yii::error('eddy limitemaximo '.$U_LimiteMaxRegalo["U_limitemaxregalo"]);
            if ($lineaP->Quantity > $U_LimiteMaxRegalo["U_limitemaxregalo"])
            {
                Yii::error('eddy necesita autorizacion '.$lineaP->Quantity." > ".$U_LimiteMaxRegalo["U_limitemaxregalo"]);
                return 2;
            }
        }
    }
    return $autorizacion;
}

private function ObtenerContactosCliente($cardCode){
    $serviceLayer = new Sincronizar();
    $data = json_encode(array("accion" => 305,"cliente"=>$cardCode));
    $respuesta = $serviceLayer->executex($data);
    $respuesta = json_decode($respuesta);
    foreach ($respuesta as $puntero) {
        $sql = "UPDATE contactos set InternalCode= '{$puntero->CntctCode}' where cardCode='{$puntero->CardCode}' and nombre='{$puntero->Name}'";
        Yii::error("actualizacion contactos sql ".$sql);
        Yii::$app->db->createCommand($sql)->execute();
    }
}

private function ObtenerSucursalesCliente($cardCode){
    Yii::error("actualizacionSucursales ");
    $serviceLayer = new Sincronizar();
    $data = json_encode(array("accion" => 56,"CardCode"=>$cardCode));
    $respuesta = $serviceLayer->executex($data);
    $respuesta = json_decode($respuesta);
    foreach ($respuesta as $puntero) {
        $sql = "UPDATE clientessucursales set RowNum= '{$puntero->LineNum}' where CardCode='{$puntero->CardCode}' and AddresName='{$puntero->AddressName}'";
        Yii::error("actualizacion contactos sql ".$sql);
        Yii::$app->db->createCommand($sql)->execute();
    }
}

private function actualizaMotivoAnulacion($docEntry,$motivoAnulacion){
    $datos=["U_XM_Anulacion"=>$motivoAnulacion];
    $serviceLayer = new Servislayer();
    $serviceLayer->actiondir = "Orders($docEntry)";
    $respuesta = $serviceLayer->executePatchPut('PATCH', $datos);
    Yii::error("motivo anulacion pedido: ".$docEntry." -> ".json_encode($respuesta));
    if($respuesta){
        return 1;
    }else{
        Yii::error("error motivo anulacion: ".json_encode($respuesta));
        return 0;
    }
}

private function actualizaMotivoAnulacionFactura($docEntry,$motivoAnulacion){
    $datos=["U_XM_Anulacion"=>$motivoAnulacion];
    $serviceLayer = new Servislayer();
    $serviceLayer->actiondir = "Invoices($docEntry)";
    $respuesta = $serviceLayer->executePatchPut('PATCH', $datos);
    Yii::error("motivo anulacion factura: ".$docEntry." -> ".json_encode($respuesta));
    if($respuesta){
        return 1;
    }else{
        Yii::error("error motivo anulacion: ".json_encode($respuesta));
        return 0;
    }
}

private function documetoMotivoAnulacion($actionDir,$docEntry,$motivoAnulacion){
     Yii::error("motivo anulacion pedido: ".$actionDir);
     //obteniendo codigigo de anulacion
      $dataAnnulacion = Yii::$app->db->createCommand("Select * from motivosanulacion where Code='{$motivoAnulacion}'")->queryOne();

        $datos=["U_XM_Anulacion"=>$dataAnnulacion['U_TipoAnulacion'],"U_EXX_FE_ANULACION_MOTIVO"=>$dataAnnulacion['codFEX']];
        $serviceLayer = new Servislayer();
         Yii::error("motivo anulacion pedido: "."$actionDir($docEntry)"." -> ");
        $serviceLayer->actiondir = "$actionDir($docEntry)";
        $respuesta = $serviceLayer->executePatchPut('PATCH', $datos);
        Yii::error("motivo anulacion pedido: ".$docEntry." -> ".json_encode($respuesta));
        if($respuesta){
            return 1;
        }else{
            Yii::error("error motivo anulacion: ".json_encode($respuesta));
            return 0;
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

public function exportSapcliente($cnf_usuario='',$id=0) {
    $respuestaEnvio=[];
    try {
      $serviceLayer = new Servislayer();
      Yii::error('inicio sincronizacion cliente2');
      if ($id==0){
        $clientes = modelcli::find()
        ->where('(Mobilecod<>0 ) and StatusSend = 0')
        ->limit(50)
        ->all();
      }else{
        $clientes = modelcli::find()
              ->where('id ='.$id)
              ->limit(50)
              ->all();
      }

      yii::error("Cliente Registrar".json_encode($clientes));

      $serviceLayer->actiondir = "BusinessPartners";
      if (count($clientes)) {
          foreach ($clientes as $value) {
               Yii::error('RECUPERANDO GRUPO CLIENTES DOSIFICACION');
            //    Yii::error('CLIENTE--->'. $value);
              $grupoCliente =Yii::$app->db->createCommand("select * from usuarioconfiguracion where idUser= '{$value["User"]}'")->queryOne();
              $grupoClienteDosificacion =$grupoCliente["grupoClienteDosificacion"];
              //if ($grupoCliente != null) $grupoClienteDosificacion = $grupoCliente->grupoClienteDosificacion;

              //obteniendo cuenta cliente region
              $dataCuentaCliente =Yii::$app->db->createCommand("select cuentaClientesRegion from equipoxcuentascontables
               INNER JOIN userequipox on equipoxcuentascontables.equipoxId=userequipox.equipoxId
               where userequipox.userId='{$value["User"]}'")->queryOne();

              $industria = $value->Industry;
              if ($industria == null || $industria == '' || $industria == '0' || $industria == 0  ) $industria = '-1';

              $clienteNuevo = [
                  "CardCode" => $value->CardCode,
                  "CardName" => $value->CardName,
                  "CardType" => 'cCustomer',
                  "Address" => $value->Address,
                  "CreditLimit" => $value->CreditLimit,
                  "MaxCommitment" => $value->MaxCommitment,
                  "DiscountPercent" => $value->DiscountPercent,
                  "SalesPersonCode" => $value->SalesPersonCode,
                  "Currency" => $value->Currency,
                  "County" => $value->County,
                  "Country" => "PE",
                  "CurrentAccountBalance" => $value->CurrentAccountBalance,
                  "NoDiscounts" => "tNO",
                //   "PriceMode" => "pmGross",
                //   "U_EXX_TIPOPERS" => $value->TipoDoumento == "1" ? "TPN" : ($value->TipoDoumento == "6" ? "TPJ" : "SND"),
                //   "U_EXX_TIPODOCU" => $value->TipoDoumento,
                   //Campos nuevos
                "U_EXX_TIPOPERS" => $value->U_EXX_TIPOPERS,
                "U_EXX_TIPODOCU" => $value->U_EXX_TIPODOCU,
                "U_EXX_APELLPAT" => $value->U_EXX_APELLPAT,
                "U_EXX_APELLMAT" => $value->U_EXX_APELLMAT,
                "U_EXX_PRIMERNO" => $value->U_EXX_PRIMERNO,
                "U_EXX_SEGUNDNO" => $value->U_EXX_SEGUNDNO,
                  "FederalTaxID" => $value->FederalTaxId,   
                  "Phone1" => $value->PhoneNumber,
                  "PayTermsGrpCode" => "5",
                  "U_XM_Latitud" => $value->Latitude,
                  "U_XM_Longitud" => $value->Longitude,
                  "U_XM_Mobilecod" => $value->Mobilecod,
                  "GroupCode" =>  $value->GroupCode,
                  "Phone2" => $value->Phone2,
                  "Cellular" => $value->Cellular,
                  "EmailAddress" => $value->EmailAddress,
                  "FreeText" => $value->FreeText . " Contacto: " . $value->ContactPerson . " Usuario Xmobile: " . $value->User,
                  "CardForeignName" => $value->CardForeignName,
                  "Territory" => $value->Territory,
                  "Properties1" => $value->Properties1,
                  "Properties2" => $value->Properties2,
                  "Properties3" => $value->Properties3,
                  "Properties4" => $value->Properties4,
                  "Properties5" => $value->Properties5,
                  "Properties6" => $value->Properties6,
                  "Properties7" => $value->Properties7,
                  "PriceListNum" => $value->PriceListNum,
                  "ContactEmployees" => [],
                  "BPAddresses" => [],
                  "DebitorAccount" => $dataCuentaCliente['cuentaClientesRegion'],
                  /**PROPIO DE COMPANEX**/
                   //"U_XM_DosificacionSocio" => $grupoClienteDosificacion,
                   //"Industry" => $industria
                   //"U_xMOB_Plataforma"=>"M",
                   //"U_Regional"=>$value->ccu1?$value->ccu1:'',
                   //"U_CanalVentas"=>$value->ccu3?$value->ccu3:''
              ];

              /*****************CAMPOS PERSONALIZADOS************************ */
              $clienteNuevo=Sapenviodoc::getCamposPersonalizados($clienteNuevo,$value,$cnf_usuario);
              /******************CONTACTOS *********************/
               $contactos = Contactos::find()->where("cardCode = '".$value->CardCode."' and idCliente=$id")->all();
                foreach($contactos as $contacto){
                    $dividir = explode(' ',$contacto->nombre);
                    $nombre = '';
                    $nombre2 = '';
                    $apellido = '';
                    switch(count($dividir)){
                        case 1: $nombre = $dividir[0]; break;
                        case 2: $nombre = $dividir[0]; $apellido = $dividir[1]; break;
                        case 3: $nombre = $dividir[0]; $apellido = $dividir[1]; $nombre2 = $dividir[2]; break;
                    }
                    $nuevoContacto = [
                        "CardCode" => $value->CardCode,
                        "Name" => $contacto->nombre,
                        "FirstName" => $nombre,
                        "MiddleName" => $nombre2,
                        "LastName" => $apellido,
                        "Phone1" => $contacto->telefono1,
                        "Phone2" => $contacto->telefono2,
                        "MobilePhone" => $contacto->celular,
                        "Address" => $contacto->direccion,
                        "E_Mail" => $contacto->correo,
                        "Remarks1"=> $contacto->comentarios,
                        "Title" => substr($contacto->titulo, 0, 9)
                    ];
                    array_push($clienteNuevo["ContactEmployees"], $nuevoContacto);
                }
                /****************************DIRECCIONES DE LOS CLIENTES*************************/
                 $direcciones = Clientessucursales::find()->where("CardCode = '".$value->CardCode."' and idCliente=$id ")->all();
                 foreach($direcciones as $direccion){
                    //$nombresucursal = explode(" ", $sucursal["nombre"]);
                    if ($direccion["AdresType"] == "B") {
                        $auxtipo = "bo_BillTo";
                        $city = "";
                    } else {
                        $auxtipo = "bo_ShipTo";
                        $city = $value->ccu2;
                    }
                    $nuevaDireccion = array(
                        "AddressName" => $direccion->AddresName,
                        "Street" => $direccion->Street,
                        //"State" => $direccion->State,
                        //"FederalTaxID" => $direccion->FederalTaxId,
                        //"TaxCode" => $direccion->TaxCode,
                        "Block"=> $direccion->Street,
                        "AddressType" => $auxtipo,

                        "Country"=> "PE",
                       // "U_XM_Latitud" => $direccion["u_lat"],
                        //"U_XM_Longitud" => $direccion["u_lon"],
                        //"U_Territorio" => $direccion["u_territorio"],
                        "City"=>"Lima"//$city,
                    );
                     //adjuntando campos dinamicos
                    $queryCamposUsuario="select * from  vi_camposusuarios where tabla='clientessucursales'";
                    $dataCampoUser=Yii::$app->db->createCommand($queryCamposUsuario)->queryAll();
                    yii::error("datacamposUsusario".json_encode($dataCampoUser));
                    foreach ($dataCampoUser as $campouser ) {
                        yii::error("datacamposUsusario para enviar".json_encode($campouser));
                        yii::error("datacamposUsusario para enviar".$campouser['Campo_Sap']);

                        $nuevaDireccion[$campouser['Campo_Sap']]=$direccion[$campouser['campo_midd']];
                    yii::error("datacamposUsusario para enviar".json_encode($nuevaDireccion));

                    }
                     array_push($clienteNuevo["BPAddresses"], $nuevaDireccion);
                 }



                 Yii::error('OBJETO FINAL CLIENTE');
                 Yii::error(json_encode($clienteNuevo));
                  /****************SERIES*********************/
                  //validando si ocupa serie
                 $dataValidCardCode=Sapenviodoc::obtenerConfig('s_cliente');
                 if($dataValidCardCode && $dataValidCardCode['valor']==1){
                    // se verifica en la configuracion si la series de pedido si esta activo y la configuracion de usuario en series sea diferente de null)

                    $configUsuario = Yii::$app->db->createCommand("SELECT seriesCliente FROM usuarioconfiguracion WHERE idUser=".$value->User)->queryOne();
                    Yii::error("Configuracion usuario usa series pedido: ".$configUsuario['seriesCliente']);
                    if(!is_null($configUsuario['seriesCliente'])){
                       Yii::error(" Usa SERIES de la configuracion del usuario: ");
                       $clienteNuevo["Series"] = $configUsuario['seriesCliente'];
                    }


                    /*$serie = Yii::$app->db->createCommand("SELECT valor FROM configuracion WHERE parametro LIKE 's_defecto_cliente'")->queryOne();
                    $clienteNuevo['Series'] = $serie['valor'];//camsa*/
                 }

              /*************************************/
              //adjuntando campos dinamicos
              $queryCamposUsuario="select * from  vi_camposusuarios where tabla='clientes'";
              $dataCampoUser=Yii::$app->db->createCommand($queryCamposUsuario)->queryAll();
              yii::error("datacamposUsusario".json_encode($dataCampoUser));
              foreach ($dataCampoUser as $campouser ) {
                  $clienteNuevo[$campouser['Campo_Sap']]=$value[$campouser['campo_midd']];
              }
              /******************* Verifica campos de tabla configuracion ***********************/
              $campos = Yii::$app->db->createCommand("SELECT * FROM `configuracion` WHERE estado = 1 and parametro like '%cliente_std%' and valor4 = 'w'")->queryAll();
              foreach($campos as $row => $val){
                  $cmp = $campos[$row]['valor2'];
                  $campVal = Yii::$app->db->createCommand("SELECT ".$campos[$row]['parametro']." FROM `clientes` WHERE id = ".$value->id)->queryAll();
                  $valor = $campVal[0][$campos[$row]['parametro']];
                  Yii::error("DATA campo y valor : " . $cmp . ' -- '. $valor);
                  if(!is_null($valor))
                      $clienteNuevo[$cmp] = $valor;
              }
              /**********************************************************************************/
              Yii::error("enviando nuevo cleinte : ");
              Yii::error(json_encode($clienteNuevo));
              $respuesta = $serviceLayer->executePost($clienteNuevo);
              Yii::error("DATA respuesta service : " . json_encode($respuesta));
              //var_dump($respuesta);
              //die;
              Yii::error("DATA respuesta service : " . $respuesta->CardCode);

              if ($respuesta && $respuesta->CardCode) {//IF F1

                $cardCodeNuevo="";
                $mensajeEnvio="";
                if(isset($respuesta->CardCode)){
                  if($respuesta->CardCode!=""){
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                    $value->StatusSend = 1;
                    $value->CardCode = $respuesta->CardCode;
                    $value->save(false);
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 1;')->execute();
                    $serie = Yii::$app->db->createCommand("UPDATE `cabeceradocumentos` SET CardCode = '".$respuesta->CardCode."'  WHERE CardCode = '".$clienteNuevo['CardCode']."'")
                            ->execute();
                    $serie = Yii::$app->db->createCommand("UPDATE `contactos` SET CardCode = '".$respuesta->CardCode."'  WHERE CardCode = '".$clienteNuevo['CardCode']."'")
                            ->execute();
                    $serie = Yii::$app->db->createCommand("UPDATE `clientessucursales` SET CardCode = '".$respuesta->CardCode."'  WHERE CardCode = '".$clienteNuevo['CardCode']."'")
                            ->execute();
                    if ($id>0){
                      //return "Correcto";
                      $cardCodeNuevo=$respuesta->CardCode;
                      return $respuesta;
                      //$mensajeEnvio="Correcto";
                    }
                  }
                  else{
                    $cardCodeNuevo=0;
                    $mensajeEnvio="Error! no guardo el registro en SAP: cardCode vacio ".json_encode($respuesta);
                    return "Error al registrar en sap: " .Sapenviodoc::obtenerMensajeError($respuesta);
                    //return "Error! no guardo el registro en SAP: cardCode vacio ".json_encode($respuesta);
                  }
                }else{
                    $cardCodeNuevo=0;
                    $mensajeEnvio="Error! no guardo el registro en SAP ".json_encode($respuesta) ;
                    return Sapenviodoc::obtenerMensajeError($respuesta);
                    //return "Error! no guardo el registro en SAP ".json_encode($respuesta) ;
                }

                 /* if (isset($respuesta->message)) {
                      Yii::error("ID-MID:{$value->id};DATA-" . json_encode($respuesta->message->value));
                     return Sapenviodoc::obtenerMensajeError($respuesta);
                  } else {
                      Yii::error("ID-MID:{$value->id};DATA-" . json_encode($respuesta));
                      return "Error al registrar no se pudo obtener el error de sap ";
                  }
                  if ($id>0){
                    return "Error! no guardo el registro en SAP ".json_encode($respuesta) ;
                  }*/

              } //FIN IF F1
              else {///ELSE F1
                    return Sapenviodoc::obtenerMensajeError($respuesta);
              }///FIN ELSE F1
            /*  array_push($respuestaEnvio, [
                "CardCode" => $cardCodeNuevo,
                "mensaje" => $mensajeEnvio,
                estado=>
              ]);*/

          }
      }
    return "No se encontro el cliente para exportar a sap ";
    }catch (\Exception $e) {
      Yii::error("Error Exception: ".$e);
      return $e;
    }catch (\Throwable $e) {
      Yii::error("Error Throwable: ".$e);
      return $e;
    }
}

public function clienteUpdate($response,$contactos,$sucursales,$cnf_usuario){
    $respuestaEnvio =[];
    try {
      $serviceLayer = new Servislayer();
      $datosCliente = [
        "CardName" => $response["CardName"],
        "CardType" => 'cCustomer',
        "Address" => $response["Address"],
        "CreditLimit" => $response["CreditLimit"],
        "MaxCommitment" => $response["MaxCommitment"],
        "DiscountPercent" => $response["DiscountPercent"],
        //"PriceListNum" => $response["PriceListNum"],
        "SalesPersonCode" => $response["SalesPersonCode"],
        //"Currency" => $response["Currency"],
        "County" => $response["County"],
        "Country" => $response["Country"],
        //"CurrentAccountBalance" => $response["CurrentAccountBalance"],
        "NoDiscounts" => "tNO",
        // "PriceMode" => "pmGross",
        "FederalTaxID" => $response["FederalTaxId"],
        "Phone1" => $response["PhoneNumber"],
        //"PayTermsGrpCode" => "-1",
        "U_XM_Latitud" => $response["Latitude"],
        "U_XM_Longitud" => $response["Longitude"],
        "GroupCode" => $response["GroupCode"],
        "Phone2" => $response["Phone2"],
        "Cellular" => $response["Cellular"],
        "EmailAddress" => $response["EmailAddress"],
        "Territory" => $response["Territory"],
        //"Industry" => $response["Industry"],
        "FreeText" => $response["FreeText"] . " Contacto: " . $response["ContactPerson"] . " Usuario Xmobile: " . $response["User"],
        "CardForeignName" => $response["CardForeignName"],
        "Properties1" => $response["Properties1"],
        "Properties2" => $response["Properties2"],
        "Properties3" => $response["Properties3"],
        "Properties4" => $response["Properties4"],
        "Properties5" => $response["Properties5"],
        "Properties6" => $response["Properties6"],
        "Properties7" => $response["Properties7"],
        //"U_xMOB_Plataforma"=>"M",
        //"U_Regional"=>$response['ccu1']?$response['ccu1']:'',
       // "U_CanalVentas"=>$response['ccu3']?$response['ccu3']:'',
        "ContactEmployees" => $contactos,
        "BPAddresses"=> $sucursales
      ];
      /********OBTIENE CAMPOS PERSONALIZADOS*************** */
      $datosCliente=Sapenviodoc::getCamposPersonalizados($datosCliente,$response,$cnf_usuario);

      $cardCode=$response["CardCode"];
      Yii::error("CARDCODE77: ". $cardCode);
      Yii::error("ENTRO AL UPDATE movil :: " . json_encode($datosCliente));
      $serviceLayer->actiondir = "BusinessPartners('".$cardCode."')";
      Yii::error($serviceLayer->actiondir);
      $clienteSap = $serviceLayer->executePatchPut('PATCH', $datosCliente);

      Yii::error("RESPUESTA SAP :: " . json_encode($clienteSap));

      if (!$clienteSap) {
          if (isset($clienteSap->message)) {
              Yii::error("ID-MID:{$response->id};DATA-" . json_encode($clienteSap->message->value));
          } else {
              Yii::error("ID-MID1:{$response->id};DATA-" . json_encode($clienteSap));
          }
          $mensajeEnvio= "Error! no se actualizo el registro en SAP.";
          //return Sapenviodoc::obtenerMensajeError($clienteSap);
          return "Error al Actualizar los datos del cliente";

      } else {
        //$clienteNuevo = Clientes::find()->where("CardCode = '{$cardCode}'")->one();
        //$clienteNuevo->StatusSend = 1;
        //$clienteNuevo->save(false);
        Yii::error("Actualizacion exitosa" . json_encode($clienteSap));
        $serviceLayer->actiondir = "BusinessPartners('".$cardCode."')";
        Yii::error(" Control Ser: ". $serviceLayer->actiondir);
        $responseSap = $serviceLayer->executex2();

        return $responseSap;
      }
      /*array_push($respuestaEnvio, [
        "CardCode" => $cardCode,
        "mensaje" => $mensajeEnvio,
      ]);*/
     // return $respuestaEnvio;
    }catch (\Exception $e) {
      Yii::error("Error Exception: ".$e);
      return "Error Exception: ".$e;

    }catch (\Throwable $e) {
      Yii::error("Error Throwable: ".$e);
      return "Error Throwable: ".$e;
    }
  }

  private function obtenerMensajeError($respuesta){
    $response=$respuesta->error;
    $response=$response->message;
    $response=$response->value;
    return $response?$response:'No se puede obtener la respuesta de error';


}

private function getCamposPersonalizados($clienteNuevo,$value,$cnf_usuario){
      //ACTUALIZA CAMPOS PERSONALIZADOS
    Yii::error(" datos de entrada desde el movil".json_encode($value));
      if($cnf_usuario['cnf_canalVenta']==1){
        $clienteNuevo['U_XM_Canal'] =$value["CAMPOUSER1"];
        $clienteNuevo['U_XM_Subcanal'] = $value["CAMPOUSER2"];
        $clienteNuevo['U_XM_TipoTienda'] = $value["CAMPOUSER3"];
        $clienteNuevo['U_XM_Cadena'] =$value["CAMPOUSER4"];
        $clienteNuevo['U_XM_CadenaDesc'] =$value["CAMPOUSER6"];
       // $clienteNuevo['U_XM_CadenaDesc'] = $value["CAMPOUSER5"];
       // $clienteNuevo['ChannelBP'] = $value["cadenaconsolidador"];
      }
      if($cnf_usuario['cnf_fex']==1){
        $clienteNuevo['U_EXX_FE_CodDocIden'] =$value["Fex_tipodocumento"];
        $clienteNuevo['U_EXX_FE_Complem'] = $value["Fex_complemento"];
        $clienteNuevo['U_EXX_FE_CodExcep'] = $value["Fex_codigoexcepcion"];
      }
      // Adicionar mas campos de configuracion
    return $clienteNuevo;

}

public function obtenerConfig($campo){
    $sql = "SELECT * FROM configuracion WHERE parametro='$campo' ";
    return Yii::$app->db->createCommand($sql)->queryOne();
}

}



