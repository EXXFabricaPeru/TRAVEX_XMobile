<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConfigUsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Config Usuarios');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Config Usuarios'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'idEstado',
            'idTipoPrecio',
            'idTipoImpresora',
            'ruta',
            //'ctaEfectivo',
            //'ctaCheque',
            //'ctaTransferencia',
            //'ctaFcEfectivo',
            //'ctaFcCheque',
            //'ctaFcTransferencia',
            //'sreOfertaVenta',
            //'sreOrdenVenta',
            //'sreFactura',
            //'sreFacturaReserva',
            //'sreCobro',
            //'flujoCaja',
            //'modInfTributaria',
            //'codEmpleadoVenta',
            //'codVendedor',
            //'nombre',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
