<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
$this->title = 'Anulaciondocmovils';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="anulaciondocmovil-index">

<p class="text-right"> <!--<button class="btn btn-success" id="btn-create">'Crear Anulaciondocmovil' </button>-->
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            //'id',
            
            [
                'header'=>'EST',
                'format' => 'raw',
                'value'=>function($data){
                    /*if($data->estado == 3 AND $data->canceled==0){
                        return '<span class="label label-success">Sap</span>';
                    }
                    else if($data->estado == 2 ){
                        return '<span class="label label-warning">Midd</span>';
                    }
                    else if($data->estado == 3 AND $data->canceled==3){*/
                        return '<span class="label label-danger">Cancel</span>';
                    //}
                }
            ],
            'fechaRegistro',
            'usuario',
            'docDate',
            'docEntry',
            'docType',
            'docNum',
            'motivoAnulacion',
            'motivoAnulacionComentario',
            'origen',

            //'estado',
            //'idUser',

             /*[
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                    . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '';
                        }
             ]*/
        ],
    ]); ?>


</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Anulaciondocmovil/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


