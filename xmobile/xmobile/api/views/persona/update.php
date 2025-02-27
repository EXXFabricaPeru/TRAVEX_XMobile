<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Persona */

$this->title = Yii::t('app', 'Update Persona: {name}', [
    'name' => $model->idPersona,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Personas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idPersona, 'url' => ['view', 'id' => $model->idPersona]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="persona-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
