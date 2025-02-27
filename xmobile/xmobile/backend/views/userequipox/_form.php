<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Viusuariopersona;
$modeluser=Viusuariopersona::find()->asArray()->all();
$arr= array();
foreach ($modeluser as $key => $value) {
    //array_push($arr, $value['id']);
    $arrUser[$value['id']]=$value['nombreCompleto'].' - '.$value['username'];
}
?>
<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'Userequipox-form']); ?>
    <div class="row">
        <div class="col-md-12">  
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\User::find()->all(), 'id', 'username'); ?>
            <?= $form->field($model, 'userId')->dropDownList($arrUser, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-userId"></span>
        </div> 
        <div class="col-md-12">  
            <?= $form->field($model, 'tiempo')->hiddenInput(['value' => date("Y-m-d H:m:s")])->label(false); ?>
            <span class="text-danger text-clear" id="error-tiempo"></span>
        </div>  
        <div class="col-md-12">   
            <?= $form->field($model, 'equipoxId')->hiddenInput(['value' => Yii::$app->session->get('IDEQUIPO')])->label(false); ?>
            <span class="text-danger text-clear" id="error-equipoxId"></span>
        </div> 
    </div>
    <?php ActiveForm::end(); ?>
</div>
