<?php

namespace backend\models;
use backend\models\Sapenvio;
use backend\models\Sapenviopagos;

use Yii;

/**
 * This is the model class for table "pagos".
 *
 * @property int $id
 * @property string $documentoId Documento
 * @property string $clienteId Cliente
 * @property string $formaPago Forma de pago
 * @property int $tipoCambioDolar Tipo de cambio de dolar
 * @property double $moneda Moneda
 * @property double $monto Monto
 * @property string $numCheque Numero de cheque
 * @property string $numComprobante Numero de comprobante
 * @property string $numTarjeta Numero de tarjeta
 * @property string $numAhorro Numero de ahorro
 * @property string $numAutorizacion Numero de autorizacion
 * @property string $bancoCode Codigo de banco
 * @property string $ci CI/DNI
 * @property string $fecha Fecha
 * @property string $hora Hora
 * @property double $cambio Cambio
 * @property double $monedaDolar Valor (Moneda dolar)
 */
class Pagos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pagos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['documentoId', 'clienteId', 'formaPago', 'fecha', 'hora', 'cambio', 'recibo'], 'required'],
            [['tipoCambioDolar','idcabecera'], 'integer'],
            [['moneda', 'monto', 'cambio', 'monedaDolar'], 'number'],
            [['fecha', 'hora','estadoEnviado','equipoId','ccost'], 'safe'],
            [['documentoId', 'clienteId', 'numCheque', 'numComprobante', 'numTarjeta', 'numAhorro', 'numAutorizacion'], 'string', 'max' => 100],
            [['formaPago'], 'string', 'max' => 20],
            [['bancoCode'], 'string', 'max' => 50],
            [['ci'], 'string', 'max' => 12],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'documentoId' => Yii::t('app', 'Documento'),
            'clienteId' => Yii::t('app', 'Cliente'),
            'formaPago' => Yii::t('app', 'Forma de pago'),
            'tipoCambioDolar' => Yii::t('app', 'Tipo de cambio de dolar'),
            'moneda' => Yii::t('app', 'Moneda'),
            'monto' => Yii::t('app', 'Monto'),
            'numCheque' => Yii::t('app', 'Numero de cheque'),
            'numComprobante' => Yii::t('app', 'Numero de comprobante'),
            'numTarjeta' => Yii::t('app', 'Numero de tarjeta'),
            'numAhorro' => Yii::t('app', 'Numero de ahorro'),
            'numAutorizacion' => Yii::t('app', 'Numero de autorizacion'),
            'bancoCode' => Yii::t('app', 'Codigo de banco'),
            'ci' => Yii::t('app', 'CI/DNI'),
            'fecha' => Yii::t('app', 'Fecha'),
            'hora' => Yii::t('app', 'Hora'),
            'cambio' => Yii::t('app', 'Cambio'),
            'monedaDolar' => Yii::t('app', 'Valor (Moneda dolar)'),
            'equipoId' => 'Equipo',
            'recibo' => 'Doc Pago',
            'ccost' => 'Centro',
            'TransId' => 'Doc Entry',
        ];
    }

    public function registrarPago($datos,$idCabecera=''){
        $pagos=new Pagos;
        Yii::error("Entra pago a registro " .json_encode($datos));
        $arr = [];
        $aux_numrecibo=0;
        if(count($datos)==0){
            return [];
        }
        if(is_array($datos)and (count($datos)>0)){
            Yii::error("es array");
            $val=$datos;
        }else{
            Yii::error(" No es array");
            $val=$datos;
        }
        Yii::error("Entra pago a registro p2 " .json_encode($val));
        //foreach ($datos as $key => $val) {

                // hacer validacion de registro doble
                //$xcantidadRecibo = $this->verificador($val["recibo"]);
                $xcantidadRecibo = $pagos->verificador($val["recibo"]);
                Yii::error("VERIFICADOR DE RECIBO: ");
                Yii::error($xcantidadRecibo);
                $xcontador=count($xcantidadRecibo);
                if(($xcontador>0) and ($val["anulado"] == 0) ){
                    $xcantidadRecibo2=$pagos->verificador2($val["recibo"],$val["clienteId"],$val["monto"],$val["formaPago"],$val["fecha"]);
                    $xcontador2=count($xcantidadRecibo2);
                    if($xcontador2>0){
                        $arr[] = [
                            "id" => $val['id'],
                            "xid" => $xcantidadRecibo[0]["id"], // id que se tiene en el servidor nuevo estado=0 ; pago anulado y registrado en middleware 1 si no 0;
                            "estado" => 1,
                            "anulado" => 0,
                            "control" => $val["recibo"],
                            "equipo" => 0,
                            "numero"=> 0,
                            "error"=>" "

                        ];
                    }else{
                        $sqlUltimo="select cast((SUBSTRING(max(recibo), -5)) as UNSIGNED)as ultimo from pagos where usuario=".$val["idUser"];
                        $ultimoNumP=Yii::$app->db->createCommand($sqlUltimo)->queryOne();
                        $arr[] = [
                            "id" => $val['id'],
                            "xid" => $xcantidadRecibo[0]["id"], // id que se tiene en el servidor nuevo estado=0 ; pago anulado y registrado en middleware 1 si no 0;
                            "estado" => 0,
                            "anulado" => 0,
                            "control" => $val["recibo"],
                            "equipo" => 0,
                            "numero"=>$ultimoNumP['ultimo'],
                            "error"=>"Duplicado"
                        ];
                    }
                    
                    Yii::error("Pagos Recibidos Middleware ===> " . json_encode($arr));
                    return $arr;
                }
        //    if(is_array($val)){
                if($val['otpp']!=1)
                $this->registrarHistorialPago($val);
                
                $estadoPago = 0 ;
                $idBaseDatos = 0;
                $control = $val["recibo"];
                $estadoPagoAnulado = 0;
                try {
                    Yii::error("ANULADO VALOR " . $val["anulado"]);
                    if($val["anulado"] == 1){
                        $idBaseDatos = $val["estado"];
                        $cantidadRecibo = $this->verificador($val["recibo"]);
                        if (count($cantidadRecibo) > 0) {
                            $sql_auxupdatepago = "UPDATE pagos SET estadoEnviado = 6 WHERE recibo = '" .$val["recibo"]. "';";
		                    $aux_updatepagos = Yii::$app->db->createCommand($sql_auxupdatepago)->execute();
                            //$respuestaActualizacion = $this->updatePago($val["recibo"]);
                            //Yii::error("Respuesta Actualizacion ==>" . json_encode($respuestaActualizacion));
                            if($aux_updatepagos==1){
                                Sapenviodoc::pagoCancelar($val["recibo"],$val["equipo"]);
                            }
                            $estadoPago = 1;
                            $estadoPagoAnulado = 6;
                        } else {
                            Yii::error("llego el pago a sap pero no encontro pagos con el recibo " . $val["recibo"]);
                            $estadoPago = 0;    
                        } 
                    } else {
                        $estadoPago = 0;

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
                        if(isset($val['cardCreditNumber'])&&$val['cardCreditNumber']!=0){
                            $val['cardCreditNumber']=$val['cardCreditNumber'];
                        }else{
                            $val['cardCreditNumber']=$val['numTarjeta'];
                        }
                        if (isset($val['CreditCard'])) {
                            $val['CreditCard'] = $val['CreditCard'];
                        } else {
                            $val['CreditCard'] = 0;
                        }
                        
                        $pos_dfo = strpos($val['documentoId'],"DOF");
                        $pos_dop = strpos($val['documentoId'],"DOP");
                        $pos_doe = strpos($val['documentoId'],"DOE");
                        Yii::error('objeto pago cont: clon DFO '.$pos_dfo.' dfa '.$pos_dfa.' dop '.$pos_dop.' doe '.$pos_doe);
                  
                        if ($val['formaPago']=="PCC") {
                            $tipotarjeta=$val['CreditCard'];
                        }else{
                            $tipotarjeta="";   
                        }
                        $tipocheque="0";
                        
                        $fechaemision=Date('y-m-d');
                        $U_LATITUD=0;
                        $U_LONGITUD=0;
                        $emitidoPor="";
                        $checkdate=Date('y-m-d');
                        $transferencedate=Date('y-m-d');
                        $NumeroTarjeta="";
                        $NumeroID="";
                        /*
                        fecharegistro
                        idcabecera
                        numerofactura
                        estadocdivisa
                        montofactura
                        */
                        /**
                         * obteniendo el nuevo carcode
                         */
                        $sqlCli="SELECT CardCode from clientes where Mobilecod='{$val['clienteId']}' ";
                        $dataCliente=Yii::$app->db->createCommand($sqlCli)->queryOne();
                        Yii::error("get data cliente card code ===> " .json_encode($dataCliente));
                        Yii::error("get data cliente card code 2===> ".$dataCliente['CardCode']);

                        if($dataCliente && $dataCliente['CardCode']){
                            $val['clienteId']=$dataCliente['CardCode'];     
                        }
                      

                         $campos = "documentoId,clienteId,formaPago,tipoCambioDolar,moneda,monto,numCheque,numComprobante,numTarjeta,numAhorro,numAutorizacion,bancoCode,ci,fecha,hora,cambio,monedaDolar,otpp,recibo,usuario,cardCreditNumber,baucher,ccost,dbtCode,cuota,equipoId,fecharegistro,numerofactura,NumeroTarjeta";
                        if (substr($val['documentoId'], 0, 3) == "DOF" OR substr($val['documentoId'], 0, 3) == "DOP"  OR substr($val['documentoId'], 0, 3) == "DOE" ) 
                        {
                            $val['otpp']=3;
                        }
                        if($val['otpp']==3){
                                
                            $sqlvar = "'{$val['clienteId']}', '{$val['clienteId']}', '{$val['formaPago']}', {$val['tipoCambioDolar']},'{$val['moneda']}', "
                            . "{$val['monto']}, '{$val['numCheque']}', '{$val['numComprobante']}', '{$val['numTarjeta']}', '{$val['numAhorro']}', "
                            . "'{$val['numAutorizacion']}', '{$val['bancoCode']}', '{$val['ci']}', '{$val['fecha']}', '{$val['hora']}', "
                            . " {$val['cambio']}, {$val['monedaDolar']},{$val['otpp']},'{$val['recibo']}',{$val['usuario']},'{$val['cardCreditNumber']}','{$val['baucher']}', '{$centro}', '{$val['DbtCode']}', {$cuota}, '{$equipo}'," 
                            ."'{$tipocheque}','{$fechaemision}','{$U_LATITUD}','{$U_LONGITUD}','{$emitidoPor}','{$checkdate}','{$transferencedate}','{$tipotarjeta}','{$NumeroTarjeta}','{$NumeroID}','{$val['CreditCard']}','0','0','0','0','','{$val['version']}','{$idCabecera}'";
                            //$sql = "SELECT insertarPagos($sqlvar) estado";
    
                        }
                        else{
                            if($val['otpp']==2){
                                if(count($val["facturas"])>0){
                            
                                    foreach ($val["facturas"] as $keyf => $factura) {
                                                $sqlfac = "'{$factura['cod']}', '{$factura['recibo']}', '{$factura['pagarx']}','{$factura['clienteId']}','{$factura['docentry']}','{$factura['docnum']}','01','2100','{$factura['nroFactura']}','{$factura['CardName']}','{$factura['saldo']}','{$factura['DocTotal']}','{$factura['cuota']}'";
                                                $sqlf="SELECT f_pagosfacturas($sqlfac) estado";
                                                Yii::$app->db->createCommand($sqlf)->queryOne();
                                                Yii::error("FACTURA ALMACENADA ===> "  . json_encode($sqlfac));
                                    }
                                    $sqlvar = "'{$val['clienteId']}', '{$val['clienteId']}', '{$val['formaPago']}', {$val['tipoCambioDolar']}, '{$val['moneda']}', "
                                    . "{$val['monto']}, '{$val['numCheque']}', '{$val['numComprobante']}', '{$val['numTarjeta']}', '{$val['numAhorro']}', "
                                    . "'{$val['numAutorizacion']}', '{$val['bancoCode']}', '{$val['ci']}', '{$val['fecha']}', '{$val['hora']}', "
                                    . " {$val['cambio']}, {$val['monedaDolar']},{$val['otpp']},'{$val['recibo']}',{$val['usuario']},'{$val['cardCreditNumber']}','{$val['baucher']}', '{$centro}', '{$val['DbtCode']}', {$cuota}, '{$equipo}'," 
                                    ."'{$tipocheque}','{$fechaemision}','{$U_LATITUD}','{$U_LONGITUD}','{$emitidoPor}','{$checkdate}','{$transferencedate}','{$tipotarjeta}','{$NumeroTarjeta}','{$NumeroID}','{$val['CreditCard']}','0','0','0','0','','{$val['version']}','{$idCabecera}'";    
                                    $mensaje="Correcto"; 
                                }
                                else{
                                    $mensaje="Error! el registro no tiene pagos facturas"; 
                                }

                            }
                            else{
                                $sqlvar = "'{$val['documentoId']}', '{$val['clienteId']}', '{$val['formaPago']}', {$val['tipoCambioDolar']}, '{$val['moneda']}', "
                                . "{$val['monto']}, '{$val['numCheque']}', '{$val['numComprobante']}', '{$val['numTarjeta']}', '{$val['numAhorro']}', "
                                . "'{$val['numAutorizacion']}', '{$val['bancoCode']}', '{$val['ci']}', '{$val['fecha']}', '{$val['hora']}', "
                                . " {$val['cambio']}, {$val['monedaDolar']},{$val['otpp']},'{$val['recibo']}',{$val['usuario']},'{$val['cardCreditNumber']}','{$val['baucher']}', '{$centro}', '{$val['DbtCode']}', {$cuota}, '{$equipo}'," 
                                ."'{$tipocheque}','{$fechaemision}','{$U_LATITUD}','{$U_LONGITUD}','{$emitidoPor}','{$checkdate}','{$transferencedate}','{$tipotarjeta}','{$NumeroTarjeta}','{$NumeroID}','{$val['CreditCard']}','{$val['anulado']}','{$val['monedaLocal']}','{$val['estado']}','{$val['tipo']}','{$val['documentoPagoId']}','{$val['version']}','{$idCabecera}'"; 
                            }
                        }
                        /*
                        $sqlvar = "'{$val['documentoId']}', '{$val['clienteId']}', '{$val['formaPago']}', {$val['tipoCambioDolar']}, {$val['moneda']}, "
                          . "{$val['monto']}, '{$val['numCheque']}', '{$val['numComprobante']}', '{$val['numTarjeta']}', '{$val['numAhorro']}', "
                          . "'{$val['numAutorizacion']}', '{$val['bancoCode']}', '{$val['ci']}', '{$val['fecha']}', '{$val['hora']}', "
                          . " {$val['cambio']}, {$val['monedaDolar']},{$val['otpp']},'{$val['recibo']}',{$val['usuario']},'{$val['cardCreditNumber']}','{$val['baucher']}', '{$centro}', {$cuota}, '{$equipo}'"; 
                        */

                        //$sql = "INSERT into pagos ({$campos}) values ({$sqlvar})";
                        //Yii::error('PAGOS-> Registro' . $sql);
                        //$resp = Yii::$app->db->createCommand($sql)->execute();
                        //$idultimo = Yii::$app->db->getLastInsertID();
                        try{
                            $sql = "SELECT insertarPagos($sqlvar) estado";
                            $resp = Yii::$app->db->createCommand($sql)->queryOne();
                            $idBaseDatos = (int) $resp['estado'];
                            Yii::error("PAGO GUARDADO ===> "  . json_encode($resp));
                            // CODIGO DE GEDESA//
                            $dataPago = Yii::$app->db->createCommand('SELECT * from pagos WHERE id='.$idBaseDatos)->queryOne();
                            $recibo = $dataPago["recibo"];
                            $equipoId=$dataPago["equipoId"];                            
                            $estadoPago=1;
                            $sqlup = "UPDATE numeracion SET numgp = (numgp+1) WHERE iduser = {$val['usuario']};";
                            Yii::$app->db->createCommand($sqlup)->execute();
                        }catch(\Exception $e){
                            Yii::error("error al registrar pago: ".$e);
                        }
                    }  
                     
                    
                    $arr[] = [
                        "id" => $val['id'],
                        "xid" => $idBaseDatos, // id que se tiene en el servidor nuevo estado=0 ; pago anulado y registrado en middleware 1 si no 0;
                        "estado" => $estadoPago,
                        "anulado" => $estadoPagoAnulado,
                        "control" => $control,
                        "equipo" => $equipoId,
                        "numero"=> 0,
                        "error"=>" "
                    ];
                    // $sqlup = "UPDATE numeracion SET numgp = (numgp+1) WHERE iduser = {$val['usuario']};";
                    //Yii::$app->db->createCommand($sqlup)->execute();
                    /////////////////////CODIGO DE GEDESA ENVIO RECIBO////////////////////////
                    if($val['otpp']==2){
                        $aux_numrecibo=$recibo;
                        Yii::error("Numero de Recibo: ".$aux_numrecibo);
                        $enviopago=Sapenvio::pagarPorRecibo($aux_numrecibo);
                        // comentado por mau para ver si llega los datos a midd
                        if(isset($enviopago->error)){                  
                            Yii::error("-->>> error".json_encode($enviopago));

                            $mensaje= $enviopago->error->message->value;
                            Yii::error("-->>> error mensaje: ".$mensaje);
                            //return $this->error($mensaje);
                            //$responseUpdate = Yii::$app->db->createCommand($sqlUpdate)->queryOne();
                            //Yii::error(json_encode($responseUpdate));
                        }else{
                            Yii::error("-->>> sin error error"); 
                            if (isset($enviopago->DocNum))
                            Yii::$app->db->createCommand('Update pagos set estadoEnviado=1,TransId="'.$enviopago->DocEntry.'" where recibo="'.$aux_numrecibo.'"')->execute();
                            Yii::error("Respuesta: ".json_encode($enviopago));
                            return $arr;
                        }
                    }
                    elseif($val['otpp']==3 && isset($recibo)){
                        switch ($val['formaPago']) {
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
                    
                } catch (\Exception $e) {
                    Yii::error('PAGOS-ERROR'.$e->getMessage());
                    $arr[] = [
                        "id" => $val['id'],
                        "xid" => 0, // id que se tiene en el servidor, pago nuevo estado=0 ; pago anulado y registrado en middleware 1 si no 0;
                        "estado" => 0,// $estadoPago
                        "anulado" => 0,//anulado
                        "control" => $control,
                        "equipo"=>0,
                        "numero"=> 0,
                        "error"=>$e->getMessage()
                    ];
                }
            //}
        //}
            
        Yii::error("Pagos Recibidos Middleware ===> " . json_encode($arr));
        return $arr;
    }
    private function verificador($cod) {
        // SIN LIMIT PREVIAMENTE
        $sql = 'SELECT * FROM pagos WHERE recibo = "' . $cod . '" LIMIT 1;';
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
    private function verificador2($cod,$cliente,$monto,$formapago,$fecha) {
        // SIN LIMIT PREVIAMENTE
        $sql = "SELECT * FROM pagos WHERE recibo = '". $cod . "' and clienteId = '". $cliente . "' and monto = '". $monto . "' and formaPago = '". $formapago . "' and fecha = '". $fecha . "' LIMIT 1 ";
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
}
