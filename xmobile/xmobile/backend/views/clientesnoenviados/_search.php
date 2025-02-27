<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ClientesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clientes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'CardCode') ?>

    <?= $form->field($model, 'CardName') ?>

    <?= $form->field($model, 'CardType') ?>

    <?= $form->field($model, 'Address') ?>

    <?php // echo $form->field($model, 'CreditLimit') ?>

    <?php // echo $form->field($model, 'MaxCommitment') ?>

    <?php // echo $form->field($model, 'DiscountPercent') ?>

    <?php // echo $form->field($model, 'PriceListNum') ?>

    <?php // echo $form->field($model, 'SalesPersonCode') ?>

    <?php // echo $form->field($model, 'Currency') ?>

    <?php // echo $form->field($model, 'County') ?>

    <?php // echo $form->field($model, 'Country') ?>

    <?php // echo $form->field($model, 'CurrentAccountBalance') ?>

    <?php // echo $form->field($model, 'NoDiscounts') ?>

    <?php // echo $form->field($model, 'PriceMode') ?>

    <?php // echo $form->field($model, 'FederalTaxId') ?>

    <?php // echo $form->field($model, 'PhoneNumber') ?>

    <?php // echo $form->field($model, 'ContactPerson') ?>

    <?php // echo $form->field($model, 'PayTermsGrpCode') ?>

    <?php // echo $form->field($model, 'Latitude') ?>

    <?php // echo $form->field($model, 'Longitude') ?>

    <?php // echo $form->field($model, 'GroupCode') ?>

    <?php // echo $form->field($model, 'User') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'DateUpdate') ?>

    <?php // echo $form->field($model, 'GroupName') ?>

    <?php // echo $form->field($model, 'U_XM_DosificacionSocio') ?>

    <?php // echo $form->field($model, 'Territory') ?>

    <?php // echo $form->field($model, 'DiscountRelations') ?>

    <?php // echo $form->field($model, 'Mobilecod') ?>

    <?php // echo $form->field($model, 'StatusSend') ?>

    <?php // echo $form->field($model, 'CardForeignName') ?>

    <?php // echo $form->field($model, 'Phone2') ?>

    <?php // echo $form->field($model, 'Cellular') ?>

    <?php // echo $form->field($model, 'EmailAddress') ?>

    <?php // echo $form->field($model, 'MailAdress') ?>

    <?php // echo $form->field($model, 'Properties1') ?>

    <?php // echo $form->field($model, 'Properties2') ?>

    <?php // echo $form->field($model, 'Properties3') ?>

    <?php // echo $form->field($model, 'Properties4') ?>

    <?php // echo $form->field($model, 'Properties5') ?>

    <?php // echo $form->field($model, 'Properties6') ?>

    <?php // echo $form->field($model, 'Properties7') ?>

    <?php // echo $form->field($model, 'FreeText') ?>

    <?php // echo $form->field($model, 'img') ?>

    <?php // echo $form->field($model, 'Industry') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
