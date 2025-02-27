<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
//$this->title = 'Bonificacionde1s';
use yii\widgets\Pjax;$this->title = 'Bonificacionde1s';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" style="width:100%" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>

<div id="windowLista" class="bonificacionde1-index"  style="width:100%" >

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Bonificacionde1' </button>
    </p>
    <?php Pjax::begin(['id' => 'Bonificacionde1-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

           // 'id',
           // 'Code',
            'Name',
           // 'U_ID_bonificacion',
           'U_regla',

             [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                   
                                    . '';
                        }
             ]
        ],
    ]); ?>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Bonificacionde1.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 

<?php Pjax::end(); ?>


</div>


