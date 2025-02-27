<?php
use yii\helpers\Html;

$ArrayData = \yii\helpers\ArrayHelper::map(backend\models\Cuentascontables::find()->all(), 'Code', 'Name');

if(isset($_POST['CONDICION'])){
    echo (json_encode ($ArrayData));
}
else{


?>
<div class="equipoxcuentascontables-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<?php
}
?>
