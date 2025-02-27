<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
$this->title = 'Xmfcabezerapagos';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="xmfcabezerapagos-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Xmfcabezerapagos' </button>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'nro_recibo',
            'correlativo',
            'usuario',
            'documentoId',
            //'fecha',
            //'hora',
            //'monto_total',
            //'tipo',
            //'otpp',
            //'tipo_cambio',
            //'moneda',
            //'cliente_carcode',
            //'razon_social',
            //'nit',
            //'estado',
            //'cancelado',
            //'tipoTarjeta',
            //'equipo',
            //'fechaSistema',
            //'TransId',
            //'latitud',
            //'longitud',
            //'idDocumento',

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


</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Xmfcabezerapagos/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


