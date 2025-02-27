<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TiposPrecios */

$this->title = Yii::t('app', 'Create Tipos Precios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipos Precios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipos-precios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
