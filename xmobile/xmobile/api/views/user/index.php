<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Usuarios');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <div class="row">
        <div class="col-md-8">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a(Yii::t('app', 'Crear usuario'), ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    // ['class' => 'yii\grid\SerialColumn'],
                    //'id',
                    'username',
                    //'auth_key',
                    //'password_hash',
                    //'password_reset_token',
                    //'status',
                    //'created_at',
                    //'updated_at',
                    //'verification_token',
                    //'access_token:ntext',
                    'idPersona',
                    //'estadoUsuario',
                    //'fechaUMUsuario',
                    //'plataformaUsuario',
                    //'plataformaPlataforma',
                    [
                        'attribute' => 'plataformaPlataforma',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return $data->plataformaPlataforma == 0 ? "Web" : $data->plataformaPlataforma;
                        }
                    ],
                    'plataformaEmei',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>

            <?php Pjax::end(); ?>

        </div>
    </div>


</div>
