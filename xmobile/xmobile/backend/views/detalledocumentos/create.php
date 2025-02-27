<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Detalledocumentos */

$this->title = 'Create Detalledocumentos';
$this->params['breadcrumbs'][] = ['label' => 'Detalledocumentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="detalledocumentos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
