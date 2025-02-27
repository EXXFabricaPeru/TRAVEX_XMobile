<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Tipo de papeles';
?>
<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>

<div class="row">
    <div class="col-md-6">
	<?php
	if(Yii::$app->session->get('NIVEL')=='2'){
	?>
        <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear tipo de papel</button></p>
	<?php
	}
	?>
        <?php Pjax::begin(['id' => 'Tipopapel-list']); ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                // 'id',
                'nombre',
                'descripcion',
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
						return $contenido;
                    }
                ]
            ],
        ]);
        ?>

        <?php Pjax::end(); ?>
    </div>
</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Tipopapel.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


