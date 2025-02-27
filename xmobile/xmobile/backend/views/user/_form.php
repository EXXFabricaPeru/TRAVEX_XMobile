<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Vipersona;
$modeluser=Vipersona::find()->asArray()->all();
?>
<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'User-form']); ?>
    <div class="row">
        <div class="col-md-12"><?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-username"></span>
        </div> 
        <?php //if (!isset($up)) { ?>
            <div class="col-md-12">  
                <?= $form->field($model, 'password')->passwordInput() ?>
                <span class="text-danger text-clear" id="error-password"></span>
            </div> 
        <?php //} ?>
        <div class="col-md-12">   
            <?php 
                $arr= array();
                foreach ($modeluser as $key => $value) {
                    //array_push($arr, $value['id']);
                    $arr[$value['idPersona']]=$value['nombreCompleto'];
                } 
            ?>
            <?= $form->field($model, 'idPersona')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-idPersona"></span>
        </div> 
        <div class="col-md-12">  
            <?= $form->field($model, 'estadoUsuario')->dropDownList(['1' => 'Activo', '0' => 'Inactivo', "2" => "Inhabilitado"], ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-estadoUsuario"></span>
        </div> 
    </div>
    <?php ActiveForm::end(); ?>
</div>
