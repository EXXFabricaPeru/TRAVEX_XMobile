<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TiposEstados */

$this->title = Yii::t('app', 'Create Tipos Estados');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipos Estados'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipos-estados-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
