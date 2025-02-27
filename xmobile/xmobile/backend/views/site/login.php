<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Inicio de Sesion';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <div class="row">
        <div class="col-lg-4" align="center">
        <a href="../../../companex_refact2.2.14.apk" target="_blank"><p class="font20h enlaces_a hacer_click" style="height:30px">Xmobile apk - descargar</p></a>
        </div>
        <div class="col-lg-4" style="background-color: #fff">
            <h4><?= Html::encode($this->title) ?></h4>
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['autocomplete' => 'off']]); ?>
            <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Usuario:') ?>
            <?= $form->field($model, 'password')->passwordInput()->label('Contraseña:') ?>
            <div class="form-group">
                <?= Html::submitButton('Ingresar', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-4"></div>
    </div>
</div>
