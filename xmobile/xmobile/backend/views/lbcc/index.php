<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;$this->title = 'Lbccs';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="lbcc-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Lbcc' </button>
    </p>
<?php Pjax::begin(['id' => 'Lbcc-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'Code',
            'Name',
            'DocEntry',
            'Canceled',
            //'Object',
            //'LogInst',
            //'UserSign',
            //'Transfered',
            //'CreateDate',
            //'CreateTime',
            //'UpdateDate',
            //'UpdateTime',
            //'DataSource',
            //'U_NumeroAutorizacion',
            //'U_ObjType',
            //'U_Estado',
            //'U_PrimerNumero',
            //'U_NumeroSiguiente',
            //'U_UltimoNumero',
            //'U_Series',
            //'U_SeriesName',
            //'U_FechaLimiteEmision',
            //'U_LlaveDosificacion',
            //'U_Leyenda',
            //'U_Leyenda2',
            //'U_TipoDosificacion',
            //'U_Sucursal',
            //'U_EmpleadoVentas',
            //'U_GrupoCliente',
            //'U_Actividad',
            //'User',
            //'Status',
            //'DateUpdate',
            //'equipoId',

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
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Lbcc/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


