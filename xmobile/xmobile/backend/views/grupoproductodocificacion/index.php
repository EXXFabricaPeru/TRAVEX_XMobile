<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;$this->title = 'Grupoproductodocificacions';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="grupoproductodocificacion-index">
	<?php
	if(Yii::$app->session->get('NIVEL')=='2'){
	?>
	<p class="text-right"> <button class="btn btn-success" id="btn-create">Nuevo grupo producto dosificacion</button>
    </p>
	<?php
	}
	?>
	
<?php Pjax::begin(['id' => 'Grupoproductodocificacion-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'nombre',

             [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            $contenido='';
                            if(Yii::$app->session->get('NIVEL')=='2'){
                                    $contenido= '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                   // . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '';
							}
							return  $contenido;
                        }
             ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Grupoproductodocificacion/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


