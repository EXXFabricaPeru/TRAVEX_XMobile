<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ConfigUsuarios */

$this->title = Yii::t('app', 'Create Config Usuarios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Config Usuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-usuarios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
