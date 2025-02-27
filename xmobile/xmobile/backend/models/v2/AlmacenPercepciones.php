<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class Almacenpercepciones extends Model{
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    public function obtenerAlmacenPercepcionesContador(){        
        $sql=' Select  Count(*) as "contador" ';              
        $from='from "OWHS" ';        
        $where=$this->obtenerCondicion();       
        $sql_hana=$sql." ".$from." ".$where;
        $resultado=  $this->hana->ejecutarconsultaOne($sql_hana);
        return $resultado;
    }

    public function obtenerAlmacenPercepciones($equipo,$usuario,$salto=0,$limite=1000){
        if($salto==""){
            $salto=0;
        }
        if($limite==""){
            $limite=1000;
        }
        $sql=" Select ";
        $campos=$this->obtenerCampos();        
        $from=' from "OWHS" ';
        $where=$this->obtenerCondicion();
        $order='';
        $limite=" limit ".$limite." OFFSET ".$salto;
        $sql_hana=$sql." ". $campos." ".$from." ".$where." ".$order." ".$limite;
        $resultado=  $this->hana->ejecutarconsultaAll($sql_hana);
        Yii::error(json_encode($resultado));
        return $resultado;
    }
    private function obtenerCondicion($almacen='',$texto=''){
        $condicion="";
        return $condicion;
    }
    private  function obtenerCampos(){
        $sql=' "WhsCode",
        "WhsName",
        "U_EXX_PERDGH"
        ';
        return $sql;
    }

    
}
