<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = 'Poligonocabeceraterritorios';

?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="poligonocabeceraterritorio-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Ruta </button>
    </p>
    <?php Pjax::begin(['id' => 'Poligonocabeceraterritorio-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            //'id',
            'fechaSistema',
            //'fechaRegistro',
            'dia',
           // 'mes',
            //'idVendedor',
            'vendedor',
            //'tipoVendedor',
            //'idTerritorio',
           // 'Territorio',
            //'idPoligono',
           //'poligono',
            //'estado',
            //'tipo',
            //'idUserRegister',
            //'userRegister',
            'nombreRuta',

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
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Poligonocabeceraterritorio.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50" ></script>

<!--
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50" async defer></script>
<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=false&key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50"></script> ***error: AIzaSyAItWswM9WTXrVeRm65fSqHJFeJbXo7zcQ
-->
