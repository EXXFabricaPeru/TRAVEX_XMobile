<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class Banco extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }//where \"UsrNumber2\"='POS'
    public function obtenerBancos($salto=0,$limite=1000){
        $sql="SELECT \"AbsEntry\" as \"id\",\"BankCode\" as \"codigo\",\"GLAccount\" as \"cuenta\",\"AcctName\" as \"nombre\" from \"DSC1\" where \"Country\"!='PE'  ORDER BY  \"AbsEntry\" ASC limit {$limite} OFFSET {$salto}  ";
        $resultado=  $this->hana->ejecutarconsultaAll($sql);
        return $resultado;
    }
    public function obtenerBancosContador(){
       // $sql="SELECT COUNT(*) as \"contador\"  from \"ADS1\" where \"Country\"='BO' GROUP BY \"AbsEntry\" ";
        $sql="SELECT COUNT(*) as \"contador\"  from \"DSC1\" where \"Country\"='PE'";
        $resultado=  $this->hana->ejecutarconsultaOne($sql);
        return $resultado;
    }
    public function obtenerGestionBancos($salto=0,$limite=1000){
        $sql="SELECT \"AbsEntry\" as \"id\",\"BankCode\" ,\"AcctName\" as \"BankName\", 0 as \"CountryCode\" from \"DSC1\" where \"UsrNumber2\"='POS'  ORDER BY  \"AbsEntry\" ASC  limit {$limite} OFFSET {$salto}  ";
        $resultado=  $this->hana->ejecutarconsultaAll($sql);
        return $resultado;
    }
    public function obtenerGestionBancosContador(){
        $sql="SELECT COUNT(*) as \"contador\"  from \"DSC1\" where \"UsrNumber2\"='POS' GROUP BY \"AbsEntry\"";
        $resultado=  $this->hana->ejecutarconsultaOne($sql);
        return $resultado;
    }

}