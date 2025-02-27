<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

header('Content-Type: application/json');
if(isset($_POST['CONDICION'])){
    switch ($_POST['CONDICION']) {
        case 'PERMISOSMIDDLE':
           $Data = ArrayHelper::map(backend\models\Permisosmiddle::find()
           ->where(['idUsuario' => $_POST['ID']])
           ->all(), 'id', 'permisomenu');
           if(count($Data)){
               echo (json_encode($Data));
           }
           else{
             echo "0";
           }
            break;
        default:
           echo "error! no existe condicion";
    }
}
else{
    echo "error! comuniquese con su administrador de sistemas";
}



?>

