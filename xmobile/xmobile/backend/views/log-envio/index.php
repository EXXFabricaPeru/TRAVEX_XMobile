<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Log Envios';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
<div class="row">
   
</div>
<div class="log-envio-index">
        <div class="col-md-12">
            <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Usuariopersona</button>
            </p>
         </div>
        
    <!--p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Log Envio' </button>
    </p-->
    <?php Pjax::begin(['id' => 'Log Envio-list']); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //'idlog',
            'documento',
            'proceso',
            // 'envio:ntext',
            'fecha',
            'respuesta:ntext',
            // 'ultimo',
            // 'endpoint',
        /* [
          'attribute' => 'Acciones',
          'format' => 'raw',
          'value' => function($data) {
          return ''
          . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->idlog . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
          . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->idlog . '" ><i class="fas fa-edit text-info"></i></button>'
          . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->idlog . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
          . '';
          }
          ] */
          ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Log Envio/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


