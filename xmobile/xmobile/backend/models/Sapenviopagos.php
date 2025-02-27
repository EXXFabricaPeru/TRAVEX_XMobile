<?php

namespace backend\models;

use backend\models\Cabeceradocumentos;
use backend\models\Clientes;
use backend\models\Configlayer;
use backend\models\Pagos;
use backend\models\Servislayer;
use backend\models\Unidadesmedida;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Yii;
use yii\base\Model;

class Sapenviopagos extends Model
{
  public $id;
  private $cliente;
  private $conf;
  private $serviceLayer;

  public function __construct($id, $module, $config = [])
  {
    parent::__construct($id, $module, $config);
    $this->conf = new Configlayer();
    $this->cliente = new Client([
      'base_uri' => $this->conf->path,
      'timeout' => 30,
      'verify' => false,
      'cookies' => true
    ]);
    $this->serviceLayer = new Servislayer();
  }

  public function efectivo($recibo='',$equipoId=''){
    $serviceLayer = new Servislayer();
    $serviceLayer->actiondir = "IncomingPayments";
    if($recibo!='' && $equipoId!=''){
      Yii::error("ingreso desde pagos");
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and formaPago = :forma and recibo=:recibo and equipo=:equipo')
      ->bindValue(':forma', 'PEF')
      ->bindValue(':recibo', $recibo)
      ->bindValue(':equipo', $equipoId)
      ->queryAll();
    }else{
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and formaPago = :forma')
      ->bindValue(':forma', 'PEF')
      ->queryAll();
    }
   
	  Yii::error("Pagos efectivox :".count($pagos));
    if ( (count($pagos)) and (count($pagos)>0)) {
       Yii::error("Pagos efectivo pago:".$pago['CardName']." =>".json_encode($pago));
      foreach ($pagos as $pago) {
		try {
	        Yii::error("Pagos efectivo pago:".$pago['CardName']." =>".json_encode($pago));
			$cuentapago=$pago['ctaEfectivo'];
			$usuarioxm = Yii::$app->db->createCommand("select username from user where id = '{$pago["usuario"]}'")->queryOne();
      $vendedor = Yii::$app->db->createCommand("select username from user where id = '{$pago["usuario"]}'")->queryOne();
      $usuarioxm =	$usuarioxm["username"];
      $pagoSAP = [
			  "DocType" => "rCustomer",
			  "HandWritten" => "tNO",
			  "Printed" => "tNO",
			  "DocDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
			  "CardCode" => $pago['CardCode'],
			  "CardName" => $pago['CardName'],
			  "Address" => $pago['Address'],
			  "CashAccount" =>$cuentapago ,
			  "CashSum" => $pago['monto'],
			  "LocalCurrency" => "tNO",
			  "SplitTransaction" => "tNO",
			  "ApplyVAT" => "tNO",
			  "TaxDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
			  "CurrencyIsLocal" => "tNO",
			  "Proforma" => "tNO",
			  "PayToCode" => (intval($pago['otpp']) !== 3) ? "Bill to": null,
			  "IsPayToBank" => "tNO",
			  "PaymentPriority" => "bopp_Priority_6",
			  "VatDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
			  "TransactionCode" => "",
			  "PaymentType" => "bopt_None",
			  "DocObjectCode" => "bopot_IncomingPayments",
			  "DocTypte" => "rCustomer",
			  "DueDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
			  "Cancelled" => "tNO",
			  "AuthorizationStatus" => "pasWithout",
			  "PaymentByWTCertif" => "tNO",
        "PaymentInvoices" => Sapenviopagos::modoPago($pago),
        "U_xMOB_Codigo" => $pago['recibo'],
        "U_xMOB_Usuario" => $usuarioxm ,
        "U_xMOB_Equipo" => $pago['equipo'],
        "U_xMOB_Cobrador" =>$pago['cobrador']
			];
			if($pago['otpp']==3){
			  //$pagoSAP["ControlAccount"]=$pago['ctaanticipo'];
			  //$pagoSAP["U_UsaLc"]=$pago['ccost'];
			}
			  Yii::error("PAGO-EFECTIVO " . json_encode($pagoSAP));
        
        $respuesta = $serviceLayer->executePost($pagoSAP);
        // Yii::error("POST PAGO EFECTIVO - ID:  " . json_encode($respuesta));
        // Yii::error("RESPUESTA PAGO EFECTIVO - ID:  " . json_encode($respuesta));
        if (isset($respuesta->DocNum)) {
  			  $pagoMiddleware = Pagos::findOne($pago['id']);
  			  $pagoMiddleware->estadoEnviado =$pagoMiddleware->estadoEnviado != 0 ? $pagoMiddleware->estadoEnviado : 1;
  			  $pagoMiddleware->TransId = $respuesta->DocEntry;
  			  $pagoMiddleware->save(false);      
        }else{
          Sapenviopagos::manejoErrorPago($respuesta,$pago['id'],$pagoSAP,$pago['formaPago'],$pago["recibo"]);
        }
      
		} catch (Exception $e) {

			Yii::error("ERROR PAGOS :".$e->getMessage());
      Sapenviopagos::manejoErrorPago($e->getMessage(),$pago['id'],$pagoSAP,$pago['formaPago'],$pago["recibo"]);
		}
      }    
    }
  }

  public function cheque($recibo='',$equipoId=''){
    $serviceLayer = new Servislayer();
    $serviceLayer->actiondir = "IncomingPayments";
    if($recibo!='' && $equipoId!=''){
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and formaPago = :forma and recibo=:recibo and equipo=:equipo')
      ->bindValue(':forma', 'PCH')
      ->bindValue(':recibo', $recibo)
      ->bindValue(':equipo', $equipoId)
      ->queryAll();
    }
    else{
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and formaPago = :forma')
      ->bindValue(':forma', 'PCH')
      ->queryAll();
    }
    
    Yii::error("Pagos cheque x :".count($pagos));
    if (count($pagos)) {
      foreach ($pagos as $pago) {
        $cuentapago=$pago['ctaCheque'];
        $usuarioxm = Yii::$app->db->createCommand("select username from user where id = '{$pago["usuario"]}'")->queryOne();
        $usuarioxm =	$usuarioxm["username"];
        $pagoSAP = [
          "DocType" => "rCustomer",
          "HandWritten" => "tNO",
          "Printed" => "tNO",
          "DocDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "CardCode" => $pago['CardCode'],
          "CardName" => $pago['CardName'],
          "Address" => $pago['Address'],
          "CashAccount" => null,
          "CheckAccount" => $cuentapago,
          "CashSum" => 0.0,
          "ContactPersonCode" => 0,
          "LocalCurrency" => "tNO",
          "SplitTransaction" => "tNO",
          "ApplyVAT" => "tNO",
          "TaxDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "CurrencyIsLocal" => "tNO",
          "Proforma" => "tNO",
          "PayToCode" => (intval($pago['otpp']) !== 3) ? "Bill to": null,
          "IsPayToBank" => "tNO",
          "PaymentPriority" => "bopp_Priority_6",
          "VatDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "TransactionCode" => "",
          "PaymentType" => "bopt_None",
          "DocObjectCode" => "bopot_IncomingPayments",
          "DocTypte" => "rCustomer",
          "DueDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "Cancelled" => "tNO",
          "AuthorizationStatus" => "pasWithout",
          "PaymentByWTCertif" => "tNO",
          "PayToCode" => "Bill to",
          "TransactionCode" => "",
         // "U_xMOB_Cobrador" =>substr ( $pago['cobrador'] , 0 ,30 ),
          "U_LB_TipoDocumento" => "1",
          "U_xMOB_Codigo" => $pago['recibo'],
          "U_xMOB_Usuario" => $usuarioxm,
          "U_xMOB_Equipo" => $pago['equipo'],
          "U_xMOB_Cobrador" =>$pago['cobrador'],
          /* "VatDate" => null, */
          "PaymentChecks" => [
            [
              "AccounttNum" => "",
              "LineNum" => 0,
              "DueDate" => Carbon::parse($pago['fecha'])->addDays(30)->toDateString(),//"DueDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
              "U_LB_Fecha_cheque" => $pago['fecha'],
              "BankCode" => $pago['banco'],
              "Trnsfrable" => "tYES",
              "CheckSum" => $pago['monto'],
              "CheckNumber"=>$pago['numCheque'],
              "Currency" => "SOL",
              "CountryCode" => "PE",
              "CheckAccount" => $cuentapago,
              "ManualCheck" => "tNO",
              "Endorse" => "tNO"
            ]
          ],
          "PaymentInvoices" => Sapenviopagos::modoPago($pago)
        ];
        if($pago['otpp']==3){
         // $pagoSAP["ControlAccount"]=$pago['ctaanticipo'];
         // $pagoSAP["U_UsaLc"]=$pago['ccost'];
        }
      }
      Yii::error("PAGO-CHEQUE " . json_encode($pagoSAP));
      $respuesta = $serviceLayer->executePost($pagoSAP);
      if (isset($respuesta->DocNum)) {
        $pagoMiddleware = Pagos::findOne($pago['id']);
       $pagoMiddleware->estadoEnviado =$pagoMiddleware->estadoEnviado != 0 ? $pagoMiddleware->estadoEnviado : 1;
        $pagoMiddleware->save(false);
      }else{
        Sapenviopagos::manejoErrorPago($respuesta,$pago['id'],$pagoSAP,$pago['formaPago'],$pago["recibo"]);
      }
      Yii::error(json_encode($respuesta));
    }
  }

  public function transferencia($recibo='',$equipoId=''){
    Yii::error('Pago con transferencia ================');
    $serviceLayer = new Servislayer();
    $serviceLayer->actiondir = "IncomingPayments";
    if($recibo!='' && $equipoId!=''){
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and  formaPago = :forma and recibo=:recibo and equipo=:equipo')
      ->bindValue(':forma', 'PBT')
      ->bindValue(':recibo', $recibo)
      ->bindValue(':equipo', $equipoId)
      ->queryAll();
    }
    else{
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and  formaPago = :forma')
      ->bindValue(':forma', 'PBT')
      ->queryAll();
    }
    
      Yii::error("Pagos transferencia x :".count($pagos));
    if (count($pagos)) {
      foreach ($pagos as $pago) {
        
        // Cuenta para Transfer Account por defecto, si no lleva la del banco lleva la del equipo
        $cuentaPagoAlternativa = Yii::$app->db->createCommand('SELECT cuentaTranferencia from equipoxcuentascontables WHERE equipoxId = :idequipo')
          ->bindValue(':idequipo', $pago['equipoId'])
          ->queryAll();
        $cuentaTransferenciaEquipo = $cuentaPagoAlternativa[0]["cuentaTranferencia"];
        $cuentapago = isset($pago['banco']) ? $pago['banco'] : $cuentaTransferenciaEquipo;
         $usuarioxm = Yii::$app->db->createCommand("select username from user where id = '{$pago["usuario"]}'")->queryOne();
         $usuarioxm =	$usuarioxm["username"];
         $pagoSAP = [
          "DocType" => "rCustomer",
          "HandWritten" => "tNO",
          "Printed" => "tNO",
          "DocDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "CardCode" => $pago['CardCode'],
          "CardName" => $pago['CardName'],
          "Address" => $pago['Address'],
          "CashAccount" => null,
          "CheckAccount" => null,
          "TransferAccount" => $cuentapago,
          "TransferReference" => $pago['cardCreditNumber'],
          "CashSum" => 0.0,
          "TransferSum" => $pago['monto'],
          "TransferDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "LocalCurrency" => "tNO",
          "SplitTransaction" => "tNO",
          "ApplyVAT" => "tNO",
          "TaxDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "CurrencyIsLocal" => "tNO",
          "Proforma" => "tNO",
          "PayToCode" => (intval($pago['otpp']) !== 3) ? "Bill to": null,
          "IsPayToBank" => "tNO",
          "PaymentPriority" => "bopp_Priority_6",
          "VatDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "TransactionCode" => "",
          "PaymentType" => "bopt_None",
          "DocObjectCode" => "bopot_IncomingPayments",
          "DocTypte" => "rCustomer",
          "DueDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "Cancelled" => "tNO",
          "AuthorizationStatus" => "pasWithout",
          "PaymentByWTCertif" => "tNO",
          "PaymentChecks" => [],
          "PaymentInvoices" => Sapenviopagos::modoPago($pago),
         // "U_xMOB_Cobrador" =>substr ( $pago['cobrador'] , 0 ,30 ),
          "U_LB_TipoDocumento" => "3",
          "U_xMOB_Codigo" => $pago['recibo'],
          "U_xMOB_Usuario" => $usuarioxm,
          "U_xMOB_Equipo" => $pago['equipo'],
          "U_xMOB_Cobrador" =>$pago['cobrador']
        ];
        if($pago['otpp']==3){
         // $pagoSAP["ControlAccount"]=$pago['ctaanticipo'];
         // $pagoSAP["U_UsaLc"]=$pago['ccost'];
        }
      }
      $respuesta = $serviceLayer->executePost($pagoSAP);
      Yii::error("PAGO-TRANSFERENCIA" . json_encode($pagoSAP));
      if (isset($respuesta->DocNum)) {
        $pagoMiddleware = Pagos::findOne($pago['id']);
        $pagoMiddleware->estadoEnviado =$pagoMiddleware->estadoEnviado != 0 ? $pagoMiddleware->estadoEnviado : 1;
        $pagoMiddleware->save(false);
      }else{
        Sapenviopagos::manejoErrorPago($respuesta,$pago['id'],$pagoSAP,$pago['formaPago'],$pago["recibo"]);
      }
      Yii::error(json_encode($respuesta));
    }
  }

  public function tarjeta($recibo='',$equipoId=''){
    $serviceLayer = new Servislayer();
    $serviceLayer->actiondir = "IncomingPayments";
    if($recibo!='' && $equipoId!=''){
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and formaPago = :forma and recibo=:recibo and equipo=:equipo')
      ->bindValue(':forma', 'PCC')
      ->bindValue(':recibo', $recibo)
      ->bindValue(':equipo', $equipoId)
      ->queryAll();
    }
    else{
      $pagos = Yii::$app->db->createCommand('select * from v_pagos where (estadoEnviado=0 or estadoEnviado=6) and formaPago = :forma')
      ->bindValue(':forma', 'PCC')
      ->queryAll();
    }
    
    Yii::error("Pagos tarjeta x :".count($pagos));
    if (count($pagos)) {
      foreach ($pagos as $pago) {

        $aux = date('Y-m-d', strtotime("{$pago["vencimiento"]} + 1 month"));
        $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));

       
          $cuentapago=$pago['ctaTarjeta'];
        $usuarioxm = Yii::$app->db->createCommand("select username from user where id = '{$pago["usuario"]}'")->queryOne();
        $usuarioxm =	$usuarioxm["username"];
        $pagoSAP = [
          "DocType" => "rCustomer",
          "HandWritten" => "tNO",
          "Printed" => "tNO",
          "DocDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "CardCode" => $pago['CardCode'],
          "CardName" => $pago['CardName'],
          "Address" => $pago['Address'],
          "CashAccount" => null,
          "CheckAccount" => null,
          
          "TransferAccount" => null,
          "CashSum" => 0.0,
          "TransferSum" => 0.0,
          "TransferDate" => null,
          "LocalCurrency" => "tNO",
          "SplitTransaction" => "tNO",
          "ApplyVAT" => "tNO",
          "TaxDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "CurrencyIsLocal" => "tNO",
          "Proforma" => "tNO",
          "PayToCode" => (intval($pago['otpp']) !== 3) ? "Bill to": null,
          "IsPayToBank" => "tNO",
          "PaymentPriority" => "bopp_Priority_6",
          "VatDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "TransactionCode" => "",
          "PaymentType" => "bopt_None",
          "DocObjectCode" => "bopot_IncomingPayments",
          "DocTypte" => "rCustomer",
          "DueDate" => Carbon::today('America/La_Paz')->format('Y-m-d'),
          "Cancelled" => "tNO",
          "AuthorizationStatus" => "pasWithout",
          "PaymentByWTCertif" => "tNO",
          "PaymentChecks" => [],
          "PaymentInvoices" => Sapenviopagos::modoPago($pago),
          "U_xMOB_Codigo" => $pago['recibo'],
          "U_xMOB_Usuario" => $usuarioxm,
          "U_xMOB_Equipo" => $pago['equipo'],
          "U_xMOB_Cobrador" =>$pago['cobrador'],
          //"U_xMOB_Cobrador" =>substr ( $pago['cobrador'] , 0 ,30 ),
          "PaymentCreditCards" => [
            [
              "LineNum" => 0,
              "CreditCard" => 1, //nombre de la tarjeta creado desde SAP
              "CreditAcct" => $cuentapago,
              "CreditCardNumber" => $pago['numTarjeta'],// solo los ultimos 4
              "CardValidUntil" => $last_day,//$pago[''] //la fecha debe tener ese formato con el ultimo dia del mes,
              "OwnerIdNum" => "1",
              "OwnerPhone" => '2475896',//$pago['2475896'],
              "PaymentMethodCode" => -1, //puede ir -1 no pude definir de donde sale
              "NumOfPayments" => 1,
              "FirstPaymentDue" => Carbon::today('America/La_Paz')->format('Y-m-d'),
              "FirstPaymentSum" => $pago['monto'],
              "AdditionalPaymentSum" => 0.0,
              "CreditSum" => $pago['monto'],
              //"CreditCur"=> "BS",//,
              "NumOfCreditPayments" => 1,
              "SplitPayments" => "tNO",
              "VoucherNum" => $pago['baucher']
            ]
          ]
        ];
        if($pago['otpp']==3){
         // $pagoSAP["ControlAccount"]=$pago['ctaanticipo'];
         // $pagoSAP["U_UsaLc"]=$pago['ccost'];
        }
      }
      Yii::error("PAGO-TARJETA" . json_encode($pagoSAP));
      $respuesta = $serviceLayer->executePost($pagoSAP);
      if (isset($respuesta->DocNum)) {
        $pagoMiddleware = Pagos::findOne($pago['id']);
       $pagoMiddleware->estadoEnviado =$pagoMiddleware->estadoEnviado != 0 ? $pagoMiddleware->estadoEnviado : 1;
        $pagoMiddleware->save(false);
      }else{
        Sapenviopagos::manejoErrorPago($respuesta,$pago['id'],$pagoSAP,$pago['formaPago'],$pago["recibo"]);
      }
      Yii::error(json_encode($respuesta));
    }
  }

  private function modoPago($pago) {
    $factura = [];
    if (intval($pago['otpp']) !== 3) {
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
    return $factura;
  }

  private function manejoErrorPago($error,$id,$objeto,$txt,$recibo){
    Yii::error("ERROR PAGOS :".json_encode($error));
    
    $xiddocumento=$recibo;
    $msg=$error->error->message->value;
    $code=$error->error->code;
    $aux_resp="codigo ".$code." msg ".str_replace("'","", $msg );
    $aux_resp=addslashes ( $aux_resp );
    $text="PAGO ".$txt; 
    $aux_env=json_encode($objeto);
    $aux_hoy=Carbon::now('America/La_Paz')->format('Y-m-d H:m:s');
    $log_aux="";
    $log_aux=" SELECT idlog from log_envio where envio='".$aux_env."'";
    $db = Yii::$app->db;
    $aux_control=$db->createCommand($log_aux)->queryOne();
    if( $aux_control["idlog"]){
      $log_aux= "UPDATE  `log_envio` set ultimo ='".$aux_hoy."' ";
      $log_aux .= " WHERE idlog=".$aux_control["idlog"]; 
      Yii::error("ERROR PAGOS  Q: ".$log_aux);                       
      $db->createCommand($log_aux)->execute();
    }else{
      
      $log_aux= "INSERT INTO `log_envio`(`proceso`, `envio`, `respuesta`,  `fecha`,`documento`) VALUES (";
      $log_aux .= "'{$text}','{$aux_env}','{$aux_resp}','{$aux_hoy}','{$recibo}');"; 
      Yii::error("ERROR PAGOS Q: ".$log_aux);                       
      Yii::$app->db->createCommand($log_aux)->execute();
    }


    /*
    if (($code==-10)) {
      $pagoMiddleware = Pagos::findOne($id);
      $pagoMiddleware->estadoEnviado = '9';// estado de envio documento cerrado
      $pagoMiddleware->save(false);      
    }
   
    else{
    
      
      

      $log_aux="";
      $log_aux=" SELECT idlog from log_envio where envio='".$aux_env."'";
      $db = Yii::$app->db;
      $aux_control=$db->createCommand($log_aux)->queryOne();
      //Yii::error("ERROR PAGOS LOG : ".jsonencode($aux_control)); 
      if( $aux_control["idlog"]){
        $log_aux= "UPDATE  `log_envio` set ultimo ='".$aux_hoy."' ";
        $log_aux .= " WHERE idlog=".$aux_control["idlog"]; 
        Yii::error("ERROR PAGOS  Q: ".$log_aux);                       
        $db->createCommand($log_aux)->execute();
      }else{
        
        $log_aux= "INSERT INTO `log_envio`(`proceso`, `envio`, `respuesta`,  `fecha`,`documento`) VALUES (";
        $log_aux .= "'{$text}','{$aux_env}','{$aux_resp}','{$aux_hoy}','{$recibo}');"; 
        Yii::error("ERROR PAGOS Q: ".$log_aux);                       
        Yii::$app->db->createCommand($log_aux)->execute();
      }
    //}
    Yii::error("ERROR PAGOS : codigo- ".$code." msg - ".$msg);
    */
  }
}
