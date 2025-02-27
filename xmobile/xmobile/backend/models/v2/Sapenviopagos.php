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
use backend\models\Xmfcabezerapagos;

class Sapenviopagos extends Model {

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

  public function pagar($idPago) {
      Yii::error('PAGOS UNICO OBJETO: '.$idPago);
      $serviceLayer = new Servislayer();
      $serviceLayer->actiondir = "IncomingPayments";
      $arr = [];
      $pago = Xmfcabezerapagos::find()
      ->where("id={$idPago} AND monto_total > 0 AND (estado = 2)")
      ->with('xmffacturaspagos')
      ->with('xmfmediospagos')
      ->asArray()
      ->one();
      //Yii::error(json_encode($pago));
      Yii::error($pago);
      $pagoSAP = [];
  
      if (count($pago)) {
        $sqlDocumento =" SELECT CardName from cabeceradocumentos where CardCode='{$pago['cliente_carcode']}'";
        $documento = Yii::$app->db->createCommand($sqlDocumento)->queryOne();
        $pagoSAP = [
          "DocType" => "rCustomer",
          "HandWritten" => "tNO",
          "Printed" => "tNO",
          "DocDate" => $pago["fecha"],
          "CardCode" => $pago['cliente_carcode'],
          "CardName" => $documento['CardName'],
          "DocCurrency" => $pago['moneda'],
          "LocalCurrency" => "tNO",
          "SplitTransaction" => "tNO",
          "ApplyVAT" => "tNO",
          "TaxDate" => $pago["fecha"],
          "CurrencyIsLocal" => "tNO",
          "Proforma" => "tNO",
          "IsPayToBank" => "tNO",
          "PaymentPriority" => "bopp_Priority_6",
          "VatDate" => $pago["fecha"],
          "TransactionCode" => "",
          "PaymentType" => "bopt_None",
          "DocObjectCode" => "bopot_IncomingPayments",
          "DocTypte" => "rCustomer",
          "DueDate" => $pago["fecha"],
          "Cancelled" => "tNO",
          "AuthorizationStatus" => "pasWithout",
          "PaymentByWTCertif" => "tNO", 
        ];
        // se verifica en la configuracion si la series de pedido si esta activo y la configuracion de usuario en series sea diferente de null)
        $configGeneral = Yii::$app->db->createCommand("SELECT valor FROM `configuracion` WHERE estado = 1 and parametro = 's_pedido'")->queryOne();

        $configUsuario = Yii::$app->db->createCommand("SELECT seriesPago FROM usuarioconfiguracion WHERE idUser=". $pago["usuario"])->queryOne();
        Yii::error("Configuracion usuario usa series pedido: ".$configUsuario['seriesPago']);
        if($configGeneral['valor']==1 and $configUsuario['seriesPago']!=null){
           Yii::error(" Usa SERIES de la configuracion del usuario: ");
           $pagoSAP["Series"] = $configUsuario['seriesPago'];
        }

        //array_push($pagoSAP,[Sapenviopagos::camposUsuario($pago)]);
        $pagoSAP+=Sapenviopagos::camposUsuario($pago);
        $pagoSAP+=Sapenviopagos::mediosPagosOb($pagoSAP,$pago);
        $pagoSAP["PaymentInvoices"] = Sapenviopagos::modoPago($pago);
    
        Yii::error("OBJETO ENVIO A SAP: ".json_encode($pagoSAP));
        
        $respuesta = $serviceLayer->executePost($pagoSAP);
        Yii::error('PAGO-RESPUESTA '. $pago['nro_recibo'] . " => " . json_encode($respuesta));
        
        if (isset($respuesta->DocNum)) {
          Yii::error('*** SE HA PAGADO *** ');
          $pagoMiddleware=Yii::$app->db->createCommand('Update xmfcabezerapagos set estado=3,TransId="'.$respuesta->DocEntry.'" where id="'.$pago['id'].'"')->execute();
          Yii::error('PAGO ACTUALIZADO => ' . json_encode($pagoMiddleware));
          $estadoEnvio=3;
          $mensaje="Envio a Sap correcto";
          $registro=true;
        }else{
          Yii::error("NO SE PAGO - REGISTRO EN LOGS");
          //Sapenviopagos::guardarlog($pagoSAP,$respuesta,'PAGO',$pago['nro_recibo']);
          $estadoEnvio=2;
          $mensaje=json_encode($respuesta->error->message->value);
          $registro=false;
        }
        //se guarda en logenvio para tener el registro del objeto que se envia a sap
        Sapenviopagos::guardarlog($pagoSAP,$respuesta,'PAGO',$pago['nro_recibo'],'Sapenviopagos->pagar');
        $arr = [
          "id" =>$pago['id'],//id cabecera xmfcabezerapagos(Midd)
          "estado" => $estadoEnvio,
          "anulado" => 0,
          "recibo" => $pago['nro_recibo'],
          "numeracion"=> 0,
          "registro"=>$registro, //control solo Midd tru=se registro y false no se registro
          "mensaje"=>$mensaje
        ];     
      }
      
      return $arr;
  }
  private function mediosPagosOb($pagoSAP,$pago){
    //INICIO MEDIOS PAGOS//
    foreach ($pago['xmfmediospagos'] as $key => $value) {
      Yii::error("forma de pago: ".$value['formaPago']);
      switch ($value['formaPago']) {
        case 'PEF':
            $pagoSAP+=Sapenviopagos::pagoEfectivo($value,$pago['moneda'],$pago['equipo'],$pago);
            $pagoSAP["Remarks"]="PR: ".$pago['cliente_carcode'];
            break;
        case 'PBT':
            $pagoSAP+=Sapenviopagos::pagoTransferencia($value,$pago['moneda'],$pago['equipo'],$pago);
            $pagoSAP["Remarks"]="PR: ".$pago['cliente_carcode']." - ".$value['bancoCode']." - ".$value['numComprobante'];
            break;
        case 'PCH':
            //array_push($pagoSAP,["PaymentChecks"=>Sapenviopagos::pagoCheque($value,$pago['equipo'])]);
            $sqlCuenta =" SELECT cuentaCheque from equipoxcuentascontables where equipoxId=".$pago['equipo'];
            $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
            $pagoSAP["PaymentChecks"]=Sapenviopagos::pagoCheque($value,$pago['equipo']);
            $pagoSAP["CheckAccount"]=$Cuentas['cuentaCheque'];
            $pagoSAP["Remarks"]="PR: ".$pago['cliente_carcode']." - ".$value['bancoCode']." - ".$value['numCheque'];

            break;
        case 'PCC':
            //array_push($pagoSAP,["PaymentCreditCards"=>Sapenviopagos::pagoTarjeta($value,$pago['equipo'])]);
            $pagoSAP["PaymentCreditCards"]=Sapenviopagos::pagoTarjeta($value,$pago['equipo']);
            $pagoSAP["Remarks"]="PR: ".$pago['cliente_carcode'];
            break;
      } 
    }
    // $pagoSAP["U_monto_boliviano"] = $value["monto"]-($value["monedaDolar"]*$pago["tipo_cambio"]);
    // $pagoSAP["U_monto_dolar"]  = $value["monedaDolar"];

    if($pago['otpp']==3){
          $sqlCuenta =" SELECT cuentaAnticipos from equipoxcuentascontables where equipoxId=".$pago['equipo'];
          $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
          $pagoSAP["ControlAccount"]=$Cuentas['cuentaAnticipos'];
    }
    
    return $pagoSAP;
    //FIN MEDIOS PAGOS//
  }

  private function pagoEfectivo($mediosPago,$moneda,$equipoId,$cabecerPago=''){
    // pago efectivo solo se permite 1

    $sqlModena ="SELECT Code from monedas where Type='L'";
    $Moneda = Yii::$app->db->createCommand($sqlModena)->queryOne();
    if($moneda==$Moneda['Code']){

      $monto=$mediosPago['monto'];
    }
    else{
        $monto=$mediosPago['monedaDolar'];
    }
    $arrEfec=[
      //,cuentaCheque,cuentaTarjeta,cuentaTranferencia
      "CashAccount" => Sapenviopagos::obtenerCuenta($equipoId,$moneda,$cabecerPago,'cuentaEfectivo'),
      "CashSum" => $monto//Sapenviopagos::pagosCashSum($pago["xmfmediospagos"],$pago["moneda"])
    ];
    return $arrEfec;
  }
  private function pagoTransferencia($mediosPago, $moneda,$equipoId,$cabecerPago=''){
     // pago transferencia solo se permite 1
    $sqlModena ="SELECT Code from monedas where Type='L'";
    $Moneda = Yii::$app->db->createCommand($sqlModena)->queryOne();
     if($moneda==$Moneda['Code']){
        $monto=$mediosPago['monto'];
     }
     else{
        $monto=$mediosPago['monedaDolar'];
     }
     $arrPago=[
       "TransferAccount" => $mediosPago['bancoCode'],
       "TransferDate" => $mediosPago['fecha'],
       "TransferSum" => $monto,
       "TransferReference" => $mediosPago['numTarjeta']    
     ];
    //"TransferAccount" => Sapenviopagos::obtenerCuenta($equipoId,$moneda,$cabecerPago,'cuentaTranferencia')
     return $arrPago;
  }
  public function pagoCheque($mediosPago,$equipoId) {
    Yii::error(" entra cheque ");
    $chequesArr = [];
    $sqlCuenta =" SELECT cuentaCheque from equipoxcuentascontables where equipoxId=".$equipoId;
    $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
    Yii::error(json_encode($mediosPago));
    Yii::error("CUENTAS: ". $mediosPago->monto);
    Yii::error("CUENTAS: ". $mediosPago['monto']);

    
    $cheques= [
      [
      "AccounttNum" => "",
      "LineNum" => 0,
      //"DueDate" => Carbon::parse($mediosPago['fecha'])->addDays(30)->toDateString(),
      "DueDate" => $mediosPago['fecha'],
      // "U_LB_Fecha_cheque" => $cheque['fecha'],
      "BankCode" => $mediosPago['bancoCode'],
      "Trnsfrable" => "tYES",
      "CheckSum" => $mediosPago['monto'],
      "CheckNumber"=>$mediosPago['numCheque'],
      "Currency" => "BS",
      "CountryCode" => "BO",
      "CheckAccount" => $Cuentas['cuentaCheque'],
      "ManualCheck" => "tNO",
      "Endorse" => "tNO"
      // "U_FEMI"=>$cheque['femision']
      ]
    ];
    //array_push($chequesArr,$cheques);
  
    Yii::error("Cheques encontrados => " . json_encode($cheques));
    return $cheques;
  }
  public function pagoTarjeta($mediosPago,$equipoId) {
    $tarjetas = [];
    $sqlCuenta =" SELECT cuentaTarjeta from equipoxcuentascontables where equipoxId=".$equipoId;
    $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();

    //foreach ($mediosPago as $tarjeta) {
      $aux = date('Y-m-d', strtotime("{$mediosPago["vencimiento"]} + 1 month"));
      $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));
      //array_push($tarjetas,[
        $tarjetas=[
          [
          "LineNum" => 0,
          "CreditCard" => 1, //nombre de la tarjeta creado desde SAP
          "CreditAcct" => $Cuentas['cuentaTarjeta'],
          "CreditCardNumber" => $mediosPago['numTarjeta'],// solo los ultimos 4
          "CardValidUntil" => $last_day,//$pago[''] //la fecha debe tener ese formato con el ultimo dia del mes,
          "OwnerIdNum" => "1",
          "OwnerPhone" => '2475896',//$pago['2475896'],
          "PaymentMethodCode" => 1,
          "NumOfPayments" => 1,
          "FirstPaymentDue" => $mediosPago['fecha'],
          "FirstPaymentSum" => $mediosPago['monto'],
          "AdditionalPaymentSum" => 0.0,
          "CreditSum" => $mediosPago['monto'],
          "NumOfCreditPayments" => 1,
          "SplitPayments" => "tNO",
          "VoucherNum" => $mediosPago['baucher']
        ]];
       
     // ]);
   // }
    return $tarjetas;
  }

  private function camposUsuario($pago){
    $sqlEquipo = "SELECT * from equipox where id = ".$pago["equipo"];
    $Equipo = Yii::$app->db->createCommand($sqlEquipo)->queryOne();

    $sqlUsuario = "SELECT username from user WHERE id= ".$pago["usuario"];
    $Usuario = Yii::$app->db->createCommand($sqlUsuario)->queryOne();
    
    $arrCamposU=[
      "U_xMOB_Codigo" => $pago['nro_recibo'],
      "U_xMOB_Usuario" => $Usuario['username'],
      "U_xMOB_Equipo" => $Equipo["equipo"],
      "U_xMOB_Cobrador" => $pago['nro_recibo']
    ];
    return $arrCamposU;
  }
  private function pagosTransfer($mediosPago,$moneda){
    $monto=0;
    foreach ($mediosPago as $key => $value) {
      if($moneda=='BS'){
        if($value['formaPago']=='PBT'){
          $monto=$monto+$value['monto'];
        }
      }
    }
    return $monto;
  }
  private function pagosCashSum($mediosPago,$moneda){
    $monto=0;
    foreach ($mediosPago as $key => $value) {
      if($moneda=='BS'){
        if($value['formaPago']=='PEF'){
          $monto=$monto+$value['monto'];
        }
      }
      
    }
    return $monto;
  }
 /* private function cuentasEfectivo($equipoId,$moneda,$cabeceraPago){
    $sqlModena ="SELECT Code from monedas where Type='L'";
    $Moneda = Yii::$app->db->createCommand($sqlModena)->queryOne();
    $cuenta="";
    switch ($cabeceraPago['otpp']) {
      case 3:
          $sqlCuenta =" SELECT cuentaAnticipos from equipoxcuentascontables where equipoxId=".$equipoId;
          $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
          $cuenta=$Cuentas['cuentaAnticipos'];
        break;
    case 1:
    case 2:
        if($Moneda['Code']==$moneda){
          $sqlCuenta =" SELECT cuentaEfectivo from equipoxcuentascontables where equipoxId=".$equipoId;
          $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
          $cuenta=$Cuentas['cuentaEfectivo'];
        }
        else{
          $sqlCuenta =" SELECT cuentaEfectivoUSD from equipoxcuentascontables where equipoxId=".$equipoId;
          $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
          $cuenta=$Cuentas['cuentaEfectivoUSD'];
        }
        break;
      
      default:
        # code...
        break;
    }
   
    return $cuenta;
  }*/
  private function obtenerCuenta($equipoId,$moneda,$cabeceraPago=null,$campoCuenta=null){
    $sqlModena ="SELECT Code from monedas where Type='L'";
    $Moneda = Yii::$app->db->createCommand($sqlModena)->queryOne();
    $cuenta="";
    Yii::error('data para corroborar'.$cabeceraPago['otpp']);
    Yii::error('data para corroborar2->'.$campoCuenta);

     $sqlCuenta='';
        Yii::error('data para corroborar3->'.$Moneda['Code']);
        Yii::error('data para corroborar3mon->'.$moneda);
        if($Moneda['Code']==$moneda){
          $sqlCuenta =" SELECT ".$campoCuenta."  from equipoxcuentascontables where equipoxId=".$equipoId;
          //$Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
          //$cuenta=$Cuentas[$campoCuenta];
        }
        else{
          $campoCuenta=$campoCuenta+'USD';
          $sqlCuenta =" SELECT ".$campoCuenta." from equipoxcuentascontables where equipoxId=".$equipoId;

          
        }
        Yii::error('data para corroborar3.3->'.$sqlCuenta);
        $Cuentas = Yii::$app->db->createCommand($sqlCuenta)->queryOne();
        $cuenta=$Cuentas[$campoCuenta];
    return $cuenta;
  }
  public function modoPago($pago){
    $factura = [];
    if (intval($pago['otpp']) == 1) {

      $sqlDocumento =" SELECT DocEntry,DocType from cabeceradocumentos where id='{$pago['idDocumento']}'";
      $documento = Yii::$app->db->createCommand($sqlDocumento)->queryOne();

      switch($documento['DocType']){
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
          "DocEntry" => $documento['DocEntry'],
          "SumApplied" => $pago['monto'],
          "InvoiceType" =>$tipo,
          "InstallmentId" =>0
        ]
      ];
    }
    if (intval($pago['otpp']) == 2){     

      $salida=[];
      $i=0;
      if (count($pago['xmffacturaspagos'])) {

        foreach ($pago['xmffacturaspagos'] as $pagof) {
          $salida=[
            "LineNum" => $i,
            "DocEntry" => $pagof['docentry'],
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
  private function guardarlog($documento,$respuesta,$proceso,$iddocumento,$endpoint){
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
        $log_aux.= "INSERT INTO `log_envio`(`proceso`, `envio`, `respuesta`,  `fecha`,`endpoint`, `documento`) VALUES (";
        $log_aux .=  "'{$proceso}','{$aux_env}','{$aux_resp}','{$aux_hoy}','{$endpoint}','{$iddocumento}');";                        
        $db->createCommand($log_aux)->execute();
    }
    Yii::error('Documentos a SAP : '.$proceso .' '. $iddocumento. json_encode($respuesta));
  }

}
