<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\TiposImpresoras */

$this->title = Yii::t('app', 'Create Tipos Impresoras');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tipos Impresoras'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipos-impresoras-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
