<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class tipocambio extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    public function obtenerTipoCambio(){
        $aux_monedaLocal="Select id from sys_vi_monedas where local=1";
        $aux_monedaSistema="Select id from sys_vi_monedas where sistema=1";
        $monedalocal=Yii::$app->db->createCommand($aux_monedaLocal)->queryOne();
        $monedasistema=Yii::$app->db->createCommand($aux_monedaSistema)->queryOne();
        $monedalocal=$monedalocal["id"];
        $monedasistema=$monedasistema["id"];
        
        $sql="SELECT 
            \"Rate\" as \"ExchangeRate\",
            $monedasistema as \"ExchangeRateFrom\",
            $monedalocal as \"ExchangeRateTo\",
            To_Date(\"RateDate\") as \"ExchangeRateDate\",
            '1' as \"User\",
            '1' as \"Status\",
            To_Date(\"UpdateDate\") as \"DateUpdate\" 
            from \"ORTT\" 
            WHERE \"RateDate\" >= CURRENT_DATE
            ORDER BY \"RateDate\" asc
         ";

        $resultado=  $this->hana->ejecutarconsultaAll($sql);
        return $resultado;
    }
    

}