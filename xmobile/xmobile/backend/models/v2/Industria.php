<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class industria extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    public function obtenerIndustrias($salto=0,$limite=1000){
        if($salto==""){
            $salto=0;
        }
        $sql="SELECT \"IndCode\" as \"id\",\"IndName\" as \"nombre\",\"IndDesc\" as \"descripcion\", '1' as \"User\", '1' as \"Status\",'0000-00-00' as \"DateUpdate\" from \"OOND\" limit {$limite} OFFSET {$salto}  ";
        $resultado=  $this->hana->ejecutarconsultaAll($sql);
        return $resultado;
    }
    public function obtenerIndustriasContador(){
        $sql="SELECT COUNT(*) as Contador  from \"OOND\"  ";
        $resultado=  $this->hana->ejecutarconsultaOne($sql);
        return $resultado;
    }

}