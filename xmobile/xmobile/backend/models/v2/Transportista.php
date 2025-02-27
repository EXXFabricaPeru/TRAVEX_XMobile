<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class Transportista extends Model{
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    public function obtenerTransportistaContador(){        
        $sql=' Select  Count(*) as "contador" ';              
        $from=" from \"OCRD\" C 
        inner join \"OCPR\" CD on C.\"CardCode\"=CD.\"CardCode\"
        left join \"@EXX_VEHICU\" V on  C.\"CardCode\" = V.\"U_EXX_CODTRA\" ";    
        $where=$this->obtenerCondicion();       
        $sql_hana=$sql." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return $resultado;
    }

    public function obtenerTransportista($equipo,$usuario,$salto=0,$limite=1000){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCampos();        
        $from=" from \"OCRD\" C 
        inner join \"OCPR\" CD on C.\"CardCode\"=CD.\"CardCode\"
        left join \"@EXX_VEHICU\" V on  C.\"CardCode\" = V.\"U_EXX_CODTRA\" ";
        $where=$this->obtenerCondicion();
        $order='';
        $limite=" limit ".$limite." OFFSET ".$salto;
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        Yii::error(json_encode($resultado));
        return $resultado;
    }
    private function obtenerCondicion($almacen='',$texto=''){
        $condicion=" where C.\"QryGroup1\"='Y' ";
        return $condicion;
    }
    private  function obtenerCampos(){
        $caracter = "''''";
        $caracterNew = "''''''";

        $sql="  C.\"CardCode\",
                REPLACE(C.\"CardName\", ".$caracter.", ".$caracterNew.") \"CardName\",
                C.\"LicTradNum\",
                C.\"Address\",
                CD.\"Name\",
                CD.\"Notes1\",
                V.\"U_EXX_PLAVEH\",
                V.\"U_EXX_MARVEH\",
                V.\"U_EXX_PLATOL\"
        ";
        return $sql;
    }

    
}
