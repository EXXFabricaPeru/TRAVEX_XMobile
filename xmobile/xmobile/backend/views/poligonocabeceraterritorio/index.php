<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
$this->title = 'Poligonocabeceraterritorios';
$datosC = Yii::$app->db->createCommand("SELECT valor from configuracion
          where parametro='campo_adicional_user_midd' and estado=1")->queryOne();
?>
<style type="text/css">
     /* preloader dgr*/
        #preloader {
        position: fixed;
        top:0; left:0;
        right:0; bottom:0;
        background: #000;
        z-index: 100;
        }
        #loader {
        width: 200px;
        height: 200px;
        position: absolute;
        left:45%; top:50%;
        background: url(../web/images/cargador/loader.gif) no-repeat center;
        margin:-50px 0 0 -50px;
        
        } 
</style>

<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none">
    <!---el div para que se inici el precargador-->
    <!--div id="preloader">
        <div id="loader">&nbsp;  
           <div id="mensaje"  align="center" style="color:#FFFFFF">Espere por favor</div>
        </div>
    </div-->
    <!--fin del precargador-->
</div>
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
            ($datosC['valor']==1)?(
            [
                'attribute' => 'U_Regional',
                'filter' => \yii\helpers\ArrayHelper::map(backend\models\Vendedores::find()->where("U_Regional IS NOT NULL")->asArray()->all(), 'U_Regional', 'U_Regional'),
                'value' => function($data) {

                    return $data->U_Regional;
                    
                }

            ]
            ):([
                'header'=>'',
                'value'=>function($data){
                    return "";
                }
            ]),

             [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                    . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '<button title="Ver detalle" class="btn-link btn-grid-action-ver" id="btn-delete" value="' . $data->id . '" onclick="verDetalle('.$data->id.')" ><i class="fa fa-solid fa-eye text-primary"></i></button>';;
                        }
             ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Poligonocabeceraterritorio.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50" ></script>

 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
     integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14="
     crossorigin=""/>
 <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js"
     integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg="
     crossorigin=""></script>
<!--
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50" async defer></script>
<script src="https://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=false&key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50"></script> ***error: AIzaSyAItWswM9WTXrVeRm65fSqHJFeJbXo7zcQ
-->
