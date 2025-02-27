<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">
    <div class="row">
        <div class="col-md-5">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a(Yii::t('app', 'Modificar'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?=
                Html::a(Yii::t('app', 'Eliminar'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            </p>

            <?=
            DetailView::widget([
                'model' => $model,
                'attributes' => [
                    // 'id',
                    'username',
                    'auth_key',
                    //'password_hash',
                    //'password_reset_token',
                    'status',
                    'created_at',
                    'updated_at',
                    //'verification_token',
                    //'access_token:ntext',
                    'idPersona',
                    'estadoUsuario',
                    'fechaUMUsuario',
                    'plataformaUsuario',
                    'plataformaPlataforma',
                    'plataformaEmei',
                ],
            ])
            ?>
        </div>
    </div>


</div>
