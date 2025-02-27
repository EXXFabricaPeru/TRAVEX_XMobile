<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="productos-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'ItemCode',
            'ItemName',
            'ItemsGroupCode',
            'ForeignName',
            'CustomsGroupCode',
            'BarCode',
            'PurchaseItem',
            'SalesItem',
            'InventoryItem',
            'UserText',
            'SerialNum',
            'QuantityOnStock',
            'QuantityOrderedFromVendors',
            'QuantityOrderedByCustomers',
            'ManageSerialNumbers',
            'ManageBatchNumbers',
            'SalesUnit',
            'SalesUnitLength',
            'SalesUnitWidth',
            'SalesUnitHeight',
            'SalesUnitVolume',
            'PurchaseUnit',
            'DefaultWarehouse',
            'ManageStockByWarehouse',
            'ForceSelectionOfSerialNumber',
            'Series',
            'UoMGroupEntry',
            'DefaultSalesUoMEntry',
            'User',
            'Status',
            'DateUpdate',
            'Manufacturer',
            'NoDiscounts',
            'created_at',
            'updated_at',
            'combo',
            'producto_std1',
            'producto_std2',
            'producto_std3',
            'producto_std4',
            'producto_std5',
            'producto_std6',
            'producto_std7',
            'producto_std8',
            'producto_std9',
            'producto_std10',
        ],
    ]) ?>

</div>
