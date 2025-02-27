<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Detalledocumentos */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Detalledocumentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="detalledocumentos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'DocEntry',
            'DocNum',
            'LineNum',
            'BaseType',
            'BaseEntry',
            'BaseLine',
            'LineStatus',
            'ItemCode',
            'Dscription',
            'Quantity',
            'OpenQty',
            'Price',
            'Currency',
            'DiscPrcnt',
            'LineTotal',
            'WhsCode',
            'CodeBars',
            'PriceAfVAT',
            'TaxCode',
            'U_4DESCUENTO',
            'U_4LOTE',
            'GrossBase',
            'idDocumento',
            'fechaAdd',
            'unidadid',
            'tc',
            'idCabecera',
            'idProductoPrecio',
            'DiscMonetary',
            'LineTotalPay',
            'SalesUnitLength',
            'SalesUnitWidth',
            'SalesUnitHeight',
            'SalesUnitVolume',
            'DiscTotalPrcnt',
            'DiscTotalMonetary',
            'ICET',
            'ICEE',
            'ICEP',
            'TreeType',
            'actsl',
            'BaseDocEntry',
            'BaseDocLine',
            'BaseDocType',
            'BaseDocumentReference',
            'GrossPrice',
            'WarehouseCode',
            'CorrectionInvoiceItem',
            'Status',
            'Stock',
            'TargetAbsEntry',
        ],
    ]) ?>

</div>
