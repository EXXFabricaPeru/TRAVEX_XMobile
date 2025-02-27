<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
?>
<style>
    #tabs{
        width: 100% !important;
        display: none;
    }

    #map {
        height: 500px;
        width: 100%;
        border: 1px solid #000;
    }

    #pickerFecha{
        margin-top: 15px;
        margin-left: 10px;
        position: absolute !important;
        z-index: 1000
    }
    
</style>
<div class="row">
    <div class="col-md-4">
        <ul class="media-list">
            <li class="media">
                <div class="media-left">
                </div>
                <div class="media-body">
                    <h4 class="media-heading">Equipo </h4>
                    <input type="hidden" id="hdEquipox" value="<?= $model->id ?>" />
                </div>
            </li>
        </ul>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                // 'id',
                'equipo',
                'uuid',
                //'keyid',
                'plataforma',
                'estado',
                'registrado',
                'version',
                //'sucursalxId',
                [
                    'attribute' => 'sucursalxId',
                    'value' => function($data) {
                        try {
                            return \backend\models\Sucursalx::findOne($data->sucursalxId)->nombre;
                        } catch (Exception $e) {
                            return '(no definido)';
                        }
                    }
                ]
            ],
        ])
        ?>
    </div>
    <div class="col-md-8">
        <p id="cargandoLoad"></p>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-0">Asignar usuarios. </a></li>
                <li><a href="#tabs-1">Conf. Dosificación.   </a></li>
                <li><a href="#tabs-2">Conf. Cuentas contables. </a></li>
                <!--li><a href="#tabs-3" onclick="initMap();">Ubicaciones del mobil. </a></li-->
            </ul>
            <div id="tabs-0">
                <p class="text-right"><button class="btn btn-success" id="btn-create-user-equipo">Asignar usuario</button></p>  
                <?php Pjax::begin(['id' => 'Userequipox-list']); ?>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProviderequip,
                    'filterModel' => $searchModelequip,
                    'columns' => [
                        //'id',
                        //  'userId',
                        [
                            'attribute' => 'userId',
                            'filter' => \yii\helpers\ArrayHelper::map(backend\models\User::find()->all(), 'id', 'username'),
                            'value' => function($data) {
                                return \backend\models\User::findOne($data->userId)->username;
                            }
                        ],
                        //'equipoxId',
                        'tiempo',
                        [
                            'attribute' => 'Acciones',
                            'format' => 'raw',
                            'value' => function($data) {
                                return ''
                                        . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete-userequipo" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                        . '<button title="Editar registro" class="btn-link btn-grid-action-edit-userequipo" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                        // . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                        . '';
                            }
                        ]
                    ],
                ]);
                ?>

                <?php Pjax::end(); ?>


            </div>
            <div id="tabs-1">

                <div class="lbcc-index">
                    <p class="text-right"> <button class="btn btn-success" id="btn-create" value="<?=$_GET['id']?>">Crear Lbcc</button>
                    </p>
                    <?php Pjax::begin(['id' => 'Lbcc-list']); ?>
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            'U_NumeroAutorizacion',
                            //'U_PrimerNumero',
                            'U_NumeroSiguiente',
                            'U_UltimoNumero',
                            //'U_Series',
                            'U_SeriesName',
                            //'U_TipoDosificacion',
                            //'papelId',
                            'U_FechaLimiteEmision',
                            // 'U_GrupoCliente',
                            //'U_Actividad',
                            [
                                'header' => 'Factura',
                                'format' => 'raw',
                                'value' => function($data) {
                                    $usaFex = backend\models\Equipox::findOne($data->equipoId);
                                    if($usaFex->fex==1){
                                        if ($data->facturaOffline==1) {
                                            return '<span class="label label-warning">Offline</span>';
                                        } else {
                                            return '<span class="label label-success">Online</span>';
                                        }
                                    } 
                                    else{
                                        return '-';
                                    }
                                   
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
                    ]);
                    ?>

                    <?php Pjax::end(); ?>
                </div>
            </div>
            <div id="tabs-2">

                <p class="text-right"> 
                    <button class="btn btn-info" id="btn-create-cuentas">Crear</button>
                    <button class="btn btn-warning" id="btn-editar-cuentas">Editar</button>
                    <button class="btn btn-danger" id="btn-eliminar-cuentas">Eliminar</button>
                </p>
                <?php Pjax::begin(['id' => 'Equipoxcuentascontables-list']); ?>
                <?=
                ListView::widget([
                    'dataProvider' => $dataProviderx,
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        return DetailView::widget([
                                    'model' => $model,
                                    'attributes' => [
                                        //'id',
                                        // 'equipoxId',
										'cuentaClientesRegion',
                                        'cuentaEfectivo',
                                        'cuentaCheque',
                                        'cuentaTranferencia',
                                        'cuentaTarjeta',
                                        'cuentaAnticipos',
                                        'cuentaEfectivoUSD',
                                        'cuentaChequeUSD',
                                        'cuentaTranferenciaUSD',
                                        'cuentaTarjetaUSD',
                                        'cuentaAnticiposUSD',
                                        [
                                            'attribute' => '',
                                            'format' => 'raw',
                                            'value' => function($data) {
                                                return '<b id="idListViewCuentas" name="' . $data->id . '"></b>';
                                            }
                                        ],
                                    ],
                        ]);
                    },
                ])
                ?>
                <?php Pjax::end(); ?>

            </div>
            <!--div id="tabs-3">
                <div class="row">
                    <table width="100%">
                        <tr>
                            <td style="width:100px;">
                                <label>Fecha inicial:</label>
                            </td>
                            <td style="text-align:left;">
                                <input type="text" />
                            </td>
                            <td style="width:100px;">
                                <label>Fecha final:</label>
                            </td>
                            <td style="text-align:left;">
                                <input type="text" />
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;">
                                <label>Usuario:</label>
                            </td>
                            <td style="text-align:left;">
                                <input type="text" />
                            </td>
                            <td style="width:100px;">
                                <label>Tipo Doc:</label>
                            </td>
                            <td style="text-align:left;">
                                <input type="text" />
                            </td>
                        </tr>
                        <tr><td colspan="4"></br></td></tr>
                        <tr><td colspan="4" style="text-align:center;"><input type="button" value="BUSCAR" style="width:100%" /></div></td></tr>
                        <tr><td colspan="4"></br></td></tr>
                        <tr><td colspan="4"><div id="map"></div></td></tr>                        
                    </table>
                </div>                
            </div-->
        </div>
        <div class="modal" id="modal">
            <div class="modal-content">
                <a href="#" class="modal-close" title="Close Modal">X</a>
                <h3>Titulo</h3>
                <div class="modal-area">
                    <p>Contenido</p>
                </div>
            </div>
        </div>
        <div class="window" style="display: none"></div>
        <div id="windowpdf" style="display: none"></div>
        <div id="windowEliminar" style="display: none"> <br/><br/>
            <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
        </div>
        <div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
    </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBAan7nzQ2E8-ax3E8shSumJ7vmkK00hT0" async defer></script>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Userequipox.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Lbcc.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Equipoxcuentascontables.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 

<script>
    var map;
    
    function initMap() {
        var equipo = $('#hdEquipox').val();
        var equipox = { equipox: equipo};
        var ubi = {lat: -16.496777, lng: -68.132031};
        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});

        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/cargarpuntoshoy']); ?>',
               type: 'POST',
               data: equipox,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                    var resultado = JSON.parse(data);
                    var coordenadas = [];
                    for(var i=0; i<resultado.length; i++){
                        var xy = { lat: Number(resultado[i]["latitud"]), lng: Number(resultado[i]["longitud"]) };
                        new google.maps.Marker({position: xy, map: map});
                        coordenadas.push(xy);
                    }
                   }
                   else{
                    console.error("ERROR: ");    
                   }
               },
               error: (jqXhr, textStatus, errorMessage) => {
                   console.error("ERROR: " + errorMessage);
               }
            });        
            
    }
    
</script>


