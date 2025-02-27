<?php

//use Yii;
//use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;


header('Content-Type: application/json');
if(isset($_POST['CONDICION'])){
    switch ($_POST['CONDICION']) {
        
        case 'USUARIOMOVILTERRITORIO':
			 $Data = ArrayHelper::map(backend\models\Usuariomovilterritorio::find()
			   ->where(['idUser' => $_POST['ID']])
			   ->all(), 'id', 'territorio');
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

