<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;$this->title = 'Geolocalizacions';
use backend\models\Viusuariopersona;
ini_set('max_execution_time', 9000000);
ini_set('memory_limit',"20000000M");
$modeluser=Viusuariopersona::find()->asArray()->all();
?>

<style>
    #tabs{
        width: 100% !important;
        display: none;
    }

    #map {
        height: 500px;
        border: 1px solid #000;
    }

    #pickerFecha{
        margin-top: 15px;
        margin-left: 10px;
        position: absolute !important;
        z-index: 1000
    }
    #tblResultado {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblResultado td, #tblResultado th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblResultado tr:nth-child(even){background-color: #f2f2f2;}

    #tblResultado tr:hover {background-color: #ddd;}

    #tblResultado th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
    //////////////////////
     #tblResultado-sin {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblResultado-sin td, #tblResultado-sin th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblResultado-sin tr:nth-child(even){background-color: #f2f2f2;}

    #tblResultado-sin tr:hover {background-color: #ddd;}

    #tblResultado-sin th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
    
    .bootstrap-select .btn{
        background-color: inherit !important;
        border: inherit !important;
        border-radius: inherit !important;
        border-bottom: inherit !important;
        color: inherit !important;
        border-color: #8c8c8c;
        outline: 5px auto #cccccc !important;
    }
    h4{
        text-transform: uppercase;
        font-size: 12px;
        background-color:#ccc;
        padding: 4px;
        color:#030E38;
    }
</style>

<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="geolocalizacion-index" style="display:none;">

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Geolocalizacion' </button>
    </p>
<?php Pjax::begin(['id' => 'Geolocalizacion-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'idequipox',
            'latitud',
            'longitud',
            'fecha',
            //'hora',
            //'idcliente',
            //'documentocod',
            //'tipodoc',
            //'estado',
            //'actividad',
            //'anexo',
            //'usuario',
            //'status',
            //'dateUpdate',

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
<div class="col-md-12">
    <div id="tabsgeo">
        <ul>
            <li><a href="#geotabs-0" onclick="mostrarTabla('0');">Por Equipo</a></li>
            <li><a href="#geotabs-1" onclick="mostrarTabla('1');">Por Usuario</a></li>
            <li><a href="#geotabs-2" onclick="mostrarTabla('2');">Por Cliente</a></li>
            <!--li><a href="#geotabs-3" onclick="mostrarTabla('3');">Cliente</a></li-->
            <li><a href="#geotabs-4" onclick="mostrarTabla('4');">No Visitas</a></li>
            <li><a href="#geotabs-5" onclick="mostrarTabla('5');">Rutas Ventas</a></li>
            <li><a href="#geotabs-6" onclick="mostrarTabla('6');">Rutas Despacho</a></li>
            <li><a href="#geotabs-7" onclick="mostrarTabla('6');">Territorios Clientes</a></li>
            <li><a href="#geotabs-8" onclick="mostrarTabla('6');">Reasignación Territorios</a></li>
        </ul>
        <div id="geotabs-0">

            <div class="row">
                <div class="col-md-1">
                    <label>Equipo:</label><br>
                    <label >
                        <input type="checkbox" id="cbddlEquipo" onclick="checkEquipos()">
                        <small style="vertical-align:text-bottom">(Todos)</small>
                    </label>
                </div>
                <div class="col-md-5">
                    <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Equipox::find()->all(), 'id', 'equipo'); ?>
                    <?= Html::dropDownList('ddlEquipo', null, $arr, ['style' => 'width:100%;', 'id' => 'ddlEquipo', 'class' => 'form-control', 'multiple' => 'multiple', 'onchange' => 'cambioEquipos()']) ?>
                </div>
                <div class="col-md-6">
                    <div class="row">

                        <div class="col-md-3" ><label>Fecha inicial:</label></div>
                        <div class="col-md-8" >
                            <input type="date" id="txtFIni" style="width:100%;" class="form-control hasDatepicker" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3" ><label>Fecha final:&nbsp;</label></div>
                        <div class="col-md-8" >
                            <input type="date" id="txtFFin" style="width:100%;" class="form-control hasDatepicker" />
                        </div>
                    </div>
                </div>                                
            </div>
            <br>   
            <div class="row">

                <div class="col-md-1" ><label>Hora inicial:</label></div>
                <div class="col-md-2" >
                    <input type="time" id="txtHIni" style="width:100%;" class="form-control" />
                </div>
                <div class="col-md-1" ><label>Hora final :&nbsp;</label></div>
                <div class="col-md-2" >
                    <input type="time" id="txtHFin" style="width:100%;" class="form-control" />
                </div>
                 
                <div class="col-md-2">
                    
                    <button class="btn btn-info" onclick="clearCargaMapa()">Limpiar</button>
                    <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapa()">Buscar</button>
                    
                </div>
            </div>
            
        </div>
        <div id="geotabs-1">
            <div class="row">
                <table width="100%">
                    <tr>
                        <td style="text-align:leftt;width:100px;"><label>Usuario:</label></td>
                        <td style="text-align:left;" colspan="3">
                            <?php //$arr = \yii\helpers\ArrayHelper::map(backend\models\User::find()->all(), 'id', 'username'); ?>
                            <?php //= Html::dropDownList('ddlGeousuario', null, $arr, ['style' => 'width:100%;', 'id' => 'ddlGeoUsuario', 'class' => 'form-control']) ?>
                            <input type="text" size="30"  name="ddlGeoUsuario" id="ddlGeoUsuario" value="" list="datalistUser"  value="" class="form-control mayusculas" data-validation="required"  placeholder="Usuario vendedor">
                            <datalist id="datalistUser">
                                <?php
                                foreach ($modeluser as $key => $value) {
                                        echo"<option id='".$value['id']."'  value='".$value['username'].' - '.$value['nombreCompleto']."' > 

                                        </option>";                                      
                                }
                                ?>
                            </datalist>

                        </td>
                    </tr>
                    <tr><td colspan="4"><br/></td></tr>
                    <tr>
                        <td style="width:100px;"><label>Fecha inicial:</label></td>
                        <td style="text-align:left;with:100%;">
                            <input type="date" id="txtGeoUsrFIni" style="width:100%;" class="form-control hasDatepicker" />
                        </td>
                        <td style="text-align:right;width:100px;"><label>Fecha final:&nbsp;</label></td>
                        <td style="text-align:right;with:100%;">
                            <input type="date" id="txtGeoUsrFFin" style="width:100%;" class="form-control hasDatepicker" />
                        </td>
                    </tr>
                    <tr><td colspan="4"><br/></td></tr>
                    <tr>
                        <td style="text-align:left;"><label>Tipo Doc:&nbsp;</label></td>
                        <td style="text-align:right;" colspan="3">
                            <?php $arr = ['DOF' => 'Oferta', 'DOP' => 'Pedido', 'DFA' => 'Factura', 'DOE' => 'Entrega', 'PAGO' => 'Pago']; ?>
                            <?= Html::dropDownList('ddlGeoDocumento', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'class' => 'form-control', 'id' => 'ddlGeoDocumento']) ?>
                        </td>
                    </tr>
                    <tr><td colspan="4"><br/></td></tr>
                    <tr><td colspan="4"><br/></td></tr>
                </table>
            </div>
            <p class="text-right"> 
                <button class="btn btn-info" id="btn-create-cuentas" onclick="clearCargarMapaDocumentos()">Limpiar</button>
                <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaDocumentos()">Buscar</button>
            </p>
        </div>
        <div id="geotabs-2">
            <div class="row">
                <table width="100%">
                    <tr>
                        <td style="text-align:leftt;width:100px;"><label>Cliente:</label></td>
                        <td style="text-align:left;" colspan="3">
                            <?php // $arr = \yii\helpers\ArrayHelper::map(backend\models\Clientes::find()->where('cliente_std4 is not null and cliente_std4 <> 0')->all(), 'id', 'CardName'); ?>
                            <?php $dataClientes = \yii\helpers\ArrayHelper::map(backend\models\Clientes::find()->where('EXISTS (SELECT * FROM clientessucursales WHERE clientessucursales.CardCode = CardCode AND AdresType="S")')->orderBy('CardName ASC')->all(), 'id', 'CardName');// ->asArray() print_r($arr);?>
                            <input type="text" size="30"  name="ddlGeoCliente" id="ddlGeoCliente" value="" list="datalisClientes"  value="" class="form-control mayusculas" data-validation="required"  placeholder="Cliente">
                            <datalist id="datalisClientes">
                                <?php
                                foreach ($dataClientes as $key => $value) {
                                        echo"<option id='".$key."'  value='".$value."' ></option>";                                      
                                }
                                ?>
                            </datalist>

                        </td>
                    </tr>                
                    <tr>
                        <td style="width:100px;"><label>Fecha inicial:</label></td>
                        <td style="text-align:left;with:100%;">
                            <input type="date" id="txtGeoCliFIni" style="width:100%;" class="form-control hasDatepicker" />
                        </td>
                        <td style="text-align:right;width:100px;"><label>Fecha final:&nbsp;</label></td>
                        <td style="text-align:right;with:100%;">
                            <input type="date" id="txtGeoCliFFin" style="width:100%;" class="form-control hasDatepicker" />
                        </td>
                    </tr>                
                    <tr>
                        <td style="text-align:left;"><label>Tipo Doc:&nbsp;</label></td>
                        <td style="text-align:right;" colspan="3">
                            <?php $arr = ['DOF' => 'Oferta', 'DOP' => 'Pedido', 'DFA' => 'Factura', 'DOE' => 'Entrega']; ?>
                            <?= Html::dropDownList('ddlGeoCliDocumento', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'class' => 'form-control', 'id' => 'ddlGeoCliDocumento']) ?>
                        </td>
                    </tr>                
                    <tr>
                        <td style="text-align:left;"><label>Grupo:&nbsp;</label></td>
                        <td style="text-align:right;" colspan="3">
                            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Clientesgrupo::find()->all(), 'Code', 'Name'); ?>
                            <?= Html::dropDownList('ddlGeoGrupoCliente', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'id' => 'ddlGeoGrupoCliente', 'class' => 'form-control']) ?>
                        </td>
                    </tr>                
                    <tr>
                        <td style="text-align:left;"><label>Territorio:&nbsp;</label></td>
                        <td style="text-align:right;" colspan="3">
                            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
                            <?= Html::dropDownList('ddlGeoTerritorioCliente', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'id' => 'ddlGeoTerritorioCliente', 'class' => 'form-control']) ?>
                        </td>
                    </tr>
                    <tr><td colspan="4"><br/></td></tr>
                </table>
            </div>
            <p class="text-right">
                <button class="btn btn-info" id="btn-create-cuentas" onclick="clearCargarMapaClientes()">Limpiar</button>
                <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaClientes()">Buscar</button>
            </p>
        </div>
        
        <!--div id="geotabs-3">
            <div class="row">
                <table width="100%">
                    <tr>
                        <td style="text-align:leftt;width:100px;"><label>Cliente1:</label></td>
                        <td style="text-align:left;" colspan="3">
                            <?php $dataClientesCoordenadas = \yii\helpers\ArrayHelper::map(backend\models\Clientes::find()->where('(Latitude is not null and Latitude<>0) and (Longitude is not null and Longitude <> 0)')->orderBy('CardName ASC')->all(), 'id', 'CardName'); ?>
                            <?php //$arr = \yii\helpers\ArrayHelper::map(backend\models\Clientes::find()->where('EXISTS (SELECT * FROM clientessucursales WHERE clientessucursales.CardCode = CardCode )')->orderBy('CardName ASC')->all(), 'id', 'CardName'); ?>
                          
                            
                            <input type="text" size="30"  name="ddlGeoSoloCliente" id="ddlGeoSoloCliente" value="" list="datalisClientesC"  value="" class="form-control mayusculas" data-validation="required"  placeholder="Cliente">
                            <datalist id="datalisClientesC">
                                <?php
                                foreach ($dataClientesCoordenadas as $key => $value) {
                                        echo"<option id='".$key."'  value='".$value."' ></option>";                                      
                                }
                                ?>
                            </datalist>
                        </td>
                    </tr>                
                    tr>
                        <td style="width:100px;"><label>Fecha inicial:</label></td>
                        <td style="text-align:left;with:100%;">
                            <input type="date" id="txtGeoSoloCliFIni" style="width:100%;" class="form-control hasDatepicker" />
                        </td>
                        <td style="text-align:right;width:100px;"><label>Fecha final:&nbsp;</label></td>
                        <td style="text-align:right;with:100%;">
                            <input type="date" id="txtGeoSoloCliFFin" style="width:100%;" class="form-control hasDatepicker" />
                        </td>
                    </tr-->                
                    <!--tr>
                        <td style="text-align:left;"><label>Grupo:&nbsp;</label></td>
                        <td style="text-align:right;" colspan="3">
                            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Clientesgrupo::find()->all(), 'Code', 'Name'); ?>
                            <?= Html::dropDownList('ddlGeoGrupoCliente', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'id' => 'ddlGeoSoloGrupoCliente', 'class' => 'form-control']) ?>
                        </td>
                    </tr>                
                    <tr>
                        <td style="text-align:left;"><label>Territorio:&nbsp;</label></td>
                        <td style="text-align:right;" colspan="3">
                            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
                            <?= Html::dropDownList('ddlGeoTerritorioCliente', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'id' => 'ddlGeoSoloTerritorioCliente', 'class' => 'form-control']) ?>
                        </td>
                    </tr>
                    <tr><td colspan="4"><br/></td></tr>
                </table>
            </div>
            <p class="text-right">
                <button class="btn btn-info" id="btn-create-cuentas" onclick="clearGeoBuscarSoloCliente()">Limpiar</button>
                <button class="btn btn-warning" id="btn-editar-cuentas" onclick="geoBuscarSoloCliente()">Buscar</button>
            </p>
        </div-->
        <div id="geotabs-4">
            <div class="row">
                <div class="row">
                    <div class="col-md-1" style="text-align:leftt;width:100px;"><label>Usuario:</label></div>
                    <div class="col-md-5" style="text-align:left;" >
                        <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\User::find()->all(), 'id', 'username'); ?>
                        <?= Html::dropDownList('ddlVisusuario', null, $arr, ['style' => 'width:100%;', 'id' => 'ddlVisusuario', 'class' => 'form-control']) ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-1"><label>Fecha inicial:</label></div>
                    <div class="col-md-2">
                        <input type="date" id="txtGeoVisFIni" style="width:100%;" class="form-control hasDatepicker" />
                    </div>
                    <div class="col-md-1" ><label>Fecha final:&nbsp;</label></div>
                    <div class="col-md-2">
                        <input type="date" id="txtGeoVisFFin" style="width:100%;" class="form-control hasDatepicker" />
                    </div>
                    <div class="col-md-2" >
                        <button class="btn btn-info" id="btn-create-cuentas" onclick="clearCargarMapaVisitas()">Limpiar</button>
                        <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaVisitas()">Buscar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="geotabs-5">
            <div class="row">
               <div class="row">
                     <?php
                        $dias=[1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado',7=>'Domingo'];
                     ?>
                    <div class="col-md-2">
                        <label>Dia </label> 
                        <select name="select" class="form-control" id="DIA" >
                            <?php
                            $diasV = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
                            date_default_timezone_set('America/La_Paz');
                            //$dias[date("w")];
                            
                            foreach ($dias as $key => $value) {
                                if($value==$diasV[date("w")]){
                                    $selected="selected";
                                }
                                else{
                                    $selected="";
                                }
                               echo ' <option value="'.$key.'"  '.$selected.'>'.$value.'</option>';
                            }
                            ?>
                          
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Vendedor</label> 
                        <input type="text" size="30"  name="operador" id="operador" value="<?=$model->vendedor?>" list="datalistUser" class="form-control mayusculas" data-validation="required"  placeholder="Usuario vendedor">

                        <span class="text-danger text-clear" id="error-operador"></span>
                    </div>
                    <div class="col-md-2">
                        <br>
                        <button class="btn btn-info" id="btn-create-cuentas" onclick="clearCargarMapaRutas()">Limpiar</button>
                        <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaRutas()">Buscar</button>
                    </div>
                    
                    <datalist id="datalistUser">
                        <?php

                        foreach ($modeluser as $key => $value) {
                                echo"<option id='".$value['id']."'  value='".$value['username'].' - '.$value['nombreCompleto']."' > 

                                </option>";                                      
                        }
                        ?>
                    </datalist>
                </div>
               
            </div>
            
        </div>
        <div id="geotabs-6">
            <div class="row">
                <div class="row">

                    <div class="col-md-2">
                        <?php
                        date_default_timezone_set('America/La_Paz');
                        $fecha=date('Y-m-d');
                        ?>
                        <label>Fecha Picking:&nbsp;</label>
                        <input type="date" id="txtfechapicking" style="width:100%;" value="<?=$fecha?>" class="form-control hasDatepicker" />
                        
                    </div>
                    <div class="col-md-4">
                        <label>Despachador</label> 
                        <input type="text" size="30"  name="despachador" id="despachador"  list="datalistUser"  class="form-control mayusculas" data-validation="required"  placeholder="Usuario Despachador">
                    </div>
                    <div class="col-md-2">
                         <br>
                         <button class="btn btn-info" id="btn-create-cuentas" onclick="clearCargarMapaRutasDespacho()">Limpiar</button>
                         <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaRutasDespacho()">Buscar</button>
                    </div>
                    
                    <datalist id="datalistUser">
                        <?php

                        foreach ($modeluser as $key => $value) {
                                echo"<option id='".$value['id']."'  value='".$value['username'].' - '.$value['nombreCompleto']."' > 

                                </option>";                                      
                        }
                        ?>
                    </datalist>
                </div>
                
            </div>
            
        </div>
        <div id="geotabs-7">
            <div class="row">
               <div class="row">

                    
                    <div class="col-md-4">
                        <label>Territorio</label> 
                        <input type="text" size="30"  name="txtterritorio" id="txtterritorio"  list="dataTerritorio"  class="form-control mayusculas" data-validation="required"  placeholder="Seleccione Territorio">
                    </div>
                    <div class="col-md-2">
                        <br> 
                        <button class="btn btn-info" id="btn-create-cuentas" onclick="clearCargarMapaTerritorioCliente()">Limpiar</button>
                        <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaTerritorioCliente()">Buscar</button>
                        
                    </div>

                    
                    <?php $arrTerritorio = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
                    <datalist id="dataTerritorio">
                        <?php

                        foreach ($arrTerritorio as $key => $value) {
                                echo"<option id='".$key."'  value='".$value."' > 

                                </option>";                                      
                        }
                        ?>
                    </datalist>
                </div>
            
            </div>
            
        </div>
        <div id="geotabs-8">
            <div class="row">
                <div class="row">

                    
                    <div class="col-md-4">
                        <label>Territorio</label> 
                        <input type="text" size="30"  name="txtterritorio" id="txtterritorio-asig"  list="dataTerritorio"  class="form-control mayusculas" data-validation="required"  placeholder="Seleccione Territorio">
                    </div>
                    <div class="col-md-2">
                        <br> 
                        <button class="btn btn-info" id="btn-create-cuentas" onclick="clearCargarMapaTerritorioCliente()">Limpiar</button>
                        <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaTerritorioClienteAsig()">Buscar</button>
                        
                    </div>

                    <div class="col-md-4">
                        <label>Reasignar Territorio</label> 
                        <input type="text" size="30"  name="txtterritorio" id="txtterritorio-asig-re"  list="dataTerritorio"  class="form-control mayusculas" data-validation="required"  placeholder="Seleccione Territorio">
                    </div>
                    <div class="col-md-2">
                        <br> 
                        <button class="btn btn-warning" id="btn-editar-cuentas" onclick="reasignarTerritorio()">Reasignar</button>
                        
                    </div>

                    
                    <?php $arrTerritorio = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
                    <datalist id="dataTerritorio">
                        <?php

                        foreach ($arrTerritorio as $key => $value) {
                                echo"<option id='".$key."'  value='".$value."' > 

                                </option>";                                      
                        }
                        ?>
                    </datalist>
                </div>
            
            </div>
            
        </div>
    </div>
</div>
<!--div class="col-md-4">
    <div id="tabsgeo1">
        <ul>
            <li><a href="#geotabs1-0">Comparaciones</a></li>
        </ul>
        <div id="geotabs1-0">
            <div class="row">
                <table width="100%">
                    <tr>
                        <td style="text-align:leftt;width:100px;"><label>Poligonos</label></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;">
                            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Poligonocabecera::find()->all(), 'id', 'nombre'); ?>
                            <?= Html::dropDownList('ddlGeoPoligino', null, $arr, ['prompt' => 'ninguno', 'style' => 'width:100%;', 'id' => 'ddlGeoPoligino', 'class' => 'form-control']) ?>
                        </td>
                    </tr>
                    <tr style="display:none">
                        <td style="text-align:left;"><label>Rutas</label></td>
                    </tr>
                    <tr style="display:none">
                        <td style="text-align:right;">
                        <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Rutacabecera::find()->all(), 'id', 'nombre'); ?>
                            <?= Html::dropDownList('ddlGeoRuta', null, $arr, ['prompt' => 'ninguno', 'style' => 'width:100%;', 'id' => 'ddlGeoRuta', 'class' => 'form-control']) ?>
                        </td>
                    </tr>
                    <tr><td colspan="2"><br/></td></tr>
                </table>
            </div>
            <p class="text-right"> 
                <button class="btn btn-info" id="btn-create-cuentas">Limpiar</button>
                <button class="btn btn-warning" id="btn-editar-cuentas" onclick="cargarMapaComparaciones()">Buscar</button>
            </p>
        </div>
    </div>    
</div-->
<div class="col-md-6">
    <div class="content">
        <div id="map"></div>
    </div>
</div>
<div class="col-md-6" id="resultados">
    <div class="modal-content">
        <h4>
            <div class="row">
                <!--div class="col-lg-4">Total Registros: <span id="cantidadClientes"> </span></div-->
                <div class="col-lg-4"><p id="p-resultado"> </p></div>
                
                <div class="col-lg-2">Buscador:</div>
                <div class="col-lg-6">
                <input id="searchTerm" type="text" class="form-control" onkeyup="doSearch('tblResultado','searchTerm','p-resultado')" />
                </div>
            </div>
        </h4>
       
    </div>
    <div class="modal-content" style="height:450px;overflow:auto;">
        <table width="100%" id="tblResultado">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <!--div class="well">
        <p id="p-resultado"> </p>  
    </div-->
</div>
<!--tabla de licnetes sin latitud y longitus-->
<div class="col-md-6" style="display:none;" id="div-clientessin">
    <div class="modal-content">
        <h4> <span  style="color: #FEFDFD">Clientes sin coordenadas:</span>
            <div class="row">
                <!--div class="col-lg-4">Total Registros: <span id="cantidadClientes"> </span></div-->
                <div class="col-lg-4"><p id="p-resultado-sin"> </p></div>
                
                <div class="col-lg-2">Buscador:</div>
                <div class="col-lg-6">
                <input id="searchTerm-sin" type="text" class="form-control" onkeyup="doSearch('tblResultado-sin','searchTerm-sin','p-resultado-sin')" />
                </div>
            </div>
        </h4>
       
    </div>
    <div class="modal-content" style="height:450px;overflow:auto;">
        <table width="100%" id="tblResultado-sin">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <!--div class="well">
        <p id="p-resultado"> </p>  
    </div-->
</div>
<!--fin de tabla sin latitud-->
<div class="modal fade" id="divGeoModificarCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width:760px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Actualizar datos del cliente</h4>
            </div>
            <div class="modal-body">
                <table width="100%">
                    <tr>
                        <td style="width:250px">Codigo:</td>
                        <td>
                            <input type="text" name="geoCardCodeModif" id="geoCardCodeModif" class="form-control" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:250px">Nombre:</td>
                        <td>
                            <input type="text" name="geoCardNameModif" id="geoCardNameModif" class="form-control" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:250px">Territorio:</td>
                        <td>
                            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
                            <?= Html::dropDownList('geoTerritoryModif', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'id' => 'geoTerritoryModif', 'class' => 'form-control']) ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="geoModificarDatosCliente();" data-dismiss="modal">Actualizar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50"></script>
-->                                                    
<!-- AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50 -->

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50" ></script>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Geolocalizacion.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/js/GeoScript.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<script>
    var mapGeo;
    var markerGeo;
    var GeoPoligono;
    var GeoRutaServiceComp;
    var GeoRutaRendererComp;    
    var primeracoordenada = true;
    var mapa0 = [];
    var mapa1 = [];
    var mapa2 = [];
    var mapa3 = [];
    var mapa4 = [];
    var mapa5 = [];
    var mapa6 = [];
    var mapa7 = [];
    var mapa8 = [];
    var tablas = {
        tabla0 : [],
        tabla1 : [],
        tabla2 : [],
        tabla3 : [],
        tabla4 : [],
        tabla5 : [],
        tabla6 : [],
        tabla7 : [],
        tabla8 : [],
    };
    var swRowColor;
    var infowindow;
    var companyMarker;
    var utimoMarker;
 
    function initMap() {
        idmarker = 1;
        var ubi = {lat: -16.496777, lng: -68.132031};
        mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});
        //google.maps.event.addListener(map, 'click', function(event) { placeMarker(event.latLng); });        
    }

    function AbrirFiltros(){
        $("#FIni").datepicker({dateFormat: 'yy-mm-dd'});
        $("#FFin").datepicker({dateFormat: 'yy-mm-dd'});
        $('#divFiltro').modal();
        $('#divFiltro').show('show');        
    }

    function cargarMapa(){
        //var equipo = $('#ddlEquipo')[0].value; //busqueda simple
        var equipo = $('#ddlEquipo').val(); //busqueda multiple
        var fechaInicio = $('#txtFIni').val();
        var fechaFin = $('#txtFFin').val();
        var horaInicio = $('#txtHIni').val();
        var horaFin = $('#txtHFin').val();
        if(horaInicio!=""){horaInicio+=":00"}
        if(horaFin!=""){horaFin+=":59";}
        var equipox = { equipox: equipo, inicio: fechaInicio, fin: fechaFin, hinicio: horaInicio, hfin: horaFin};
        // console.log(equipox);
        deleteMarkers(0);
        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/cargarpuntoshoy']); ?>',
               type: 'POST',
               data: equipox,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                    var resultado = JSON.parse(data);
                    tablas.tabla0 = resultado;
                    console.log(resultado);
                    var coordenadas = [];
                    var directionsService = new google.maps.DirectionsService;
                    var directionsRenderer = new google.maps.DirectionsRenderer;
                    var waypts = [];
                    var inicio = {};
                    var fin = {}
                    
                    $('#tblResultado thead > th').remove();
                    $('#tblResultado tbody > tr').remove();
                    var tabla = $('#tblResultado');
                    //tabla.find('thead').append('<th>EQUIPO</th><th>USUARIO</th><th>LATITUD</th><th>LONGITUD</th><th>FECHA</th><th>HORA</th>');
                    tabla.find('thead').append('<th>EQUIPO</th><th>USUARIO</th><th>FECHA</th><th>HORA</th><th>LATITUD</th><th>LONGITUD</th>');
                    var cuerpo = '';
                    var InformacionAdicional = '';
                    var coordenadaInterna = true;

                    /*const tourStops: [google.maps.LatLngLiteral, string][] = [
                        [{ lat: 34.8791806, lng: -111.8265049 }, "Boynton Pass"],
                        [{ lat: 34.8559195, lng: -111.7988186 }, "Airport Mesa"],
                        [{ lat: 34.832149, lng: -111.7695277 }, "Chapel of the Holy Cross"],
                        [{ lat: 34.823736, lng: -111.8001857 }, "Red Rock Crossing"],
                        [{ lat: 34.800326, lng: -111.7665047 }, "Bell Rock"],
                    ];*/


                    for(var i=0;  i < resultado.length; i++)
                    {
                        InformacionAdicional ={CardCode:resultado[i]["equipo"],CardName:resultado[i]["username"]};

                        cuerpo = '<tr id="tr-tabla0-'+i+'" onclick="ordenaEtiquetaTabla0(this)"><td>' + resultado[i]["equipo"] + '</td>' +
                                 '<td>' + resultado[i]["username"] + '</td>' +                                
                                 '<td>' + resultado[i]["fecha"] + '</td>' +
                                 '<td>' + resultado[i]["hora"] + '</td>' + 
                                  '<td>' + resultado[i]["latitud"] + '</td>' +
                                 '<td>' + resultado[i]["longitud"] + '</td></tr>' ;
                        tabla.find('tbody').append(cuerpo);
                        var xy = { lat: Number(resultado[i]["latitud"]), lng: Number(resultado[i]["longitud"]) }; 
                        if (primeracoordenada){                            
                            mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                            //directionsRenderer.setMap(mapGeo);
                            inicio = xy;
                            primeracoordenada = false;
                            coordenadaInterna = false;
                        }
                        else if (coordenadaInterna){
                            coordenadaInterna = false;
                            inicio = xy;
                            mapGeo.setCenter(xy);
                        }
                        placeMarker(xy, 4, InformacionAdicional);
                        
                        coordenadas.push(xy);
                        fin = xy;
                        waypts.push({ location: xy, stopover: true});
                        mapa0.push({ location: xy, stopover: true});
                    }
                    /*directionsService.route({
                                                origin: inicio,
                                                destination: fin,
                                                waypoints: waypts,
                                                optimizeWaypoints: true,
                                                travelMode: 'DRIVING'
                                            }, function(response, status) {
                                                if (status === 'OK') {
                                                    directionsRenderer.setDirections(response);
                                                    var route = response.routes[0];
                                                } else {
                                                    alert('Directions request failed due to ' + status);
                                                }
                    });*/
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

    function placeMarker(location, color, informacion = null) {
        console.log("ENTRA A PLASCEMARKER");
        var icono;
        color=6;
        switch (color){
            case 1: 
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png" };
                break;
            case 2:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/purple-dot.png" };
                break;
            case 3:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png" };
                break;
            case 4:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png" };
                break;
            case 5:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/pink-dot.png" };
                break;
            case 6:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/orange-dot.png" };
                break;
            default:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png" };
                break;
        }
        
        var tooltip = '';
        var cardcode = 0;
        if (informacion != null){
            tooltip = "Codigo: " + informacion.CardCode + '\n' + 'Nombre: ' + informacion.CardName;
            cardcode = informacion.CardCode;
        }

        

        var marker = new google.maps.Marker({
            position: location, 
            map: mapGeo,
            draggable: true,
            animation: google.maps.Animation.DROP,
            icon: icono,
            title: tooltip,
            id: cardcode,
            draggable: false
        });
        /*if (informacion == null) 
            marker.addListener('click', toggleBounce);
        else{
            marker.addListener('click', function() {
            //map.setZoom(8);
            //map.setCenter(marker.getPosition());
            geoAbrirClienteEspecifico(informacion);
        }); 
        }*/

       /* var infowindow = new google.maps.InfoWindow({
            content: tooltip,
        });
        
        marker.addListener('click', function() 
        {
            infowindow.open(mapGeo, marker);
        });*/
    }

    function geoAbrirClienteEspecifico(informacion){
        $('#geoCardCodeModif').val(informacion.CardCode);
        $('#geoCardNameModif').val(informacion.CardName);
        //$('#geoTerritoryModif').val(informacion.Territory);
        $("#geoTerritoryModif").val(informacion.Territory);
        $('#divGeoModificarCliente').modal();
        $('#divGeoModificarCliente').show('show');
    }

    function geoModificarDatosCliente(){
        var territorioid = $('#geoTerritoryModif')[0].value;
        var cardcode = $('#geoCardCodeModif').val();
        var informacion = { CardCode: cardcode, Territory: territorioid };
        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/actualizarcliente']); ?>',
               type: 'POST',
               data: informacion,
               success: (data, status, xhr) => {
                console.log(data);
                   if (status == 'success'){                       
                        alert('datos actualizados');
                   }
                   else{
                        alert('error al actualizar');
                   }
               },
               error: (jqXhr, textStatus, errorMessage) => {
                   console.error("ERROR: " + errorMessage);
               }
            });
    }

    function toggleBounce() {
        if (markerGeo.getAnimation() !== null) {
            markerGeo.setAnimation(null);
        } else {
            markerGeo.setAnimation(google.maps.Animation.BOUNCE);
        }
        alert('entro');
    }

    function cargarMapaDocumentos(){
        var opt = $('option[value="'+$("#ddlGeoUsuario").val()+'"]');
        var usuarioid=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);

        if(usuarioid!='NO OPTION'){
            // var usuarioid = $('#ddlGeoUsuario')[0].value;
            var documentoid = $('#ddlGeoDocumento')[0].value;
            var fechaDocumentoInicio = $('#txtGeoUsrFIni').val();
            var fechaDocumentoFin = $('#txtGeoUsrFFin').val();
            var informacion = {usuario: usuarioid, documento: documentoid, inicio: fechaDocumentoInicio, fin: fechaDocumentoFin};
            deleteMarkers(1);
            $.ajax({
                   url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/mapapordocumento']); ?>',
                   type: 'POST',
                   data: informacion,
                   success: (data, status, xhr) => {
                       if (status == 'success'){
                        var resultado = JSON.parse(data);
                        tablas.tabla1 = resultado;
                        console.log(resultado);
                        var coordenadas = [];
                        var directionsService = new google.maps.DirectionsService;
                        var directionsRenderer = new google.maps.DirectionsRenderer;
                        var GeoDocwaypts = [];
                        var inicio = {};
                        var fin = {}

                        $('#tblResultado thead > th').remove();
                        $('#tblResultado tbody > tr').remove();
                        var tabla = $('#tblResultado');
                        tabla.find('thead').append('<th>USUARIO</th><th>DOCUMENTO</th><th>CLIENTE</th><th>TIPO DOC.</th><th>FECHA</th>');
                        var cuerpo = '';                    
                        var InformacionAdicional = '';
                        var coordenadaInterna = true;
                        for(var i=0; i < 24 && i < resultado.length; i++){

                            InformacionAdicional ={CardCode:resultado[i]["CardCode"],CardName:resultado[i]["CardName"]};

                                     cuerpo = '';                        
                            cuerpo = '<tr id="tr-tabla1-'+i+'" onclick="ordenaEtiquetaTabla1(this)"><td>' + resultado[i]["username"] + '</td>' +
                                     '<td>' + resultado[i]["idDocPedido"] + '</td>' +
                                     '<td>' + resultado[i]["CardCode"] + ' - ' + resultado[i]["CardName"] + '</td>' +
                                     '<td>' + getTipoDoc(resultado[i]["DocType"]) + '</td>' +
                                     '<td style="display: none;">' + resultado[i]["U_LATITUD"] + '</td>' +
                                     '<td style="display: none;"> ' + resultado[i]["U_LONGITUD"] + '</td>' +
                                     '<td>' + resultado[i]["fecharegistro"] + '</td></tr>';
                            tabla.find('tbody').append(cuerpo);

                            var xy = { lat: Number(resultado[i]["U_LATITUD"]), lng: Number(resultado[i]["U_LONGITUD"]) }; 
                            if (primeracoordenada){                            
                                mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                                //directionsRenderer.setMap(mapGeo);
                                inicio = xy;
                                primeracoordenada = false;
                                coordenadaInterna = false;
                            }
                            else if (coordenadaInterna){
                                coordenadaInterna = false;
                                inicio = xy;
                                mapGeo.setCenter(xy);
                            }
                            placeMarker(xy, 1, InformacionAdicional);
                            coordenadas.push(xy);
                            fin = xy;
                            GeoDocwaypts.push({ location: xy, stopover: true});
                            mapa1.push({ location: xy, stopover: true});
                        }
                        /*directionsService.route({
                                                    origin: inicio,
                                                    destination: fin,
                                                    waypoints: GeoDocwaypts,
                                                    optimizeWaypoints: true,
                                                    travelMode: 'DRIVING'
                                                }, function(response, status) {
                                                    if (status === 'OK') {
                                                        directionsRenderer.setDirections(response);
                                                        var route = response.routes[0];
                                                    } else {
                                                        alert('Directions request failed due to ' + status);
                                                    }
                        });*/
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
            else{
                alert("Usuario invalido!");
            }
    }

    function cargarMapaComparaciones(){
        
        var poligonoid = $('#ddlGeoPoligino')[0].value;
        var rutaid = $('#ddlGeoRuta')[0].value;
        if (poligonoid != '' || rutaid != ''){
            var informacion = {poligono: poligonoid, ruta: rutaid };
            $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/mapacomparaciones']); ?>',
               type: 'POST',
               data: informacion,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                    var resultado = JSON.parse(data);
                    if (poligonoid != '') calcularPoligono(resultado.poligonos);
                    if (rutaid != '') calcularRuta(resultado.rutas);
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
    }

    function calcularPoligono(poligono){
        var coordenadas = [];
        for(var i = 0; i < poligono.length; i++){
            var coordenada = { lat: Number(poligono[i]["latitud"]), lng: Number(poligono[i]["longitud"]) };
            coordenadas.push(coordenada);
        }
        if (GeoPoligono != undefined) GeoPoligono.setMap(null);
        GeoPoligono = new google.maps.Polygon({
                                                        paths: coordenadas,
                                                        strokeColor: '#FF0000',
                                                        strokeOpacity: 0.8,
                                                        strokeWeight: 2,
                                                        fillColor: '#FF0000',
                                                        fillOpacity: 0.35
                                                    });
        GeoPoligono.setMap(mapGeo);
        google.maps.event.addListener(mapGeo, 'overlaycomplete', function(e) {GeoPoligono = e.overlay; });
    }

    function calcularRuta(ruta){
        if (GeoRutaServiceComp == undefined)
            GeoRutaServiceComp = new google.maps.DirectionsService;
        if (GeoRutaRendererComp == undefined)
            GeoRutaRendererComp = new google.maps.DirectionsRenderer;
        var coordenadas = [];
        var inicio;
        var fin;
        var inicial = false;
        for(var i = 0; i < ruta.length; i++){
            var coordenada = { lat: Number(ruta[i]["latitud"]), lng: Number(ruta[i]["longitud"]) };
            if (!inicial) inicio = coordenada;
            fin = coordenada;
            if ((i > 0) && (i < ruta.length - 1)) coordenadas.push({ location: coordenada, stopover: true});
        }        
        if (GeoRutaRendererComp != undefined) GeoRutaRendererComp.setMap(null);
        GeoRutaRendererComp.setMap(mapGeo);

        GeoRutaServiceComp.route({
                origin: inicio,
                destination: fin,
                waypoints: coordenadas,
                optimizeWaypoints: true,
                travelMode: 'DRIVING'
            }, function(response, status) {
                if (status === 'OK') {
                    GeoRutaRendererComp.setDirections(response);
                    var route = response.routes[0];
                } else {
                    alert('Directions request failed due to ' + status);
                }
        });
    }

    function cargarMapaClientes(){
        var opt = $('option[value="'+$("#ddlGeoCliente").val()+'"]');
        var id=opt.length ? opt.attr('id') : 'NO OPTION';
        console.log("ID Cliente: "+id);

        if(id!='NO OPTION'){

            var clienteid = $('#ddlGeoCliente')[0].value;
            var documentoid = $('#ddlGeoCliDocumento')[0].value;
            var grupoid = $('#ddlGeoGrupoCliente')[0].value;
            var territorioid = $('#ddlGeoTerritorioCliente')[0].value;
            var fechaDocumentoInicio = $('#txtGeoCliFIni').val();
            var fechaDocumentoFin = $('#txtGeoCliFFin').val();
            var informacion = {
                cliente: id, 

                documento: documentoid, 
                grupo: grupoid, 
                territorio: territorioid,
                inicio: fechaDocumentoInicio,
                fin: fechaDocumentoFin
            };
            deleteMarkers(2);
            $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/mapaporcliente']); ?>',
               type: 'POST',
               data: informacion,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                    var result = JSON.parse(data);
                    var resultado = result["resultado"];
                    tablas.tabla2 = resultado;
                    console.log(resultado);
                    var coordenadas = [];
                    //var directionsService = new google.maps.DirectionsService;
                    //var directionsRenderer = new google.maps.DirectionsRenderer;
                    var GeoDocwaypts = [];
                    var inicio = {};
                    var fin = {}

                    $('#tblResultado thead > th').remove();
                    $('#tblResultado tbody > tr').remove();
                    var tabla = $('#tblResultado');
                    tabla.find('thead').append('<th>CLIENTE</th><th>DOCUMENTO</th><th>TIPO DOC.</th><th>GRUPO</th><th>DIRECCIÓN</th><th>FECHA</th><th>USUARIO</th>');
                    var cuerpo = '';
                    var InformacionAdicional = '';
                    var coordenadaInterna = true;

                    for(var i=0; i < 24 && i < resultado.length; i++){
                        InformacionAdicional ={CardCode:resultado[i]["CardCode"],CardName:resultado[i]["CardName"]};

                        cuerpo = '';
                        cuerpo = '<tr id="tr-tabla2-'+i+'" onclick="ordenaEtiquetaTabla2(this)"><td>' + resultado[i]["CardCode"] + ' - ' + resultado[i]["CardName"] + '</td>' +
                                '<td>' + resultado[i]["idDocPedido"] + '</td>' +
                                '<td>' + getTipoDoc(resultado[i]["DocType"]) + '</td>' +
                                '<td>' + resultado[i]["GroupCode"] + '</td>' +
                                '<td>' + resultado[i]["Direccion"] + '</td>' +
                                 '<td style="display: none;">' + resultado[i]["U_LATITUD"] + '</td>' +
                                 '<td style="display: none;">' + resultado[i]["U_LONGITUD"] + '</td>' +
                                 '<td>' + resultado[i]["fecharegistro"] + '</td>' +
                                 '<td>' + resultado[i]["username"] + '</td></tr>';
                        tabla.find('tbody').append(cuerpo);

                        var xy = { lat: Number(resultado[i]["U_LATITUD"]), lng: Number(resultado[i]["U_LONGITUD"]) }; 
                        //var xy = { lat: Number(resultado[i]["U_LATITUD"]), lng: Number(resultado[i]["U_LONGITUD"]) }; 
                        if (primeracoordenada){                            
                            mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                            //directionsRenderer.setMap(mapGeo);
                            inicio = xy;
                            primeracoordenada = false;
                            coordenadaInterna = false;
                        }
                        else if (coordenadaInterna){
                            coordenadaInterna = false;
                            inicio = xy;
                            mapGeo.setCenter(xy);
                        }
                        console.log(xy);
                        placeMarker(xy, 2, InformacionAdicional);
                        coordenadas.push(xy);
                        fin = xy;
                        GeoDocwaypts.push({ location: xy, stopover: true});
                        mapa2.push({ location: xy, stopover: true});
                    }
                    /*directionsService.route({
                                                origin: inicio,
                                                destination: fin,
                                                waypoints: GeoDocwaypts,
                                                optimizeWaypoints: true,
                                                travelMode: 'DRIVING'
                                            }, function(response, status) {
                                                if (status === 'OK') {
                                                    directionsRenderer.setDirections(response);
                                                    var route = response.routes[0];
                                                } else {
                                                    alert('Directions request failed due to ' + status);
                                                }
                    });*/
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
        else{
            alert("Cliente no existe!");
        }
    }

    function geoBuscarSoloCliente(){
       /* var idclientes = [];
        $(".selectCheboxGeoPorCliente").each(function (index, value) {
                var dx = $(value).is(':checked');
                if (dx == true) {
                    idclientes.push($(value).val());
                }
            });        
        var datosClientes = { clientes: idclientes };*/
        var opt = $('option[value="'+$("#ddlGeoSoloCliente").val()+'"]');
        var clienteid=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);

        if(clienteid!='NO OPTION'){

            var clienteid = $('#ddlGeoSoloCliente')[0].value;
            var grupoid = $('#ddlGeoSoloGrupoCliente')[0].value;
            var territorioid = $('#ddlGeoSoloTerritorioCliente')[0].value;
            var fechaDocumentoInicio = $('#txtGeoSoloCliFIni').val();
            var fechaDocumentoFin = $('#txtGeoSoloCliFFin').val();
            var informacion = {
                cliente: clienteid, 
                grupo: grupoid, 
                territorio: territorioid,
                inicio: fechaDocumentoInicio,
                fin: fechaDocumentoFin
            };
            deleteMarkers(3);
            $.ajax({
                   url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/cargarpuntossoloclientes']); ?>',
                   type: 'POST',
                   data: informacion,
                   success: (data, status, xhr) => {
                       if (status == 'success'){
                        var result = JSON.parse(data);
                        var resultado = result["resultado"];
                        tablas.tabla3 = resultado;
                        console.log(result);
                        console.log(resultado);
                        var coordenadas = [];
                        var directionsService = new google.maps.DirectionsService;
                        var directionsRenderer = new google.maps.DirectionsRenderer;
                        var waypts = [];
                        var inicio = {};
                        var fin = {}

                        $('#tblResultado thead > th').remove();
                        $('#tblResultado tbody > tr').remove();
                        var tabla = $('#tblResultado');
                        tabla.find('thead').append('<th>CLIENTE</th><th>GRUPO</th><th>LONGITUD</th><th>LATITUD</th><th>FECHA</th>');
                        var cuerpo = '';
                        var InformacionAdicional = '';
                        var coordenadaInterna = true;
                        for(var i=0;  i < resultado.length; i++){
                            InformacionAdicional ={CardCode:resultado[i]["CardCode"],CardName:resultado[i]["CardName"]};

                            cuerpo = '';
                            cuerpo = '<tr id="tr-tabla3-'+i+'" onclick="ordenaEtiquetaTabla3(this)"><td>' + resultado[i]["CardName"] + '</td>' +
                                     '<td>' + resultado[i]["GroupCode"] + '</td>' +
                                     '<td>' + resultado[i]["lat"] + '</td>' +
                                     '<td>' + resultado[i]["lon"] + '</td>' +
                                     '<td>' + resultado[i]["DateUpdate"] + '</td></tr>';
                            tabla.find('tbody').append(cuerpo);

                            var xy = { lat: Number(resultado[i]["lat"]), lng: Number(resultado[i]["lon"]) }; 
                            // console.log(resultado[i]["CardName"]);
                            // console.log(xy);

                            if (primeracoordenada){
                                console.log()
                                console.log(xy);
                                mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});                        
                                inicio = xy;
                                primeracoordenada = false;
                                coordenadaInterna = false;
                            }
                            else if (coordenadaInterna){
                                coordenadaInterna = false;
                                inicio = xy;
                                mapGeo.setCenter(xy);
                            }
                            var info = {
                                CardCode:  resultado[i]["CardCode"],
                                CardName:  resultado[i]["CardName"],
                                Territory: resultado[i]["Territory"]
                            };
                            placeMarker(xy, 3, InformacionAdicional);
                            coordenadas.push(xy);
                            fin = xy;
                            waypts.push({ location: xy, stopover: true});
                            mapa3.push({ location: xy, stopover: true});
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
            else{
                alert("Cliente invalido.");
            }
    }

    function cargarMapaVisitas(){
        var usuarioid = $('#ddlVisusuario')[0].value;
        var fechaDocumentoInicio = $('#txtGeoVisFIni').val();
        var fechaDocumentoFin = $('#txtGeoVisFFin').val();
        console.log('entro aqui');
        console.log(usuarioid);
        console.log(fechaDocumentoInicio);
        console.log(fechaDocumentoFin);
        var informacion = {usuario: usuarioid, inicio: fechaDocumentoInicio, fin: fechaDocumentoFin};
        deleteMarkers(4);
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['geolocalizacion/mapavisitas']); ?>',
            type: 'POST',
            data: informacion,
            success: (data, status, xhr) => {
                if (status == 'success'){
                    var resultado = JSON.parse(data);
                    tablas.tabla4 = resultado;
                    console.log(resultado);
                    var coordenadas = [];
                    var directionsService = new google.maps.DirectionsService;
                    var directionsRenderer = new google.maps.DirectionsRenderer;
                    var GeoDocwaypts = [];
                    var inicio = {};
                    var fin = {}

                    $('#tblResultado thead > th').remove();
                    $('#tblResultado tbody > tr').remove();
                    var tabla = $('#tblResultado');
                    tabla.find('thead').append('<th>CLIENTE</th><th>FECHA HORA</th><th>MOTIVO</th><th>DESCRIPCION</th><th>LATITUD </th><th> LONGITUD</th>');
                    var cuerpo = '';
                    var InformacionAdicional = '';
                    var coordenadaInterna = true;
                    for(var i=0; i < 24 && i < resultado.length; i++){
                        InformacionAdicional ={CardCode:resultado[i]["CardCode"],CardName:resultado[i]["CardName"]};

                        cuerpo = '';
                        cuerpo = '<tr id="tr-tabla4-'+i+'" onclick="ordenaEtiquetaTabla4(this)"><td>' + resultado[i]["CardCode"] + ' ' + resultado[i]["CardName"]  + '</td>' +
                                    '<td>' + resultado[i]["fecha"] +' ' +resultado[i]["hora"]+'</td>' +
                                    '<td>'  + resultado[i]["motivoRazon"] +'</td>' +
                                    '<td>' + resultado[i]["descripcion"] + '</td>' +
                                    '<td>' + resultado[i]["lat"] + '</td>' +
                                    '<td>' + resultado[i]["lng"] + '</td></tr>';
                        tabla.find('tbody').append(cuerpo);

                        var xy = { lat: Number(resultado[i]["lat"]), lng: Number(resultado[i]["lng"]) }; 
                        if (primeracoordenada){                            
                            mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                            //directionsRenderer.setMap(mapGeo);
                            inicio = xy;
                            primeracoordenada = false;
                            coordenadaInterna = false;
                        }
                        else if (coordenadaInterna){
                            coordenadaInterna = false;
                            inicio = xy;
                            mapGeo.setCenter(xy);
                        }
                        placeMarker(xy, 5, InformacionAdicional);
                        coordenadas.push(xy);
                        fin = xy;
                        GeoDocwaypts.push({ location: xy, stopover: true});
                        mapa4.push({ location: xy, stopover: true});
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
    
    function cargarMapaRutas(){
        var opt = $('option[value="'+$("#operador").val()+'"]');
        var idVendedor=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);
        if(idVendedor!=undefined){
            if(idVendedor!='NO OPTION'){
                //alert(idVendedor+" - "+$("#DIA").val());
                var informacion = {vendedor: idVendedor, dia: $("#DIA").val()};
                deleteMarkers(4);
                $.ajax({
                    url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocabeceraterritorio/listaclienterutas']); ?>',
                    type: 'POST',
                    data: informacion,
                    success: (data, status, xhr) => {
                        if (status == 'success'){
                            var resultado = JSON.parse(data);
                            tablas.tabla5 = resultado;
                            console.log(resultado);
                            var coordenadas = [];
                            var directionsService = new google.maps.DirectionsService;
                            var directionsRenderer = new google.maps.DirectionsRenderer;
                            var GeoDocwaypts = [];
                            var inicio = {};
                            var fin = {}

                            $('#tblResultado thead > th').remove();
                            $('#tblResultado tbody > tr').remove();
                            var tabla = $('#tblResultado');
                            tabla.find('thead').append('<th>Nro</th><th>Codigo</th><th>Nombre</th><th>Dirección</th><th>Territorio </th>');
                            var cuerpo = '';
                            var InformacionAdicional = '';
                            var coordenadaInterna = true;
                            for(var i=0; i < resultado.length; i++){
                                InformacionAdicional ={CardCode:resultado[i]["cardcode"],CardName:resultado[i]["cardname"]};
                                           
                                cuerpo = '';
                                cuerpo = '<tr id="tr-tabla5-'+i+'" onclick="ordenaEtiquetaTabla5(this)"><td>' +(i+1)+ '</td>' +
                                            '<td>' + resultado[i]["cardcode"]+'</td>' +
                                            '<td>'  + resultado[i]["cardname"] +'</td>' +
                                            '<td>' + resultado[i]["calle"] + '</td>' +
                                            '<td>' + resultado[i]["territoryname"] + '</td>' +
                                            '<td style="display: none;">' + resultado[i]["latitud"] + '</td>' +
                                            '<td style="display: none;">' + resultado[i]["longitud"] + '</td>' +
                                            '</tr>';
                                tabla.find('tbody').append(cuerpo);

                                var xy = { lat: Number(resultado[i]["latitud"]), lng: Number(resultado[i]["longitud"]) }; 
                                if (primeracoordenada){                            
                                    mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                                    //directionsRenderer.setMap(mapGeo);
                                    inicio = xy;
                                    primeracoordenada = false;
                                    coordenadaInterna = false;
                                }
                                else if (coordenadaInterna){
                                    coordenadaInterna = false;
                                    inicio = xy;
                                    mapGeo.setCenter(xy);
                                }
                                placeMarker(xy, 5, InformacionAdicional);
                                coordenadas.push(xy);
                                fin = xy;
                                GeoDocwaypts.push({ location: xy, stopover: true});
                                mapa5.push({ location: xy, stopover: true});
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
            else{
                alert("Vendedor incorrecto.");
            }
        }
        else{
            alert("Seleccione un vendedor");
        }
    } 
    function cargarMapaRutasDespacho(){
        var opt = $('option[value="'+$("#despachador").val()+'"]');
        var idVendedor=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);
        if(idVendedor!=undefined){
            if(idVendedor!='NO OPTION'){
                console.log(idVendedor+" - "+$("#txtfechapicking").val());
                var informacion = {vendedor: idVendedor, fecha: $("#txtfechapicking").val()};
                deleteMarkers(4);
                $.ajax({
                    url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocabeceraterritorio/listaclienterutasdespacho']); ?>',
                    type: 'POST',
                    data: informacion,
                    success: (data, status, xhr) => {
                        if (status == 'success'){
                            var resultado = JSON.parse(data);
                            tablas.tabla6 = resultado;
                            console.log(resultado);
                            var coordenadas = [];
                            var directionsService = new google.maps.DirectionsService;
                            var directionsRenderer = new google.maps.DirectionsRenderer;
                            var GeoDocwaypts = [];
                            var inicio = {};
                            var fin = {}

                            $('#tblResultado thead > th').remove();
                            $('#tblResultado tbody > tr').remove();
                            var tabla = $('#tblResultado');
                            tabla.find('thead').append('<th>Nro</th><th>Codigo</th><th>Nombre</th><th>Dirección</th><th>Territorio </th><th>Doc Num</th>');
                            var cuerpo = '';
                            var InformacionAdicional = '';
                            var coordenadaInterna = true;
                            for(var i=0; i < resultado.length; i++){
                                InformacionAdicional ={CardCode:resultado[i]["cardcode"],CardName:resultado[i]["cardname"]};
                                           
                                cuerpo = '';
                                cuerpo = '<tr id="tr-tabla6-'+i+'" onclick="ordenaEtiquetaTabla5(this)"><td>' +(i+1)+ '</td>' +
                                            '<td>' + resultado[i]["cardcode"]+'</td>' +
                                            '<td>'  + resultado[i]["cardname"] +'</td>' +
                                            '<td>' + resultado[i]["calle"] + '</td>' +
                                            '<td>' + resultado[i]["territoryname"] + '</td>' +

                                            '<td style="display: none;">' + resultado[i]["latitud"] + '</td>' +
                                            '<td style="display: none;">' + resultado[i]["longitud"] + '</td>' +
                                            '<td>' + resultado[i]["DocNum"] + '</td>' +
                                            '</tr>';
                                tabla.find('tbody').append(cuerpo);

                                var xy = { lat: Number(resultado[i]["latitud"]), lng: Number(resultado[i]["longitud"]) }; 
                                if (primeracoordenada){                            
                                    mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                                    //directionsRenderer.setMap(mapGeo);
                                    inicio = xy;
                                    primeracoordenada = false;
                                    coordenadaInterna = false;
                                }
                                else if (coordenadaInterna){
                                    coordenadaInterna = false;
                                    inicio = xy;
                                    mapGeo.setCenter(xy);
                                }
                                placeMarker(xy, 5, InformacionAdicional);
                                coordenadas.push(xy);
                                fin = xy;
                                GeoDocwaypts.push({ location: xy, stopover: true});
                                mapa6.push({ location: xy, stopover: true});
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
            else{
                alert("Despachador incorrecto.");
            }
        }
        else{
            alert("Seleccione un despachador");
        }
    }

    function cargarMapaTerritorioCliente(){
        var opt = $('option[value="'+$("#txtterritorio").val()+'"]');
        var territorioid=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);
        if(territorioid!=undefined){
            if(territorioid!='NO OPTION'){
                console.log(territorioid);
                var informacion = {territorioid: territorioid};
                deleteMarkers(4);
                $.ajax({
                    url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocabeceraterritorio/listaterritoriocliente']); ?>',
                    type: 'POST',
                    data: informacion,
                    success: (data, status, xhr) => {
                        if (status == 'success'){
                            var result = JSON.parse(data);
                            console.log(result);
                            var resultado=result.clientescon;
                            console.log(resultado);
                            tablas.tabla7 = resultado;
                            
                            var coordenadas = [];
                            var directionsService = new google.maps.DirectionsService;
                            var directionsRenderer = new google.maps.DirectionsRenderer;
                            var GeoDocwaypts = [];
                            var inicio = {};
                            var fin = {}

                            $('#tblResultado thead > th').remove();
                            $('#tblResultado tbody > tr').remove();
                            var tabla = $('#tblResultado');
                            tabla.find('thead').append('<th>Nro</th><th>Codigo</th><th>Nombre</th><th>Dirección</th><th>Territorio </th><th>Latitud</th><th>Longitud</th>');
                            var cuerpo = '';
                            var InformacionAdicional = '';
                            var coordenadaInterna = true;

                            for(var i=0; i < resultado.length; i++){
                                InformacionAdicional ={CardCode:resultado[i]["cardcode"],CardName:resultado[i]["cardname"]};
                                           
                                cuerpo = '';
                                cuerpo = '<tr id="tr-tabla7-'+i+'" onclick="ordenaEtiquetaTabla5(this)"><td>' +(i+1)+ '</td>' +
                                            '<td>' + resultado[i]["cardcode"]+'</td>' +
                                            '<td>'  + resultado[i]["cardname"] +'</td>' +
                                            '<td>' + resultado[i]["calle"] + '</td>' +
                                            '<td>' + resultado[i]["territoryname"] + '</td>' +
                                            '<td style="">' + resultado[i]["latitud"] + '</td>' +
                                            '<td style="">' + resultado[i]["longitud"] + '</td>' +
                                            '</tr>';
                                tabla.find('tbody').append(cuerpo);

                                var xy = { lat: Number(resultado[i]["latitud"]), lng: Number(resultado[i]["longitud"]) }; 
                                if (primeracoordenada){                            
                                    mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                                    //directionsRenderer.setMap(mapGeo);
                                    inicio = xy;
                                    primeracoordenada = false;
                                    coordenadaInterna = false;
                                }
                                else if (coordenadaInterna){
                                    coordenadaInterna = false;
                                    inicio = xy;
                                    mapGeo.setCenter(xy);
                                }
                                placeMarker(xy, 5, InformacionAdicional);
                                coordenadas.push(xy);
                                fin = xy;
                                GeoDocwaypts.push({ location: xy, stopover: true});
                                mapa7.push({ location: xy, stopover: true});
                            }

                            ///clientes sin coordenadas//
                            $('#div-clientessin').show();
                            $('#tblResultado-sin thead > th').remove();
                            $('#tblResultado-sin tbody > tr').remove();
                            var tabla = $('#tblResultado-sin');
                            tabla.find('thead').append('<th>Nro</th><th>Codigo</th><th>Nombre</th><th>Dirección</th><th>Territorio </th><th>Latitud</th><th>Longitud</th>');
                            var cuerpo = '';
               
                            var resultado2=result.clientessin;
                            console.log(resultado2);
                            for(var i=0; i < resultado2.length; i++){

                                cuerpo = '<tr id="tr-tabla7-'+i+'" ><td>' +(i+1)+ '</td>' +
                                            '<td>' + resultado2[i]["cardcode"]+'</td>' +
                                            '<td>'  + resultado2[i]["cardname"] +'</td>' +
                                            '<td>' + resultado2[i]["calle"] + '</td>' +
                                            '<td>' + resultado2[i]["territoryname"] + '</td>' +
                                            '<td style="">' + resultado2[i]["latitud"] + '</td>' +
                                            '<td style="">' + resultado2[i]["longitud"] + '</td>' +
                                            '</tr>';
                                tabla.find('tbody').append(cuerpo);

                            }
                            //fin de clientes sin
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
            else{
                alert("Territorio Incorrecto.");
            }
        }
        else{
            alert("Seleccione un Territorio");
        }
    }

    function cargarMapaTerritorioClienteAsig(){
        var opt = $('option[value="'+$("#txtterritorio-asig").val()+'"]');
        var territorioid=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);
        if(territorioid!=undefined){
            if(territorioid!='NO OPTION'){
                console.log(territorioid);
                var informacion = {territorioid: territorioid};
                deleteMarkers(4);
                $.ajax({
                    url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocabeceraterritorio/listaterritoriocliente']); ?>',
                    type: 'POST',
                    data: informacion,
                    success: (data, status, xhr) => {
                        if (status == 'success'){
                            var result = JSON.parse(data);
                            console.log(result);
                            var resultado=result.clientescon;
                            console.log(resultado);
                            tablas.tabla7 = resultado;
                            
                            var coordenadas = [];
                            var directionsService = new google.maps.DirectionsService;
                            var directionsRenderer = new google.maps.DirectionsRenderer;
                            var GeoDocwaypts = [];
                            var inicio = {};
                            var fin = {}

                            $('#tblResultado thead > th').remove();
                            $('#tblResultado tbody > tr').remove();
                            var tabla = $('#tblResultado');
                            tabla.find('thead').append('<th>Nro</th><th>Asig.</th><th>Codigo</th><th>Nombre</th><th>Territorio </th><th>Latitud</th><th>Longitud</th>');
                            var cuerpo = '';
                            var InformacionAdicional = '';
                            var coordenadaInterna = true;

                            for(var i=0; i < resultado.length; i++){
                                InformacionAdicional ={CardCode:resultado[i]["cardcode"],CardName:resultado[i]["cardname"]};
                                           
                                cuerpo = '';
                                cuerpo = '<tr id="tr-tabla8-'+i+'" onclick="ordenaEtiquetaTabla8(this)"><td>' +(i+1)+ '</td>' +
                                            '<td><input type="checkbox" class="selectChebox" id="'+resultado[i]["cardcode"]+'" value="'+resultado[i]["cardcode"]+'"> </td>' +
                                            '<td>' + resultado[i]["cardcode"]+'</td>' +
                                            '<td>'  + resultado[i]["cardname"] +'</td>' +
                                            '<td>' + resultado[i]["territoryname"] + '</td>' +
                                            '<td style="">' + resultado[i]["latitud"] + '</td>' +
                                            '<td style="">' + resultado[i]["longitud"] + '</td>' +
                                            '</tr>';
                                tabla.find('tbody').append(cuerpo);

                                var xy = { lat: Number(resultado[i]["latitud"]), lng: Number(resultado[i]["longitud"]) }; 
                                if (primeracoordenada){                            
                                    mapGeo = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                                    //directionsRenderer.setMap(mapGeo);
                                    inicio = xy;
                                    primeracoordenada = false;
                                    coordenadaInterna = false;
                                }
                                else if (coordenadaInterna){
                                    coordenadaInterna = false;
                                    inicio = xy;
                                    mapGeo.setCenter(xy);
                                }
                                placeMarker(xy, 5, InformacionAdicional);
                                coordenadas.push(xy);
                                fin = xy;
                                GeoDocwaypts.push({ location: xy, stopover: true});
                                mapa7.push({ location: xy, stopover: true});
                            }

                            ///clientes sin coordenadas//
                        
                            $('#tblResultado-sin thead > th').remove();
                            $('#tblResultado-sin tbody > tr').remove();
                            var tabla = $('#tblResultado-sin');
                            tabla.find('thead').append('<th>Nro</th><th>Codigo</th><th>Nombre</th><th>Dirección</th><th>Territorio </th><th>Latitud</th><th>Longitud</th>');
                            var cuerpo = '';
               
                            var resultado2=result.clientessin;

                            if(resultado2.length>0){
                                $('#div-clientessin').show(); 
                            }
                            else{
                                $('#div-clientessin').hide();
                            }

                            console.log(resultado2);
                            for(var i=0; i < resultado2.length; i++){

                                cuerpo = '<tr id="tr-tabla8-'+i+'" ><td>' +(i+1)+ '</td>' +
                                            '<td>' + resultado2[i]["cardcode"]+'</td>' +
                                            '<td>'  + resultado2[i]["cardname"] +'</td>' +
                                            '<td>' + resultado2[i]["calle"] + '</td>' +
                                            '<td>' + resultado2[i]["territoryname"] + '</td>' +
                                            '<td style="">' + resultado2[i]["latitud"] + '</td>' +
                                            '<td style="">' + resultado2[i]["longitud"] + '</td>' +
                                            '</tr>';
                                tabla.find('tbody').append(cuerpo);

                            }
                            //fin de clientes sin
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
            else{
                alert("Territorio Incorrecto.");
            }
        }
        else{
            alert("Seleccione un Territorio");
        }
    }    

    function deleteMarkers(marcador) {
        // mapGeo.setMap(null);
        initMap();
        switch (marcador){
            //limpia todos los mapas
            case 0:
                mapa0 = [];
                break;
            case 1:
                mapa1 = [];
                break;
            case 2:
                mapa2 = [];
                break;
            case 3:
                mapa3 = [];
                break;
            case 4:
                mapa4 = [];
                break;
            case 5:
                mapa5 = [];
                break;
            case 6:
                mapa6 = [];
                break;
            case 7:
                mapa7 = [];
                break;
        };
        $('#div-clientessin').hide();
        markers = [];
    }

    function mostrarTabla(busqueda){
        console.log('entro a mostrarTabla');
        //console.log(tabla);
        console.log(busqueda);
        var cuerpo = '';
        var coordenadaInterna = true;
        $('#tblResultado thead > th').remove();
        $('#tblResultado tbody > tr').remove();
        var tabla = $('#tblResultado');
        switch(busqueda){
            case '0':
                deleteMarkers(0);
                console.log(tablas.tabla0);
                /*if (tablas.tabla0.length > 0){
                    var resultado = tablas.tabla0;
                    tabla.find('thead').append('<th>EQUIPO</th><th>USUARIO</th><th>LONGITUD</th><th>LATITUD</th><th>FECHA</th><th>HORA</th>');                
                    for(var i=0;  i < resultado.length; i++){
                        cuerpo = '<tr><td>' + resultado[i]["equipo"] + '</td>' +
                                 '<td>' + resultado[i]["username"] + '</td>' +
                                 '<td>' + resultado[i]["latitud"] + '</td>' +
                                 '<td>' + resultado[i]["longitud"] + '</td>' +
                                 '<td>' + resultado[i]["fecha"] + '</td>' +
                                 '<td>' + resultado[i]["hora"] + '</td></tr>';
                        tabla.find('tbody').append(cuerpo);                    
                        var xy = { lat: Number(resultado[i]["latitud"]), lng: Number(resultado[i]["longitud"]) };                     
                        if (coordenadaInterna){
                            coordenadaInterna = false;                        
                            mapGeo.setCenter(xy);
                        }  
                         
                        placeMarker(xy, 4, null);                 
                    }
                }*/
            break;
            case '1':
                deleteMarkers(1);
                console.log(tablas.tabla1);
                /*if (tablas.tabla1.length > 0){
                    var resultado = tablas.tabla1;
                    tabla.find('thead').append('<th>USUARIO</th><th>DOCUMENTO</th><th>CLIENTE</th><th>TIPO DOC.</th><th>FECHA</th>');
                    for(var i=0; i < 24 && i < resultado.length; i++){
                        cuerpo = '';
                        cuerpo = '<tr><td>' + resultado[i]["username"] + '</td>' +
                                 '<td>' + resultado[i]["idDocPedido"] + '</td>' +
                                 '<td>' + resultado[i]["CardCode"] + ' - ' + resultado[i]["CardName"] + '</td>' +
                                 '<td>' + getTipoDoc(resultado[i]["Doctype"]) + '</td>' +
                                //  '<td>' + resultado[i]["U_LATITUD"] + '</td>' +
                                //  '<td>' + resultado[i]["U_LONGITUD"] + '</td>' +
                                 '<td>' + resultado[i]["fecharegistro"] + '</td></tr>';
                        tabla.find('tbody').append(cuerpo);
                        var xy = { lat: Number(resultado[i]["U_LATITUD"]), lng: Number(resultado[i]["U_LONGITUD"]) }; 
                        if (coordenadaInterna){
                            coordenadaInterna = false;
                            mapGeo.setCenter(xy);
                        }
                        
                        placeMarker(xy, 1, null);
                    }
                }*/
            break;
            case '2':
                deleteMarkers(2);
                console.log(tablas.tabla2);
                /*if (tablas.tabla2.length > 0){
                    var resultado = tablas.tabla2;
                    tabla.find('thead').append('<th>CLIENTE</th><th>DOCUMENTO</th><th>TIPO DOC.</th><th>GRUPO</th><th>DIRECCIÓN</th><th>FECHA</th><th>USUARIO</th>');
                    for(var i=0; i < 24 && i < resultado.length; i++){
                        cuerpo = '';
                        cuerpo = '<tr><td>' + resultado[i]["CardCode"] + ' - ' + resultado[i]["CardName"] + '</td>' +
                                '<td>' + resultado[i]["idDocPedido"] + '</td>' +
                                '<td>' + getTipoDoc(resultado[i]["DocType"]) + '</td>' +
                                '<td>' + resultado[i]["GroupCode"] + '</td>' +
                                '<td>' + resultado[i]["Direccion"] + '</td>' +
                                //  '<td>' + resultado[i]["U_LATITUD"] + '</td>' +
                                //  '<td>' + resultado[i]["U_LONGITUD"] + '</td>' +
                                 '<td>' + resultado[i]["fecharegistro"] + '</td>' +
                                 '<td>' + resultado[i]["username"] + '</td></tr>';
                        tabla.find('tbody').append(cuerpo);
                        var xy = { lat: Number(resultado[i]["U_LATITUD"]), lng: Number(resultado[i]["U_LONGITUD"]) };                         
                        if (coordenadaInterna){
                            coordenadaInterna = false;
                            mapGeo.setCenter(xy);
                        }
                        
                        placeMarker(xy, 2, null);
                    }
                }*/
            break;
            case '3':
                deleteMarkers(3);
                console.log(tablas.tabla3);
                /*if (tablas.tabla3.length > 0){
                    var resultado = tablas.tabla3;
                    tabla.find('thead').append('<th>CLIENTE</th><th>GRUPO</th><th>LONGITUD</th><th>LATITUD</th><th>FECHA</th>');
                    for(var i=0;  i < resultado.length; i++){
                        cuerpo = '';
                        cuerpo = '<tr><td>' + resultado[i]["CardName"] + '</td>' +
                                 '<td>' + resultado[i]["GroupCode"] + '</td>' +
                                 '<td>' + resultado[i]["lon"] + '</td>' +
                                 '<td>' + resultado[i]["lat"] + '</td>' +
                                 '<td>' + resultado[i]["DateUpdate"] + '</td></tr>';
                        tabla.find('tbody').append(cuerpo);
                        var xy = { lat: Number(resultado[i]["lat"]), lng: Number(resultado[i]["lon"]) }; 
                        if (coordenadaInterna){
                            coordenadaInterna = false;
                            mapGeo.setCenter(xy);
                        }
                        
                        placeMarker(xy, 3, null);
                    }
                }*/
            break;
            case '4':
                deleteMarkers(4);
                console.log('entro al 4');
                console.log(tablas.tabla4);
                /*if (tablas.tabla4.length > 0){
                    var resultado = tablas.tabla4;
                    tabla.find('thead').append('<th>CLIENTE</th><th>FECHA</th><th>DURACION</th><th>LONGITUD</th><th>LATITUD</th>');
                    for(var i = 0; i < 24 && i < resultado.length; i++){
                        cuerpo = '';
                        cuerpo = '<tr><td>' + resultado[i]["CardCode"] + ' ' + resultado[i]["CardName"]  + '</td>' +
                                    '<td>' + resultado[i]["fecha"] + '</td>' +
                                    '<td>' + 'Desde: ' + resultado[i]["hora"] + ' Hasta: ' + resultado[i]["horafin"] + '</td>' +
                                    '<td>' + resultado[i]["lat"] + '</td>' +
                                    '<td>' + resultado[i]["lng"] + '</td></tr>';
                        tabla.find('tbody').append(cuerpo);
                        var xy = { lat: Number(resultado[i]["lat"]), lng: Number(resultado[i]["lng"]) }; 
                        if (coordenadaInterna){
                            coordenadaInterna = false;
                            mapGeo.setCenter(xy);
                        }
                        
                        placeMarker(xy, 5, null);
                    }
                }*/
            break;
            case '5':
                deleteMarkers(5);
                console.log('entro al 5');
                console.log(tablas.tabla4);
            case '6':
                deleteMarkers(6);
                console.log('entro al 5');
                console.log(tablas.tabla4);
            case '7':
                deleteMarkers(7);
                console.log('entro al 5');
                console.log(tablas.tabla4);
               
            break;
        }
    }

    // selecciona o deselecciona los equipos seleccionados
    function checkEquipos(){
        if($("#cbddlEquipo").is(':checked')){
            $("#ddlEquipo option").prop('selected',true);
        }else{
            $("#ddlEquipo option").prop('selected',false);
        }
    }
    
    // cuando se realiza un cambio de equipo manual se setea el checkbox
    function cambioEquipos(){
        $("#cbddlEquipo").prop('checked', false);
    }

    // obtenermos la descripcion del tipo de documento
    function getTipoDoc(tipoDoc){
        switch (tipoDoc) {
            case 'DOF': return 'Oferta'; break;
            case 'DOP': return 'Pedido'; break;
            case 'DFA': return 'Factura'; break;
            case 'DOE': return 'Entrega'; break;
            case 'Pago': return 'Pago'; break;
            default: return 'Desconocido'; break;
        }
    }
    // obtenermos la descripcion del tipo de documento
    function getGrupo(grupo){
        switch (grupo) {
            case 'DOF': return 'Oferta'; break;
            case 'DOP': return 'Pedido'; break;
            case 'DFA': return 'Factura'; break;
            case 'DOE': return 'Entrega'; break;
            case 'Pago': return 'Pago'; break;
            default: return 'Desconocido'; break;
        }
    }

    // limpiamos las pestañas de filtros
    function clearCargaMapa(){
        deleteMarkers(0);
        $("#cbddlEquipo").prop('checked', false);
        checkEquipos();
        $("#txtFIni, #txtFFin, #txtHIni, #txtHFin").val("");
        clearContenedorresultado();
        tablas.tabla0=[];
    }

    function clearCargarMapaDocumentos(){
        deleteMarkers(1);
        //$("#ddlGeoUsuario").val($("#ddlGeoUsuario option:first").val());
        $("#txtGeoUsrFIni, #txtGeoUsrFFin").val("");
        $("#ddlGeoDocumento").val($("#ddlGeoDocumento option:first").val());
        clearContenedorresultado();
        tablas.tabla1=[];
    }

    function clearCargarMapaClientes(){       
        deleteMarkers(2);
        $("#ddlGeoCliente").val($("#ddlGeoCliente option:first").val());
        $("#txtGeoCliFIni, #txtGeoCliFFin").val("");
        $("#ddlGeoCliDocumento").val($("#ddlGeoCliDocumento option:first").val());
        $("#ddlGeoGrupoCliente").val($("#ddlGeoGrupoCliente option:first").val());
        $("#ddlGeoTerritorioCliente").val($("#ddlGeoTerritorioCliente option:first").val());
        clearContenedorresultado();
        tablas.tabla2=[];
    }

    function clearGeoBuscarSoloCliente(){
        deleteMarkers(3);
        $("#ddlGeoSoloCliente").val($("#ddlGeoSoloCliente option:first").val());
        $("#txtGeoSoloCliFIni, #txtGeoSoloCliFFin").val("");
        $("#ddlGeoSoloGrupoCliente").val($("#ddlGeoSoloGrupoCliente option:first").val());
        $("#ddlGeoSoloTerritorioCliente").val($("#ddlGeoSoloTerritorioCliente option:first").val());
        clearContenedorresultado();
        tablas.tabla3=[];
    }

    function clearCargarMapaVisitas(){
        deleteMarkers(4);
        $("#ddlVisusuario").val($("#ddlVisusuario option:first").val());
        $("#txtGeoVisFIni, #txtGeoVisFFin").val("");
        clearContenedorresultado();
        tablas.tabla4=[];
    }

    function clearCargarMapaRutas(){
        deleteMarkers(5);
        $("#ddlVisusuario").val($("#ddlVisusuario option:first").val());
        $("#txtGeoVisFIni, #txtGeoVisFFin").val("");
        clearContenedorresultado();
        tablas.tabla5=[];
    }

    function clearCargarMapaRutasDespacho(){
        deleteMarkers(6);
        clearContenedorresultado();
        tablas.tabla6=[];
    }

    function clearCargarMapaTerritorioCliente(){
        deleteMarkers(7);
        clearContenedorresultado();
        tablas.tabla7=[];
    }

    function clearContenedorresultado(){
        $("#tblResultado thead, #tblResultado tbody").html("");
        $("#searchTerm").val("");
        $("#p-resultado").text("");
    }

    function ordenaEtiquetaTabla0(row){
        var equipo= $(row).find('td').eq(0).html();
        var usuario= $(row).find('td').eq(1).html(); 
        var latitud= $(row).find('td').eq(4).html(); 
        var longitud= $(row).find('td').eq(5).html(); 
        var fecha= $(row).find('td').eq(2).html(); 
        
        var contenidoModal='<b>Equipo: </b>'+equipo+
                    '<br><b>Usuario: </b>'+usuario+
                    '<br><b>Latitud: </b> '+latitud+
                    '<br><b>Longitud: </b> '+longitud+
                    '<br><b>Fecha:</b> '+fecha;
        var contenidoLabel='Equipo: '+equipo+
                    '\nUsuario: '+usuario+
                    '\nLatitud: '+latitud+
                    '\nLongitud: '+longitud+
                    '\nFecha: '+fecha;
        cambiarColor(row);
        mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud);
        
    }


    function ordenaEtiquetaTabla1(row){
        var usuario= $(row).find('td').eq(0).html();
        var documento= $(row).find('td').eq(1).html(); 
        var cliente= $(row).find('td').eq(2).html(); 
        var tipoDoc= $(row).find('td').eq(3).html(); 
        var latitud= $(row).find('td').eq(4).html(); 
        var longitud= $(row).find('td').eq(5).html(); 
        var fecha= $(row).find('td').eq(6).html(); 
        var contenidoModal='<b>Usuario: </b>'+usuario+
                    '<br><b>Documento: </b>'+documento+
                    '<br><b>Cliente:</b> '+cliente+
                    '<br><b>Tipo Doc.:</b> '+tipoDoc+
                    '<br><b>Fecha:</b> '+fecha;
        var contenidoLabel='Usuario: '+usuario+
                    '\nDocumento: '+documento+
                    '\nCliente: '+cliente+
                    '\nTipo Doc.: '+tipoDoc+
                    '\nFecha: '+fecha;
        cambiarColor(row);
        mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud);
        
    }
    
    function ordenaEtiquetaTabla2(row){
        var cliente= $(row).find('td').eq(0).html();
        var documento= $(row).find('td').eq(1).html(); 
        var tipoDoc= $(row).find('td').eq(2).html(); 
        var latitud= $(row).find('td').eq(5).html(); 
        var longitud= $(row).find('td').eq(6).html(); 
        var fecha= $(row).find('td').eq(7).html(); 

        var contenidoModal='<b>Cliente: </b>'+cliente+
                    '<br><b>Documento: </b>'+documento+
                    '<br><b>Tipo Doc.:</b> '+tipoDoc+
                    '<br><b>Fecha:</b> '+fecha;
        var contenidoLabel='Cliente: '+cliente+
                    '\nDocumento: '+documento+
                    '\nTipo Doc.: '+tipoDoc+
                    '\nFecha: '+fecha;
        cambiarColor(row);
        mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud);
        
    }

    function ordenaEtiquetaTabla3(row){
        var cliente= $(row).find('td').eq(0).html();
        var grupo= $(row).find('td').eq(1).html(); 
        var latitud= $(row).find('td').eq(2).html(); 
        var longitud= $(row).find('td').eq(3).html(); 

        var contenidoModal='<b>Cliente: </b>'+cliente+
                    '<br><b>Grupo: </b>'+grupo;
        var contenidoLabel='Cliente: '+cliente+
                    '\nGrupo: '+grupo;
        cambiarColor(row);
        mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud);
        
    }

    function ordenaEtiquetaTabla4(row){
        var cliente= $(row).find('td').eq(0).html();
        var fecha= $(row).find('td').eq(1).html(); 
        var duracion= $(row).find('td').eq(2).html(); 
        var latitud= $(row).find('td').eq(4).html(); 
        var longitud= $(row).find('td').eq(5).html(); 
        var contenidoModal='<b>Cliente: </b>'+cliente+
                    '<br><b>Fecha: </b>'+fecha+
                    '<br><b>Motivo:</b> '+duracion;
        var contenidoLabel='Cliente: '+cliente+
                    '\nFecha: '+fecha+
                    '\nMotivo: '+duracion;
        cambiarColor(row);        
        mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud);
        
    }

    function ordenaEtiquetaTabla5(row){
        var codigo= $(row).find('td').eq(1).html();
        var nombre= $(row).find('td').eq(2).html(); 
        var direccion= $(row).find('td').eq(3).html(); 
        var territorio= $(row).find('td').eq(4).html(); 
        var latitud= $(row).find('td').eq(5).html(); 
        var longitud= $(row).find('td').eq(6).html(); 
        var contenidoModal='<b>Codigo: </b>'+codigo+
                    '<br><b>Nombre: </b>'+nombre+
                    '<br><b>Dirección: </b>'+direccion+
                    '<br><b>Territorio:</b> '+territorio;
       var contenidoLabel='Codigo:'+codigo+
                    '\nNombre: '+nombre+
                    '\nDirección: '+direccion+
                    '\nTerritorio: '+territorio;
        cambiarColor(row);        
        mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud);
        
    }

    function ordenaEtiquetaTabla8(row){
        var codigo= $(row).find('td').eq(2).html();
        var nombre= $(row).find('td').eq(3).html();  
        var territorio= $(row).find('td').eq(4).html(); 
        var latitud= $(row).find('td').eq(5).html(); 
        var longitud= $(row).find('td').eq(6).html(); 
        var contenidoModal='<b>Codigo: </b>'+codigo+
                    '<br><b>Nombre: </b>'+nombre+
                    '<br><b>Territorio:</b> '+territorio;
       var contenidoLabel='Codigo:'+codigo+
                    '\nNombre: '+nombre+
                    '\nTerritorio: '+territorio;
        cambiarColor(row);        
        mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud);
        
    }
    ///muestra los datos en un label de punto seleccionado en la fila de la tabla
    function mostrarEtiqueta(contenidoModal,contenidoLabel,latitud,longitud){
        icono= { url: "http://maps.google.com/mapfiles/ms/icons/orange-dot.png" };
        ubicacion = { lat:Number(latitud), lng: Number(longitud) }; 
        infowindow = new google.maps.InfoWindow({
           content: contenidoModal,
         });
        companyMarker = new google.maps.Marker({ 
            position: ubicacion,
            map: mapGeo,
            icon: icono,
            title:contenidoLabel,
            visible:true
        });
        infowindow.open(map,companyMarker);
    
    }

    ///cambia de color fila
    function cambiarColor(celda){
        if(swRowColor!=null){
            colorTr = swRowColor.style.backgroundColor;
		    swRowColor.style.backgroundColor="#F9FAFC";
            infowindow.close();
        }
        colorTr = celda.style.backgroundColor;
        celda.style.backgroundColor="#F8DC3D";
        swRowColor=celda;
        utimoMarker=companyMarker;  	
	}
    // Reasignar terrritorio
    function reasignarTerritorio(){
        var territoryname=$("#txtterritorio-asig-re").val();
        var opt = $('option[value="'+$("#txtterritorio-asig-re").val()+'"]');
        var territorioid=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);
        if(territorioid!=undefined){
            if(territorioid!='NO OPTION'){

                console.log(territorioid);

                var cardCodeArray=[];
                $(".selectChebox").each(function (index) {    
                    if ($(this).is(':checked')) {
                        console.log($(this).val());
                        cardCodeArray.push($(this).val());
                    } else {
                       
                    }
                   
                });
                console.log(cardCodeArray);
                var informacion = {CardCode: cardCodeArray,TerritoryId:territorioid,TerritoryName:territoryname};

                $.ajax({
                    url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['clientesterritorio/reasignarterritorio']); ?>',
                    type: 'POST',
                    data: informacion,
                    success: (data, status, xhr) => {
                        if (status == 'success'){
                            var result = JSON.parse(data);
                            console.log(result);
                            cargarMapaTerritorioClienteAsig();
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
            else{
                alert("Territorio Incorrecto.");
            }
        }
        else{
            alert("Seleccione un Territorio");
        }

       

    }
    function doSearch(tablalista,buscador,resultado){
        const tableReg = document.getElementById(tablalista);
        const searchText = document.getElementById(buscador).value.toLowerCase();
        let total = 0;

        // Recorremos todas las filas con contenido de la tabla
        for (let i = 0; i < tableReg.rows.length; i++) {
            // Si el td tiene la clase "noSearch" no se busca en su cntenido
            if (tableReg.rows[i].classList.contains("noSearch")) {
                continue;
            }

            let found = false;
            const cellsOfRow = tableReg.rows[i].getElementsByTagName('td');
            // Recorremos todas las celdas
            for (let j = 0; j < cellsOfRow.length && !found; j++) {
                const compareWith = cellsOfRow[j].innerHTML.toLowerCase();
                // Buscamos el texto en el contenido de la celda
                if (searchText.length == 0 || compareWith.indexOf(searchText) > -1) {
                    found = true;
                    total++;
                }
            }
            if (found) {
                tableReg.rows[i].style.display = '';
            } else {
                // si no ha encontrado ninguna coincidencia, esconde la
                // fila de la tabla
                tableReg.rows[i].style.display = 'none';
            }
        }

        // mostramos las coincidencias
       // const lastTR=tableReg.rows[tableReg.rows.length-1];
        //const td=lastTR.querySelector("td");
        //lastTR.classList.remove("hide", "red");
        if (searchText == "") {
           // lastTR.classList.add("hide");
            $("#"+resultado).text("");
        } else if (total) {
            $("#"+resultado).text("Se ha encontrado "+total+" resultado"+((total>1)?"s":""));
           // td.innerHTML="Se ha encontrado "+total+" coincidencia"+((total>1)?"s":"");
        } else {
            //lastTR.classList.add("red");
            $("#"+resultado).text("No se han encontrado resultados");
            //td.innerHTML="No se han encontrado coincidencias";
        }
    }
</script>

