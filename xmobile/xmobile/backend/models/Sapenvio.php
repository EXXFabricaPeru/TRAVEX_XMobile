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

class Sapenvio extends Model {

    /**
     * @var Servislayer $model
     */
    private $model;
    private $model_odbc;

    public function __construct() {
        $this->model = new Servislayer();
        $this->model_odbc = new Sincronizar();
    }

   /*   modelo pago de varias facturas tipo otpp=2 */

  private function pagar() {
      Yii::error('PAGOS UNICO OBJETO');
      $serviceLayer = new Servislayer();
      $serviceLayer->actiondir = "IncomingPayments";
      Yii::$app->db->createCommand('CALL pa_obtenerPagosPendientes();')->execute();
      $comando = 'SELECT * FROM pagospendientesenvio';
      $pagos = Yii::$app->db->createCommand($comando)
        ->queryAll();
      
      Yii::error("Cantidad Pagos GENERAL -> pago unico:".count($pagos));
      Yii::error("Dato del pago:".json_encode($pagos));

      $pagoSAP = [];
    
      if (count($pagos)) {
        $cheques = [];
        $tarjetas = [];
        $contador = 0;
        foreach ($pagos as $pago) {
          $pagoSAP = [
            "DocType" => $pago["DocType"],
            "HandWritten" => $pago["HandWritten"],
            "Printed" => $pago["Printed"],
            "DocDate" => $pago["DocDate"],
            "CardCode" => $pago['cardcode'],
            "CardName" => $pago['cardname'],
            "DocCurrency" => $pago['xmoneda'],
            //"Address" => $pago['Address'],
            "LocalCurrency" => $pago["LocalCurrency"],
            "SplitTransaction" => $pago["SplitTransaction"],
            "ApplyVAT" => $pago["ApplyVAT"],
            "TaxDate" => $pago["DocDate"],
            "CurrencyIsLocal" => $pago["CurrencyIsLocal"],
            "Proforma" => $pago["Proforma"],
            //"PayToCode" => $pago['PayToCode'],
            "IsPayToBank" => $pago["IsPayToBank"],
            "PaymentPriority" => $pago["PaymentPriority"],
            "VatDate" => $pago["DocDate"],
            "TransactionCode" => $pago["TransactionCode"],
            "PaymentType" => $pago["PaymentType"],
            "DocObjectCode" => $pago["DocObjectCode"],
            "DocTypte" => $pago["DocTypte"],
            "DueDate" => $pago["DocDate"],
            "Cancelled" => $pago["Cancelled"],
            "AuthorizationStatus" => $pago["AuthorizationStatus"],
            "PaymentByWTCertif" => "tNO", // falta
            
            "CashAccount" => $pago["CashAccount"],
            "TransferAccount" => $pago["TransferAccount"],
            // "CheckAccount" => $cuentapago, //falta
            "TransferDate" => $pago['TransferDate'],
            "TransferSum" => $pago["TransferSum"],
            "TransferReference" => $pago["TransferReference"],
            "CashSum" => $pago["CashSum"],
            
            //"U_4USUARIO"=>$pago["usuario"],
            //"U_4DOCUMENTOORIGEN"=>$pago["recibo"],

            "U_xMOB_Cobrador" =>substr ( $pago['cobrador'] , 0 ,30 ),
            "U_xMOB_Codigo" => $pago['recibo'],
            "U_xMOB_Usuario" => $pago['usuario'],
            "U_xMOB_Equipo" => $pago['equipo'],
            /*"U_xMOB_Venta1" => $pago['auxiliar2'],
            "U_xMOB_Almacen" => $pago['auxiliar1']*/
            //"NumRecibo" => $pago["recibo"]
          ];
          
          
          $pagoSAP["PaymentChecks"] = $this->pagoCheque($pago['recibo']);
          if(count($pagoSAP["PaymentChecks"]) > 0) {
            $pagoSAP["CheckAccount"] = $pagoSAP["PaymentChecks"][0]["CheckAccount"];
          } else {
            $pagoSAP["CheckAccount"] = "";
          }
          $pagoSAP["PaymentCreditCards"] = $this->pagoTarjeta($pago['recibo']);
          $pago['otpp']=2;
          Yii::error("AAR-00108");
          //$pagoSAP["PaymentInvoices"] = $this->modoPago($pago['recibo']);        
          $pagoSAP["PaymentInvoices"] = $this->modoPago($pago);

          
          Yii::error("Objeto Pago " . $pago['recibo'] . " => ". json_encode($pagoSAP));

          $respuesta = $serviceLayer->executePost($pagoSAP);
          Yii::error('PAGO-RESPUESTA '. $pago['recibo'] . " => " . json_encode($respuesta));
          Yii::error('PAGO-RESPUESTA '. $pago['recibo'] . " => " . $respuesta->DocNum);
          //
          if (isset($respuesta->DocNum)) {
            Yii::error('*** SE HA PAGADO *** ');
            //foreach ($pagos as $pago) {
              //$pagoMiddleware = Pagos::findOne($pago['idPago']);
              //$pagoMiddleware->estadoEnviado = 1;
              //$pagoMiddleware->TransId = $respuesta->DocEntry;
              Yii::$app->db->createCommand('Update pagos set estadoEnviado=1,TransId="'.$respuesta->DocEntry.'" where recibo="'.$pago['recibo'] .'"')->execute();
              Yii::error('PAGO ACTUALIZADO => ' . json_encode($pagoMiddleware));
              //$pagoMiddleware->save(false);      
            //}
          }else{
          // $this->manejoErrorPago($respuesta,$pago['recibo'],$pagoSAP,$pagoSAP['DocType']);
          } 
          Yii::$app->db->createCommand('CALL pa_obtenerPagosPendientes()')->execute();
        //
        } // final foreach
          
      }
  }
  public function pagarPorRecibo($recibo) {
      Yii::error('PAGOS UNICO OBJETO');
      $serviceLayer = new Servislayer();
      $serviceLayer->actiondir = "IncomingPayments";
      Yii::$app->db->createCommand('CALL pa_obtenerPagosPendientes();')->execute();
      $comando = 'SELECT * FROM pagospendientesenvio where recibo="'.$recibo.'" ';
      $pagos = Yii::$app->db->createCommand($comando)
      ->queryAll();
      
      Yii::error("Cantidad Pagos GENERAL -> pago unico:".count($pagos));
      Yii::error("query pago:".$comando);

      $pagoSAP = [];
      Yii::error("Dato del pago:".json_encode($pagos));
      if (count($pagos)) {
        $cheques = [];
        $tarjetas = [];
        $contador = 0;
        foreach ($pagos as $pago) {
          $sqlEquipo = "SELECT * from equipox where id = ".$pago["equipo"];
          $Equipo = Yii::$app->db->createCommand($comando)->queryOne();
          $pagoSAP = [
            "DocType" => $pago["DocType"],
            "HandWritten" => $pago["HandWritten"],
            "Printed" => $pago["Printed"],
            "DocDate" => $pago["DocDate"],
            "CardCode" => $pago['cardcode'],
            "CardName" => $pago['cardname'],
            //"Address" => $pago['Address'],
            "DocCurrency" => $pago['xmoneda'],
            "LocalCurrency" => $pago["LocalCurrency"],
            "SplitTransaction" => $pago["SplitTransaction"],
            "ApplyVAT" => $pago["ApplyVAT"],
            "TaxDate" => $pago["DocDate"],
            "CurrencyIsLocal" => $pago["CurrencyIsLocal"],
            "Proforma" => $pago["Proforma"],
            //"PayToCode" => $pago['PayToCode'],
            "IsPayToBank" => $pago["IsPayToBank"],
            "PaymentPriority" => $pago["PaymentPriority"],
            "VatDate" => $pago["DocDate"],
            "TransactionCode" => $pago["TransactionCode"],
            "PaymentType" => $pago["PaymentType"],
            "DocObjectCode" => $pago["DocObjectCode"],
            "DocTypte" => $pago["DocTypte"],
            "DueDate" => $pago["DocDate"],
            "Cancelled" => $pago["Cancelled"],
            "AuthorizationStatus" => $pago["AuthorizationStatus"],
            "PaymentByWTCertif" => "tNO", // falta
            
            "CashAccount" => $pago["CashAccount"],
            "TransferAccount" => $pago["TransferAccount"],
            // "CheckAccount" => $cuentapago, //falta
            "TransferDate" => $pago['TransferDate'],
            "CashSum" => $pago["CashSum"],
            "TransferSum" => $pago["TransferSum"],
            //"U_4USUARIO"=>$pago["usuario"],
            //"U_4DOCUMENTOORIGEN"=>$pago["recibo"],

            //"U_xMOB_Cobrador" =>substr ( $pago['cobrador'] , 0 ,30 ),
            "U_xMOB_Codigo" => $pago['recibo'],
            "U_xMOB_Usuario" => $pago['usuario'],

            "U_xMOB_Cobrador" =>$pago['cobrador'] ,
            "U_xMOB_Equipo" => $Equipo["equipo"], // $pago['equipo'],
            //"U_xMOB_Venta1" => $pago['auxiliar2'],
            "U_xMOB_Almacen" => $pago['auxiliar1'],
            "U_xMOB_Equipo" => $pago['equipo'],
          // "U_xMOB_Venta1" => $pago['auxiliar2'],
            //"U_xMOB_Almacen" => $pago['auxiliar1']
            //"NumRecibo" => $pago["recibo"]
          ];
          Yii::error('*** pago objeto 0  *** '.$pago['recibo']);
          
          $pagoSAP["PaymentChecks"] = Sapenvio::pagoCheque($pago['recibo']);
          if(count($pagoSAP["PaymentChecks"]) > 0) {
            $pagoSAP["CheckAccount"] = $pagoSAP["PaymentChecks"][0]["CheckAccount"];
          } else {
            $pagoSAP["CheckAccount"] = "";
          }
          Yii::error('*** pago objeto 1  *** '.json_encode( $pagoSAP));
          $pagoSAP["PaymentCreditCards"] = Sapenvio::pagoTarjeta($pago['recibo']);
          
          $pago['otpp']=2;
          Yii::error("AAR-00108");
          //$pagoSAP["PaymentInvoices"] = $this->modoPago($pago['recibo']);        
          $pagoSAP["PaymentInvoices"] = Sapenvio::modoPago($pago);

          
          Yii::error("Objeto Pago " . $pago['recibo'] . " => ". json_encode($pagoSAP));

          $respuesta = $serviceLayer->executePost($pagoSAP);
          Yii::error('PAGO-RESPUESTA '. $pago['recibo'] . " => " . json_encode($respuesta));
          Yii::error('PAGO-RESPUESTA '. $pago['recibo'] . " => " . $respuesta->DocNum);
          Sapenvio::guardarlog($pagoSAP,$respuesta,'PAGO',$respuesta->DocNum);
          //return $respuesta;
          //
          /*if(!isset($respuesta->error)){ 
              Sapenvio::eliminarFacturasPago($pago);
          }else{
              Sapenvio::eliminarPagos($pago);
          }*/
          return $respuesta; 
              
        } // final foreach
        
        
      }
  }
  public function pagoCheque($nRecibo) {
    Yii::error(" entra cheque ");
    $cheques = [];
    $comando = 'SELECT * FROM vistapagoscheques WHERE recibo = ' . $nRecibo;
    // $comando = 'SELECT * FROM pagos WHERE recibo = ' . $nRecibo;
    $pagosCheques = Yii::$app->db->createCommand($comando)
      ->queryAll();
    Yii::error("Cheques encontrados => " . json_encode($pagosCheques));
    foreach ($pagosCheques as $cheque) {
      array_push($cheques,[
        "AccounttNum" => "",
        "LineNum" => 0,
        "DueDate" => Carbon::parse($cheque['fecha'])->addDays(30)->toDateString(),//"DueDate" => $pago['fecharegistro'],
        // "U_LB_Fecha_cheque" => $cheque['fecha'],
        "BankCode" => $cheque['banco'],
        "Trnsfrable" => "tYES",
        "CheckSum" => $cheque['monto'],
        "CheckNumber"=>$cheque['numCheque'],
        "Currency" => "SOL",
        "CountryCode" => "PE",
        "CheckAccount" => $cheque['ctaCheque'],
        "ManualCheck" => "tNO",
        "Endorse" => "tNO",
       // "U_FEMI"=>$cheque['femision']
      ]);
    }
    return $cheques;
  }

  public function pagoTarjeta($nRecibo) {
    $tarjetas = [];
    $comando = 'SELECT * FROM vistapagostarjetas WHERE recibo = ' . $nRecibo;
    // $query = 'SELECT * FROM pagos WHERE recibo = ' . $nRecibo;
    $pagosTarjetas = Yii::$app->db->createCommand($comando)
      ->queryAll();
    foreach ($pagosTarjetas as $tarjeta) {
      $aux = date('Y-m-d', strtotime("{$tarjeta["vencimiento"]} + 1 month"));
      $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));
      array_push($tarjetas,[
        "LineNum" => count($tarjetas),
        "CreditCard" => 1, //nombre de la tarjeta creado desde SAP
        "CreditAcct" => $tarjeta['ctaTarjeta'],
        "CreditCardNumber" => $tarjeta['numTarjeta'],// solo los ultimos 4
        "CardValidUntil" => $last_day,//$pago[''] //la fecha debe tener ese formato con el ultimo dia del mes,
        "OwnerIdNum" => "1",
        "OwnerPhone" => '2475896',//$pago['2475896'],
        "PaymentMethodCode" => 1,
        "NumOfPayments" => 1,
        "FirstPaymentDue" => $pago['fecharegistro'],
        "FirstPaymentSum" => $tarjeta['monto'],
        "AdditionalPaymentSum" => 0.0,
        "CreditSum" => $tarjeta['monto'],
        "NumOfCreditPayments" => 1,
        "SplitPayments" => "tNO",
        "VoucherNum" => $tarjeta['baucher']
      ]);
    }
    return $tarjetas;
  }
  public function modoPago($pago){
    $factura = [];
    if (intval($pago['otpp']) == 1) {
      switch($pago['DocType']){
        case 'DFA':
          $tipo="it_Invoice";
        break;  
        case 'DOP':
          $tipo="it_Order";
        break;
        case 'DOF':
          $tipo="it_Quotation";
        break;

      }

      $factura = [
        [
          "LineNum" => 0,
          "DocEntry" => $pago['DocEntry'],
          "SumApplied" => $pago['monto'],
          "InvoiceType" =>$tipo,
          "InstallmentId" => $pago['cuota']
        ]
      ];
    }
    if (intval($pago['otpp']) == 2){     

      $pagosfacturas = Yii::$app->db->createCommand('select * from  pagosfacturas where recibo = :recibo group by cod,recibo,monto,cliente,docEntry')
      ->bindValue(':recibo', $pago['recibo'])
      ->queryAll();
      $salida=[];
      $i=0;
      if (count($pagosfacturas)) {

        foreach ($pagosfacturas as $pagof) {
          $salida=[
            "LineNum" => $i,
            "DocEntry" => $pagof['docEntry'],
            "SumApplied" => $pagof['monto'],
            "InvoiceType" =>"it_Invoice",
            "InstallmentId" => $pagof['cuota']
          ];
          array_push($factura,$salida);
          $i=$i+1;
        }
      }

    }
    return $factura;
  }
  public function eliminarFacturasPago($pago)
  {
    $factura = [];
    
      $pagosfacturas = Yii::$app->db->createCommand('select * from  pagosfacturas where recibo = :recibo group by cod,recibo,monto,cliente,docEntry')
      ->bindValue(':recibo', $pago['recibo'])
      ->queryAll();
      $salida=[];
      $i=0;
      if (count($pagosfacturas)) {
        foreach ($pagosfacturas as $pagof) {
            $q="Delete from facturas where Docentry=".$pagof['docEntry'];
            Yii::error($q);
            Yii::$app->db->createCommand($q)->execute();
        }
      }
  }
  public function eliminarPagos($pago)
  {
    $factura = [];
    
      $pagosfacturas = Yii::$app->db->createCommand(' update pagosfacturas set recibo = :recibou where recibo = :recibo ')
      ->bindValue(':recibo', $pago['recibo'])
      ->bindValue(':recibou', $pago['recibo']."_A")
      ->execute();
      $pagosfacturas = Yii::$app->db->createCommand(' update pagos set recibo = :recibou,estadoEnviado=8 where recibo = :recibo ')
      ->bindValue(':recibo', $pago['recibo'])
      ->bindValue(':recibou', $pago['recibo']."_A")
      ->execute();
      $salida=[];
      $i=0;
     
  }
  public function cliente($cnf_usuario='',$id=0) {
    $respuestaEnvio=[];
    try {
      $serviceLayer = new Servislayer();
      Yii::error('inicio sincronizacion cliente2');
      if ($id==0){
        $clientes = Clientes::find()
        ->where('(Mobilecod<>0 ) and StatusSend = 0')
        ->limit(50)
        ->all();
      }else{
        $clientes = Clientes::find()
              ->where('id ='.$id)
              ->limit(50)
              ->all();
      }
      
      $serviceLayer->actiondir = "BusinessPartners";
      if (count($clientes)) {
          foreach ($clientes as $value) {
               Yii::error('RECUPERANDO GRUPO CLIENTES DOSIFICACION');
                
              $grupoCliente =Yii::$app->db->createCommand("select * from usuarioconfiguracion where idUser= '{$value["User"]}'")->queryOne();
              $grupoClienteDosificacion =$grupoCliente["grupoClienteDosificacion"];
              //if ($grupoCliente != null) $grupoClienteDosificacion = $grupoCliente->grupoClienteDosificacion;
      
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
                  "Country" => "PE",//$value->Country,
                  "CurrentAccountBalance" => $value->CurrentAccountBalance,
                  "NoDiscounts" => "tNO",
                  "PriceMode" => "pmGross",
                  "FederalTaxID" => $value->FederalTaxId,
                  "Phone1" => $value->PhoneNumber,
                  "PayTermsGrpCode" => "-1",
                  "U_XM_Latitud" => $value->Latitude,
                  "U_XM_Longitud" => $value->Longitude,
                  "U_XM_Mobilecod" => $value->Mobilecod,
                  "GroupCode" => 100,
                  "Phone2" => $value->Phone2,
                  "Cellular" => $value->Cellular,
                  "EmailAddress" => $value->EmailAddress,
                  "FreeText" => $value->FreeText . " Contacto: " . $value->ContactPerson . " Usuario Xmobile: " . $value->User,
                  "CardForeignName" => $value->CardForeignName,
                  "Territory" => $value->Territory,
                  /*"Properties1" => $value->Properties1,
                  "Properties2" => $value->Properties2,
                  "Properties3" => $value->Properties3,
                  "Properties4" => $value->Properties4,
                  "Properties5" => $value->Properties5,
                  "Properties6" => $value->Properties6,
                  "Properties7" => $value->Properties7,*/
                  "PriceListNum" => $value->PriceListNum,
                  "ContactEmployees" => [],
                  "BPAddresses" => [],
                  /**PROPIO DE COMPANEX**/
                   //"U_XM_DosificacionSocio" => $grupoClienteDosificacion,
                   //"Industry" => $industria
                   //"U_xMOB_Plataforma"=>"M",
                   //"U_Regional"=>$value->ccu1?$value->ccu1:'',
                   //"U_CanalVentas"=>$value->ccu3?$value->ccu3:''
              ];

              /*****************CAMPOS PERSONALIZADOS************************ */
              $clienteNuevo=Sapenvio::getCamposPersonalizados($clienteNuevo,$value,$cnf_usuario);
              /******************CONTACTOS *********************/
               $contactos = Contactos::find()->where("cardCode = '".$value->CardCode."'")->all();
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
                        "Title" => substr($contacto->titulo, 0, 9),
                    ];                    
                    array_push($clienteNuevo["ContactEmployees"], $nuevoContacto);
                }
                /****************************DIRECCIONES DE LOS CLIENTES*************************/
                 $direcciones = Clientessucursales::find()->where("CardCode = '".$value->CardCode."'")->all();
                 foreach($direcciones as $direccion){
                    //$nombresucursal = explode(" ", $sucursal["nombre"]);
                    if ($direccion["AdresType"] == "B") {
                        $auxtipo = "bo_BillTo";
                        $city = "";
                    } else {
                        $auxtipo = "bo_ShipTo";
                        $city = $value->ccu2;
                    }                      
                    $nuevaDireccion = [
                        "AddressName" => $direccion->AddresName,
                        "Street" => $direccion->Street,
                        //"State" => $direccion->State,
                        //"FederalTaxID" => $direccion->FederalTaxId,
                        //"TaxCode" => $direccion->TaxCode,
                        "Block"=> $direccion->Street,
                        "AddressType" => $auxtipo,
                       // "U_XM_Latitud" => $direccion["u_lat"],
                        //"U_XM_Longitud" => $direccion["u_lon"],
                        //"U_Territorio" => $direccion["u_territorio"],
                        "City"=>$city,
                     ];
                     array_push($clienteNuevo["BPAddresses"], $nuevaDireccion);
                 }



                 Yii::error('OBJETO FINAL CLIENTE');
                 Yii::error(json_encode($clienteNuevo));
              /****************SERIES*********************/
              $serie = Yii::$app->db->createCommand("SELECT valor FROM configuracion WHERE parametro LIKE 's_defecto_cliente'")
                          ->queryOne();
              $clienteNuevo['Series'] = $serie['valor'];//camsa
              /*************************************/
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
              Yii::error(json_encode($clienteNuevo));
              $respuesta = $serviceLayer->executePost($clienteNuevo);
              Yii::error("DATA error service : " . json_encode($respuesta));
              //var_dump($respuesta);
              //die;
              if (isset($respuesta->error)) {//IF F1
                  if (isset($respuesta->message)) {
                      Yii::error("ID-MID:{$value->id};DATA-" . json_encode($respuesta->message->value));
                  } else {
                      Yii::error("ID-MID:{$value->id};DATA-" . json_encode($respuesta));
                  }
                  if ($id>0){
                    return "Error! no guardo el registro en SAP ".json_encode($respuesta) ;
                  }
                  
              } //FIN IF F1
              else {///ELSE F1
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
                      $mensajeEnvio="Correcto";
                    }
                  }
                  else{
                    $cardCodeNuevo=0;
                    $mensajeEnvio="Error! no guardo el registro en SAP: cardCode vacio ".json_encode($respuesta);
                    //return "Error! no guardo el registro en SAP: cardCode vacio ".json_encode($respuesta);
                  }
                }
                else{
                    $cardCodeNuevo=0;
                    $mensajeEnvio="Error! no guardo el registro en SAP ".json_encode($respuesta) ;
                    //return "Error! no guardo el registro en SAP ".json_encode($respuesta) ;
                }
                 
              }///FIN ELSE F1
              array_push($respuestaEnvio, [
                "CardCode" => $cardCodeNuevo,
                "mensaje" => $mensajeEnvio,
              ]);

          }
      }
    return $respuestaEnvio;
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
        "PriceListNum" => $response["PriceListNum"],
        "SalesPersonCode" => $response["SalesPersonCode"],
        //"Currency" => $response["Currency"],
        "County" => $response["County"],
        "Country" => $response["Country"],
        "CurrentAccountBalance" => $response["CurrentAccountBalance"],
        "NoDiscounts" => "tNO",
        "PriceMode" => "pmGross",
        "FederalTaxID" => $response["FederalTaxId"],
        "Phone1" => $response["PhoneNumber"],
        "PayTermsGrpCode" => "-1",
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
        /*"Properties1" => $response["Properties1"],
        "Properties2" => $response["Properties2"],
        "Properties3" => $response["Properties3"],
        "Properties4" => $response["Properties4"],
        "Properties5" => $response["Properties5"],
        "Properties6" => $response["Properties6"],
        "Properties7" => $response["Properties7"],*/
        //"U_xMOB_Plataforma"=>"M",
        //"U_Regional"=>$response['ccu1']?$response['ccu1']:'',
       // "U_CanalVentas"=>$response['ccu3']?$response['ccu3']:'',
        "ContactEmployees" => $contactos,
        "BPAddresses"=> $sucursales
      ];
      /********OBTIENE CAMPOS PERSONALIZADOS*************** */
      $datosCliente=Sapenvio::getCamposPersonalizados($datosCliente,$response,$cnf_usuario);

      $cardCode=$response["CardCode"];
      Yii::error("CARDCODE77: ". $cardCode);
      Yii::error("ENTRÓ AL UPDATE movil :: " . json_encode($datosCliente));
      $serviceLayer->actiondir = "BusinessPartners('".$cardCode."')";
      Yii::error($serviceLayer->actiondir);
      $clienteSap = $serviceLayer->executePatchPut('PATCH', $datosCliente);

      Yii::error("RESPUESTA SAP :: " . json_encode($clienteSap));

      if (isset($clienteSap->error)) {
          if (isset($clienteSap->message)) {
              Yii::error("ID-MID:{$response->id};DATA-" . json_encode($clienteSap->message->value));
          } else {
              Yii::error("ID-MID1:{$response->id};DATA-" . json_encode($clienteSap));
          }
          $mensajeEnvio= "Error! no se actualizo el registro en SAP.";
      } else {
        $clienteNuevo = Clientes::find()->where("CardCode = '{$cardCode}'")->one();
        $clienteNuevo->StatusSend = 1;
        $clienteNuevo->save(false); 
        $mensajeEnvio= "Correcto";
      }
      array_push($respuestaEnvio, [
        "CardCode" => $cardCode,
        "mensaje" => $mensajeEnvio,
      ]);
      return $respuestaEnvio;
    }catch (\Exception $e) {
      Yii::error("Error Exception: ".$e);
      return $e;
    }catch (\Throwable $e) {
      Yii::error("Error Throwable: ".$e);
      return $e;
    }
  }

  function getCamposPersonalizados($clienteNuevo,$value,$cnf_usuario){
      //ACTUALIZA CAMPOS PERSONALIZADOS
      if($cnf_usuario['cnf_canalVenta']==1){
        $clienteNuevo['U_XM_Canal'] =$value["codecanal"];
        $clienteNuevo['U_XM_Subcanal'] = $value["codesubcanal"];
        $clienteNuevo['U_XM_TipoTienda'] = $value["codetipotienda"];
        $clienteNuevo['U_XM_Cadena'] =$value["cadena"];
        $clienteNuevo['U_XM_CadenaDesc'] = $value["cadenatxt"];
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
  }

}
