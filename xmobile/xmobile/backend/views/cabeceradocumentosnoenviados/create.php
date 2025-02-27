<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Cabeceradocumentos */

$this->title = 'Create Cabeceradocumentos';
$this->params['breadcrumbs'][] = ['label' => 'Cabeceradocumentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cabeceradocumentos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
