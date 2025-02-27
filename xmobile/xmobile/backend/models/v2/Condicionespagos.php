<?php
namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class Condicionespagos extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    public function getAll($salto=0,$limite=1000){
        $sql="SELECT 
        \"GroupNumber\",
        \"PaymentTermsGroupName\",
        \"StartFrom\",
        \"NumberOfAdditionalMonths\",
        \"NumberOfAdditionalDays\",
        \"CreditLimit\",
        \"GeneralDiscount\",
        \"InterestOnArrears\",
        \"PriceListNo\",
        \"LoadLimit\",
        \"OpenReceipt\",
        \"DiscountCode\",
        \"DunningCode\",
        \"BaselineDate\",
        \"NumberOfInstallments\",
        \"NumberOfToleranceDays\",
        \"U_UsaLc\",
        \"User\",
        \"DateUpdated\",
        \"Status\"
          from \"OCTG\"   limit {$limite} OFFSET {$salto}   "; // WHERE  \"Status\"='1' 
        $resultado=  $this->hana->ejecutarconsultaAll($sql);
        return $resultado;
    }
    public function getCount(){
        $sql="SELECT COUNT(*) as Contador  from \"OCTG\"  ";
        $resultado=  $this->hana->ejecutarconsultaOne($sql);
        return $resultado;
    }

}