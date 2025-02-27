<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<span id="valueSucursalID" data-id="<?= $id; ?>"></span>
<div class="row">
    <div class="col-md-4">
        <h4>Sucursal</h4>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                //  'id',
                'nombre',
                'direccion',
                //'descripcion:ntext',
                //'codigo',
                'empresa',
                'telefonouno',
                'telefonodos',
                'pais',
                'ciudad',
                'leyendauno',
                'leyendados',
            ],
        ])
        ?>
    </div>
    <div class="col-md-8">
        <h4>Asignación de almacenes </h4>

        <div class="window" style="display: none"></div>
        <div id="windowpdf" style="display: none"></div>
        <div id="windowEliminar" style="display: none"> <br/><br/>
            <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
        </div>
        <div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
        <div class="sucursalalmacenes-index">

            <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Sucursalalmacenes</button>
            </p>
            <?php Pjax::begin(['id' => 'Sucursalalmacenes-list']); ?>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //'id',
                    //'sucursalId',
                    'almacenesId',
                    'almacenesId',
                    'tiempo',
                    [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                    //. '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '';
                        }
                    ]
                ],
            ]);
            ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Sucursalalmacenes.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
