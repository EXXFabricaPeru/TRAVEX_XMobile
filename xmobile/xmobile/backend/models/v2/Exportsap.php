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
use backend\models\Cabeceradocumentos;
use backend\models\Clientes;
use backend\models\Lotes;
use backend\models\Seriesproductos;
use backend\models\Seriesmarketing;
use backend\models\Unidadesmedida;
use backend\models\TraspasosCabecera;
use backend\models\TraspasosDetalle;
use backend\models\TraspasosLote;
use backend\models\TraspasosSerie;
use backend\models\Productosalmacenes;
use backend\models\Pagos;
use backend\models\Contactos;
use backend\models\Clientessucursales;
use backend\models\Anulaciondocmovil;
use backend\models\hana;

class Exportsap extends Model {

    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }

public function pedido($idDocPedido='') {
  Yii::error('inicio  pedido');
  $serviceLayer = new Servislayer();
  $miODBC=new Sincronizar();
  $serviceLayer->actiondir = "Orders";
  if($idDocPedido==''){
      $pedidos = Cabeceradocumentos::find()
          ->where("DocEntry is null and DocTotal > 0 AND DocType = 'DOP' AND (estado = 3 OR estado = 6)")
          ->with('detalledocumentos')
          ->limit(150)
          ->asArray()
          ->all();
  }
  else{
      $pedidos = Cabeceradocumentos::find()
          ->where("DocEntry is null and DocTotal > 0 AND idDocPedido = '".$idDocPedido."' AND (estado = 3 OR estado = 6)")
          ->with('detalledocumentos')
          ->limit(150)
          ->asArray()
          ->all();
  }
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          // Yii>>
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
          $datos = [
              "CardCode" => $pedido["CardCode"],
              "DocDueDate" => $pedido["DocDueDate"],
              "DocType" => $pedido["DocType"],
              "CardName" => $pedido["CardName"],
              "Address2" =>  $dataShipToAddress && $dataShipToAddress['Street']?$dataShipToAddress['Street']:'',//envio
              "Address" => $dataBillToAddress && $dataBillToAddress['Street']?$dataBillToAddress['Street']:'',//cobro
              "DocTotal" => $pedido["DocTotalPay"],
              "PaymentGroupCode" => $pedido["PayTermsGrpCode"],
              "SalesPersonCode" => $pedido["SlpCode"],
              //
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
              "U_XM_Autorizacion" => Sapenviodoc::ObtenerAutorizacion($pedido["detalledocumentos"],'pedido')
          ];
          Yii::error('campos USUARIO ******:'. json_encode($datos));
          $auxClone = $pedido["clone"];
          $aLineas = [];
          foreach ($pedido["detalledocumentos"] as $lineaPedido) {
              $lote = [];
              $lote=Sapenviodoc::LoteCode($lineaPedido["ItemCode"],$lineaPedido["Quantity"], $pedido["idDocPedido"],$lineaPedido["unidadid"],$lineaPedido["WhsCode"]);
             // $unidadNegociox = $this->unidadNegocio($lineaPedido["ItemCode"]);$lineaP->unidadid,$lineaP->WhsCode

               /// inicio datos centros de costo
              $aux_cc_c1="SELECT * from vendedores where SalesEmployeeCode=".$pedido["SlpCode"];
              $respuesta_aux_cc_c1 = Yii::$app->db->createCommand($aux_cc_c1)->queryOne();
              $aux_cc1=$respuesta_aux_cc_c1["U_Regional"];
              $aux_cc2=$respuesta_aux_cc_c1["U_Area"];

              $aux_cc_c3="SELECT * from clientes where CardCode='".$pedido["CardCode"]."'";
              $respuesta_aux_cc_c3 = Yii::$app->db->createCommand($aux_cc_c3)->queryOne();

              $data_aux_cc_c3 = json_encode(array("accion" => 400,"codeSubCanal"=>$respuesta_aux_cc_c3["codesubcanal"]));
              $respuesta_c3 = $miODBC->executex($data_aux_cc_c3);
              $respuesta_c3 = json_decode($respuesta_c3);
              
              foreach ($respuesta_c3 as $key)  $aux_cc3=$key->U_Centrodecosto;

              Yii::error("CENTRO DE COSTO 3  DATOS CLIENTE: ".$pedido["idDocPedido"]." - ".$aux_cc3);  

              $aux_cc_c4="SELECT * from productos where ItemCode='".$lineaPedido["ItemCode"]."' limit 1";
              $respuesta_aux_cc_c4 = Yii::$app->db->createCommand($aux_cc_c4)->queryOne();

              $data_aux_cc_c4 = json_encode(array("accion" => 401,"marca"=>$respuesta_aux_cc_c4["producto_std7"]));
              $respuesta_c4 = $miODBC->executex($data_aux_cc_c4);
              $respuesta_c4 = json_decode($respuesta_c4);
              
              foreach ($respuesta_c4 as $key)  $aux_cc4=$key->U_Centrodecosto;

              Yii::error("CENTRO DE COSTO 4  DATOS PRODUCTO: ".$pedido["idDocPedido"]." - ".$aux_cc4);  
              /// fin obtencion de centros de costo

             $gastosAdicionales=Sapenviodoc::gastosAdicionalesLinea($lineaPedido["ICEE"],$lineaPedido["ICEP"],$lineaPedido["LineTotalPay"],$aux_cc1,$aux_cc2,$aux_cc3,$aux_cc4);
             // $gastosAdicionales=[];
              if($lineaPedido["U_4DESCUENTO"]>0){
                  $descuento=($lineaPedido["U_4DESCUENTO"]*100/($lineaPedido["Quantity"]*$lineaPedido["Price"]));
              }else{
                  $descuento=0;
              }
             

              $linea = [
                  "DocNum" => $lineaPedido["DocNum"],
                  "LineNum " => $lineaPedido["LineNum"],
                  "ItemCode" => $lineaPedido["ItemCode"],
                  "Dscription" => $lineaPedido["Dscription"],
                  "Quantity" => $lineaPedido["Quantity"],
                  "SalesPersonCode" => $pedido["SlpCode"],
                  //"Price" => $lineaPedido["Price"],
                  "GrossPrice" => $lineaPedido["Price"],
                 // "GrossTotal" => $lineaPedido["LineTotalPay"],
                  "Currency" => $lineaPedido["Currency"],
                  "TaxCode" => "IVA",
                  "DiscountPercent" =>$descuento,
                  "LineTotal" => $lineaPedido["LineTotal"],
                  "WarehouseCode" => $lineaPedido["WhsCode"],
                  "UoMEntry" => Sapenviodoc::unidadEntry($lineaPedido["unidadid"]),
               
                  "BatchNumbers" => $lote,
                  // centros de costo
                  "CostingCode"=>  $aux_cc1,
                  "CostingCode2"=>  $aux_cc2,
                  "CostingCode3"=>  $aux_cc3,
                  "CostingCode4"=>  $aux_cc4,
                  "COGSCostingCode"=>  $aux_cc1,
                  "COGSCostingCode2"=>  $aux_cc2,
                  "COGSCostingCode3"=>  $aux_cc3,
                  "COGSCostingCode4"=>  $aux_cc4,                        
                  // fin centros de costo
                  "U_XM_Bonif"=>$lineaPedido["bonificacion"],
                  "U_XM_CodeBonif"=>$lineaPedido["codeBonificacionUse"],
                  "U_XM_ListaPrecios"=>$lineaPedido["listaPrecio"],
                  "DocumentLineAdditionalExpenses" =>$gastosAdicionales
              ];
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
                      $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne(); 
                      $aux_doc_org = $aux_doc_org["DocEntry"]; 
                       Yii::error('objeto entrega linea cont: clon dfo2  docentry'.$aux_doc_org);
                      $aux_doctipo= Yii::$app->db->createCommand("select DocType from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne();
                      $aux_doctipo  = $aux_doctipo["DocType"];
                      Yii::error('objeto pedido: clon '.$auxClone);
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
                      $linea["BaseEntry"]=$aux_doc_org;
                      $linea["BaseLine"]=intval($lineaPedido["BaseLine"]);
                      $linea["BaseType"]=$aux_doc_base;
                      if(is_null($lineaPedido["BaseLine"])){
                          $linea["BaseEntry"]="";
                          $linea["BaseLine"]="";
                          $linea["BaseType"]=""; 
                      }
                  }  

              }
              
             
              array_push($aLineas, $linea);
          }
          $datos["DocumentLines"] = $aLineas;
          $datos["AddressExtension"]=[
              "ShipToStreet"=> $dataShipToAddress && $dataShipToAddress['Street']?$dataShipToAddress['Street']:'',
             "ShipToCity"=> "Lima", //$dataShipToAddress && $dataShipToAddress['City']?$dataShipToAddress['City']:'',
              "ShipToCounty"=> $dataShipToAddress && $dataShipToAddress['County']?$dataShipToAddress['County']:'',
              "ShipToState"=> $dataShipToAddress && $dataShipToAddress['State']?$dataShipToAddress['State']:'',
              "ShipToCountry"=>  'PE',
              

              "BillToStreet"=> $dataBillToAddress && $dataBillToAddress['Street']?$dataBillToAddress['Street']:'' ,
             "BillToCity"=> "Lima", //$dataBillToAddress && $dataBillToAddress['City']?$dataBillToAddress['City']:'',
              "BillToCounty"=> $dataBillToAddress && $dataBillToAddress['County']?$dataBillToAddress['County']:'',
              "BillToState"=> $dataBillToAddress && $dataBillToAddress['State']?$dataBillToAddress['State']:'',
              "BillToCountry"=> 'PE',
           ];

          $datos["DocumentAdditionalExpenses"]=Sapenviodoc::GastosAdicionalesCab($pedido["DocTotalPay"]);
          Yii::error('objeto pedido envio a sap: '.json_encode($datos));
          $respuesta = $serviceLayer->executePost($datos); //$serviceLayer->executePost(json_encode($datos));
          Yii::error('respuesta pedido: '.json_encode($respuesta));
          if (isset($respuesta->DocEntry)) {
              $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
              $actualizaPedido->DocEntry = $respuesta->DocEntry;
              $actualizaPedido->estado = $pedido['estado'] != 3 ? 11: 4;
              $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz')->format('Y-m-d');
              $actualizaPedido->DocNumSAP = $respuesta->DocNum;
              $actualizaPedido->save(false);
              //Yii::error("ID-MID:{$pedido["id"]};DATA-" . $respuesta->DocEntry);
              if($idDocPedido!='') return true; //repuesta si el pedido es envio desde el xmobile directo
              
          } else {
              Sapenviodoc::guardarlog($datos,$respuesta,'Pedido',$pedido["idDocPedido"]);
              if($idDocPedido!='') return $respuesta; //repuesta si el pedido es envio desde el xmobile directo
              
          }
      }
  }
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
      ->where("DocEntry is null and DocTotal > 0 AND DocType = 'DFA' AND (estado = 3 OR estado = 6)")
      ->with('detalledocumentos')
      ->groupBy('idDocPedido')
      ->having('count(*)=1')
      ->limit(100)
      ->all();
  }
  else{
      $facturas = Cabeceradocumentos::find()
      ->where("DocEntry is null and DocTotal > 0 AND idDocPedido = '".$idDocPedido."' AND (estado = 3 OR estado = 6)")
      ->with('detalledocumentos')
      ->groupBy('idDocPedido')
      ->having('count(*)=1')
      ->limit(100)
      ->all();
  }

  
          
   Yii::error('Listo para el count');

  if (count($facturas)) {

      foreach ($facturas as $key => $factura) {

          Yii::error('factura a mandarse: ' . ($factura->idDocPedido) . "=======================================================================");
          $aLineas = [];
          $lineNumber = 0;
          $totalIT = 0;
          $empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '{$factura->SlpCode}'")->queryOne();
          // se verifica en la configuracion que valor tiene es 1 o 0 si es 1 se consulta a la tabla series (exclusivo para companex)
           $verifica = Yii::$app->db->createCommand("SELECT valor FROM `configuracion` WHERE estado = 1 and parametro = 'lbcc'")->queryOne();
           if($verifica['valor']==0){
              Yii::error(" SERIE LBCC: ");
               $tipofactura=Yii::$app->db->createCommand("select U_series as series from lbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}'")->queryOne();
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

                $q_cuf="SELECT * FROM fex_cufd WHERE codigocontrol ='".$factura->U_LB_NumeroAutorizac."'";
                $resp_cuf= Yii::$app->db->createCommand($q_cuf)->queryOne();
                $fex_sucursal= $resp_cuf["sucursal"];
                $fex_puntoventa= $resp_cuf["puntoventa"]; 
                $fex_metodoPago=Sapenviodoc::metodoPagoFEx($factura->id,$factura->PayTermsGrpCode);
                $fex_tipoDocumento=Sapenviodoc::obtenertipodocumentofex($factura);  
                $sqlSeries="select U_series as series from vi_fexlbcc where equipoId = '{$factura->equipoId}' and papelId='{$factura->papelId}' and codigoSIN='{$fex_tipoDocumento}' "; 
                $xtipofactura = Yii::$app->db->createCommand($sqlSeries)->queryOne();
                Yii::error("Serie de documento:");
                Yii::error($xtipofactura['series']);               

            }else{
                $fex_sucursal= 0;
                $fex_puntoventa= 0; 
                $fex_metodoPago=1;
            }

          $facturaSAP = [
              "DocDate" =>$factura->DocDate,
              "DocDueDate" => $aux_vencimiento, // vencimiento
              "CardCode" => $factura->CardCode,
              "CardName" => $factura->CardName,
              "DocTotal" => $factura->DocTotalPay,
              "DocCurrency" => $factura->DocCur,
              "PaymentGroupCode" => $factura->PayTermsGrpCode,
              "SalesPersonCode" => $factura->SlpCode,
              "TaxDate" => $factura->DocDate,
              "TaxCode" => "IVA",
              "Series"=>$uso_fex==1?$xtipofactura['series']:$tipofactura['series'], //tipo de factura
              "Indicator"=> 4,
              "FederalTaxID" => $factura->U_4NIT,
              "U_LB_NIT" => $factura['U_4NIT'],
              "U_LB_RazonSocial" => $factura['U_4RAZON_SOCIAL'],
              "U_LB_ObjType"=>13,
              "BaseAmount" => round(($factura->DocTotalPay - ($factura->DocTotalPay * 13) / 100), 2),
              "DiscountPercent" => Sapenviodoc::ObtenerPorcentaje($factura->DocTotalPay, $factura->DocTotal),
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
              "U_LB_FechaLimiteEmis" => $limiteEmision["U_FechaLimiteEmision"],
              "U_LB_NumeroFactura" => $factura->UNumFactura,
              "U_LB_CodigoControl" => $factura->ControlCode,
              "U_LB_NumeroAutorizac" => $factura->U_LB_NumeroAutorizac,
              "U_XM_Autorizacion" => Sapenviodoc::ObtenerAutorizacion($factura->detalledocumentos,'factura'),
              "U_XM_Campana"=>$factura["campania"],
              "U_XM_CampUsa"=>$factura["monto"]
          ];
          //Yii::error('campos USUARIO ******:'. json_encode($datos));
         // Yii::error('DETALLE DOCUMENTO =========>'. json_encode($factura->detalledocumentos));

          foreach ($factura->detalledocumentos as $lineaP) {
              //if(intval($lineaP->Price) != 0){
              
              $lote = [];
              $lote=Sapenviodoc::LoteCode($lineaP->ItemCode,$lineaP->Quantity, $factura->idDocPedido,$lineaP->unidadid,$lineaP->WhsCode);
              //$unidadNegociox = $this->unidadNegocio($lineaP->ItemCode);
              /// inicio datos centros de costo
              $aux_cc_c1="SELECT * from vendedores where SalesEmployeeCode=".$factura["SlpCode"];
              $respuesta_aux_cc_c1 = Yii::$app->db->createCommand($aux_cc_c1)->queryOne();
              $aux_cc1=$respuesta_aux_cc_c1["U_Regional"];
              $aux_cc2=$respuesta_aux_cc_c1["U_Area"];

              $aux_cc_c3="SELECT * from clientes where CardCode='".$factura["CardCode"]."'";
              $respuesta_aux_cc_c3 = Yii::$app->db->createCommand($aux_cc_c3)->queryOne();

              $data_aux_cc_c3 = json_encode(array("accion" => 400,"codeSubCanal"=>$respuesta_aux_cc_c3["codesubcanal"]));
              $respuesta_c3 = $miODBC->executex($data_aux_cc_c3);
              $respuesta_c3 = json_decode($respuesta_c3);
              
              foreach ($respuesta_c3 as $key)  $aux_cc3=$key->U_Centrodecosto;

              Yii::error("CENTRO DE COSTO 3  DATOS CLIENTE: ".$factura["idDocPedido"]." - ".$aux_cc3);  

              $aux_cc_c4="SELECT * from productos where ItemCode='".$lineaP["ItemCode"]."'";
              $respuesta_aux_cc_c4 = Yii::$app->db->createCommand($aux_cc_c4)->queryOne();

              $data_aux_cc_c4 = json_encode(array("accion" => 401,"marca"=>$respuesta_aux_cc_c4["producto_std7"]));
              $respuesta_c4 = $miODBC->executex($data_aux_cc_c4);
              $respuesta_c4 = json_decode($respuesta_c4);
              
              foreach ($respuesta_c4 as $key)  $aux_cc4=$key->U_Centrodecosto;

              Yii::error("CENTRO DE COSTO 4  DATOS PRODUCTO: ".$factura["idDocPedido"]." - ".$aux_cc4);  
              /// fin obtencion de centros de costo

              $gastosAdicionales=Sapenviodoc::gastosAdicionalesLinea($lineaP->ICEE,$lineaP->ICEP,$lineaP->LineTotalPay,$aux_cc1,$aux_cc2,$aux_cc3,$aux_cc4);
             // $gastosAdicionales=[];
              if($lineaP->U_4DESCUENTO > 0){
                  $descuento=Round(($lineaP->U_4DESCUENTO*100/($lineaP->Quantity * $lineaP->Price)),2);
              }else{
                  $descuento=0;
              }
              $linea = [
                  "BatchNumbers" => $lote,
                  "SerialNumbers" => Sapenviodoc::NumeroSerie($lineaP->ItemCode, $lineaP->Quantity, $factura->idDocPedido),
                  "ItemCode" => $lineaP->ItemCode,
                  "ItemDescription" => $lineaP->Dscription,
                  "Quantity" => $lineaP->Quantity,
                  "SalesPersonCode" => $pedido["SlpCode"],
                  //"Price"=> 16.290,´                        
                  "Currency" =>$lineaP->Currency,
                  //"DiscountPercent" => $this->ObtenerPorcentaje($lineaP->LineTotalPay, $lineaP->LineTotal),
                  "WarehouseCode" => $lineaP->WhsCode,
                  "SalesPersonCode" => $factura->SlpCode,
                  "TreeType" => $lineaP->TreeType,
                  "TaxCode" => "IVA",
                  "DiscountPercent" => $descuento,
                  //"LineTotal" => round($lineaP->LineTotalPay - (($lineaP->LineTotalPay * 13) / 100), 2),
                  "LineTotal" => $lineaP->LineTotal,
                  "TaxPercentagePerRow" => 13.0,
                  "LineStatus" => "bost_Open",
                  "OpenAmount" => round($lineaP->LineTotalPay - (($lineaP->LineTotalPay * 13) / 100), 2),
                  "UoMEntry" => Sapenviodoc::unidadEntry($lineaP->unidadid),
                  "UoMCode" => $lineaP->unidadid,
                  "GrossPrice" => $lineaP->Price,
                  //"GrossTotal" => $lineaP->LineTotalPay,
                  "ShipDate" => Sapenviodoc::facturaReserva($factura->DocDueDate, $factura->Reserve) ? $factura->DocDueDate : null,
                  "BackOrder" => Sapenviodoc::facturaReserva($factura->DocDueDate, $factura->Reserve) ? "tYES" : "tNO",
                  "ActualDeliveryDate" => Sapenviodoc::facturaReserva($factura->DocDueDate, $factura->Reserve) ? null : Carbon::today('America/La_Paz')->format('Y-m-d'),
                 // "COGSCostingCode" => $empleadoCiudad['HomeState'],
                  //"CostingCode2" => $unidadNegociox,
                  "U_XM_ListaPrecios"=>$lineaPedido["ListaPrecios"],
                  // centros de costo
                  "CostingCode"=>  $aux_cc1,
                  "CostingCode2"=>  $aux_cc2,
                  "CostingCode3"=>  $aux_cc3,
                  "CostingCode4"=>  $aux_cc4,
                  "COGSCostingCode"=>  $aux_cc1,
                  "COGSCostingCode2"=>  $aux_cc2,
                  "COGSCostingCode3"=>  $aux_cc3,
                  "COGSCostingCode4"=>  $aux_cc4,                        
                  // fin centros de costo

                  "U_XM_Bonif"=>$lineaPedido["bonificacion"],
                  "U_XM_CodeBonif"=>$lineaPedido["codeBonificacionUse"],
                  "LineTaxJurisdictions" => [
                      [
                          "JurisdictionCode" => "IVA",
                          "TaxRate" => 13.0
                      ]
                  ],
                  "DocumentLineAdditionalExpenses" =>$gastosAdicionales
              ];
              if($fex_tipoDocumento=="14"){
                $sql_aux_prod=" select producto_std3,producto_std4 from productos where itemcode='".$lineaP->ItemCode."'";
                $miaux_prod= Yii::$app->db->createCommand($sql_aux_prod)->queryOne();
                $linea["U_EXX_FE_AlicuotaPorcentual"]=$miaux_prod["producto_std3"];
                $linea["U_EXX_FE_AlicuotaEspecifica"]=$miaux_prod["producto_std4"];
            }
              if (intval($lineaP["BaseEntry"])!=0){
                  $linea["BaseEntry"]=intval($lineaP["BaseEntry"]);
                  $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                  $linea["BaseType"]=intval($lineaP["BaseType"]);
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
                  $linea["BaseEntry"]=$aux_doc_org;
                  $linea["BaseLine"]=intval($lineaP["BaseLine"]);
                  $linea["BaseType"]=$aux_doc_base;
                  if(is_null($lineaP["BaseLine"])){
                      $linea["BaseEntry"]="";
                      $linea["BaseLine"]="";
                      $linea["BaseType"]=""; 
                  }
                  
               } 
              $totalIT += round(($lineaP->LineTotalPay * self::IT) / 100, 2);
              Yii::error("LINEA DE DOCUMENTO =======> " . json_encode($linea));
              array_push($aLineas, $linea);

              $lineNumber++;
              //}
          }

          $auxCuotas=Sapenviodoc::obtenercuotasFactura($factura->PayTermsGrpCode,$factura->DocTotalPay,$factura->DocDueDate);
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
          $facturaSAP["DocumentAdditionalExpenses"]=Sapenviodoc::GastosAdicionalesCab($factura->DocTotalPay);
          Yii::error('FACTURA-ENVIO' . json_encode($facturaSAP));
          //echo 'FACTURA-ENVIO';
          //print_r($facturaSAP);
          $respuesta = $serviceLayer->executePost($facturaSAP);

          Yii::error('FACTURA-RESPUESTA' . json_encode($respuesta));

          if (isset($respuesta->DocEntry)) {
              $factura->DocEntry = $respuesta->DocEntry;
              $factura->estado = $factura->estado != 3 ? 11 : 4;
              $factura->fechaupdate = Carbon::today('America/La_Paz');
              $factura->DocNumSAP = $respuesta->DocNum;
              $factura->save(false);
              if($idDocPedido!='') return true; //repuesta si la factura se envio desde el xmobile en linea
          } else {
              Sapenviodoc::guardarlog($facturaSAP,$respuesta,'Factura',$factura["idDocPedido"]);
              if($idDocPedido!='') return $respuesta; //repuesta si el factura es envio desde el xmobile directo
          }
      }
  }
}

public function oferta($idDocPedido='') {/**!**/
  Yii::error('inicio ofertaMC');
  $serviceLayer = new Servislayer();
  $serviceLayer->actiondir = "Quotations";
  if($idDocPedido==''){
    $pedidos = Cabeceradocumentos::find()
    ->where("DocEntry is null and DocTotal > 0 AND DocType = 'DOF' AND (estado = 3 OR estado = 6)")
    ->with('detalledocumentos')
    ->limit(50)
    ->asArray()
    ->all(); 
  }
  else{
    $pedidos = Cabeceradocumentos::find()
    ->where("DocEntry is null and DocTotal > 0 AND idDocPedido = '".$idDocPedido."' AND (estado = 3 OR estado = 6)")
    ->with('detalledocumentos')
    ->limit(50)
    ->asArray()
    ->all();
  }
  
  Yii::error('pedidos ofertas:'. json_encode($pedidos));
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          //$empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadempleado where idUser = {$pedido["idUser"]}")->queryOne();
          //Yii::error('Query oferta'. "select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '{$pedido["SlpCode"]}'");
          $empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '{$pedido["SlpCode"]}'")->queryOne();
          $usuarioxm = Yii::$app->db->createCommand("select nombreusuario from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
          $equipoxm = Yii::$app->db->createCommand("select equipo from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
          $sucursalxm = Yii::$app->db->createCommand("select nombresucursal from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
          $plataforma=Yii::$app->db->createCommand("select plataforma from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
          
          
          
          $datos = [
              "CardCode" => $pedido["CardCode"],
              "DocDueDate" => $pedido["DocDueDate"],
              "DocType" => $pedido["DocType"],
              "CardName" => $pedido["CardName"],
              "Address" => $pedido["Address"],
              "DocTotal" => $pedido["DocTotalPay"],
              "PaymentGroupCode" => $pedido["PayTermsGrpCode"],
              "SalesPersonCode" => $pedido["SlpCode"],
              // "U_xMOB_Usuario"=> $pedido["idUser"],
              "U_LB_NIT" => $pedido["U_4NIT"],
              "U_LB_RazonSocial" => $pedido["U_4RAZON_SOCIAL"],
              "U_xMOB_Equipo"=> $pedido["equipoId"],
              "U_xMOB_GCardSerie"=> $pedido["giftcard"],
              "U_xMOB_GCardMonto"=> $pedido["giftcard"],
              "U_xMOB_Comentario"=> $pedido["Comments"],
              "U_xMOB_Codigo"=> $pedido["idDocPedido"],
              "U_xMOB_Usuario" => $usuarioxm["nombreusuario"],
              "U_xMOB_Equipo" => $equipoxm["equipo"],
              "U_xMOB_Sucursal" => $sucursalxm["nombresucursal"]
          ];
          Yii::error('campos USUARIO OFERTA ******:'. json_encode($datos));
          $aLineas = [];
          foreach ($pedido["detalledocumentos"] as $lineaPedido) {
              $unidadNegociox = Sapenviodoc::unidadNegocio($lineaPedido["ItemCode"]);
              $gastosAdicionales=Sapenviodoc::gastosAdicionalesLinea($lineaPedido["ICEE"],$lineaPedido["ICEP"],$lineaPedido["LineTotalPay"]);
              if($lineaPedido["U_4DESCUENTO"]>0){
                  $descuento=($lineaPedido["U_4DESCUENTO"]*100/($lineaPedido["Quantity"]*$lineaPedido["Price"]));
              }else{
                  $descuento=0;
              }
              $linea = [
                  "DocNum" => $lineaPedido["DocNum"],
                  "LineNum " => $lineaPedido["LineNum"],
                  "ItemCode" => $lineaPedido["ItemCode"],
                  "Dscription" => $lineaPedido["Dscription"],
                  "Quantity" => $lineaPedido["Quantity"],
                  "SalesPersonCode" => $pedido["SlpCode"],
                 // "Price" => $lineaPedido["Price"],
                 "GrossPrice" => $lineaPedido["Price"],
                 "DiscountPercent" => $descuento,
                  //"GrossTotal" => $lineaPedido["LineTotalPay"],
                 "GrossTotal" => $lineaPedido["LineTotal"],
                  "TaxCode" => "IVA",
                  "Currency" => $lineaPedido["Currency"],
                  "TotalDiscount"=> $lineaPedido["U_4DESCUENTO"],
                  //"DiscPrcnt" => ($lineaPedido["U_4DESCUENTO"]*100/$lineaPedido["LineTotal"]),
                  "LineTotal" => $lineaPedido["LineTotal"],
                  "WarehouseCode" => $lineaPedido["WhsCode"],
                  "UoMEntry" => Sapenviodoc::unidadEntry($lineaPedido["unidadid"]),
                  "COGSCostingCode" => $empleadoCiudad['HomeState'],
                  "CostingCode2" => $unidadNegociox,
                  "DocumentLineAdditionalExpenses" =>$gastosAdicionales
                  
              ];
              if (intval($lineaPedido["BaseEntry"])!=0){
                  $linea["BaseEntry"]=intval($lineaPedido["BaseEntry"]);
                  $linea["BaseLine"]=intval($lineaPedido["BaseLine"]);
                  $linea["BaseType"]=intval($lineaPedido["BaseType"]);
              }
              array_push($aLineas, $linea);
          }
          $datos["DocumentLines"] = $aLineas;
          $datos["DocumentAdditionalExpenses"]=Sapenviodoc::GastosAdicionalesCab($pedido["DocTotalPay"]);
          Yii::error('inicio oferta2 Json: '.json_encode($datos));
          $respuesta = $serviceLayer->executePost($datos); //$serviceLayer->executePost(json_encode($datos));
           Yii::error('error oferta2 Json: '.json_encode($respuesta));
          if (isset($respuesta->DocEntry)) {
              $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
              $actualizaPedido->DocEntry = $respuesta->DocEntry;
              $actualizaPedido->estado = $pedido['estado'] != 3 ? 11: 4;
              $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz')->format('Y-m-d');
              $actualizaPedido->DocNumSAP = $respuesta->DocNum;
              $actualizaPedido->save(false);
             // Yii::error("ID-MID:{$pedido["id"]};DATA-" . $respuesta->DocEntry);
          } else {
             Sapenviodoc::guardarlog($datos,$respuesta,'Oferta',$pedido["idDocPedido"]);
              
                  
              
          }
      }
  }
  Yii::error('fin');
}

public function entrega($idDocPedido=''){
  Yii::error('inicio entrega');
  $serviceLayer = new Servislayer();
  $serviceLayer->actiondir = "DeliveryNotes";
  if($idDocPedido==''){
    $entregas = Cabeceradocumentos::find()
    ->where("DocEntry is null AND DocTotal > 0 AND DocType = 'DOE' AND ( estado = 3 OR estado = 6 )")
    ->with('detalledocumentos')
    ->limit(50)
    ->asArray()
    ->all(); 
  }
  else{
    $entregas = Cabeceradocumentos::find()
    ->where("DocEntry is null AND DocTotal > 0 AND idDocPedido = '".$idDocPedido."' AND ( estado = 3 OR estado = 6 )")
    ->with('detalledocumentos')
    ->limit(50)
    ->asArray()
    ->all();
  }
  
  if (count($entregas)) 
  {
    foreach ($entregas as $entrega)
    {
        $empleadoCiudad = Yii::$app->db->createCommand("select HomeState from vi_ciudadvendedor where SalesEmployeeCode = '{$entrega["SlpCode"]}'")->queryOne();
        $auxClone = $entrega["clone"];
        //Yii::error('objeto entrega: clon '.$auxClone);
        $usuarioxm = Yii::$app->db->createCommand("select nombreusuario from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
        $equipoxm = Yii::$app->db->createCommand("select equipo from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
        $sucursalxm = Yii::$app->db->createCommand("select nombresucursal from vi_cabeceradocumentos where idCabecera = '{$pedido["id"]}'")->queryOne();
        $datos = [
        "CardCode" => $entrega["CardCode"],
        "DocDueDate" => $entrega["DocDueDate"],
        "DocType" => $entrega["DocType"],
        "CardName" => $entrega["CardName"],
        "PaymentGroupCode" => $entrega["PayTermsGrpCode"],
        "SalesPersonCode" => $entrega["SlpCode"],
        /* "Address" => $entrega["Address"], */
        "ControlAccount" => $entrega["ControlAccount"],
        "DocTotal" =>$entrega["DocTotal"],
        //      "Indicator" => $entrega["Indicator"],
        /* "Reference1" => $entrega["DocNumSAP"], */
        "Series" => intval($entrega["Series"]),
        "ShipToCode" => $entrega["ShipToCode"],
        "DocObjectCode" => "DocObjectCode",
        "CreationDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
        "DocDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
        "Document_ApprovalRequests" => [],
        "DocumentAdditionalExpenses" => [],
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
        "U_xMOB_Sucursal" => $sucursalxm["nombresucursal"]
        ];
        Yii::error('campos USUARIO ******:'. json_encode($datos));
        $aLineas = [];
        foreach ($entrega["detalledocumentos"] as $lineaEntrega) 
        {
          $lote = [];
          $lote=Sapenviodoc::LoteCode($lineaEntrega["ItemCode"],$lineaEntrega["Quantity"],$entrega["idDocPedido"],$lineaEntrega["unidadid"],$lineaEntrega["WhsCode"] );
           //$lote=$this->LoteCode($lineaP->ItemCode,$lineaP->Quantity, $factura->idDocPedido,$lineaP->unidadid,$lineaP->WhsCode);
          $unidadNegociox = Sapenviodoc::unidadNegocio($lineaEntrega["ItemCode"]);
          if($lineaEntrega["U_4DESCUENTO"]>0)
          {
              $descuento=($lineaEntrega["U_4DESCUENTO"]*100/($lineaEntrega["Quantity"]*$lineaEntrega["Price"]));
          }
          else
          {
              $descuento=0;
          }
          $linea = [
          /* "DocNum"     => $lineaEntrega["DocNum"], */
          "BatchNumbers" => $lote,
          "LineNum"   => intval($lineaEntrega["LineNum"]),
          "ItemCode"   => $lineaEntrega["ItemCode"],
          /* "ItemDescription" => $this->remplaceString($lineaEntrega["Dscription"]), */
          "Quantity"   => intval($lineaEntrega["Quantity"]),
          /* "Price"      => $lineaEntrega["Price"], */
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
          "GrossPrice" =>$lineaEntrega["Price"],
          "GrossTotal" =>$lineaEntrega["LineTotal"],
          "TaxCode" => $lineaEntrega["TaxCode"],
          "WarehouseCode" => $lineaEntrega["WhsCode"], // no se esta guardando este dato en la BD t.
          "CorrectionInvoiceItem" => isset($lineaEntrega["CorrectionInvoiceItem"]) ? $lineaEntrega["CorrectionInvoiceItem"] : "ciis_ShouldBe",
          "Status" => isset($lineaEntrega["Status"]) ? $lineaEntrega["Status"] : "bost_Close",
          "Stock" => isset($lineaEntrega["Stock"]) ? $lineaEntrega["Stock"] : "tNO",
          "TargetAbsEntry" => intval($lineaEntrega["TargetAbsEntry"]),
          "BatchNumbers" => $lote,
          "SerialNumbers" => Sapenviodoc::NumeroSerie($lineaEntrega["ItemCode"], $lineaEntrega["Quantity"], $entrega["idDocPedido"]),
          "COGSCostingCode" => $empleadoCiudad['HomeState'],
          "CostingCode2" => $unidadNegociox,
          "COGSCostingCode2"=> $unidadNegociox,
          "LineTaxJurisdictions" => [
              [
                  "JurisdictionCode" => "IVA",
                      "TaxRate" => 13.0
              ]
              ],
          //"BaseEntry" => intval($lineaEntrega["BaseEntry"]),
          //"BaseLine" => intval($lineaEntrega["BaseLine"]),
          //"BaseType" => intval($lineaEntrega["BaseType"])
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
          Yii::error('objeto entrega linea cont: clon dfo '.$pos_dfo.' dfa '.$pos_dfa.' dop '.$pos_dop.' doe '.$pos_doe);
          if(($auxClone != '0') and (($pos_dfo!=0) OR ($pos_dfa!=0) OR ($pos_dop!=0)  OR ($pos_doe!=0))) 
          {
              $auxClone='0' ;
          //$linea["BaseType"]=13;
          //$auxClone=$auxClone;
          }
          else
          {
              $auxClone=$auxClone;
          } 
          Yii::error('objeto entrega linea: clon '.$auxClone);
          if ($auxClone != '0')
          {               

              Yii::error('objeto entrega: clon '.$auxClone);
              /*
              if(is_null($lineaEntrega["BaseEntry"])){
              $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne(); 
              $aux_doc_org = $aux_doc_org["DocEntry"]; 
              }else{
                  $aux_doc_org=intval($lineaEntrega["BaseEntry"]);
              }*/
              $aux_doc_org = Yii::$app->db->createCommand("select DocEntry from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne(); 
              $aux_doc_org = $aux_doc_org["DocEntry"];
              Yii::error('objeto entrega origen : clon '.$aux_doc_org);
              $aux_doctipo= Yii::$app->db->createCommand("select DocType from cabeceradocumentos where idDocPedido = '{$auxClone}'")->queryOne();
              $aux_doctipo  = $aux_doctipo["DocType"];
              Yii::error('objeto entrega origen : clon '.$auxClone);
              switch ($aux_doctipo)
              {
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
              $linea["BaseEntry"]=$aux_doc_org;
              $linea["BaseLine"]=intval($lineaEntrega["BaseLine"]);
              $linea["BaseType"]=$aux_doc_base;
              if(is_null($lineaEntrega["BaseLine"]))
              {
                  $linea["BaseEntry"]="";
                  $linea["BaseLine"]="";
                  $linea["BaseType"]=""; 
              }
          }
          array_push($aLineas, $linea);
      }
      $datos["DocumentLines"] = $aLineas;
      Yii::error("Entrega enviada.- " . json_encode($datos));
      $respuesta = $serviceLayer->executePost($datos);
      if(isset($respuesta->DocEntry))
      {
          $actualizaEntrega = Cabeceradocumentos::findOne($entrega['id']);
          $actualizaEntrega->DocEntry = $respuesta->DocEntry;
          $actualizaEntrega->estado = $entrega['estado'] != 3 ? 11 : 4;
          $actualizaEntrega->fechaupdate = Carbon::today('America/La_Paz')->format('Y-m-d');
          $actualizaEntrega->save(false);
          //Yii::error("ID-MID:{$entrega["id"]};DATA-".$respuesta->DocEntry);
      }
      else 
      {
         Sapenviodoc::guardarlog($datos,$respuesta,'Entrega',$entrega["idDocPedido"]);
      }
    }
  }
}

public function pedidoCancelar($idDocPedido='') {
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

public function ofertaCancelar($idDocPedido='') {/**!**/
  Yii::error('inicio cancelar oferta');
  $serviceLayer = new Servislayer();
  if($idDocPedido==''){
    $pedidos = Cabeceradocumentos::find()
          ->where("DocType = 'DOF' AND (estado = 11 or estado = 6 or eliminado=1) and DocEntry is not null and estado!=7")
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
  
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          $serviceLayer->actiondir = "Quotations({$pedido['DocEntry']})/Cancel";
          $respuesta = $serviceLayer->executePost([]); //$serviceLayer->executePost(json_encode($datos));

          Yii::error("OFERTA ANULADA =====>" .json_encode($respuesta));
          if ($respuesta==1) {
              $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
              $actualizaPedido->estado = 7;
              $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
              $actualizaPedido->update(false);
          } else {
              Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
              /* $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
              $actualizaPedido->estado = 3;
              $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
              $actualizaPedido->update(false); */
          }
      }
  }
  Yii::error('fin');
}

public function entregaCancelar($idDocPedido='') {
  Yii::error('inicio cancelar entrega');
  $serviceLayer = new Servislayer();
  if($idDocPedido==''){
    $pedidos = Cabeceradocumentos::find()
          ->where("DocType = 'DOE' AND (estado = 11 or estado = 6 or eliminado=1) and DocEntry is not null and estado!=7")
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
  
  if (count($pedidos)) {
      foreach ($pedidos as $pedido) {
          $serviceLayer->actiondir = "DeliveryNotes({$pedido['DocEntry']})/Cancel";
          $respuesta = $serviceLayer->executePost([]); //$serviceLayer->executePost(json_encode($datos));
          if ($respuesta) {
              $actualizaPedido = Cabeceradocumentos::findOne($pedido['id']);
              $actualizaPedido->estado = 6;
              $actualizaPedido->fechaupdate = Carbon::today('America/La_Paz');
              $actualizaPedido->update(false);
          } else {
              Yii::error("ID-MID:{$pedido["id"]};DATA-" . json_encode($respuesta));
          }
      }
  }
  Yii::error('fin');
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

private function GastosAdicionalesLinea($icee,$icep,$totalpagar,$cc1="",$cc2="",$cc3="",$cc4=""){
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
          "LineTotal" => round(($totalpagar * self::IT) / 100, 2)*-1,
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
private function GastosAdicionalesCab($total){
    $q='select ice,it,itg,icee,icep from  vi_gastosadicionales';
    $resultado= Yii::$app->db->createCommand($q)->queryone();
    $gitg=[
                  "ExpenseCode" => $resultado['it'],
                  "TaxCode" => "IVA",
                  "LineTotal" => round(($total * self::IT) / 100, 2),
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

private function unidadNegocio($itemCode) 
{
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

    private function ObtenerAutorizacion($lineasPedido,$tipoDoc) 
    {                
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

    private function ObtenerAutorizacionFactura($lineasPedido) 
    {        
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

}


