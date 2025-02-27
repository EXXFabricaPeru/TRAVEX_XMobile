<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;$this->title = 'Usuarioconfiguracions';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="usuarioconfiguracion-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Usuarioconfiguracion' </button>
    </p>
<?php Pjax::begin(['id' => 'Usuarioconfiguracion-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'idEstado',
            'idTipoPrecio',
            'estadoListaPrecio',
            'idTipoImpresora',
            //'ruta',
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
            //'almacenes',
            //'idUser',
            //'modMoneda',
            //'estadoAlmacenes',
            //'ctaTarjeta',
            //'ctaFcTarjeta',
            //'crearCliente',
            //'moneda',
            //'territorio',
            //'grupoCliente',
            //'listaPrecios',
            //'descuentos',
            //'totalDescuentoDocumento',
            //'editarDocumento',
            //'aperturaCaja',
            //'cierreCaja',
            //'totalDescuento',
            //'condicionPago',
            //'ctaanticipo',
            //'multiListaPrecios',

             [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                    . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '';
                        }
             ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Usuarioconfiguracion/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


