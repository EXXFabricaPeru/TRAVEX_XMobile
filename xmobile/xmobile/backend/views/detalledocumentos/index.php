<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DetalledocumentosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Detalledocumentos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="detalledocumentos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php Html::a('Create Detalledocumentos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
			//'ItemCode',
            'Dscription',
            'DocEntry',
            //'DocNum',
            'LineNum',
            //'BaseType',
            //'BaseEntry',
            'BaseLine',
            'LineStatus',

            'Quantity',
            //'OpenQty',
            'Price',
           
			'LineTotal',
            'DiscPrcnt',
			'U_4DESCUENTO',
			//'DiscMonetary',
			
            'LineTotalPay',
			'Currency',
            //'WhsCode',
            //'CodeBars',
            //'PriceAfVAT',
            //'TaxCode',
            //'U_4DESCUENTO',
            //'U_4LOTE',
            //'GrossBase',
            //'idDocumento',
            //'fechaAdd',
            //'unidadid',
            //'tc',
            'idCabecera',
            //'idProductoPrecio',
            //'DiscMonetary',
            //'LineTotalPay',
            //'SalesUnitLength',
            //'SalesUnitWidth',
            //'SalesUnitHeight',
            //'SalesUnitVolume',
            //'DiscTotalPrcnt',
            //'DiscTotalMonetary',
            //'ICET',
            //'ICEE',
            //'ICEP',
            //'TreeType',
            //'actsl',
            //'BaseDocEntry',
            //'BaseDocLine',
            //'BaseDocType',
            //'BaseDocumentReference',
            //'GrossPrice',
            //'WarehouseCode',
            //'CorrectionInvoiceItem',
            //'Status',
            //'Stock',
            //'TargetAbsEntry',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
