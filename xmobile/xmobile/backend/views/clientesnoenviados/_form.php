<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Clientes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clientes-form">

    <?php $form = ActiveForm::begin(); ?>
	    <?= $form->field($model, 'Latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'CardCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CardName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CardType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CreditLimit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'MaxCommitment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DiscountPercent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PriceListNum')->textInput() ?>

    <?= $form->field($model, 'SalesPersonCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'County')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CurrentAccountBalance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'NoDiscounts')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PriceMode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FederalTaxId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PhoneNumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ContactPerson')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PayTermsGrpCode')->textInput(['maxlength' => true]) ?>



    <?= $form->field($model, 'GroupCode')->textInput() ?>

    <?= $form->field($model, 'User')->textInput() ?>

    <?= $form->field($model, 'Status')->textInput() ?>

    <?= $form->field($model, 'DateUpdate')->textInput() ?>

    <?= $form->field($model, 'GroupName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'U_XM_DosificacionSocio')->textInput() ?>

    <?= $form->field($model, 'Territory')->textInput() ?>

    <?= $form->field($model, 'DiscountRelations')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Mobilecod')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'StatusSend')->textInput() ?>

    <?= $form->field($model, 'CardForeignName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Phone2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cellular')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'EmailAddress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'MailAdress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Properties1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Properties2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Properties3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Properties4')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Properties5')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Properties6')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Properties7')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FreeText')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Industry')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
