<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->title = 'Productos';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
<style>
    .imgprod{
        background-color: #ccc;
        height: 100px;
        width:100px;
    }
</style>
<div class="productos-index">

    <!--p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Productos' </button>
        </p-->
    <?php Pjax::begin(['id' => 'Productos-list']); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'header' => 'Imagen',
                'value' => function($data) {
                    return '<div class="thumbnail btn-grid-action-upload" data-id="' . $data->ItemCode . '">' . Html::img(str_replace("backend", "api", \Yii::$app->request->BaseUrl) . '/imgs/prod/' . $data->ItemCode . '.jpeg', ['class' => 'imgprod']) . '</div>';
                },
                'format' => 'raw',
            ],
            //'id',
            'ItemCode',
            'ItemName',
            'ItemsGroupCode',
            //'ForeignName',
            'CustomsGroupCode',
            'BarCode',
            //'PurchaseItem',
            //'SalesItem',
            //'InventoryItem',
            //'UserText',
            //'SerialNum',
            //'QuantityOnStock',
            'QuantityOrderedFromVendors',
            'QuantityOrderedByCustomers',
            //'ManageSerialNumbers',
            //'ManageBatchNumbers',
            'SalesUnit',
            //'SalesUnitLength',
            //'SalesUnitWidth',
            //'SalesUnitHeight',
            //'SalesUnitVolume',
            //'PurchaseUnit',
            //'DefaultWarehouse',
            //'ManageStockByWarehouse',
            //'ForceSelectionOfSerialNumber',
            'Series',
            'UoMGroupEntry',
            //'DefaultSalesUoMEntry',
            //'User',
            //'Status',
            //'DateUpdate',
            //'Manufacturer',
            //'NoDiscounts',
            //'created_at',
            //'updated_at',
            'combo',
        //'producto_std1',
        //'producto_std2',
        //'producto_std3',
        //'producto_std4',
        //'producto_std5',
        //'producto_std6',
        //'producto_std7',
        //'producto_std8',
        //'producto_std9',
        //'producto_std10',

        /* [
          'attribute' => 'Acciones',
          'format' => 'raw',
          'value' => function($data) {
          return ''
          . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
          . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
          . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
          . '';
          }
          ] */
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>

<div id="dialogupload" style="display:none;">
    <form action="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['productos/upload']); ?>" class="dropzone" id="my-awesome-dropzone">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>" />
        <input id="namefoto" type="hidden" name="id" value="0" />
    </form>
</div>
<?php $this->registerJsFile(Yii::getAlias('@web') . '/js/dropzone/dist/dropzone.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?php $this->registerCssFile(Yii::getAlias('@web') . '/js/dropzone/dist/dropzone.css', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?php $this->registerJsFile(Yii::getAlias('@web') . '/scripts/imgpro.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


