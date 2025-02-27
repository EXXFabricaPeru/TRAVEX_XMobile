<?php

namespace backend\models\v2;
use backend\models\Historialpagos;
use backend\models\Xmfcabezerapagos;
use backend\models\Xmffacturaspagos;
use backend\models\Xmfmediospagos;
use yii\base\Model;
use Yii;

class Pagos extends Model
{
    public function __construct(){
       
    }
    public function registrarPago($datos,$idCabecera='',$idHistorial){
        $pagos = new Pagos;
        Yii::error("Entra pago a registro " .json_encode($datos));
        $arr = [];
        if(is_array($datos)and (count($datos)>0)){
            Yii::error("es array");
            $val=$datos;
            // Si el pago esta duplicado ingresa al if//
            $respuesta=$pagos->validaExistePago($val);
            Yii::error("Respuesta Validador: ".json_encode($respuesta));
            if(!$respuesta['registro']) return $respuesta;

            $estadoPago = 0 ;
            $idBaseDatos = 0;
            $control = $val["nro_recibo"];
            $estadoPagoAnulado = 0;
            try {
                Yii::error("ANULADO VALOR " . $val["cancelado"]);
                if($val["cancelado"] == 1){

                    /*if (count($xcantidadRecibo) > 0) {
                        $sql_auxupdatepago = "UPDATE pagos SET estadoEnviado = 6 WHERE recibo = '" .$val["recibo"]. "';";
                        $aux_updatepagos = Yii::$app->db->createCommand($sql_auxupdatepago)->execute();
                        $estadoPago = 1;
                        $estadoPagoAnulado = 6;
                    } else {
                        Yii::error("llego el pago a sap pero no encontro pagos con el recibo " . $val["recibo"]);
                        $estadoPago = 0;    
                    }*/ 
                } else {
                    $val=$pagos->camposAdicionales($val);
                    $respuestaRC=$pagos->registrarCabeceraPago($val,$idCabecera,$idHistorial);
                    Yii::error("RESPUESTA REGISTRO CABECERA: ". json_encode($respuestaRC));
                    if($respuestaRC['respuesta']){
                        $respuestaRF=$pagos->registrarFacturasPago($val['facturaspago'],$respuestaRC['id']);
                        $respuestaRM=$pagos->registrarMediosPagos($val['mediosPago'],$respuestaRC['id']);
                        if(!$respuestaRM){
                            //realiazar un rollBack() a la cabecera
                            // falta codificar para el rollback
                            Yii::error("REALIAZAR UN ROLLBACK A ALA CABECERA DE PAGOS");
                            $estadoPago=0;
                            $registro=false;
                            $mensaje="Error! no se registro medios pagos";
                        }
                    }
                    
                }  
    
                $arr = [
                    "id" =>$respuestaRC['id'],//id cabecera xmfcabezerapagos(Midd)
                    "estado" => $respuestaRC['estado'],
                    "anulado" => 0,
                    "recibo" => $control,
                    "numeracion"=> 0,
                    "codigo"=>200,
                    "registro"=>true, //control solo Midd tru=se registro y false no se registro
                    "mensaje"=>$respuestaRC['mensaje']
                ];
                
            } catch (\Exception $e) {
                Yii::error('PAGOS-ERROR'.$e->getMessage());
                $arr = [
                    "id" =>0,//id cabecera xmfcabezerapagos(Midd)
                    "estado" =>1,
                    "anulado" => 0,
                    "recibo" => $control,
                    "numeracion"=> 0,
                    "codigo"=>201,
                    "registro"=>false, //control solo Midd tru=se registro y false no se registro
                    "mensaje"=>$e->getMessage()
                ];
            }
        }
        else{
            $arr = [
                "id" =>0,//id cabecera xmfcabezerapagos(Midd)
                "estado" => 1,
                "anulado" => 0,
                "recibo" => 0,
                "numeracion"=> 0,
                "codigo"=>201,
                "registro"=>false, //control solo Midd tru=se registro y false no se registro
                "mensaje"=>"Error! la factura no tiene pago"
            ];
            
        }
        return $arr;
    }
    public function registrarHistorial($datos){

        try {
            Yii::error("Inserta HistorialPagos");
            date_default_timezone_set('America/La_Paz');
            $historialpagos = new Historialpagos();
            $historialpagos->id = 0;
            $historialpagos->fecha = date('Y-m-d');
            $historialpagos->fechaHora = date('Y-m-d H:i:s');
            $historialpagos->usuario = $datos['usuario'];
            $historialpagos->recibo = $datos['nro_recibo'];
            $historialpagos->otpp = $datos['otpp'];
            $historialpagos->cadenaPago = json_encode($datos);
            $historialpagos->cadenaFacturas = "";
        
             if($historialpagos->save(false)){
                $registro=true;
                $mensaje="Correcto";
                $estado=1;
                $id=$historialpagos->id;
                Yii::error("Registro Correcto");
            }
            else{
                $registro=false;
                $data = $historialpagos->getErrors();
                $mensaje=json_encode($data);
                $estado=0;
                $id=0;
                Yii::error("Error al registrar historial pagos: ".json_encode($data));
            }
            $arr = ["registro"=>$registro,"id"=>$id,"estado"=>$estado,"mensaje"=>$mensaje];
        } 
        catch (\Exception $e) {
            Yii::error('PAGOS-ERROR'.$e->getMessage());
            $arr = ["registro"=>false,"id"=>$id,"estado"=>0,"mensaje"=>$e->getMessage()];
        }
        return  $arr;
    }
    private function verificador($cod) {
        // SIN LIMIT PREVIAMENTE
        $sql = 'SELECT * FROM xmfcabezerapagos WHERE nro_recibo = "' . $cod . '" and estado=3  LIMIT 1;';
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
    private function verificador2($cod,$cliente,$monto,$fecha) {
        // SIN LIMIT PREVIAMENTE
        $sql = "SELECT * FROM xmfcabezerapagos WHERE nro_recibo = '". $cod . "' and cliente_carcode = '". $cliente . "' and monto_total = '". $monto . "'  and fecha = '". $fecha . "' and estado=3 LIMIT 1 ";
		return Yii::$app->db->createCommand($sql)->queryAll();
	}
	public function verificadorByreciboOne($cod) {
        // SIN LIMIT PREVIAMENTE
        $sql = 'SELECT * FROM xmfcabezerapagos WHERE nro_recibo = "' . $cod . '" and estado=3  LIMIT 1;';
		return Yii::$app->db->createCommand($sql)->queryOne();
	}
    private function camposAdicionales($val){

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

        if ($val['formaPago']=="PCC") {
            $tipotarjeta=$val['CreditCard'];
        }else{
            $tipotarjeta="";   
        }
        /*$tipocheque="0";
        $fechaemision=Date('y-m-d');
        $checkdate=Date('y-m-d');
        $transferencedate=Date('y-m-d');
        */
        return $val;
    }
    private function validaExistePago($val){
        Yii::error("VERIFICADOR DE RECIBO: ");
        $xcantidadRecibo = $this->verificador($val["nro_recibo"]);
        Yii::error($xcantidadRecibo);
        if((count($xcantidadRecibo)>0) and ($val["cancelado"] == 0) ){
            $xcantidadRecibo2=$this->verificador2($val["nro_recibo"],$val["cliente_carcode"],$val["monto_total"],$val["fecha"]);
            
            if(count($xcantidadRecibo2)>0){
    
                $arr = [
                    "id" =>$xcantidadRecibo[0]["id"],//id cabecera xmfcabezerapagos(Midd)
                    "estado" => 1,
                    "anulado" => 0,
                    "recibo" => $val["nro_recibo"],
                    "numeracion"=> 0,
                    "codigo"=>201,
                    "registro"=>false, //control solo Midd true=se registro y false no se registro
                    "mensaje"=>"Error! Registro ya existe"
                ];
            }else{
                //$sqlUltimo="select cast((SUBSTRING(max(nro_recibo), -5)) as UNSIGNED)as ultimo from xmfcabezerapagos where usuario=".$val["usuario"];
                $sqlUltimo="select  max(cast((SUBSTRING(nro_recibo, -5)) as UNSIGNED))as ultimo from xmfcabezerapagos where usuario=".$val["usuario"];
                $ultimoNumP=Yii::$app->db->createCommand($sqlUltimo)->queryOne();
                $arr = [

                    "id" => $xcantidadRecibo[0]["id"], // id que se tiene en el servidor nuevo estado=0 ; pago anulado y registrado en middleware 1 si no 0;
                    "estado" => 0,
                    "anulado" => 0,
                    "recibo" => $val["nro_recibo"],
                    "numeracion"=>($ultimoNumP['ultimo']+1),
                    "codigo" => 201,
                    "registro"=>false,//control solo Midd true=se registro y false no se registro
                    "mensaje"=>"Recibo duplicado, enviar nuevamente el pago"
                ];
            }
            
            Yii::error("Pagos Recibidos Middleware ===> " . json_encode($arr));
            return $arr;
        }
        return ["registro"=>true];
    }
    private function registrarCabeceraPago($val,$idCabecera=0,$idHistorial){
        //registro de cabecera de pago
        try {
            date_default_timezone_set('America/La_Paz');
            $xmfcabezerapagos = new Xmfcabezerapagos;
            $xmfcabezerapagos->nro_recibo=$val["nro_recibo"];
            $xmfcabezerapagos->correlativo=$val["correlativo"];
            $xmfcabezerapagos->usuario=$val["usuario"];
            $xmfcabezerapagos->documentoId=$val["documentoId"];
            $xmfcabezerapagos->fecha=$val["fecha"];
            $xmfcabezerapagos->hora=$val["hora"];
            $xmfcabezerapagos->monto_total=$val["monto_total"];
            $xmfcabezerapagos->tipo=$val["tipo"];
            $xmfcabezerapagos->otpp=$val["otpp"];
            $xmfcabezerapagos->tipo_cambio=$val["tipo_cambio"];
            $xmfcabezerapagos->moneda=$val["moneda"];
            $xmfcabezerapagos->cliente_carcode=$val["cliente_carcode"];
            $xmfcabezerapagos->razon_social=$val["razon_social"];
            $xmfcabezerapagos->nit=$val["nit"];
            $xmfcabezerapagos->estado=2;
            $xmfcabezerapagos->cancelado=$val["cancelado"];
            $xmfcabezerapagos->fechaSistema=date('Y-m-d h:i:s');
            $xmfcabezerapagos->equipo=$val["equipo"];
            $xmfcabezerapagos->latitud=$val["latitud"];
            $xmfcabezerapagos->longitud=$val["longitud"];
            $xmfcabezerapagos->idDocumento=$idCabecera;
            $xmfcabezerapagos->idHistorial=$idHistorial;
            $xmfcabezerapagos->save();
            if($xmfcabezerapagos->save(false)){
                $registro=true;
                $mensaje="Correcto";
                $estado=2;
                $id=$xmfcabezerapagos->id;
                Yii::error("Registro Correcto");
            }
            else{
                $registro=false;
                $data = $historialpagos->getErrors();
                $mensaje=json_encode($data);
                $estado=1;
                $id=0;
                Yii::error("Error al registrar historial pagos: ".json_encode($data));
            }
            $arr = ["respuesta"=>$registro,"id"=>$id,"estado"=>$estado,"mensaje"=>$mensaje];
            Yii::error("REGISTROS CORRECTAMENTE LA CABECERA ");
            return $arr;
        }catch (\Exception $e){
            Yii::error("Error al Registrar: ".$e->getMessage());
            $arr = ["respuesta"=>false,"id"=>0,"estado"=>0,"mensaje"=>$e->getMessage()];
            return $arr;
        }
    }
    private function registrarFacturasPago($datos,$id){
        Yii::error("REGISTRO FACTURAS PAGOS: ".json_encode($datos));
        if(!is_object($datos) ){
            if(!is_array($datos)){
                Yii::error("NO ES ARRAY FACTURAS PAGOS: NO SE REGISTRO");
                $datos=json_decode($datos,true);  
            }            
            //$datos = json_decode(json_encode($datos), FALSE);
            //return false;
        }
        if(count($datos)>0){
            Yii::error("IDCABECERA: ".$id);
            foreach ($datos as $key => $value) {
                Yii::error("REGISTRO FACTURAS PAGOS: DATOS: ".json_encode($value));
                //$value=json_encode($value);
                $xmffacturaspagos = new Xmffacturaspagos;
                $xmffacturaspagos->idCabecera=$id;
                $xmffacturaspagos->clienteId=$value['clienteId'];
                $xmffacturaspagos->nro_recibo=$value['nro_recibo'];
                $xmffacturaspagos->documentoId=$value['documentoId'];
                $xmffacturaspagos->docentry=$value['docentry'];
                $xmffacturaspagos->monto=$value['monto'];
                $xmffacturaspagos->CardName=$value['CardName'];
                $xmffacturaspagos->saldo=$value['saldo'];
                $xmffacturaspagos->nroFactura=$value['nroFactura'];
                $xmffacturaspagos->DocTotal=$value['DocTotal'];
                $xmffacturaspagos->cuota=$value['cuota'];
                $xmffacturaspagos->save(false);
            }
        }else{
            Yii::error("NO EXISTE REGISTROS DE FACTURAS PAGOS");
            return false;
        }
        return true;   
    }
    private function registrarMediosPagos($datos,$id){
        Yii::error("REGISTRO MEDIO PAGOS: ".json_encode($datos));

        if(!is_object($datos) ){
            if(!is_array($datos)){
                Yii::error("NO ES ARRAY MEDIOS PAGOS: NO SE REGISTRO");
                $datos=json_decode($datos,true);  
            }            
            //$datos = json_decode(json_encode($datos), FALSE);
            //return false;
        }
        if(count($datos)>0){
            foreach ($datos as $key => $value) {
                Yii::error("REGISTRO MEDIO PAGOS DATOS: ".json_encode($value));
                //$value=json_encode($value);
                $xmfmediospagos = new Xmfmediospagos;
                $xmfmediospagos->idCabecera=$id;
                $xmfmediospagos->nro_recibo=$value['nro_recibo'];
                $xmfmediospagos->documentoId=$value['documentoId'];
                $xmfmediospagos->formaPago=$value['formaPago'];
                $xmfmediospagos->monto=$value['monto'];
                $xmfmediospagos->numCheque=$value['numCheque'];
                $xmfmediospagos->numComprobante=$value['numComprobante'];
                $xmfmediospagos->numTarjeta=$value['numTarjeta'];
                $xmfmediospagos->bancoCode=$value['bancoCode'];
                $xmfmediospagos->fecha=$value['fecha'];
                $xmfmediospagos->cambio=$value['cambio'];
                $xmfmediospagos->monedaDolar=$value['monedaDolar'];
                $xmfmediospagos->monedaLocal=$value['monedaLocal'];
                $xmfmediospagos->centro=$value['centro'];
                $xmfmediospagos->baucher=$value['baucher'];
                $xmfmediospagos->checkdate=$value['checkdate'];
                $xmfmediospagos->transferencedate=$value['transferencedate'];
                $xmfmediospagos->CreditCard=$value['CreditCard'];
                $xmfmediospagos->save(false);
            }
        }else{
            Yii::error("NO EXISTE REGISTROS DE MEDIOS PAGOS");
            return false;
        }
        return true;
    }
}
