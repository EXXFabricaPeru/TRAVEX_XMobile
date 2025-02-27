<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
use backend\models\Contactos;
use backend\models\Clientessucursales;
//use backend\models\Clientes as climodel;
use backend\models\v2\Sapenviodoc ;
use backend\models\v2\Common;


//\app\models\Clientes
class Clientes extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    // todos los Clientes (POS)
    public function obtenerTodosClientes($usuario,$texto){
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaClientes($usuario);        
        $from=' from "OCRD" C ';
        $from.=' left join (Select Count(*)as "NumFVencidas","CardCode" from "OINV" where "DocDueDate"< CURRENT_DATE group by "CardCode")D on C."CardCode"=D."CardCode"';
        $from.=' left join "OCRG" CG on C."GroupCode"=CG."GroupCode"';
        $from.=' left join "OCTG" CCP on C."GroupNum"=CCP."GroupNum"';
        $from.=' left join "OTER" CT on C."Territory"=CT."territryID"';
        $where=$this->obtenerCondicionConsulta($usuario,1,$texto);
        $order='order by C."CardCode"';        
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ";
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    // funciones Maestro de Clientes
    public function obtenerClientes($usuario,$salto=0,$limite=1000){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaClientes($usuario);        
        $from=' from "OCRD" C ';
        $from.=' left join (Select Count(*)as "NumFVencidas","CardCode" from "OINV" where "DocDueDate"< CURRENT_DATE and (("DocTotal"-"PaidToDate")>0) group by "CardCode")D on C."CardCode"=D."CardCode"';
        $from.=' left join "OCRG" CG on C."GroupCode"=CG."GroupCode"';
        $from.=' left join "OCTG" CCP on C."GroupNum"=CCP."GroupNum"';
        $from.=' left join "OTER" CT on C."Territory"=CT."territryID"';
        $where=$this->obtenerCondicionConsulta($usuario);
        $order='order by C."CardCode"';
        $limite=" limit {$limite} OFFSET {$salto}";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    private function obtenerVariablesSTD(){
            $cadena="";
            $sql = "SELECT * FROM configuracion WHERE parametro LIKE 'cliente_std%' ORDER BY parametro";
            $parametrosClientes = Yii::$app->db->createCommand($sql)->queryAll();
            $cantidadCliente = count($parametrosClientes);

            if (count($cantidadCliente)){
                for ($c = 0; $c < $cantidadCliente; $c++){
                    if($parametrosClientes[$c]["estado"]==1){
                        $cadena.= ',C."'.$parametrosClientes[$c]["valor2"].'" as "'.$parametrosClientes[$c]["parametro"].'"';
                    }else{
                        $cadena.= ',0 as "'.$parametrosClientes[$c]["parametro"].'"';
                    }
                        
                }
            }
            return $cadena;
    }
    private function obtenercamposUsuario($tabla){
        Yii::error("campos de usuario");
        $cadena="";
        $sql_camposaux="Select * from vi_camposusuarios where tabla='".$tabla."'";
        Yii::error($sql_camposaux);
        $campos_aux=Yii::$app->db->createCommand($sql_camposaux)->queryAll();
        $cantidadcampos = count($campos_aux);
        if (count($cantidadcampos)){
            for ($c = 0; $c < $cantidadcampos; $c++){   
                switch ($tabla){
                    case "clientes": 
                        $cadena.= ',C."'.$campos_aux[$c]["Campo_Sap2"].'" as "'.$campos_aux[$c]["nombre"].'"';                 
                        break;
                    case "clientessucursales": 
                        $cadena.= ',S."'.$campos_aux[$c]["Campo_Sap"].'" as "'.$campos_aux[$c]["nombre"].'"';                 
                        break;
                }   
                    
            }
        }
        Yii::error($cadena);
        return $cadena;
    }
    private function obtenerCamposConsultaClientes($usuario){
        $aux_variablesStd=$this->obtenerVariablesSTD();
        $configuracion_tipo_vendedor_sql="Select * from sys_vi_cnf_usuario where id=".$usuario;
        $configuracion_tipo_vendedor = Yii::$app->db->createCommand($configuracion_tipo_vendedor_sql)->queryOne();
        $aux_cnf_canales=$configuracion_tipo_vendedor["canales"];
        $aux_cnf_fex=0;//$configuracion_tipo_vendedor["FEX"];
        $aux_campos_canales='';
        $aux_campos_fex='';
        if($aux_cnf_canales==1){
            $aux_campos_canales='
             C."U_XM_Canal" as "codeCanal",
             C."U_XM_Subcanal" as "codeSubCanal",
             C."U_XM_TipoTienda" as "codeTipoTienda",
             C."U_XM_Cadena" as "cadena",
             C."U_XM_CadenaDesc" as "cadenatxt",
             C."ChannlBP" as "cadenaconsolidador",
            ';
        }else{
            $aux_campos_canales='
            \'\' as "codeCanal",
            \'\' as "codeSubCanal",
            \'\' as "codeTipoTienda",
            \'\' as "cadena",
            \'\' as "cadenatxt",
            \'\' as "cadenaconsolidador",
            ';
        }
        if($aux_cnf_fex==1){
            $aux_campos_fex='
            C."U_EXX_FE_CodDocIden" as "Fex_tipodocumento",          
            C."U_EXX_FE_Complem" as "Fex_complemento",
            C."U_EXX_FE_CodExcep" as "Fex_codigoexcepcion"
            ';
        }else{
            $aux_campos_fex='
            \'\' as "Fex_tipodocumento",
            \'\' as "Fex_complemento",
            \'\' as "Fex_codigoexcepcion"
            ';
        }
        $sql_campos_usuario=$this->obtenercamposUsuario("clientes");
        $hoy=Date('Y-m-d');
        $caracterold = "'&'";
        $caracternew = "'Y'";
        // REPLACE(C."CardName", '.$caracterold.','.$caracternew.') "CardName",

        $sql='
            C."CardCode",
            C."CardName",
            C."CreditLine" as "CreditLimit",
            C."DebtLine" as "MaxCommitment",
            C."Discount" as "DiscountPercent",
            C."ListNum" as "PriceListNum",
            C."SlpCode" as "SalesPersonCode",
            C."Currency",
            C."County",
            C."Country",
            C."Balance" as "CurrentAccountBalance",
            \'0\' as "NoDiscounts",
            C."PriceMode",            
            C."LicTradNum" as "FederalTaxId",
            C."Phone1",
            \'0\' as "ContactPerson",
            C."GroupNum" as "PayTermsGrpCode",
            C."GroupNum" as "cndpago",
            C."GroupCode",
            \'0\' as "BPAddresses",
            C."Territory",
            C."CardType",
            \'0\' as "DiscountRelations",   
            C."Phone1" as "PhoneNumber",
            C."Phone2" as "celular",
            \'0\' as "ContactPerson",
            C."MailAddres" as "Address",
            C."E_Mail" as  "correoelectronico",
            C."Address",      
            C."CardName" as "razonsocial",
            C."QryGroup1" as "lunes",
            C."QryGroup2" as "martes",
            C."QryGroup3" as "miercoles",
            C."QryGroup4" as "jueves",
            C."QryGroup5" as "viernes",
            C."QryGroup6" as "sabado",
            C."QryGroup7" as "domingo",
            C."Cellular",
            C."Cellular" as "personacontactocelular",
            \'0\' as "ContactEmployees",
            C."U_XM_Longitud" as "Longitude",
            C."U_XM_Latitud" as "Latitude", 
            \'\' as "Mobilecod",
            \'\' as "cadenaCcus",        

            CASE 
            when C."validFor"=\'Y\' then 
                case 
                when CURRENT_DATE between to_date(C."validFrom") and to_date(C."validTo") then \'Y\'
                when C."validFrom" is null then \'Y\'
                when C."validTo" is null then \'Y\'
                else \'N\'
                end
            when C."frozenFor"=\'Y\' then 
                case 
                when CURRENT_DATE between to_date(C."frozenFrom") and to_date(C."frozenTo") then \'N\'
                when C."frozenFrom" is null then \'N\'
                when C."frozenTo" is null then \'N\'
                else \'Y\'
            end
            else \'Y\'  
            
            end as "activo",
                       
            CASE 
                when D."NumFVencidas">=0 then  D."NumFVencidas" 
                when D."NumFVencidas" is null  then  0
                else 0
            end as "FVencidas",
            \'1\' as "User",
            \'1\' as "Status", 
            \''.$hoy.'\' as "DateUpdate",
            0 AS "grupoSIN", 
            0 AS "iva", 
            0 AS "DescuentoG", 
            0 AS "DescuentoC", 
            0 AS "DescuentoCC",
            0 AS "DescuentoA", 
            CG."GroupName",
            CCP."PymntGroup" as "cndpagoname",
            0 as "anticipos",
            CT."descript" as "Description",
            U_EXX_TIPOPERS,
            U_EXX_TIPODOCU,
            U_EXX_APELLPAT,
            U_EXX_APELLMAT,
            U_EXX_PRIMERNO,
            U_EXX_SEGUNDNO,
        ';
        $sql.= $aux_campos_canales;
        $sql.= $aux_campos_fex;
        $sql.=$aux_variablesStd;
        $sql.=$sql_campos_usuario;
        $sql.=',
        C."IndustryC" as "tipoEmpresa",
        C."Free_Text" as "comentario"';
        return $sql;
    }
    private function obtenerCondicionConsulta($usuario,$pos=0,$texto=''){
        $configuracion_tipo_vendedor_sql="Select * from sys_vi_cnf_usuario where id=".$usuario;
        $configuracion_tipo_vendedor = Yii::$app->db->createCommand($configuracion_tipo_vendedor_sql)->queryOne();
        $aux_cnf_tv=$configuracion_tipo_vendedor["cnfvendedor"];
        $cadena=" ";
        if($pos==1){
            $cadena=' where C."CardType"=\'C\' ';
        }else{
            switch ($aux_cnf_tv){
                case 0:
                    $aux_cnf_slp=$configuracion_tipo_vendedor["codEmpleadoVenta"];
                    $cadena=' where (TO_VARCHAR(C."SlpCode") = \''.$aux_cnf_slp.'\' OR \'\' = \''.$aux_cnf_slp.'\' ) AND C."CardType"=\'C\' ';
                break;
                case 1: 
                    $sql_aux_cnf_territorios="select GROUP_CONCAT(idTerritorio) as territorios from usuariomovilterritoriodetalle where idUserMovil=".$usuario." group by idUserMovil" ;
                    $aux_cnf_territorios = Yii::$app->db->createCommand($sql_aux_cnf_territorios)->queryOne();
                    $aux_cnfu_trr=$aux_cnf_territorios["territorios"];             
                    $cadena=' where C."Territory" in ('.$aux_cnfu_trr.') and  C."CardType"=\'C\' ';
                break;
                case 2:
                    $cadena=' left join ';
                    $cadena.=' where C."Territory" =\'C\' and  C."CardType"=\'C\' ';
                break;
                case 3:
                    $cadena=' where C."Territory" =\'C\' and  C."CardType"=\'C\' ';
                break;
    
            }
        }
        if($texto!=''){
            $cadena.= ' and  (UPPER (C."CardCode") like \'%'.strtoupper($texto).'%\' or UPPER (C."CardName") like \'%'.strtoupper($texto).'%\' or UPPER (C."LicTradNum") like \'%'.strtoupper($texto).'%\' )'; 
        }
        return $cadena;

    }
    public function obtenerClientesContador($usuario){        
        $sql=" Select ";
        $campos=' Count(*) as "contador"';        
        $from=" from \"OCRD\" C";
        $where=$this->obtenerCondicionConsulta($usuario);
        $sql_hana=$sql." ". $campos." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return $resultado;
    }

    // funciones Sucursales de Clientes
    public function obtenerSucursalesTodos($usuario,$texto){
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaSucursales($usuario);        
        $from=' from "CRD1" S ';
        $from.=' left join "OCRD" C on S."CardCode"=C."CardCode"';
        $where=$this->obtenerCondicionConsulta($usuario,1,$texto);
        $order='order by S."CardCode" ';        
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerSucursales($usuario,$salto=0,$limite=1000){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaSucursales($usuario);        
        $from=' from "CRD1" S ';
        $from.=' left join "OCRD" C on S."CardCode"=C."CardCode"';
        $where=$this->obtenerCondicionConsulta($usuario);
        $order='order by S."CardCode" ';
        $limite=" limit {$limite} OFFSET {$salto}";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    private function obtenerCamposConsultaSucursales($usuario){
        $configuracion_tipo_vendedor_sql="Select * from sys_vi_cnf_usuario where id=".$usuario;
        $configuracion_tipo_vendedor = Yii::$app->db->createCommand($configuracion_tipo_vendedor_sql)->queryOne();
        $aux_cnf_tv=$configuracion_tipo_vendedor["cnfvendedor"];
        $sqlaux="";
        if($aux_cnf_tv==4){
            $sqlaux='
            ,S."U_Territorio" as "u_territorio",
            S."U_XM_Latitud" as "u_lat",
            S."U_XM_Longitud" as "u_lon",
            S."U_Zona" as "u_zona",
            S."U_Vendedor" as "u_vendedor"';
        }
        $sql_campos_usuario=$this->obtenercamposUsuario("clientessucursales");
        $hoy=Date('Y-m-d');
        $sql='
            S."LineNum" as "RowNum",
            S."Block",
            S."Address" AS "AddresName",
            S."Street",
            S."Country",
            S."City",
            S."State",
            S."LineNum" AS "Code",
            S."LicTradNum" AS "FederalTaxID",
            \'0\' AS "CreditLimit",
            S."TaxCode",
            S."CardCode",
            S."UserSign",
            S."AdresType",            
            S."TaxCode" as "Tax",
            1 as "User",
            1 as "Status", 
            \''.$hoy.'\' as "DateUpdate"  

        ';
        $sql= $sql.$sqlaux.$sql_campos_usuario;
        return $sql;
    }
    public function obtenerSucursalesContador($usuario){        
        $sql=' Select  Count(*) as "contador" ';              
        $from=' from "CRD1" S ';
        $from.=' left join "OCRD" C on S."CardCode"=C."CardCode"';
        $where=$this->obtenerCondicionConsulta($usuario);       
        $sql_hana=$sql." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return $resultado;
    }

    // funciones Contactos de Clientes
    public function obtenerContactosTodos($usuario,$texto){
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaContactos();        
        $from=' from "OCPR" CC ';
        $from.=' left join "OCRD" C on CC."CardCode"=C."CardCode"';
        $where=$this->obtenerCondicionConsulta($usuario,1,$texto);
        $order='order by CC."CardCode" ';        
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerContactos($usuario,$salto=0,$limite=1000){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaContactos();        
        $from=' from "OCPR" CC ';
        $from.=' left join "OCRD" C on CC."CardCode"=C."CardCode"';
        $where=$this->obtenerCondicionConsulta($usuario);
        $order='order by CC."CardCode" ';
        $limite=" limit {$limite} OFFSET {$salto}";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    private function obtenerCamposConsultaContactos(){
        $hoy=Date('Y-m-d');
        $sql='
            CC."Name" as "nombre",
            CC."CardCode" as "cardCode", 
            CC."Address",
            CC."Tel1" AS "telefono",
            CC."Tel2" AS "Phone2",
            CC."Cellolar" AS "MobilePhone",
            CC."Title" as "titulo",
            CC."UserSign" AS "User",
            CC."Notes1" AS "comentario",
            CC."E_MailL" AS "correo",
            CC."CntctCode" AS "InternalCode",
            CC."CntctCode",
            \''.$hoy.'\' as "fecha"            
        ';
        return $sql;
    }
    public function obtenerContactosContador($usuario){        
        $sql=' Select  Count(*) as "contador" ';              
        $from=' from "OCPR" CC ';
        $from.=' left join "OCRD" C on CC."CardCode"=C."CardCode"';
        $where=$this->obtenerCondicionConsulta($usuario);       
        $sql_hana=$sql." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return $resultado;
    }

    //validaciones
    public function validateClientByNitOrCardCode($CardCode,$rut,$idUser){
        $dataValidNit=Clientes::obtenerConfig('nits_duplicados');
        $dataValidCardCode=Clientes::obtenerConfig('s_cliente');
        Yii::error("data response".json_encode($dataValidNit));
        Yii::error("data response2".$dataValidNit["valor"]);

        $response=array(
                "serie"=>0,
                "rut"=>0,
                "nuevaserie"=>0,
                "mensaje"=>"Validacion Correcta"
        );
          $from='select *  from "OCRD" C ';       
           $where="";
        if($dataValidNit && $dataValidNit["valor"]==0){
            //se valida el nit
             Yii::error("validando nit");
           $where=" where C.\"LicTradNum\"='$rut' ;";
           $sqlHana=$from."".$where;
           Yii::error("consulta=".$sqlHana);
           $resultado= $this->hana->ejecutarconsultaOne($sqlHana);
           Yii::error("data response".json_encode($resultado));

           if($resultado){
            $response["rut"]=1;
            $response["mensaje"]="Nit existente";
          
           }

        }
        if($dataValidCardCode && $dataValidCardCode["valor"]==0){
            //se valida el nit
           $where="";
           $where.=" where C.\"CardCode\"='$CardCode'";
            $sqlHana=$from."".$where;
             Yii::error("consulta=".$sqlHana);
            $resultado=  $this->hana->ejecutarconsultaOne($sqlHana);
            Yii::error("data response".json_encode($resultado));
          

           if($resultado){
            //validadndo nuevo carcode
            $sqlNumeracion="UPDATE numeracion set numcli=numcli+1 where iduser='$idUser'"; 
            Yii::$app->db->createCommand($sqlNumeracion)->queryOne(); 
            Yii::error("actualizando numeracion del cliente",$sqlNumeracion);
            $sql = "SELECT * FROM numeracion WHERE iduser='$idUser' ";
             $dataNumeracion= Yii::$app->db->createCommand($sql)->queryOne();

             $response["serie"]=1;
            $response["nuevaserie"]=$dataNumeracion['numcli'];
            $response["mensaje"]="Serie repetida";
            

           }

        }
        return $response; 
    }
    public function obtenerConfig($campo){
            $sql = "SELECT * FROM configuracion WHERE parametro='$campo' ";
            return Yii::$app->db->createCommand($sql)->queryOne();
    }
    // Consultas adicionales
    public function obtenerCamposEspecificos($campos,$where){        
        $sql=" Select ";       
        $from=" from \"OCRD\" ";
        $sql_hana=$sql." ". $campos." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return  $resultado;
    }

    // obtener clientes por id territorio
    public function obtenerClientesPorTerritorio($condicionTerritorio){
        $sql=" Select ";
        $campos='C."CardCode",C."CardName",C."U_XM_Latitud" as "latitud", C."U_XM_Longitud" as "longitud", C."Territory" as "territorio", C."Address" as "direccion"';        
        $from=' from "OCRD" C ';
        $where=' WHERE '.$condicionTerritorio;
        $order='order by C."CardCode"';        
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ";
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }

    /*public function obtenerCamposEspecificos($campos,$where){        
        $sql=" Select ";       
        $from=" from \"OCRD\" ";
        $sql_hana=$sql." ". $campos." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return  $resultado;
    }*/
    public function obtenerCamposEspecificosLista($campos,$where){        
        $sql=" Select ";       
        $from=" from \"OCRD\" C ";
        $sql_hana=$sql." ". $campos." ".$from." ".$where;
        Yii::error("CONSULTA CLIENTES: ".$sql_hana);
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return  $resultado;
    }

    /**
    *Creacion de cliente data object maetro cliente , contacto y sucursal
    *
    */
    public function createClient($cliente){
        $cnf_fex="";
        $cnf_canalVenta="";
        $cnf_clienteLocal="";

        $configuracion = Yii::$app->db->createCommand("select parametro,valor from configuracion where estado=1")->queryAll();   
        $response = array("estado"=>0, "mensaje"=>"No se pudo registrar");        
        foreach ($configuracion as $key => $value) {
            if($value['parametro']=='FEX') $cnf_fex=$value['valor'];
            if($value['parametro']=='CanalVenta') $cnf_canalVenta=$value['valor'];
            if($value['parametro']=='ClientesLocalizacion') $cnf_clienteLocal=$value['valor'];
        }

         $cnf_usuario=["cnf_fex"=>$cnf_fex,"cnf_canalVenta"=>$cnf_canalVenta,"cnf_clienteLocal"=>$cnf_clienteLocal];
         $dataClienteMaestro="";
         try {

            //insertando datos maestros del cliente
             $dataClienteMaestro=Clientes::insertClinetes($cliente);
             if($dataClienteMaestro->id){
                //ACTUALIZA CAMPOS PERSONALIZADO
                 $datosClient= Clientes::actualizaCampos($cliente,$cnf_usuario,$dataClienteMaestro->id);
                 //insertando datos de contactos
                 yii::error("idnuevo usuario: ".$dataClienteMaestro->id);
                 $dataContacto=Clientes::insertContactos($cliente,$cliente["ContactPerson"],$dataClienteMaestro->id);
                 yii::error("return new contacto: ".$dataClienteMaestro->id);
                 //insertando datos de sucursales
                 $dataSucursal=Clientes::insertSucursales($cliente,$cliente["SucursalesCliente"],$dataClienteMaestro->id);
                //actualizando campos de usuario dinamicos
                  $commonModel=new Common;
                  $dResUpCd=$commonModel->updateCampoDinamicos($cliente["camposusuario"],array("campo"=>'id',"value"=>$dataClienteMaestro->id));
                  $modelCliente=new \backend\models\Clientes;
                  $modelCliente=$modelCliente->findOne($dataClienteMaestro->id);
                    try {
                         $sapenvioDoc=new Sapenviodoc;
                         $respuesta=$sapenvioDoc->exportSapcliente($cnf_usuario,$dataClienteMaestro->id);
                         yii::error("response cardCode".$respuesta->CardCode);
                        if($respuesta && $respuesta->CardCode){
                            $modelCliente->Status=3;  
                            $modelCliente->save(false); 
                            $response = array("estado"=>3, "mensaje"=>"Registro exitoso", "data"=>$respuesta); 
                                $idUser=$dataClienteMaestro->User;
                                $sqlNumeracion="UPDATE numeracion set numcli=numcli+1 where iduser='$idUser'"; 
                                Yii::$app->db->createCommand($sqlNumeracion)->execute(); 
                                yii::error("data quwery".$sqlNumeracion);
                                $dataContactos=$respuesta->ContactEmployees;
                                $dataSucursales=$respuesta->BPAddresses;
                                $auxIdCliente=$dataClienteMaestro->id;
                                foreach ($dataContactos as $contact ) {
                                 $sqlAuxContacto="UPDATE contactos set InternalCode=".$contact->InternalCode." where UPPER(nombre)=UPPER('".$contact->Name."')  and idCliente= $auxIdCliente and InternalCode is null"; 
                                yii::error("Listo para actualizar el internal code : ".$sqlAuxContacto);

                                Yii::$app->db->createCommand($sqlAuxContacto)->execute();  
                                }
                                foreach ($dataSucursales as $sucursal ) {
                                    if($sucursal->AddressType=='bo_BillTo'){
                                            $auxadressType='B';
                                    }else{
                                        $auxadressType='S';

                                    }
                                  $sqlAuxSucursales="UPDATE clientessucursales set RowNum=".$sucursal->RowNum." where AddresName='".$sucursal->AddressName."' and AdresType='".$auxadressType."'  and idCliente =$auxIdCliente"; 
                                  yii::error("Listo para actualizar el internal code : ".$sqlAuxSucursales);
                                  Yii::$app->db->createCommand($sqlAuxSucursales)->execute();  
                                }

                                
                        }else{
                            $response = array("estado"=>0, "mensaje"=>$respuesta); 
                             $modelCliente->Status=2;  
                                $modelCliente->save(false);      

                        }
                    } catch (Exception $e) {
                          $modelCliente->Status=2;  
                          $modelCliente->save(false);  
                          yii::error("error al enviar a sap".json_encode($respuesta)); 
                       $response = array("estado"=>2, "mensaje"=>"Error al momento de enviar a sap");      
                    }
               }else{
                    $response = array("estado"=>1, "mensaje"=>"No se pudo registrar el maestro de negocio");
               }
            

         } catch (Exception $e) {
            Yii::error("error al registrar en la base local en algun lado".json_encode($e));
            if($dataClienteMaestro && $dataClienteMaestro['id'] ){
                $modelCliente= new \backend\models\Clientes;
                $modelCliente= $modelCliente::findOne(($dataClienteMaestro->id?$dataClienteMaestro->id:null));
                
                $modelCliente=$modelCliente->findOne($dataClienteMaestro->id);
                $modelCliente->Status=1;  
                $modelCliente->save(false); 
            }
             $response = array("estado"=>1, "mensaje"=>"Error al realizar los inserts en los objetos");
           
         }
         return $response;
       
    }

    private function insertClinetes($cliente){
      Yii::error("Listo para registrar cliente".json_encode($cliente));
        if($cliente["Longitude"]==""){
            $cliente["Longitude"]=$cliente["SucursalPerson"][0]->u_lon;
            $cliente["Latitude"]=$cliente["SucursalPerson"][0]->u_lat;
        }
           // Yii::error("Parametros de configuracion: cnf_fex= ".$cnf_usuario['cnf_fex']." - cnf_canalVenta= ".$cnf_usuario['cnf_canalVenta']." - cnf_clienteLocal=".$cnf_usuario['cnf_clienteLocal']);
            $registro = new \backend\models\Clientes;
            //$registro->id = $cliente["id"];
            $registro->CardCode = $cliente["CardCode"];
            $registro->Mobilecod = $cliente["Mobilecod"];
            $registro->CardName =strtoupper($cliente["CardName"]);
            $registro->FederalTaxId = $cliente["FederalTaxId"];
            $registro->Address = $cliente["Address"];
            $registro->PhoneNumber = $cliente["PhoneNumber"];
            $registro->PriceListNum = $cliente["PriceListNum"];
            $registro->ContactPerson = $cliente["ContactPerson"];
           
            // $registro->img = "sin imagen";
            $registro->Latitude = $cliente["Latitude"];
            $registro->Longitude = $cliente["Longitude"];
            $registro->CardType = $cliente["CardType"];
            $registro->CreditLimit = $cliente["CreditLimit"];
            $registro->MaxCommitment = $cliente["MaxCommitment"];
            $registro->DiscountPercent = $cliente["DiscountPercent"];
            $registro->SalesPersonCode = $cliente["SalesPersonCode"];
            $registro->Currency = $cliente["Currency"];
            $registro->County = $cliente["County"];
            $registro->Country = $cliente["Country"];
            $registro->CurrentAccountBalance = $cliente["CurrentAccountBalance"];
            $registro->NoDiscounts = $cliente["NoDiscounts"];
            $registro->PriceMode = $cliente["PriceMode"];
            $registro->PayTermsGrpCode = $cliente["PayTermsGrpCode"];
            $registro->GroupCode = $cliente["GroupCode"];
            $registro->User = $cliente["idUser"];
            $registro->Status = 0;
            $registro->DateUpdate = $cliente["DateUpdate"];
            $registro->Phone2 = $cliente["celular"];
            $registro->Cellular = $cliente["pesonacontactocelular"];
            $registro->EmailAddress = $cliente["correoelectronico"];
            $registro->FreeText = $cliente["comentario"];
            $registro->CardForeignName = $cliente["razonsocial"];
            $registro->Mobilecod = $cliente["CardCode"];
            $diasVisita = explode(',', $cliente["diavisita"]);
            $registro->Properties1 = 'tNO';
            $registro->Properties2 = 'tNO';
            $registro->Properties3 = 'tNO';
            $registro->Properties4 = 'tNO';
            $registro->Properties5 = 'tNO';
            $registro->Properties6 = 'tNO';
            $registro->Properties7 = 'tNO';

            //Campos nuevos
            $registro->U_EXX_TIPOPERS = $cliente["U_EXX_TIPOPERS"];
            $registro->U_EXX_TIPODOCU = $cliente["U_EXX_TIPODOCU"];
            $registro->U_EXX_APELLPAT = $cliente["U_EXX_APELLPAT"];
            $registro->U_EXX_APELLMAT = $cliente["U_EXX_APELLMAT"];
            $registro->U_EXX_PRIMERNO = $cliente["U_EXX_PRIMERNO"];
            $registro->U_EXX_SEGUNDNO = $cliente["U_EXX_SEGUNDNO"];

            //$registro->Fex_tipodocumento=$cliente["Fex_tipodocumento"];
           // $registro->Fex_complemento=$cliente["Fex_complemento"];
           // $registro->Fex_codigoexcepcion=$cliente["Fex_codigoexcepcion"];

            foreach ($diasVisita as $diaVisita) {
                switch ($diaVisita) {
                    case 'Lunes':
                        $registro->Properties1 = 'tYES';
                        break;
                    case 'Martes':
                        $registro->Properties2 = 'tYES';
                        break;
                    case 'Miercoles':
                        $registro->Properties3 = 'tYES';
                        break;
                    case 'Jueves':
                        $registro->Properties4 = 'tYES';
                        break;
                    case 'Viernes':
                        $registro->Properties5 = 'tYES';
                        break;
                    case 'Sabado':
                        $registro->Properties6 = 'tYES';
                        break;
                    case 'Domingo':
                        $registro->Properties7 = 'tYES';
                        break;
                }
            }
            
            //$registro->Territory = $cliente["rutaterritorisap"];
            $registro->Territory = $cliente["territorio"];

            //$registro->Territory = $cliente["SucursalPerson"][0]["u_territorio"];
            if ($cliente["tipoEmpresa"] == '' || $cliente["tipoEmpresa"] == 'undefined' || $cliente["tipoEmpresa"] == '0') {
                $registro->Industry = '-1';
            }else {
                $registro->Industry = $cliente["tipoEmpresa"];
            }

            $fileName="addPhoto.svg";
            if($registro->img && $registro->img!=''){
              
               // $decodificado = base64_decode($registro->img); 
               preg_match("/data:image\/(.*?);/",$registro->img,$image_extension); // extract the image extension
               $image = preg_replace('/data:image\/(.*?);base64,/','',$registro->img); // remove the type part
               $image = str_replace(' ', '+', $image);
               $fileName = $registro->CardCode.'-'.time()."-".uniqid().".jpg"; 
               Yii::error("decode img ->".$image); 
               Yii::error("decode img -> 2".json_encode($registro->img)); 
             
                file_put_contents ("./imgs/cli/".$fileName, base64_decode($image));
            }
           $registro->img = $fileName;
           $registro->save(false);
           
        return $registro;
    }
    
    private function insertContactos($clientes,$clienteContactos,$clienteId){
        $contactos = [];
        if (!empty($clienteContactos)) {
            Yii::error("CONTACTOS CLIENTES: ". json_encode($clienteContactos));

            foreach ($clienteContactos as $contacto) {
                /*$responseContacto = Yii::$app->db->createCommand("CALL pa_SincronizarContactos(:cardcode,:nombre,'',:telefono,'','','',:comentario,:id,:correo,:titulo,:Mobilecode)", [
                    ":cardcode" => $contacto["cardCode"],
                    ":nombre" => $contacto["nombre"],
                    ":telefono" => $contacto["telefono"],
                    ":comentario" => $contacto["comentario"],
                    ":id" => $clientes["idUser"],
                    ":correo" => $contacto["correo"],
                    ":titulo" => $contacto["titulo"],
                    ":Mobilecode"=>$clientes['Mobilecod']
                ])->execute();*/
                Yii::error("listo para crear objeto : ".json_encode($contacto) );

                 $responseContacto= new Contactos;
                 $responseContacto->cardCode=$contacto["cardCode"];
                 $responseContacto->nombre=$contacto["nombrePersonaContacto"];
                 $responseContacto->direccion='';
                 $responseContacto->telefono1=$contacto["fonoPersonaContacto"];
                 $responseContacto->telefono2=$contacto["fonoPersonaContacto"];
                 $responseContacto->celular='';
                 $responseContacto->tipo='';
                 $responseContacto->comentarios=$contacto["comentarioPersonaContacto"];
                 $responseContacto->Mobilecode=$contacto['cardCode'];
                 $responseContacto->titulo=$contacto["tituloPersonaContacto"];
                 $responseContacto->correo=$contacto["correoPersonaContacto"];
                 $responseContacto->User=$clientes["idUser"];
                 $responseContacto->idsap=null;
                 $responseContacto->InternalCode=null;
                 $responseContacto->Status=1;
                 $responseContacto->idCliente=$clienteId;

                 //$responseContacto->DateUpdate
                
               /* $aux1 = $contacto["nombre"];
                $aux2 = $clientes['Mobilecod'];
                $RowNum = "";
                $serie = Yii::$app->db->createCommand("SELECT InternalCode FROM contactos WHERE Nombre = '$aux1' and Mobilecode = '$aux2' ")->queryOne();
                $RowNum = $serie['InternalCode'];*/
                $responseContacto->save(false);
                $nombreContacto = explode(" ", $contacto["nombre"]);
                Yii::error("InternalCode : ".$responseContacto->InternalCode );
                
               
                  $contactoObjeto = [
                        "InternalCode"=> 0,
                        "CardCode" => $contacto["cardCode"],
                        "Name" =>  $contacto["nombrePersonaContacto"],
                        "FirstName" => $nombreContacto[0],
                        "LastName" => $nombreContacto[1],
                        "Phone1" => $contacto["fonoPersonaContacto"],
                        "Remarks1" => $contacto["comentarioPersonaContacto"],
                        "Title" =>substr($contacto["tituloPersonaContacto"], 0, 9),
                        "E_Mail" => $contacto["correoPersonaContacto"],
                        "Active" => "tYES"
                     ];
                array_push($contactos, $contactoObjeto);
              
                Yii::error(json_encode($contactoObjeto));
            }
        }
        return $contactos;
    }

    private function insertSucursales($clientes,$clienteSucursal,$clienteId){
        $sucursales = [];
        if (!empty($clienteSucursal)) {
            foreach ($clienteSucursal as $sucursal) {
                Yii::error("response database sucursal->" . json_encode($sucursal));
                Yii::error("response database sucursal->" . $sucursal["LineNum"]);

             //  {"idUser":"70","AddresName":"sub prueba","Street":"27, Las Terrazas 090107, Ecuador","LineNum":0,"State":0,"FederalTaxId":0,"CreditLimit":0,"CardCode":"1007000013","User":"70","Status":1,"DateUpdate":"","idDocumento":0,"TaxCode":"","AdresType":"B","u_zona":"","u_lat":-2.2426713,"u_lon":-79.8936767,"u_territorio":"-2","u_vendedor":"35","labelTerritorio":"Bolivia"}
                $sucursalData=new Clientessucursales;
                $sucursalData->Status=$sucursal["Status"];
                $sucursalData->User=$sucursal["User"];
                $sucursalData->idupdate=null;
                $sucursalData->RowNum=$sucursal["LineNum"];
              //  $sucursalData->DateUpdate
                $sucursalData->AddresName=$sucursal["AddresName"];
                $sucursalData->Street=$sucursal["Street"];
                $sucursalData->State=$sucursal["State"];
                $sucursalData->FederalTaxId=$sucursal["FederalTaxId"];
                $sucursalData->CreditLimit=$clientes["CreditLimit"];
                $sucursalData->CardCode=$sucursal["CardCode"];
                $sucursalData->TaxCode=$sucursal["TaxCode"];
                $sucursalData->AdresType=$sucursal["AdresType"];
                $sucursalData->u_zona=$sucursal["u_zona"]?$sucursal["u_zona"]:'';
                $sucursalData->u_lat=$sucursal["u_lat"]?$sucursal["u_lat"]:'';
                $sucursalData->u_lon=$sucursal["u_lon"]?$sucursal["u_lon"]:'';
                $sucursalData->u_territorio=$sucursal["u_territorio"]?$sucursal["u_territorio"]:'';
                $sucursalData->u_vendedor=$sucursal["u_vendedor"];
                $sucursalData->idCliente=$clienteId;
                $sucursalData->Mobilecode=$sucursal['CardCode'];
                //$sucursalData->Mobilecode=$clientes['Mobilecod'];
                //$sucursalData->CAMPOUSER1=$clientes['CAMPOUSER1'];
                //$sucursalData->CAMPOUSER2=$clientes['CAMPOUSER2'];
                //$sucursalData->CAMPOUSER3=$clientes['CAMPOUSER3'];
                //$sucursalData->CAMPOUSER4=$clientes['CAMPOUSER4'];
                //$sucursalData->CAMPOUSER5=$clientes['CAMPOUSER5'];
                //{"RowNum":null,"BPCode":"1007000013","AddressName":"sub prueba","AddressName2":"27, Las Terrazas 090107, Ecuador","Street":"27, Las Terrazas 090107, Ecuador","AddressType":"bo_BillTo","City":""}
                Yii::error("antes de guardar la  sucursal->".json_encode($sucursalData->CardCode));
                $sucursalData->save(false);
                $commonModel=new Common;
                //  $dResUpCd=$commonModel->updateCampoDinamicos($sucursal["camposusuario"],array("campo"=>'id',"value"=>$sucursalData->id));
                //  $nombresucursal = explode(" ", $sucursal["nombre"]);
                
                $aux1 = $sucursal["AddresName"];
                $aux2 = $clientes['CardCode'];
                $aux3 = $sucursal["AdresType"];
                $RowNum = "";
                $serie = Yii::$app->db->createCommand("SELECT RowNum FROM clientessucursales WHERE AddresName = '$aux1' and Mobilecode = '$aux2' and AdresType = '$aux3'  ")
                ->queryOne();
                $RowNum = $serie['RowNum'];
    
                
                if ($sucursal["AdresType"] == "B") 
                {
                    $auxtipo = "bo_BillTo";
                    $city="";
                } 
                else 
                {
                    $auxtipo = "bo_ShipTo";
                    $city=$clientes['ccu2'];
                }
    
                $sucursalObjeto = [
                    "RowNum" => $RowNum,
                    "BPCode" => $clientes['CardCode'],
                    "AddressName" => $sucursal["AddresName"],
                    "AddressName2"=> $sucursal["Street"],
                    "Street" => $sucursal["Street"],
                    "AddressType" => $auxtipo,
                    "City"=>$city,
                    //"U_XM_Latitud" => $sucursal["u_lat"],
                    //"U_XM_Longitud" => $sucursal["u_lon"],
                    //"U_Territorio" => $sucursal["u_territorio"],
                    
                ];
                array_push($sucursales, $sucursalObjeto);
                Yii::error("ClienteSucursal");
                Yii::error("response database sucursal" . json_encode($sucursalObjeto));
            }     
        }
        return $sucursales;
    }

    private function actualizaCampos($cliente,$cnf_usuario,$id){
     Yii::error("Actualizando... campos : " . json_encode($cliente));
            //ACTUALIZA CAMPOS PERSONALIZADOS
            if($cnf_usuario['cnf_canalVenta']==1){
                $canalVenta=",".
                "codecanal = '" . $cliente["codeCanal"] . "', " .
                "codesubcanal = '" . $cliente["codeSubCanal"] . "', " .
                "codetipotienda = '" . $cliente["codeTipoTienda"] . "', " .
                "cadena = '" . $cliente["cadena"] . "', " .
                "cadenatxt = '" . $cliente["cadenaTxt"] . "', " .
                "cadenaconsolidador = '" . $cliente["codeCadenaConsolidador"] . "'" ;    
            }else{
                $canalVenta="";
            }
            if($cnf_usuario['cnf_fex']==1){
                $camposFex=",".
                "Fex_tipodocumento = '" . $cliente["Fex_tipodocumento"] . "', " .
                "Fex_complemento = '" . $cliente["Fex_complemento"] . "', " .
                "Fex_codigoexcepcion = '" . $cliente["Fex_codigoexcepcion"] . "'" ;
            }else{
                $camposFex =""; 
            }

            /***************CCUS******************* */
             if (!empty($cliente["cuccs"])) {
                Yii::error(" CAMPOS PERSONALIZADOS: ".$cliente["cuccs"]);
                $clienteCcus=json_decode($cliente["cuccs"]);
                foreach ($clienteCcus as $cuc) {
                   $datosCcu1.=", ".$cuc->variablemovil."='".$cuc->code."' ";
                }
                if(count($cliente["cuccs"])>0){
                    $ccucs=$datosCcu1.", cadenaCcus='".$cliente["cuccs"]."'";
                }
                else{
                    $ccucs="";
                }
            }
            else{
                $ccucs="";
            }
            // UPDATE A TODOS LOS CAMPOS PERSONALIZADOS// 
            $up = "UPDATE `clientes` SET cliente_std1 = '" . $cliente["cliente_std1"] . "', " .
                "cliente_std2 = 'N', " .
                "cliente_std3 = '" . $cliente["cliente_std3"] . "', " .
                "cliente_std4 = '" . $cliente["cliente_std4"] . "', " .
                "cliente_std5 = '" . $cliente["cliente_std5"] . "', " .
                "cliente_std6 = '" . $cliente["cliente_std6"] . "', " .
                "cliente_std7 = '" . $cliente["cliente_std7"] . "', " .
                "cliente_std8 = '" . $cliente["cliente_std8"] . "', " .
                "cliente_std9 = '" . $cliente["cliente_std9"] . "', " .
                "cliente_std10 = '" . $cliente["cliente_std10"] . "'".
                $canalVenta.
                $camposFex.
                $ccucs.
                " where id = " . $id;
            Yii::error("DATA Query Update : " . $up);
            $res = Yii::$app->db->createCommand($up)->execute();
            $datos = Yii::$app->db->createCommand("SELECT *, 0 AS New FROM clientes WHERE id =".$id)->queryOne();
            
            Yii::error($datos);
            return $datos;
    }
    public function updateCliente($cliente){
        $cnf_fex="";
        $cnf_canalVenta="";
        $cnf_clienteLocal="";

        $configuracion = Yii::$app->db->createCommand("select parametro,valor from configuracion where estado=1")->queryAll();   
        $response = array("estado"=>0, "mensaje"=>"No se pudo registrar");        
        foreach ($configuracion as $key => $value) {
            if($value['parametro']=='FEX') $cnf_fex=$value['valor'];
            if($value['parametro']=='CanalVenta') $cnf_canalVenta=$value['valor'];
            if($value['parametro']=='ClientesLocalizacion') $cnf_clienteLocal=$value['valor'];
        }

         $cnf_usuario=["cnf_fex"=>$cnf_fex,"cnf_canalVenta"=>$cnf_canalVenta,"cnf_clienteLocal"=>$cnf_clienteLocal];
         $dataClienteMaestro="";
         try {

            //insertando datos maestros del cliente
             $dataClienteMaestro=Clientes::putCliente($cliente);
             if($dataClienteMaestro->id){
               

                 //Mau actualiza campos dinamicos
                  $commonModel=new Common;
                  $dResUpCd=$commonModel->updateCampoDinamicos($cliente["camposusuario"],array("campo"=>'id',"value"=>$dataClienteMaestro->id));
                // fin Mau actualiza campos dinamicos
                  
                   //ACTUALIZA CAMPOS PERSONALIZADO
                 $datosClient= Clientes::actualizaCampos($cliente,$cnf_usuario,$dataClienteMaestro->id);

                 //insertando datos de contactos
                 yii::error("idnuevo usuario: ".$dataClienteMaestro->id);
                 $dataContacto=Clientes::putContacto($cliente,$cliente["ContactPerson"],$dataClienteMaestro->id);
                 yii::error("return new contacto: ".$dataClienteMaestro->id);
                 //insertando datos de sucursales
                 $dataSucursal=Clientes::putSucursales($cliente,$cliente["SucursalesCliente"],$dataClienteMaestro->id);
                // $modelCliente= climodel::findOne($dataClienteMaestro["id"]);
                 //$modelCliente= climodel::findOne($dataClienteMaestro["id"]);
                  $modelCliente=new \backend\models\Clientes;
                  $modelCliente=$modelCliente->findOne($dataClienteMaestro->id);
                    try {
                         $modelCliente->Status=3;  
                            $modelCliente->save(false); 

                        $sapenvioDoc=new Sapenviodoc;
                         $respuesta=$sapenvioDoc->clienteUpdate($datosClient,$dataContacto,$dataSucursal,$cnf_usuario);
                         yii::error("response cardCode".$respuesta->CardCode);
                        if($respuesta && $respuesta->CardCode){
                            $modelCliente->StatusSend=1;  
                            $modelCliente->save(false); 
                                $response = array("estado"=>3, "mensaje"=>"Actualización exitosa","data"=>$respuesta); 
                               // $idUser=$dataClienteMaestro->User;
                                //$sqlNumeracion="UPDATE numeracion set numcli=numcli+1 where iduser='$idUser'"; 
                                //Yii::$app->db->createCommand($sqlNumeracion)->execute(); 
                                //yii::error("data quwery".$sqlNumeracion);
                                
                        }else{
                            $response = array("estado"=>0, "mensaje"=>$respuesta); 
                             $modelCliente->StatusSend=0;  
                                $modelCliente->save(false);      

                        }
                    } catch (Exception $e) {
                          $modelCliente->StatusSend=0;  
                          $modelCliente->save(false);  
                          yii::error("error al enviar a sap".json_encode($respuesta)); 
                       $response = array("estado"=>2, "mensaje"=>"Error al momento de enviar a sap");      
                    }
               }else{
                    $response = array("estado"=>1, "mensaje"=>"No se pudo actualizar el maestro de negocio");
               }
            

         } catch (Exception $e) {
            Yii::error("error al registrar en la base local en algun lado".json_encode($e));
            if($dataClienteMaestro && $dataClienteMaestro->id ){
                $modelCliente= new \backend\models\Clientes;
                $modelCliente= $modelCliente::findOne(($dataClienteMaestro->id?$dataClienteMaestro->id:null));
                
               // $modelCliente=$modelCliente->findOne($dataClienteMaestro->id);
                $modelCliente->StatusSend=0;  
                $modelCliente->save(false); 
            }
             $response = array("estado"=>1, "mensaje"=>"Error al realizar la actualizar en los objetos");
           
         }
         return $response;
    }

    private function putCliente($cliente){
       Yii::error("Listo para actualizar el cliente".json_encode($cliente));
       try {
           
      
        if($cliente["Longitude"]==""){
            $cliente["Longitude"]=$cliente["SucursalPerson"][0]->u_lon;
            $cliente["Latitude"]=$cliente["SucursalPerson"][0]->u_lat;
        }
           // Yii::error("Parametros de configuracion: cnf_fex= ".$cnf_usuario['cnf_fex']." - cnf_canalVenta= ".$cnf_usuario['cnf_canalVenta']." - cnf_clienteLocal=".$cnf_usuario['cnf_clienteLocal']);
            Yii::error("Cliente encontrado".$cliente["CardCode"]);
            $registro =  \backend\models\Clientes::find()->where(" (CardCode='". $cliente["CardCode"]."' or Mobilecod= '". $cliente["CardCode"]."' ) and Status=3 ")->one();
           // $registro =  $registro->find()->where(" (CardCode='". $cliente["CardCode"]."' or Mobilecode= '". $cliente["CardCode"]."' ) and Status=3 ");
            if($registro->CardName==''){

                $registro =  \backend\models\Clientes::find()->where(" (CardCode='". $cliente["CardCode"]."' and Mobilecod= '0' ) and Status=1 ")->one();
            }
            Yii::error("Cliente encontrado".$registro->CardName);
            //$registro->id = $cliente["id"];
            //$registro->CardCode = $cliente["CardCode"];
            $registro->CardName =strtoupper($cliente["CardName"]);

            $registro->FederalTaxId = $cliente["FederalTaxId"];
            $registro->Address = $cliente["Address"];
            $registro->PhoneNumber = $cliente["PhoneNumber"];
            $registro->PriceListNum = $cliente["PriceListNum"];
            $registro->ContactPerson = $cliente["ContactPerson"];
           
            // $registro->img = "sin imagen";
            $registro->Latitude = $cliente["Latitude"];
            $registro->Longitude = $cliente["Longitude"];
            $registro->CardType = $cliente["CardType"];
            $registro->CreditLimit = $cliente["CreditLimit"];
            $registro->MaxCommitment = $cliente["MaxCommitment"];
            $registro->DiscountPercent = $cliente["DiscountPercent"];
            $registro->SalesPersonCode = $cliente["SalesPersonCode"];
            $registro->Currency = $cliente["Currency"];
            $registro->County = $cliente["County"];
            $registro->Country = "PE"; //BO
            $registro->CurrentAccountBalance = $cliente["CurrentAccountBalance"];
            $registro->NoDiscounts = $cliente["NoDiscounts"];
            $registro->PriceMode = $cliente["PriceMode"];
            $registro->PayTermsGrpCode = $cliente["PayTermsGrpCode"];
            $registro->GroupCode = $cliente["GroupCode"];
            $registro->User = $cliente["idUser"];

            $registro->Status = 3;
            $registro->DateUpdate = $cliente["DateUpdate"];
            $registro->Phone2 = $cliente["celular"];
            $registro->Cellular = $cliente["pesonacontactocelular"];
            $registro->EmailAddress = $cliente["correoelectronico"];
            $registro->FreeText = $cliente["comentario"];
            //$registro->Mobilecod = $cliente["CardCode"];
            $registro->CardForeignName = $cliente["razonsocial"];
            $diasVisita = explode(',', $cliente["diavisita"]);
            Yii::error("Cliente encontrado 01 : ".$registro->CardCode);
            /*$registro->Properties1 = 'tNO';
            $registro->Properties2 = 'tNO';
            $registro->Properties3 = 'tNO';
            $registro->Properties4 = 'tNO';
            $registro->Properties5 = 'tNO';
            $registro->Properties6 = 'tNO';
            $registro->Properties7 = 'tNO';*/
            Yii::error("Cliente encontrado 02".$registro->CardCode);
            //$registro->Fex_tipodocumento=$cliente["Fex_tipodocumento"];
           // $registro->Fex_complemento=$cliente["Fex_complemento"];
           // $registro->Fex_codigoexcepcion=$cliente["Fex_codigoexcepcion"];

            /*foreach ($diasVisita as $diaVisita) {
                switch ($diaVisita) {
                    case 'Lunes':
                        $registro->Properties1 = 'tYES';
                        break;
                    case 'Martes':
                        $registro->Properties2 = 'tYES';
                        break;
                    case 'Miercoles':
                        $registro->Properties3 = 'tYES';
                        break;
                    case 'Jueves':
                        $registro->Properties4 = 'tYES';
                        break;
                    case 'Viernes':
                        $registro->Properties5 = 'tYES';
                        break;
                    case 'Sábado':
                        $registro->Properties6 = 'tYES';
                        break;
                    case 'Domingo':
                        $registro->Properties7 = 'tYES';
                        break;
                }
            }*/
            Yii::error("Cliente encontrado 2".$registro->CardCode);
            //$registro->Territory = $cliente["rutaterritorisap"];
            $registro->Territory = $cliente["territorio"];
            //$registro->Territory = $cliente["SucursalPerson"][0]["u_territorio"];
            if ($cliente["tipoEmpresa"] == '' || $cliente["tipoEmpresa"] == 'undefined' || $cliente["tipoEmpresa"] == '0') {
                $registro->Industry = '-1';
            }else {
                $registro->Industry = $cliente["tipoEmpresa"];
            }

            $fileName="addPhoto.svg";
            /*if($registro->img && $registro->img!=''){
              
               // $decodificado = base64_decode($registro->img); 
               preg_match("/data:image\/(.*?);/",$registro->img,$image_extension); // extract the image extension
               $image = preg_replace('/data:image\/(.*?);base64,/','',$registro->img); // remove the type part
               $image = str_replace(' ', '+', $image);
               $fileName = $registro->CardCode.'-'.time()."-".uniqid().".jpg"; 
               Yii::error("decode img ->".$image); 
               Yii::error("decode img -> 2".json_encode($registro->img)); 
             
                file_put_contents ("./imgs/cli/".$fileName, base64_decode($image));
            }*/
           $registro->img = $fileName;
           Yii::error("Cliente encontrado 3".$registro->CardCode);
           $registro->save(false);
          
           return $registro;
        }
        catch (Exception $e) {
            Yii::error("ERROR ACTUALIZAR: ". $e);   
       }
    }
    private function putContacto($clientes,$clienteContactos,$clienteId){
         $contactos = [];
        if (!empty($clienteContactos)) {
            Yii::error("CONTACTOS CLIENTES listo para actualizar: ". json_encode($clienteContactos));

            foreach ($clienteContactos as $contacto) {
                
                Yii::error("encontrado contacto 0: ".json_encode($contacto) );

                 //$responseContacto=  new Contactos;
                 $responseContacto=Contactos::find()->where("InternalCode='".$contacto["internalcode"]."' and idCliente= $clienteId")->one();
                 Yii::error("encontrado contacto 1 : ".$responseContacto->InternalCode );
                 if($responseContacto->id){
                 //actualizando nuevo contacto
                 //$responseContacto->cardCode=$contacto["cardCode"];
                 $responseContacto->nombre=$contacto["nombrePersonaContacto"];
                // $responseContacto->direccion='';
                 $responseContacto->telefono1=$contacto["fonoPersonaContacto"];
                 $responseContacto->telefono2=$contacto["fonoPersonaContacto"];
               //  $responseContacto->celular='';
                // $responseContacto->tipo='';
                 $responseContacto->comentarios=$contacto["comentarioPersonaContacto"];
                 //$responseContacto->Mobilecode=$clientes['Mobilecod'];
                 $responseContacto->titulo=$contacto["tituloPersonaContacto"];
                 $responseContacto->correo=$contacto["correoPersonaContacto"];
                 $responseContacto->User=$clientes["idUser"];
                 //$responseContacto->idsap=null;
                  //$responseContacto->InternalCode=null;
                 //$responseContacto->Status=1;
                 //$responseContacto->idCliente=$clienteId;
                 $responseContacto->save(false);
                 }else{
                    //creacion del nuevo contacto
                    $auxNewContact=array();
                    array_push($auxNewContact, $contacto);
                   $responseContacto=  Clientes::insertContactos($clientes,$auxNewContact,$clienteId);
                 }

                
                

                
                $nombreContacto = explode(" ", $contacto["nombre"]);
                Yii::error("InternalCode : ".$responseContacto->InternalCode );
                
               
                  $contactoObjeto = [
                        "InternalCode"=> $responseContacto->InternalCode?$responseContacto->InternalCode:null,
                        "CardCode" => $contacto["cardCode"],
                        "Name" =>  $contacto["nombrePersonaContacto"],
                        "FirstName" => $nombreContacto[0],
                        "LastName" => $nombreContacto[1],
                        "Phone1" => $contacto["fonoPersonaContacto"],
                        "Remarks1" => $contacto["comentarioPersonaContacto"],
                        "Title" =>substr($contacto["tituloPersonaContacto"], 0, 9),
                        "E_Mail" => $contacto["correoPersonaContacto"],
                        "Active" => "tYES"
                     ];
                array_push($contactos, $contactoObjeto);
              
                Yii::error(json_encode($contactoObjeto));
            }
        }
      return $contactos;
    }

    private function putSucursales($clientes,$clienteSucursal,$clienteId){
         $sucursales = [];
        if (!empty($clienteSucursal)) {
            foreach ($clienteSucursal as $sucursal) {
                Yii::error("response database sucursal->" . json_encode($sucursal));
                Yii::error("response database sucursal->" . $sucursal["LineNum"]);

             //  {"idUser":"70","AddresName":"sub prueba","Street":"27, Las Terrazas 090107, Ecuador","LineNum":0,"State":0,"FederalTaxId":0,"CreditLimit":0,"CardCode":"1007000013","User":"70","Status":1,"DateUpdate":"","idDocumento":0,"TaxCode":"","AdresType":"B","u_zona":"","u_lat":-2.2426713,"u_lon":-79.8936767,"u_territorio":"-2","u_vendedor":"35","labelTerritorio":"Bolivia"}
                //$sucursalData=new Clientessucursales;
                 $sucursalData= Clientessucursales::find()->where("RowNum='".$sucursal["LineNum"]."' and idCliente=$clienteId")->one();
                 Yii::error("antes de guardar la  sucursal 1->".$sucursalData->CardCode);
                 if($sucursalData && $sucursalData->id){
                     Yii::error("antes de guardar la  sucursal->".$sucursalData->CardCode);
                        $sucursalData->Status=$sucursal["Status"];
                        $sucursalData->User=$sucursal["User"];
                        $sucursalData->idupdate=null;
                        $sucursalData->RowNum=$sucursal["LineNum"];
                      //  $sucursalData->DateUpdate
                        $sucursalData->AddresName=$sucursal["AddresName"];
                        $sucursalData->Street=$sucursal["Street"];
                        $sucursalData->State=$sucursal["State"];
                        $sucursalData->FederalTaxId=$sucursal["FederalTaxId"];
                        $sucursalData->CreditLimit=$clientes["CreditLimit"];
                        //$sucursalData->CardCode=$sucursal["CardCode"];
                        $sucursalData->TaxCode=$sucursal["TaxCode"];
                        $sucursalData->AdresType=$sucursal["AdresType"];
                        $sucursalData->u_zona=$sucursal["u_zona"]?$sucursal["u_zona"]:'';
                        $sucursalData->u_lat=$sucursal["u_lat"]?$sucursal["u_lat"]:'';
                        $sucursalData->u_lon=$sucursal["u_lon"]?$sucursal["u_lon"]:'';
                        $sucursalData->u_territorio=$sucursal["u_territorio"]?$sucursal["u_territorio"]:'';
                        $sucursalData->u_vendedor=$sucursal["u_vendedor"];
                        $sucursalData->idCliente=$clienteId;

                        //$sucursalData->Mobilecode=$clientes['Mobilecod'];
                        //$sucursalData->CAMPOUSER1=$clientes['CAMPOUSER1'];
                        //$sucursalData->CAMPOUSER2=$clientes['CAMPOUSER2'];
                        //$sucursalData->CAMPOUSER3=$clientes['CAMPOUSER3'];
                        //$sucursalData->CAMPOUSER4=$clientes['CAMPOUSER4'];
                        //$sucursalData->CAMPOUSER5=$clientes['CAMPOUSER5'];
                        //{"RowNum":null,"BPCode":"1007000013","AddressName":"sub prueba","AddressName2":"27, Las Terrazas 090107, Ecuador","Street":"27, Las Terrazas 090107, Ecuador","AddressType":"bo_BillTo","City":""}
                        Yii::error("antes de guardar la  sucursal->".$sucursalData->CardCode);
                        $sucursalData->save(false);
                 }else{
                     $auxNewSucursal=array();
                    array_push($auxNewSucursal, $sucursal);
                   $sucursalData= Clientes::insertSucursales($clientes,$auxNewSucursal,$clienteId);
                 }
              
                
                $nombresucursal = explode(" ", $sucursal["nombre"]);
                
                $aux1 = $sucursal["AddresName"];
                $aux2 = $clientes['CardCode'];
                $aux3 = $sucursal["AdresType"];
                $RowNum = "";
                $serie = Yii::$app->db->createCommand("SELECT RowNum FROM clientessucursales WHERE AddresName = '$aux1' and CardCode = '$aux2' and AdresType = '$aux3'  ")
                ->queryOne();
                $RowNum = $serie['RowNum'];
    
                
                if ($sucursal["AdresType"] == "B") 
                {
                    $auxtipo = "bo_BillTo";
                    $city="";
                } 
                else 
                {
                    $auxtipo = "bo_ShipTo";
                    $city=$clientes['ccu2'];
                }
    
                $sucursalObjeto = [
                    "RowNum" => $RowNum,
                    "BPCode" => $clientes['CardCode'],
                    "AddressName" => $sucursal["AddresName"],
                    "AddressName2"=> $sucursal["Street"],
                    "Street" => $sucursal["Street"],
                    "AddressType" => $auxtipo,
                    "City"=>$city,
                    //"U_XM_Latitud" => $sucursal["u_lat"],
                    //"U_XM_Longitud" => $sucursal["u_lon"],
                    //"U_Territorio" => $sucursal["u_territorio"],
                    
                ];
                array_push($sucursales, $sucursalObjeto);
                Yii::error("ClienteSucursal");
                Yii::error("response database sucursal" . json_encode($sucursalObjeto));
            }     
        }
        return $sucursales;
    }
}