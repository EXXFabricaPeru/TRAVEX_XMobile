<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class Gestionbanco extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    public function obtenerBancos($salto=0,$limite=1000){
        $sql="SELECT distinct(\"AbsEntry\") as \"id\",\"BankCode\" ,\"AcctName\" as \"BankName\", 0 as \"CountryCode\" from \"ADS1\" where \"Country\"='PE' limit {$limite} OFFSET {$salto}  ";
        $resultado=  $this->hana->ejecutarconsultaAll($sql);
        return $resultado;
    }
    public function obtenerBancosContador(){
        $sql="SELECT COUNT(*) as Contador  from \"ADS1\" where \"Country\"='PE' ";
        $resultado=  $this->hana->ejecutarconsultaOne($sql);
        return $resultado;
    }

}