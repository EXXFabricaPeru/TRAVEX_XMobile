<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Cabeceradocumentos */

$this->title = 'Update Cabeceradocumentos: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cabeceradocumentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cabeceradocumentos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
