<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
//$this->title = 'Bonificacionescas';
use yii\widgets\Pjax;$this->title = 'Bonificaciones';
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use backend\models\Bonificacionde1;
use backend\models\Bonificacionde2;
//use kartik\export\FILTER_SELECT2;

ini_set('max_execution_time', 9000);
ini_set('memory_limit',"20000M");
$this->params['breadcrumbs'][] = $this->title;

function getEstado($estado){
    $seleccionado="";
    if(isset($_GET['estado'])){
        if($_GET['estado']==$estado){
            $seleccionado = "selected";
        }
        else {
            $seleccionado = ""; 
        }      
    }
   return $seleccionado;
}
if(isset($_GET['fecha'])){
    $fecha=$_GET['fecha'];    
}else{
    $fecha=date("Y-m-d");
}

?>

<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro que borrará el registro bonificación / descuento?</b></p>
</div>

<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>

<div  class="bonificacionesca-index">
<?php
if(Yii::$app->session->get('NIVEL')=='2'){
?>
<div class='row'>
        <div class="col-md-1"> 
        </div>
        <div class="col-md-1">
            <span style="color:#09104D"> ESTADO: </span>
        </div>
        <div class="col-md-2">
            <select name="VIGENTE" id="VIGENTE" class="form-control" value="">
                    
                    <option  value='1' <?=getEstado('1')?>> VIGENTES </option> 
                    <option  value='2' <?=getEstado('2')?> > VENCIDOS </option>                                      
                    <option value="0" <?=getEstado('0')?>>TODOS</option>
                
            </select>    
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" name="fechaFiltro" id="fechaFiltro" value="<?php echo $fecha;?>">
        </div>

        <div class="col-md-2">
            <button class="btn btn-success" id="btn-filtrar" onclick="cargarPagina();"> Filtrar </button>
        </div>
      

        <div class='col-md-2'>
            <!--?php
                //Modal::begin{[]}
                $gridColumns=[
                    'Code',
					'Name',
                    'territorio',
                    'U_cliente',
					'U_fecha_inicio',
                    'U_fecha_fin',
					'U_estado',
					
					'U_cantidadbonificacion',
					'U_observacion',
					'U_reglatipo',
					'U_reglaunidad',
					'U_reglacantidad',
					'U_bonificaciontipo',
					'U_bonificacionunidad',
				    'U_bonificacioncantidad'
			
                ];
                //exporta solo los registros del compaginador seleccionado
            /* echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $gridColumns,
                ]);*/
                ///exporta todos los regisros
                echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'dropdownOptions' => [
                    'label' => 'Export all',
                    'class' => 'btn btn-outline-secondary'
                    ]
                ]) ;
            ?-->
            <button type="button" class="btn btn-outline-secondary" onclick="exportarExcel();">Exportar Excel</button>
        </div>   
        <div class='col-md-2'>
            <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Bonificacion </button>
            </p>
        </div>
    </div>
<?php
}//
?>
    </p>
	
    <?php Pjax::begin(['id' => 'Bonificacionesca-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            //'id',
            'Code',
            'Name',
			/*[
                    'attribute' => 'territorio',
                    'filter' => $dataTerritorio = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'Description', 'Description'),
                    'value' => function($data) {
                       return $data->territorio;
                    }
            ],*/
           // 'U_tipo',
            [
                    'attribute' => 'U_cliente',
                    'filter' => $dataClientes = \yii\helpers\ArrayHelper::map(backend\models\ClientesGrupo::find()->all(), 'Name', 'Name'),
                    'value' => function($data) {
                       return $data->U_cliente;
                    }
            ],
            //'U_fecha',
            'U_fecha_inicio',
            'U_fecha_fin',
            [
                    'attribute' => 'U_estado',
                    'filter' => array("ACTIVO" => "ACTIVO", "INACTIVO" => "INACTIVO"),
                    'value' => function($data) {
                        switch ($data->U_estado) {
                            case ('ACTIVO') : return 'ACTIVO';
                            case ('INACTIVO') : return 'INACTIVO';
                           
                        }
                    }
             ],
            //'U_entrega',
            //'U_cantidadbonificacion',
            //'U_observacion',
           
            'U_reglatipo',
            //'U_reglaunidad',
            //'U_reglacantidad',
            'U_bonificaciontipo',
            //'U_bonificacionunidad',
          //  'U_bonificacioncantidad',

             [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
							$cartbuys='<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
							<path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
							</svg>';
							$cartbond='<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus-fill" viewBox="0 0 16 16">
							<path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM9 5.5V7h1.5a.5.5 0 0 1 0 1H9v1.5a.5.5 0 0 1-1 0V8H6.5a.5.5 0 0 1 0-1H8V5.5a.5.5 0 0 1 1 0z"/>
							</svg>';
							$cartdoulble='<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
							<path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
							<path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
							</svg>';
							$contenido='';
							if(Yii::$app->session->get('NIVEL')=='2'){
                                //verificamos si tiene items bonificables
                               
                                $DataBono=Bonificacionde1::find()->where('U_ID_bonificacion='. $data->id)->asArray()->all();
                                $swBono="";
                                if(count($DataBono)==0){
                                    $swBono='style="background-color:red"';
                                }
                                $DataCompra=Bonificacionde2::find()->where('U_ID_bonificacion='. $data->id)->asArray()->all();
                                $swCompra="";
                                if(count($DataCompra)==0){
                                    $swCompra='style="background-color:red"';
                                }

								$contenido='<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> ';
								$contenido=$contenido.'<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>';
								
								$contenido=$contenido.'<button onclick="" title="Articulo Conpras"  '.$swCompra.' class="btn-adiciona-compra"  value="' .$data->id.'@'. $data->Name.'@'.$data->U_tipo .'@'.$data->idReglaBonificacion.'@'.$data->U_reglacantidad. '" >'.$cartbuys.'<span style="font-size:10px">Compra</span></button> ';
					
								if($data->idBonificacionTipo==1){
									$contenido=$contenido. '<button onclick="" title="Articulo Bono" '.$swBono.'  class="btn-adiciona-bono"  value="' . $data->id.'@'. $data->Name.'@'.$data->U_tipo.'" >'.$cartbond.'<span style="font-size:10px">Bono</span></button> ';
								}
								else{
								   // $contenido=$contenido.'<button onclick="" title="Articulo Descuento" class="btn-adiciona-descuento"  value="' .$data->id.'@'. $data->Name.'@'.$data->U_tipo. '" ><i class="fal fa-layer-group"></i></button> ';
								}
								$contenido=$contenido.'<button onclick="" title="Duplicar Bonificación" class="btn-duplicar"  value="' .$data->id.'@'.$data->idBonificacionTipo.'" ><span style="font-size:10px"></span>'.$cartdoulble.'</button> ';
							}
                                 
                            return $contenido;            
                        }
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>

<div id="windowListaDescuento" style="display: none"> <br/><br/>
    <div class="row">
                <div class="col-md-12" >
                     <div class="row" id="DIV-PRODUCTODESCUENTO">
                        <div class="col-md-6" >
                        <input type="text" size="30"  name="PRODUCTODESCUENTO" id="PRODUCTODESCUENTO" list="datalistProducto" value="<?=$valueDetalle?>" class="form-control mayusculas" data-validation="required"  placeholder="Articulo Específico">
 
                        </div>
                        <div class="col-md-6">
                            <button onclick="AdicionarFilaDescuento('PRODUCTODESCUENTO')"> 
                                <span class="label label-warning">Adicionar fila</span>
                            </button> 
                        </div>
                    </div>

                    <div class="row" id="DIV-GRUPODESCUENTO">
                        <div class="col-md-6" >
                            <input type="text" size="30"  name="GRUPODESCUENTO" id="GRUPODESCUENTO" list="datalistGrupo" value="<?=$valueDetalle?>" class="form-control mayusculas" data-validation="required"  placeholder="Grupo Articulo">
              
                        </div>

                        <div class="col-md-6">
                            <button onclick="AdicionarFilaDescuento('GRUPODESCUENTO')"> 
                                <span class="label label-warning">Adicionar fila</span>
                            </button> 
                        </div>
                    </div> 
                    <br>
                    <div class="panel panel-default" >
                        <div class="panel-heading">
                            Detalle Articulos Descuento
                        </div>
                        <div class="panel-body" >
                            <div class="table-content">
                                <table class="table-responsive" id="table-list-descuento" name="table-list-descuento" style="width:100%" >
                                    <thead class="table-dark">
                                        <tr>
                                            <td width="10%"><strong>Nº</strong> </td>
                                            <td width="30%" ><strong>CODIGO</strong> </td>
                                            <td align="" width="50%" ><strong>ARTICULO</strong></td>
                                            <td align="center" width="10%"> <strong>ELIMINAR</strong></td>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="tr-detalle-descuento">
                                    
                                    </tbody>
                                </table>
                            <!--<span class="glyphicon glyphicon-plus-sign"></span>-->
                            <hr>
                    
                            
                        </div>
                    </div>
                </div>
                <!-- /.col-md-12 -->
            </div>
            
        </div>
    </div>

   
</div>

<div id="windowLista" style="display: none"> <br/><br/>
    <div class="row">
                <div class="col-md-12" >
                    <div class="row" id="DIV-PRODUCTOBONIFICACION">
                        <div class="col-md-6" >
                            <input type="text" size="30"  name="PRUDUCTOBINIFICACION" id="PRUDUCTOBINIFICACION" list="datalistProducto" value="<?=$valueDetalle?>" class="form-control mayusculas" data-validation="required"  placeholder="Articulo Específico">

                        </div>
                        <div class="col-md-6">
                            <button onclick="AdicionarFila('PRUDUCTOBINIFICACION')"> 
                                <span class="label label-warning">Adicionar fila</span>
                            </button> 
                        </div>
                    </div> 

                    <div class="row" id="DIV-GRUPOBONIFICACION">
                        <div class="col-md-6">
                            <input type="text" size="30"  name="GRUPOBINIFICACION" id="GRUPOBINIFICACION" list="datalistGrupo" value="<?=$valueDetalle?>" class="form-control mayusculas" data-validation="required"  placeholder="Grupo Articulo">
                        
                        </div>

                        <div class="col-md-6">
                            <button onclick="AdicionarFila('GRUPOBINIFICACION')"> 
                                <span class="label label-warning">Adicionar fila</span>
                            </button> 
                        </div>
                    </div>
                    <br>
                    <div class="panel panel-default" >
                        <div class="panel-heading">
                            Detalle Articulos Bonificacion
                        </div>
                        <div class="panel-body" >
                            <div class="table-content">
                                <table class="table-responsive" id="table-list-bonificacion" name="table-list-bonificacion" style="width:100%" >
                                    <thead class="table-dark">
                                        <tr>
                                            <td width="10%"><strong>Nº</strong> </td>
                                            <td width="30%" ><strong>CODIGO</strong></td>
                                            <td align="" width="50%" ><strong>ARTICULO</strong></td>
                                            <td align="center" width="10%"> <strong>ELIMINAR</strong></td>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="tr-detalle-bonificacion">
                                    
                                    </tbody>
                                </table>
                            <!--<span class="glyphicon glyphicon-plus-sign"></span>-->
                            <hr>
                    
                            
                        </div>
                    </div>
                </div>
                <!-- /.col-md-12 -->
            </div>
            
        </div>
    </div>

</div>

<div id="windowListaCompra" style="display: none"> <br/><br/>
    <div class="row">
                <input id="CANTIDADCOMPRA" name="CANTIDADCOMPRA" type="hidden" value="">
                <div class="col-md-12" >
                    <div class="row" id="DIV-PRODUCTOCOMPRA">
                        <div class="col-md-6">
                        <input type="text" size="30"  name="PRODUCTOCOMPRA" id="PRODUCTOCOMPRA" list="datalistProducto" value="<?=$valueDetalle?>" class="form-control mayusculas" data-validation="required"  placeholder="Articulo Específico">
               
                        </div>

                        <div class="col-md-6">
                            <button onclick="AdicionarFilaCompra('PRODUCTOCOMPRA')"> 
                                <span class="label label-warning">Adicionar fila</span>
                            </button> 
                        </div>  
                        
                    </div> 
                    <div class="row" id="DIV-GRUPOCOMPRA">
                        <div class="col-md-6" >
                            <input type="text" size="30"  name="GRUPOCOMPRA" id="GRUPOCOMPRA" list="datalistGrupo" value="<?=$valueDetalle?>" class="form-control mayusculas" data-validation="required"  placeholder="Grupo Artículo">
                        </div>

                        <div class="col-md-6">
                            <button onclick="AdicionarFilaCompra('GRUPOCOMPRA')"> 
                                <span class="label label-warning">Adicionar fila</span>
                            </button> 
                        </div>  
                        
                    </div> 
                    <br>
                    <div class="panel panel-default" >
                        <div class="panel-heading">
                            Detalle Articulos Compra
                        </div>
                        <div class="panel-body" >
                            <div class="table-content">
                                <table class="table-responsive" id="table-list-compra" name="table-list-compra" style="width:100%" >
                                    <thead class="table-dark">
                                        <tr>
                                            <td width="10%"><strong>Nº</strong></td>
                                            <td width="30%" ><strong>CODIGO</strong></td>
                                            <td align="" width="40%" ><strong>ARTICULO</strong></td>
                                            <td id="td-cantidad" style="display:none;" width="10%" ><strong>CANTIDAD</strong></td>
                                            <td id="td-check" style="display:none;" width="30%" ><strong>HABILITADO </strong></td>
                                            <td align="center" width="20%"><strong>ELIMINAR</strong> </td>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="tr-detalle-compra">
                                    
                                    </tbody>
                                </table>
                            <!--<span class="glyphicon glyphicon-plus-sign"></span>-->
                            <hr>
                    
                            
                        </div>
                    </div>
                </div>
                <!-- /.col-md-12 -->
            </div>
            
        </div>
    </div>

   
</div>

<datalist id="datalistGrupo">
    <?php
    $DataArticulos = \yii\helpers\ArrayHelper::map(backend\models\ProductosGrupo::find()->all(), 'Number', 'GroupName');
    //print_r($DataArticulos);
    foreach ($DataArticulos as $key => $value) {
            echo"<option id='".$key."' value='".$key.' - '.$value."'> 

            </option>";                                      
    }
    ?>
</datalist> 

<datalist id="datalistProducto">
    <?php
    $DataArticulos = \yii\helpers\ArrayHelper::map(backend\models\Productos::find()->all(), 'ItemCode', 'ItemName');
    //print_r($DataArticulos);
    foreach ($DataArticulos as $key => $value) {
            echo"<option id='".$key."' value='".$key.' - '.$value."'> 

            </option>";                                      
    }
    ?>
</datalist>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Bonificacionesca.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?php //$this->registerJsFile(Yii::getAlias('@web') . '/scripts/Bonificacionde1.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


