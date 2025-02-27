<?php
namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
class Common extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    /**
    *data para actualizar campos dinamicos
    *
    */
    public function updateCampoDinamicos($dataTable,$valueValid){
        Yii::error("sqlquery listo para actualizar".json_encode($dataTable)." - id:".json_encode($valueValid));
        foreach ($dataTable as $data ) {
                $table=$data["tabla"];
                $campo=$data["cmidd"];
                $value=$data["valor"];

                $campoValid=$valueValid["campo"];
                $valueCampo=$valueValid["value"];
                $sqlUpdate="UPDATE $table SET $campo='$value' Where $campoValid='$valueCampo' ";
                Yii::error("sqlquery".$sqlUpdate);
                Yii::$app->db->createCommand($sqlUpdate)->execute();
            }    
        
            
        return true;
    }
    public function getCount(){
      
    }

}