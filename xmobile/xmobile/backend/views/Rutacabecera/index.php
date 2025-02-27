<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
$this->title = 'Rutacabeceras';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="rutacabecera-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Ruta </button>
    </p>
    <?php Pjax::begin(['id' => 'Rutacabecera-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            //'id',
            'fecha',
            'nombre',
            'vendedor'
            /*[
                'attribute' => 'idvendedor',
                'format' => 'raw',
                'value' => function($data) {
                    $DataUser = ArrayHelper::map(backend\models\User::find()
                    ->where(['id' => $data->idvendedor])
                    ->all(), 'id', 'username');
                
                    foreach ($DataUser as $key => $value) {
                        return $value;

                    }
                    
                }
            ]*/,
            //'idvendedor',
           
            //'idclienteinicial',
            //'latitud',
          //  'longitud',
          
            //'status',
            //'dateUpdate',
            [   
                'attribute' => 'status',
                'filter' => array("1" => "ACTIVO", "0" => "INACTIVO"),
                'value' => function($data) {
                    if($data->status=='0')return 'INACTIVO';
                    else return 'ACTIVO';
                       
                }
            ],

            [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                  //  . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '';
                        }
             ]
        ],
    ]); ?>
<?php Pjax::end(); ?>

</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Rutacabecera.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=false&key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50"></script>

