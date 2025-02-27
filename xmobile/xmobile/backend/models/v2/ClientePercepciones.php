<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class Clientepercepciones extends Model{
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    public function obtenerClientesPercepcionesContador(){        
        $sql=' Select  Count(*) as "contador" ';              
        $from='from "OCRD" ';        
        $where=$this->obtenerCondicion();       
        $sql_hana=$sql." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return $resultado;
    }

    public function obtenerClientePercepciones($equipo,$usuario,$salto=0,$limite=1000){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCampos();        
        $from=' from "OCRD" ';
        $where=$this->obtenerCondicion();
        $order='order by "CardCode"';
        $limite=" limit ".$limite." OFFSET ".$salto;
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        Yii::error(json_encode($resultado));
        return $resultado;
    }
    private function obtenerCondicion($almacen='',$texto=''){
        $condicion="Where \"CardType\"='C' or \"QryGroup1\" = 'Y'";
        return $condicion;
    }
    private  function obtenerCampos(){
        $sql='  "CardCode", 
                "CardType", 
                "QryGroup1", 
                "QryGroup2", 
                "QryGroup3", 
                "QryGroup4", 
                "QryGroup6", 
                "QryGroup7", 
                "QryGroup8", 
                "LicTradNum", 
                \'\' as "U_EXX_TIPOPERS", 
                \'\' as "U_EXX_PERCOM", 
                \'\' as "U_EXX_PERCDI"
        ';
        return $sql;
    }

    
}
