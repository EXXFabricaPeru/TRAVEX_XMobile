<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
date_default_timezone_set('America/La_Paz');

$fecha=date('Y-m-d');
if(isset($_GET['fecha'])){
    $fecha=$_GET['fecha'];
}

$this->title = 'Log Envios';
?>
<style>
     #tabs{
        width: 100% !important;
        display: none;
    }
        /*::beforetable.dataTable th,
        table.dataTable td {
            white-space: nowrap;
        }*/
</style>

<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
<div class="row">
   
</div>

<div class="log-envio-index">

    <div id="tabs">
        <ul>
            <li><a href="#tabs-0" onclick="">Logs Clientes</a></li>
            <li><a href="#tabs-1" onclick="">Logs Documentos</a></li>
            <li><a href="#tabs-2" onclick="">Logs Pagos</a></li>

            <!--li><a href="#geotabs-5" onclick="mostrarTabla('5');">Rutas</a></li-->
        </ul>   
        <hr>    
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-1">
                <span style="color:#09104D"> FECHA REGISTRO: </span> 
            </div>
            <div class="col-lg-2">
                <input id="FECHAFIN" name="FECHAFIN" type="date"  class="form-control" data-validation="date" data-validation-format="yyyy-mm-dd" value="<?=$fecha?>" />
            </div>
            <div class="col-lg-2">
                <button class="btn btn-success" id="btn-filtrar" onclick="cargarPagina()"> Filtrar </button>
            </div>
            <div class="col-lg-3"></div>
        </div>
        <div id="tabs-0">
            <h3>Logs Clientes</h3>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <!--table table-striped table-bordered table-hover//table table-striped table-bordered dt-responsive nowrap//table table-striped table-bordered nowrap-->
                            <table style="width:100%" class="table table-striped table-bordered dt-responsive" id="dataTables-view-pc-egreso">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Codigo</th>
                                        <th>Objeto Envio Midd</th>
                                        <th>Respuesta Sap</th>
                                        <th>EndPoint</th>                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //$dataProvider->orderby(['fecha'=>'asc']);
                                    //$dataProvider->$searchModel;
                                    foreach ($dataProvider->getModels() as $key => $value) {
                                        if($value['idlog'] == 24){
                                            $estado= '<span class="label label-success">Sap</span>';
                                        } else {
                                            $estado= '<span class="label label-warning">Midd</span>';
                                        }

                                        $cantidad=strlen($value['envio']);
                                        $arr1=explode('","',$value['envio']);
                                        $linea=2;
                                        $envio="";
                                        for($i=0;$i<count($arr1);$i++){
                                            if($i==$linea){
                                                $envio.='<br>';
                                                $linea=$linea+2;
                                            }
                                            $envio.='"'.$arr1[$i].'",';
                                        }
                                        $envio=substr($envio, 1);
                                        $envio=substr($envio, 0,-2);
                                    ?>
                                    <tr>
                                        <td><?=$estado?></td>
                                        <td width="5%"><?=$value['fecha']?></td>
                                        <td width="10%"><?=$value['documento']?></td>
                                        <td width="50%"><?=$envio?></td>
                                        <td width="20%"><?=$value['respuesta']?></td>
                                        <td width="10%"><?=$value['endpoint']?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                    
                                </tbody>
                            </table>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <div id="tabs-1">
            <h3>Logs Documentos</h3>
            <?php Pjax::begin(['id' => 'Logs-documentos']); ?>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'header' => 'Estado',
                            'format' => 'raw',
                            'value' => function($data) {
                               
                                if($data->idlog == 24){
                                    return '<span class="label label-success">Sap</span>';
                                } else {
                                    return '<span class="label label-warning">Midd</span>';
                                }
                                   
                            }
                        ],
                        'fecha',
                        //'idlog',
                        'documento',
                        //'proceso',
                        // 'envio:ntext',
                        'envio',
                        'respuesta',
                        'endpoint',
                    
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
                    ],
                ]);
                ?>
            <?php Pjax::end(); ?>
        </div>
        <div id="tabs-2">
            <h3>Logs Pagos</h3>
            <?php Pjax::begin(['id' => 'Logs-pagos']); ?>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'header' => 'Estado',
                            'format' => 'raw',
                            'value' => function($data) {
                                if($data->idlog == 24){
                                    return '<span class="label label-success">Sap</span>';
                                } else {
                                    return '<span class="label label-warning">Midd</span>';
                                }
                            }
                        ],
                        'fecha',
                        //'idlog',
                        'documento',
                        //'proceso',
                        // 'envio:ntext',
                        'envio',
                        'respuesta',
                        'endpoint',
                    
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
                    ],
                ]);
                ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Logs.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
