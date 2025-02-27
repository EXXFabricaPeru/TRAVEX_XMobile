<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;


$var = \yii\helpers\ArrayHelper::map(backend\models\Persona::find()->all(), 'idPersona', 'nombrePersona');
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'plataformaPlataforma')->textInput(["placeholder" => "Android, IOS, web"]) ?>
            <?= $form->field($model, 'plataformaUsuario')->textInput(["placeholder" => "m,w"]) ?>
            <?= $form->field($model, 'fechaUMUsuario')->textInput(["placeholder" => date("Y-m-d")]) ?>
            <?= $form->field($model, 'estadoUsuario')->textInput(["value" => 1]) ?>
            <?= $form->field($model, 'idPersona')->dropDownList($var, ['prompt' => 'Seleccione persona']); ?>
            <?= $form->field($model, 'plataformaEmei')->textInput() ?>
            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
