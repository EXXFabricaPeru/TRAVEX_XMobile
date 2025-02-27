<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\admin\models\Localidad */

$this->title = Yii::t('app', 'Create Localidad');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Localidads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="localidad-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
