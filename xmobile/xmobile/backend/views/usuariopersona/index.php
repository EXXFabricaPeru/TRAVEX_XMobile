<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Usuariopersonas';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowllaves" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
<div class="row">
    <div class="col-md-10">

		<?php
		if(Yii::$app->session->get('NIVEL')=='2'){
		?>
        <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Usuario persona</button>
        </p>
		<?php
		}
		?>
	
        <?php Pjax::begin(['id' => 'Usuariopersona-list']); ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //  'idPersona',
                'nombrePersona',
                'apellidoPPersona',
                'apellidoMPersona',
                'documentoIdentidadPersona',
                'fechaUMPersona',
                //'estadoPersona',
                /*  [
                  'attribute' => 'estadoPersona',
                  'filter' => array("1" => "Activados", "2" => "Desactivados"),
                  'value' => function($data) {
                  if ($data->estadoPersona == 1)
                  return 'Activados';
                  else
                  return 'Desactivados';
                  }
                  ], */
                [
                    'attribute' => 'Acciones',
                    'format' => 'raw',
                    'value' => function($data) {
                            $contenido='';
                            if(Yii::$app->session->get('NIVEL')=='2'){
                                $contenido= '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->idPersona . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->idPersona . '" ><i class="fas fa-edit text-info"></i></button>'
                                //. '<button title="Accesos de usuario" class="btn-link btn-grid-action-key" value="' . $data->idPersona . '" ><i class="fas fa-key  text-warning"></i></button>'
                                // . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->idPersona . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                . '';
							}
							return  $contenido;
                    }
                ]
            ],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Usuariopersona.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


