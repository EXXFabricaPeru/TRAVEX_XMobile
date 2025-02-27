<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
use backend\models\Usuarioconfiguracion;

class Documentos extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    // consultas para cabeceras de documentos
    private function obtenerCamposConsultaDocumentos($tipodocumento,$usuario){
        // tipodocumento  0=oferta,1=pedido,2=factura,3=entrega,4=deuda
        // vemos configuracion  general
        $configuracion_tipo_vendedor_sql="Select * from sys_vi_cnf_usuario where id=".$usuario;
        $configuracion_tipo_vendedor = Yii::$app->db->createCommand($configuracion_tipo_vendedor_sql)->queryOne();
        $aux_cnf_fex=$configuracion_tipo_vendedor["FEX"];       
        // fin configuracion general
        $sql_cabecera_condicional="";
        $sql_cabecera_fex="";
        if($aux_cnf_fex==1){
            $sql_cabecera_fex=' ,CAB."U_EXX_FE_Email"';
        }else{
            $sql_cabecera_fex="";
        }
        
        $sql="";
        switch ($tipodocumento){
            case 0:
                $sql_cabecera_condicional.='\'DOF\' as "DocType",'; 
                $sql_cabecera_condicional.='\'0\' as "cuota",'; 
                $sql_cabecera_condicional.='\'0\' as "saldo",'; 
                $sql_cabecera_condicional.='\'0\' as "InsTotal",'; 
                $sql_cabecera_condicional.='\'0\' as "InstPrcnt",';
                $sql_cabecera_condicional.='\'0\' as pagado';                            
            break; 
            case 1:
                $sql_cabecera_condicional.='\'DOP\' as "DocType",'; 
                $sql_cabecera_condicional.='\'0\' as "cuota",'; 
                $sql_cabecera_condicional.='\'0\' as "saldo",'; 
                $sql_cabecera_condicional.='\'0\' as "InsTotal",'; 
                $sql_cabecera_condicional.='\'0\' as "InstPrcnt",';
                $sql_cabecera_condicional.='\'0\' as pagado';                            
            break;
            case 2:
                $sql_cabecera_condicional.='\'DFA\' as "DocType",'; 
                $sql_cabecera_condicional.='\'0\' as "cuota",'; 
                $sql_cabecera_condicional.='\'0\' as "saldo",'; 
                $sql_cabecera_condicional.='\'0\' as "InsTotal",'; 
                $sql_cabecera_condicional.='\'0\' as "InstPrcnt",';
                $sql_cabecera_condicional.='"PaidToDate" as pagado';                            
            break;  
            case 3:
                $sql_cabecera_condicional.='\'DOE\' as "DocType",'; 
                $sql_cabecera_condicional.='\'0\' as "cuota",'; 
                $sql_cabecera_condicional.='\'0\' as "saldo",'; 
                $sql_cabecera_condicional.='\'0\' as "InsTotal",'; 
                $sql_cabecera_condicional.='\'0\' as "InstPrcnt",';
                $sql_cabecera_condicional.='\'0\' as pagado';                            
            break;
            case 4: 
                $sql_cabecera_condicional.='\'DFA\' as "DocType",';      
                $sql_cabecera_condicional.='DEU."InstlmntID" as "cuota",';
                $sql_cabecera_condicional.='(DEU."InsTotal"-DEU."Paid") as "Saldo",';
                $sql_cabecera_condicional.='DEU."InsTotal",';
                $sql_cabecera_condicional.='DEU."InstPrcnt",';
                $sql_cabecera_condicional.='DEU."Paid" as pagado';
            break; 
        }
        // Revisar con documentosmovilsap - anterio version//
        $sql_cabecera_condicional.=', \'0\' as "status",';      
        $sql_cabecera_condicional.=' \'0\' as "DateUpdate",';
        $sql_cabecera_condicional.=' \'0\' as "PaidtoDate",';
        $sql_cabecera_condicional.=' \'0\' as "DateUpdate",';
        $sql_cabecera_condicional.=' \'0\' as "FederalTaxId",';
        $sql_cabecera_condicional.=' \'0\' as "cndpago",';
        $sql_cabecera_condicional.=' \'0\' as "cndpagoname",';
        $sql_cabecera_condicional.=' \'0\' as "U_XMB_AUX1",';
        $sql_cabecera_condicional.=' \'0\' as "Address2",';
        $sql_cabecera_condicional.=' \'0\' as "U_XMB_Latitud",';
        $sql_cabecera_condicional.=' \'0\' as "U_XMB_Longitud",';
        $sql_cabecera_condicional.=' \'0\' as "U_XMB_Territorio",';
        $sql_cabecera_condicional.=' \'0\' as "grupoclientedocificacion",';
        $sql_cabecera_condicional.=' \'0\' as "grupoproductodocificacion",';
        $sql_cabecera_condicional.=' \'0\' as "PriceListNum",';
        $sql_cabecera_condicional.=' \'0\' as "PickDate",';
        $sql_cabecera_condicional.=' \'0\' as "AbsEntry",';
        $sql_cabecera_condicional.=' \'0\' as "rowNum" ';
        // Fin Revisar con documentosmovilsap - anterio version//
        
        $sql_cabecera='
        CAB."DocEntry",
        CAB."DocNum",
        TO_DATE( CAB."DocDate") as "DocDate",
        CAB."CardCode",
        CAB."CardName",
        CAB."DocCur" AS "DocCurrency",
        CAB."DocCur" as "Currency",
        CAB."JrnlMemo" AS "JournalMemo",
        CAB."GroupNum" AS "PayTermsGrpCode",
        CAB."DocTotal",
        TO_DATE(CAB."TaxDate") AS "TaxDate",
        TO_DATE(CAB."CreateDate") AS "CreationDate",
        TO_DATE(CAB."UpdateDate") AS "UpdateDate",
        TO_DATE(CAB."DocDueDate")as "DocDueDate",
        CAB."DiscPrcnt" as "descuento", 
        CAB."isIns" as "ReserveInvoice",
        CAB."Address",  
        CAB."U_LB_NumeroFactura",
        CAB."U_LB_NumeroAutorizac",
        CAB."U_LB_CodigoControl",
        CAB."U_LB_EstadoFactura",
        CAB."U_LB_RazonSocial",
        CAB."U_LB_NIT",
        CAB."U_xMOB_Codigo",
        CAB."U_LB_TipoFactura",
        CAB."SlpCode",
        CAB."SlpCode" AS "U_XMB_repartidor",       
        TO_FIXEDCHAR(DET."Costo2",100) as "centrocosto",
        TO_FIXEDCHAR(DET."Costo1",100) as "UnidadNegocio",
        TO_FIXEDCHAR(DET."Costo1",100) as "Costo1",
        TO_FIXEDCHAR(DET."Costo2",100) as "Costo2",
        TO_FIXEDCHAR(DET."Almacenes",100) as "almacenes",
        DET."Pendiente",
        DET."GTotal",
        ';

        $sql=$sql_cabecera." ".$sql_cabecera_condicional." ".$sql_cabecera_fex;
        return $sql;
    }
    private function obtenerCondicionConsultaDocumentos($tipodocumento,$equipo,$usuario,$texto='',$fecha1='',$fecha2=''){
        // tipodocumento  0=oferta,1=pedido,2=factura,3=entrega,4=deuda
        // vemos configuracion  general
            $configuracion_tipo_vendedor_sql="Select * from sys_vi_cnf_usuario where id=".$usuario;
            $configuracion_tipo_vendedor = Yii::$app->db->createCommand($configuracion_tipo_vendedor_sql)->queryOne();
            $aux_cnf_di=$configuracion_tipo_vendedor["importados"];
            $aux_cnf_cv=$configuracion_tipo_vendedor["codEmpleadoVenta"];
        // fin configuracion general
        
        
        $cadena=" ";
        switch ($tipodocumento){
            case 4://deuda
                $cadena='WHERE  (CAB."CANCELED" = \'N\') AND ((DEU."InsTotal"-DEU."Paid")>0) AND (CAB."DocStatus"!=\'C\') ';
            break;
            case 2://facturas de reserva
                $cadena='WHERE (CAB."isIns"=\'Y\') and (DET."Pendiente">0) AND (CAB."InvntSttus"=\'O\') and (CAB."U_xMOB_Codigo"<>\'\')';
            break;
            default://demas documentos
                $cadena='WHERE (DET."Pendiente">0) AND (CAB."InvntSttus"=\'O\') and (CAB."U_xMOB_Codigo"<>\'\')';
            break;
        }
        
        if($texto!=''){
            // falta poner las fechas  en las condiciones
            $cadena.= ' and  (CAB."CardCode" like \'%'.$texto.'%\' or CAB."CardName" like \'%'.$texto.'%\')'; 
        }else{
            switch ($aux_cnf_di){
                case 0:
                    $cadena.=' AND (CAB."SlpCode"='.$aux_cnf_cv.') ';
                break;
                case 1:
                    //$cadena.=' AND (CAB."U_XMB_repartidor"='.$aux_cnf_cv.') ';
                    $cadena.=' AND (CAB."SlpCode"='.$aux_cnf_cv.') ';
                break;
                case 2:
                    // vemos los almacenes del equipo
                        $cnf_equipo_sql="select whs from sys_vi_equiposalmacenes where equipoid=".$equipo." group by equipoid" ;
                        $cnf_equipo = Yii::$app->db->createCommand($cnf_equipo_sql)->queryAll();            
                    // fin consulta almacenes equipo
                    $cadena.=' AND (';
                    foreach ($cnf_equipo as $xalmacen){
                        $cadena.='(DET."almacenes" like \'%'.$xalmacen->whs.'%\') OR ';
                    }
                    $cadena=substr($cadena,0,-3);
                    $cadena.=')';
                break;
                default:
                    $cadena.=' AND (CAB."SlpCode"='.$aux_cnf_cv.') ';
                break;            
            }
        }
        return $cadena;
    }
    private function obtenerFromCabecera($tipodocumento){
        $t1="";
        $t2="";
        $t3="";
        switch ($tipodocumento){
            case 0:
                $t1="OQUT";
                $t2="QUT1";                  
            break;
            case 1:
                $t1="ORDR";
                $t2="RDR1";                 
            break;
            case 2:
                $t1="OINV";
                $t2="INV1";                 
            break;
            case 3:
                $t1="ODLN";
                $t2="DLN1";                
            break;
            case 4:
                $t1="OINV";
                $t2="INV1";
                $t3="INNER JOIN INV6 DEU ON CAB.\"DocEntry\" = DEU.\"DocEntry\"";                 
            break;
        }
        $from='  FROM "'.$t1.'" CAB  
        left join ( select
            "DocEntry",
            STRING_AGG("OcrCode2",\',\') as "Costo1" ,
            STRING_AGG("OcrCode",\',\')  as "Costo2",
            SUM("OpenQty")             as "Pendiente",
            SUM("GTotal")             as "GTotal",
            STRING_AGG("WhsCode",\',\') as "Almacenes" 
            from "'.$t2.'" 
            group by "DocEntry") DET on CAB."DocEntry" = DET."DocEntry"
        '.$t3.' 
        ';
        return $from;
    }
    public function obtenerDocumentosContador($tipodocumento,$equipo,$usuario){        
        $sql=" Select ";
        $campos=" Count(*) as Contador"; 
        $from=$this->obtenerFromCabecera($tipodocumento);
        $where=$this->obtenerCondicionConsultaDocumentos($tipodocumento,$equipo,$usuario);
        $sql_hana=$sql." ". $campos." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerDocumentos($tipodocumento,$equipo,$usuario,$salto=0,$limite=0){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaDocumentos($tipodocumento,$usuario);        
        $from=$this->obtenerFromCabecera($tipodocumento);       
        $where=$this->obtenerCondicionConsultaDocumentos($tipodocumento,$equipo,$usuario);
        $order='order by CAB."DocEntry"';
        $limite=" limit {$limite} OFFSET {$salto}";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerDocumentosTodos($tipodocumento,$equipo,$usuario,$texto,$fecha1,$fecha2){
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaDocumentos($tipodocumento,$usuario);        
        $from=$this->obtenerFromCabecera($tipodocumento);       
        $where=$this->obtenerCondicionConsultaDocumentos($tipodocumento,$equipo,$usuario,$texto,$fecha1,$fecha2);
        $order='order by CAB."DocEntry"';        
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    // consultas para detallede documentos
    private function obtenerCamposConsultaDocumentosDetalle(){
        $sql='
         CAB."DocNum",
         DET."DocEntry",
         DET."LineNum",
         DET."ItemCode",
         DET."Dscription" AS "ItemDescription",
         (DET."GPBefDisc"-(((DET."GPBefDisc" *DET."Quantity") - DET."GTotal")/DET."Quantity")) AS "PriceAfterVAT",
         DET."Currency",
         DET."Rate",
         DET."TotInclTax" AS "TaxTotal",
         DET."PriceBefDi" AS "UnitPrice",
         DET."Quantity",
         DET."Price",
         /*DET."LineTotal",
         */ DET."GTotal" AS "LineTotal",
         DET."OpenQty",
         (DET."Quantity"-DET."OpenQty") AS "Entregado",
         CAB."CANCELED",
         DET."WhsCode" ,
         DET."OcrCode",
         DET."OcrCode2",
         DET."LineStatus",
         DET."InvntSttus",
         DET."OpenSum" ,
         DET."UomCode",
         (((DET."GPBefDisc" *DET."Quantity") - DET."GTotal")/DET."Quantity") AS "U_Descuento" 
         -- DET."U_ListaPrecio" as "U_AListaPrecio"
        '; 
        return $sql;
    }
    private function obtenerFromDetalle($tipodocumento){
        $t1="";
        $t2="";
        $t3="";
        switch ($tipodocumento){
            case 0:
                $t1="OQUT";
                $t2="QUT1";                  
            break;
            case 1:
                $t1="ORDR";
                $t2="RDR1";                 
            break;
            case 2:
                $t1="OINV";
                $t2="INV1";                 
            break;
            case 3:
                $t1="ODLN";
                $t2="DLN1";                
            break;
            case 4:
                $t1="OINV";
                $t2="INV1";
                $t3="INNER JOIN INV6 DEU ON CAB.\"DocEntry\" = DEU.\"DocEntry\"";                 
            break;
        }
        $from=' FROM "'.$t1.'" CAB 
        INNER JOIN "'.$t2.'" DET on CAB."DocEntry" = DET."DocEntry"        
        ';
        return $from;
    }
    private function obtenerCondicionConsultaDetalle($tipodocumento,$equipo,$usuario,$texto=''){
        // tipodocumento  0=oferta,1=pedido,2=factura,3=entrega,4=deuda
        // vemos configuracion  general
            $configuracion_tipo_vendedor_sql="Select * from sys_vi_cnf_usuario where id=".$usuario;
            $configuracion_tipo_vendedor = Yii::$app->db->createCommand($configuracion_tipo_vendedor_sql)->queryOne();
            $aux_cnf_di=$configuracion_tipo_vendedor["importados"];
            $aux_cnf_cv=$configuracion_tipo_vendedor["codEmpleadoVenta"];
        // fin configuracion general
        $sql=' Select CAB."DocEntry" ';               
        $from=$this->obtenerFromCabecera($tipodocumento);       
        $where=$this->obtenerCondicionConsultaDocumentos($tipodocumento,$equipo,$usuario);
        $order='order by CAB."DocEntry"';       
        $sql_hana=$sql." ".$from." ".$where." ".$order." ".$limite;        
        $cadena='WHERE DET."DocEntry" IN('.$sql_hana.')';
           
        if($texto!=''){          
            $cadena.= ' and  (CAB."DocEntry"='.$texto.')'; 
        }
        return $cadena;
    }
    public function obtenerDetalles($tipodocumento,$equipo,$usuario,$salto=0,$limite=0){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaDocumentosDetalle();        
        $from=$this->obtenerFromDetalle($tipodocumento);       
        $where=$this->obtenerCondicionConsultaDetalle($tipodocumento,$equipo,$usuario);
        $order='order by DET."DocEntry"';
        $limite=" limit {$limite} OFFSET {$salto}";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerDetallesTodos($tipodocumento,$equipo,$usuario,$texto=''){
        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaDocumentosDetalle();        
        $from=$this->obtenerFromDetalle($tipodocumento);       
        $where=$this->obtenerCondicionConsultaDetalle($tipodocumento,$equipo,$usuario,$texto);
        $order='order by DET."DocEntry"';      
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerDetallesContador($tipodocumento,$equipo,$usuario){
        $sql=" Select Count(*) as Contador";             
        $from=$this->obtenerFromDetalle($tipodocumento);       
        $where=$this->obtenerCondicionConsultaDetalle($tipodocumento,$equipo,$usuario);        
        $sql_hana=$sql." ". $from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    // consultas registro documentos

    // fin registro Documentos
    // consultas envio documentos
    // consulta obtener documentos del picking list
    /*public function obtenerListaPicking($codEmpleado){

        $sql='SELECT  PS."TaxDate",
                0 AS "id",
                PS."DocEntry",
                concat( \'DOP\', PS."DocNum" ) AS "DocNum",
                \'DOP\' AS "DocType",
                PS."DocDate",
                PS."DocDueDate",
                PS."CardCode",
                PS."CardName",
                PS."DocTotal",
                PS."Status",
                PS."Status" AS "DateUpdate",
                PS."ReserveInvoice",
                0 AS "PaidtoDate",
                0 AS "Saldo",
                0 AS "descuento",
                PS."DirEntrega" AS "idSucursalMobile",
                
                0 as "idUser",  
                0 AS "Pendiente",
                0 AS "centrocosto",
                0 AS "unidadnegocio",
                "OCRD"."LicTradNum" AS "FederalTaxId",
                "OCRD"."GroupNum" AS "cndpago",
                0 AS "cndpagoname",
                "OCRD"."GroupNum" AS "PayTermsGrpCode"  ,

                PS."Repartidor" AS "U_XMB_repartidor",
                PS."AUX1" AS "U_XMB_AUX1",
                \'\' as "Address",
                PS."Address2",
                PS."U_Latitud" AS "U_XMB_Latitud",
                PS."U_Longitud" AS "U_XMB_Longitud",
                PS."U_Territorio" AS "U_XMB_Territorio",
                "OCRD"."U_XM_DosificacionSocio" as "grupoclientedocificacion",
                1 AS "grupoproductodocificacion",
                0 AS "PriceListNum",
                TO_DATE(PS."PickDate") as "PickDate", 
                PS."AbsEntry", 
                0 as "rowNum"'; 
        
        $from='from "EXX_XM_PedidoSap" AS PS
                left join "OCRD" on PS."CardCode"="OCRD"."CardCode" '; *     
        $where=" WHERE PS.\"Repartidor\"='".$codEmpleado."'";        
        $sql_hana=$sql." ". $from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
*/
    public function obtenerListaPicking($codEmpleado){
        $sql=" Select * ";             
        $from=" from \"EXX_XM_PedidoSapGeo\" ";       
        $where=" WHERE \"Repartidor\"='".$codEmpleado."'";        
        $sql_hana=$sql." ". $from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerListaPickingFecha($fecha,$vededor){

        $usuarioConfig = Usuarioconfiguracion::find()->where('idUser = '.$vededor)->asArray()->one();
        $repartidor=$usuarioConfig['codEmpleadoVenta'];

        $sql='Select "CardCode" as "cardcode","CardName" as "cardname","U_Latitud" as "latitud","U_Longitud" as "longitud", "U_Territorioname" as "territoryname", "DireccionEntrega" as "calle" , "DocNum"';             
        $from=" from \"EXX_XM_PedidoSapGeoRep\" ";       
        $where=" WHERE \"Repartidor\"='{$repartidor}' and \"PickDate\"='{$fecha}'";        
        $sql_hana=$sql." ". $from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }

    public function obtenerListaPickingContador($codEmpleado){
        $sql=" Select ";    
        $campos="Count(*) as Contador";         
        $from=" from \"EXX_XM_PedidoSap\" ";       
        $where=" WHERE \"Repartidor\"='".$codEmpleado."'";        
        $sql_hana=$sql." ". $campos." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }

    /*public function obtenerListaPickingDetalle($codEmpleado){
        $sql=' select
         PDS."UomCode" AS "unityCode",
         0 AS "id",
         PDS."LineNum" AS "LineNum",
         PDS."ItemCode" AS "ItemCode",
         PDS."ItemDescription" AS "ItemDescription",
         PDS."Quantity" AS "Quantity",
         PDS."Price" AS "Price",
         PDS."Price" AS "PriceAfterVAT",
         PDS."Currency" AS "Currency",
         PDS."LineTotal" AS "LineTotal",
         PDS."DocEntry" AS "DocEntry",
         1 AS "Status",
        concat( \'DOP\', PDS."DocNum" ) AS "DocNum",
        \'DOP\' AS "Doctype",
        17 AS "DocBase",
        PDS."Quantity" * PDS."PriceAfterVAT" AS "totalLinea",
        ( SELECT "OITM"."ManSerNum" FROM "OITM" WHERE "OITM"."ItemCode" = PDS."ItemCode" ) AS "ManageSerialNumbers",
        ( SELECT "OITM"."ManBtchNum" FROM "OITM" WHERE "OITM"."ItemCode" = PDS."ItemCode" ) AS "ManageBatchNumbers",
        ( SELECT "OITM"."TreeType" FROM "OITM" WHERE "OITM"."ItemCode" = PDS."ItemCode" ) AS "combo",
        PDS."OpenQty" AS "OpenQty",
        PDS."LineStatus" AS "LineStatus",
        PDS."WhsCode" AS "WhsCode",
        ( SELECT sum( "OITW"."OnHand" ) FROM "OITW"
        WHERE "OITW"."ItemCode" = "OITW"."ItemCode" 
        AND "OITW"."WhsCode" = PDS."WhsCode" ) AS "Name_exp_22", 
        PDS."U_XMB_CANTREP" AS "U_XMB_CANTREP",
        \'\' AS "U_XMB_ALMREP",
        \'\' AS "U_XMB_LOTEREP",
        \'\' AS "U_XMB_SERIEREP",
        \'\' AS "GroupName",
        "OITM"."ItmsGrpCod" AS "ItemsGroupCode",
        "OITM"."U_XM_Actividad" as "grupoproductodocificacion",
        "OITM"."U_XM_ICEtipo" as "ICETIPO",
        PDS."ICEP" as "ICEPorcentual",
        PDS."ICEE" as "ICEEspecifico",
        "OITM"."U_XM_ICEPorcentual" as "iceorpor",
        "OITM"."U_XM_ICEEspecifico" as "iceoresp",
        PDS."Bonif",
        PDS."CodeBonif",
        PDS."DiscPrcnt" as "descuento",
        PDS."BaseQty",
        PDS."Repartidor"
        ';             
        $from=' FROM
        "EXX_XM_pedidosDetalleSap" AS PDS
        LEFT JOIN "EXX_XM_PedidoSap" as PD on PD."DocEntry"=PDS."DocEntry" 
        and PD."DocNum"=PDS."DocNum"
        LEFT JOIN "OITM" on PDS."ItemCode"="OITM"."ItemCode" ';       
        $where=" WHERE PDS.\"Repartidor\"='".$codEmpleado."'";        
        $sql_hana=$sql." ". $from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    */
    public function obtenerListaPickingDetalle($codEmpleado){
        $sql=" Select * ";             
        $from=" from \"EXX_XM_pedidosDetalleSap\" ";       
        $where=" WHERE \"Repartidor\"='".$codEmpleado."'";        
        $sql_hana=$sql." ". $from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    public function obtenerListaPickingDetalleContador($codEmpleado){
        $sql=" Select ";    
        $campos="Count(*) as Contador";         
        $from=" from \"EXX_XM_pedidosDetalleSap\" ";       
        $where=" WHERE \"Repartidor\"='".$codEmpleado."'";        
        $sql_hana=$sql." ". $campos." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }
    // consultas para linea de documentos basenum 
    
    public function LineNum_detalle($docentry,$ItemCode,$Quantity,$DocType,$unidadMedida) {
        try {
            switch ($DocType){
                case 'DOF':
                    $tabla="QUT1";//DOC OFERTA
                break;
                case 'DOP':
                    $tabla="RDR1";//DETALLE PEDIDO
                break;
                case 'DFA':
                    $tabla="INV1";//DETALLE FACTURA
                break;
                case 'DOE':
                    $tabla="DLN1";//DETALLE ENTRAGA
                break;
            }

            $sql='Select ';
            $campos='"LineNum"';       
            $from=' from "'.$tabla.'"';
            $where=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."' ";
            // $where=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."' AND \"UomEntry\"='".$unidadMedida."'";
            $sql_hana=$sql." ". $campos." ".$from." ".$where;
            Yii::error("Respuesta Line Num Detalle: query:".$sql_hana);
            $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
            Yii::error("Respuesta Line Num Detalle".json_encode($resultado));
           // $resultado = json_decode($resultado);
            return $resultado["LineNum"];
        } catch (\Throwable $e) { 
            throw $e;
        }
    }
    //obtener registros de detalle
    public function LineNum_detalleTodo($docentry,$ItemCode,$Quantity,$DocType,$unidadMedida) {
        try {
            switch ($DocType){
                case 'DOF':
                    $tabla="QUT1";//DOC OFERTA
                break;
                case 'DOP':
                    $tabla="RDR1";//DETALLE PEDIDO
                break;
                case 'DFA':
                    $tabla="INV1";//DETALLE FACTURA
                break;
                case 'DOE':
                    $tabla="DLN1";//DETALLE ENTRAGA
                break;
            }

            $sql='Select ';
            $campos='"LineNum","ItemCode","Quantity","UomEntry"';       
            $from=' from "'.$tabla.'"';
            $where=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."' ";
            // $where=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."' AND \"UomEntry\"='".$unidadMedida."'";
            $sql_hana=$sql." ". $campos." ".$from." ".$where;
            Yii::error("Respuesta Line Num Detalle: query:".$sql_hana);
            $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
            Yii::error("Respuesta Line Num Detalle 2 ".json_encode($resultado));
           // $resultado = json_decode($resultado);
            return $resultado;
        } catch (\Throwable $e) { 
            throw $e;
        }
    }

    // facturas pendientes de pago//
    public function obtenerFacturasPendientesPago(){

    }

    public function obtenerDeudaCLiente($CardCode){

        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaDocumentosDeuda();        
        $from=$this->obtenerFromCabeceraDeuda();       
        $where=$this->obtenerCondicionConsultaDocumentosDeuda($CardCode,0);
        $order="";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }

    public function obtenerDeudaCLienteTodos($usuario=0){

        $sql=" Select ";
        $campos=$this->obtenerCamposConsultaDocumentosDeuda();        
        $from=$this->obtenerFromCabeceraDeuda();       
        $where=$this->obtenerCondicionConsultaDocumentosDeuda('',$usuario);
        $order=" ";//" limit 1000 offset 3000";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }

    public function obtenerDeudaCLienteTodosContador($usuario=0){

        $sql=" Select ";
        $campos="Count(*) as Contador";        
        $from=$this->obtenerFromCabeceraDeuda();       
        $where=$this->obtenerCondicionConsultaDocumentosDeuda('',$usuario);
        $order=" ";
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        return $resultado;
    }


    private function obtenerCamposConsultaDocumentosDeuda(){

        $campos.='CASE FAC."U_LB_NumeroFactura" when \'\' 
                                then \'Sin Numero\' 
                                else FAC."U_LB_NumeroFactura" 
                                end as "U_XMB_AUX1",';
        $campos.='CONCAT(\'DFA\',';
        $campos.='FAC."DocNum")AS "DocNum",';
        $campos.='\'DFA\' as "DocType",';
        $campos.='\'0\' AS "Pendiente",';
        $campos.='\'0\' as "PriceListNum",';
        $campos.=' \'0\' AS "GTotal",';
        $campos.='\'0\' as "U_XMB_Latitud",';
        $campos.=' \'0\' as "U_XMB_Longitud",';
        $campos.='\'0\' as "U_XMB_Territorio",';
        $campos.='\'0\' as "grupoclientedocificacion",';
        $campos.='\'0\' as "grupoproductodocificacion",';
        $campos.='\'0\' as "centrocosto",';
        $campos.='\'0\' as "unidadnegocio",';

        $campos.='FAC."Address",
                 FAC."Address" as "Address2",
                 FAC."CardCode",
                 FAC."CardName",
                 TO_DATE(FAC."UpdateDate") as "DateUpdate",
                 TO_DATE(FAC."DocDate") as "DocDate",
                 TO_DATE(FAC."DocDueDate")as "DocDueDate",
                 FAC."DocEntry",
                 
                 T1."InsTotal" as "DocTotal",
                
                 CLI."LicTradNum" as "FederalTaxId",
                 
                 FAC."PaidToDate" as "PaidtoDate",
                 FAC."GroupNum" as"PayTermsGrpCode",
                 
                 FAC."isIns" as "ReserveInvoice",
                 (T1."InsTotal"-T1."Paid") as "Saldo",
                 FAC."DocStatus" AS "Status",
                 TO_DATE(FAC."TaxDate") AS "TaxDate",
                 
                 
                 FAC."SlpCode" AS "U_XMB_repartidor",
                
                 CNDPAGO."GroupNum" as "cndpago",
                 CNDPAGO."PymntGroup" as "cndpagoname",
                 FAC."DiscPrcnt" as "descuento",
                 FAC."DiscPrcnt" as "descuentos",
                
                 FAC."DocEntry" as "id",
                 
                 T1."InstlmntID" as "Cuota",
                 FAC."DocCur" as "Currency",
                 CLI."Territory" ';

        return $campos;
    }
    private function obtenerFromCabeceraDeuda(){
        $from='from "OINV" FAC 
                    left join "OCRD" CLI on FAC."CardCode"=CLI."CardCode" 
                    INNER JOIN INV6 T1 ON FAC."DocEntry" = T1."DocEntry" 
                    left join "OCTG" CNDPAGO on FAC."GroupNum"=CNDPAGO."GroupNum" ';
        return $from;
    }
    private function obtenerCondicionConsultaDocumentosDeuda($CardCode='',$usuario){
       
        $where=' where (FAC."CANCELED" = \'N\') 
                    AND ((T1."InsTotal"-T1."Paid")>0) 
                    AND (FAC."DocStatus"!=\'C\') ';
        if($CardCode!=''){
          $where.=' AND FAC."CardCode"=\''.$CardCode.'\' ';  
        }
        if($usuario!=0){//CLI."Territory",
            $sql_aux_cnf_territorios="select GROUP_CONCAT(idTerritorio) as territorios from usuariomovilterritoriodetalle where idUserMovil=".$usuario." group by idUserMovil" ;
                    $aux_cnf_territorios = Yii::$app->db->createCommand($sql_aux_cnf_territorios)->queryOne();
                    $aux_cnfu_trr=$aux_cnf_territorios["territorios"];             
                    
           // $where.=' AND CLI."Territory" in('.$aux_cnfu_trr.')'; 
        }

        return $where;
    }
    /*
    public function LineNum_detalle_pedidos($docentry,$ItemCode,$Quantity) {
        try {
            $serviceLayer = new Sincronizar();
            Yii::error("....LineNum_detalle_pedidos 5002:".$docentry."-".$ItemCode);
            $data = json_encode(array("accion" => 5002,"docentry"=>$docentry,"ItemCode"=>$ItemCode,"Quantity"=>$Quantity));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            if(count($respuesta)>0){
                if(is_array($respuesta)){
                    return $respuesta[0]->LineNum;
                }else{
                    return -1;
                }
                
            }else{
                return -1;
            }
           // return $respuesta[0]->LineNum;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'LineNum_detalle_pedidos', $e);
            throw $e;
        }
    }

    public function LineNum_detalle_factura($docentry,$ItemCode,$Quantity) {
        try {
            $serviceLayer = new Sincronizar();
            Yii::error("....LineNum_detalle_factura 5003:".$docentry."-".$ItemCode);
            $data = json_encode(array("accion" => 5003,"docentry"=>$docentry,"ItemCode"=>$ItemCode,"Quantity"=>$Quantity));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta, true);
            return $respuesta[0]->LineNum;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'LineNum_detalle_factura', $e);
            throw $e;
        }
    }

    public function LineNum_detalle_entrga($docentry,$ItemCode,$Quantity) {
        try {
            $serviceLayer = new Sincronizar();
            Yii::error("....LineNum_detalle_entrga 5004:".$docentry."-".$ItemCode);
            $data = json_encode(array("accion" => 5004,"docentry"=>$docentry,"ItemCode"=>$ItemCode,"Quantity"=>$Quantity));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta, true);
            return $respuesta[0]->LineNum;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->insertLog2('online', 'LineNum_detalle_entrga', $e);
            throw $e;
        }
    }
    case 5001: //detalle oferta
		$docentry=$objDatos->docentry;
		$ItemCode=$objDatos->ItemCode;
		$Quantity=$objDatos->Quantity;
		$tabla="QUT1";
		$campos='"LineNum"';
		//$campos="*";
		$condicion=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."'";
		$condicion=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."'";
		$respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
		if ($respuesta["json"]=='') {
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
		} else {
			echo $respuesta["json"];
		}
	break;

	case 5002: //detalle order o pedidos
		$docentry=$objDatos->docentry;
		$ItemCode=$objDatos->ItemCode;
		$Quantity=$objDatos->Quantity;
		$tabla="RDR1";
		$campos='"LineNum"';
		//$campos="*";
		$condicion=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."'";
		$condicion=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."'";
		$respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
		if ($respuesta["json"]=='') {
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
		} else {
			echo $respuesta["json"];
		}
	break;

	case 5003:  //detalle factura
		$docentry=$objDatos->docentry;
		$ItemCode=$objDatos->ItemCode;
		$Quantity=$objDatos->Quantity;
		$tabla="INV1";
		$campos='"LineNum"';
		//$campos="*";
		$condicion=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."'";
		$condicion=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."'";
		$respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
		if ($respuesta["json"]=='') {
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
		} else {
			echo $respuesta["json"];
		}
	break;

	case 5004:  //detalle entrga
		$docentry=$objDatos->docentry;
		$ItemCode=$objDatos->ItemCode;
		$Quantity=$objDatos->Quantity;
		$tabla="DLN1";
		$campos='"LineNum"';
		//$campos="*";
		$condicion=" WHERE \"DocEntry\"='".$docentry."' AND \"ItemCode\"='".$ItemCode."' AND \"Quantity\"='".$Quantity."'";
		$respuesta=$accion->devolver_datos_tabla($tabla,$campos,$condicion);
		if ($respuesta["json"]=='') {
		echo json_encode(array("estado"=>600,"error"=>"Problema de encode json "));
		} else {
			echo $respuesta["json"];
		}
	break;
    
    
    
    */ 
}