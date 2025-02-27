<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;$this->title = 'Poligonocabeceras';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="poligonocabecera-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Poligonocabecera' </button>
    </p>
<?php Pjax::begin(['id' => 'Poligonocabecera-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'nombre',
            [
                'attribute' => 'Territorio',
                'filter' => \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'),
                'value' => function($data) {
                    if (isset(\backend\models\Territorios::findOne(["TerritoryID" =>$data->territoryid])->Description))
                        return \backend\models\Territorios::findOne(["TerritoryID" =>$data->territoryid])->Description;
                    else
                        return NULL;
                }
            ],
            'usuario',
            'status',
            'dateUpdate',
             [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                    . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '<button title="Cargar clientes" class="btn-link btn-grid-action-poligonocliente" value="' . $data->id . '" ><i class="fas fa-address-book text-success"></i></button> ';
                        }
             ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Poligonocabecera.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Poligonocliente.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50" async defer></script>
                                                    <!-- AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50 -->


