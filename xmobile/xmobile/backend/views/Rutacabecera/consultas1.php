<?php

//use Yii;
//use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;


header('Content-Type: application/json');
if(isset($_POST['CONDICION'])){
    switch ($_POST['CONDICION']) {
        case 'DOCUMENTOSIMPORTADOS':
            $DataServicios = Yii::$app->db->createCommand("CALL pa_documentosimportadosrutas('".$_POST['usuario']."','0','0','".$_POST['fechaPicking']."')")->queryAll();
            echo (json_encode ($DataServicios));
            break;
        default:
           echo "error! no existe condicion";
    }
}
else{
    echo "error! comuniquese con su administrador de sistemas";
}



?>

