<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\i18n\Formatter;
use yii\base\Widget;
use yii\jui\DatePicker;

?>
<style type="text/css">
     #hdTerritorios {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        font-size: 13px;
        width: 100%;
    }
    #hdTerritorios tr:nth-child(even){background-color: #f2f2f2;}

    #hdTerritorios tr:hover {background-color: #ddd;}

    #hdTerritorios th {
        padding-top: 1px;
        padding-bottom: 1px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
        padding: 5px;
    
    }
</style>


<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Bonificacionesca-form']); ?>
    <div class="row">
        <div class="col-md-4">    
           <?= $form->field($model, 'Code')->textInput(['maxlength' => true,'id'=>'Code','onblur'=>'codigoBonificacion()']) ?><span class="text-danger text-clear" id="error-Code"></span>
        </div> 
        <div class="col-md-4">    
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-Name"></span>
        </div> 
       
        <div class="col-md-4">  
           <?php 
                $dataBonificacionT = \yii\helpers\ArrayHelper::map(backend\models\Bonificaciontipo::find()->where('estado=1')->all(), 'idTipoRegla', 'tipoRegla'); 
                array_unshift($dataBonificacionT,"Seleccionar");
               
            ?> 
           <?= $form->field($model, 'idBonificacionTipo')->dropDownList($dataBonificacionT,['onchange'=>'validaCajaTexto(this.value);getDetalleEspecifico(this.value);']) ?><span class="text-danger text-clear" id="error-idBonificacionTipo"></span>
        </div> 
    </div>
   <!-- <div class="col-md-4">    
        <?php /*///'disabled' => 'disabled'/* $form->field($model,'U_fecha')->
         widget(DatePicker::className(),[
        'dateFormat' => 'yyyy-MM-dd',
        'clientOptions' => [
            'yearRange' => '-115:+0',
            'changeYear' => true]
        , 'options' => ['class' => 'form-control', 'style' => 'width:100%']
        ])*/ ?>
       <?= $form->field($model, 'U_fecha')->textInput(['maxlength' => true,'disabled' => 'disabled']) ?><span class="text-danger text-clear" id="error-U_fecha"></span>
    </div> 
    -->
    <div class="row">

        <?php 
            if(isset($model->idBonificacionTipo)){
                $dataBonificacionD = \yii\helpers\ArrayHelper::map(backend\models\Bonificaciontipo::find()->where('idTipoRegla='.$model->idBonificacionTipo.' AND estado=1 ')->all(), 'detalle', 'detalle');
            }
            else{
                $dataBonificacionD="";
            }
        ?> 

        <div class="col-md-4">   
             <?= $form->field($model, 'detalleEspecifico')->dropDownList($dataBonificacionD,['onchange'=>'getTipoReglaCompra(this.value)']) ?><span class="text-danger text-clear" id="error-detalleEspecifico"></span>
        </div> 
        <div class="col-md-4">    
             <?= $form->field($model, 'tipoReglaCompra')->textInput(['readonly'=>'true']) ?><span class="text-danger text-clear" id="error-tipoReglaCompra"></span>
        </div> 
        <div class="col-md-4">   
        <?= $form->field($model, 'U_estado')->dropDownList(['ACTIVO' => 'ACTIVO','INACTIVO' => 'INACTIVO']) ?><span class="text-danger text-clear" id="error-U_estado"></span>
        </div> 
    </div> 
    <div class="row">
        <div class="col-md-4">  
            <?php
                if(isset($model->U_fecha_inicio)){
                    $fechaInicio=$model->U_fecha_inicio;
                }
                else{
                    $fechaInicio=date("Y-m-d");
                }
            ?>
            <?= $form->field($model, 'U_fecha_inicio')->textInput(['value'=>$fechaInicio]) ?><span class="text-danger text-clear" id="error-U_fecha_inicio"></span>
        </div> 
        <div class="col-md-4">
            <?php
                if(isset($model->U_fecha_fin)){
                    $fechaFin=$model->U_fecha_fin;
                }
                else{
                    $fechaFin=date("Y-m-d");
                }
            ?>   
            <?= $form->field($model, 'U_fecha_fin')->textInput(['value'=>$fechaFin]) ?><span class="text-danger text-clear" id="error-U_fecha_fin"></span>
        </div> 
        <div class="col-md-4">   
            <?php $dataClientes = \yii\helpers\ArrayHelper::map(backend\models\ClientesGrupo::find()->all(), 'Name', 'Name'); ?>  
            <?php
               
               array_unshift ($dataClientes,"TODOS");
            ?>
            <?= $form->field($model, 'U_cliente')->dropDownList($dataClientes) ?><span class="text-danger text-clear" id="error-U_cliente"></span>
        </div>

        <div class="col-md-4">    
            <?= $form->field($model, 'U_reglabonificacion')->dropDownList(['OBLIGATORIO' => 'OBLIGATORIO','OPCIONAL' => 'OPCIONAL']) ?><span class="text-danger text-clear" id="error-U_reglabonificacion"></span>
        </div> 
        <!--div class="col-md-4">
            <?php $dataTerritorio = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->where('TerritoryID!=-2')->orderby('TerritoryID ASC')->all(), 'TerritoryID', 'Description'); ?>
                          
            <?= $form->field($model, 'idTerritorio')->dropDownList($dataTerritorio) ?><span class="text-danger text-clear" id="error-idTerritorio"></span>
        </div-->
        <div class="col-md-4">  
           <?php 
                $dataCanal = \yii\helpers\ArrayHelper::map(backend\models\Companexcanal::find()->all(), 'code', 'name'); 
                array_unshift($dataCanal,"Todos");
               
            ?> 
           <?= $form->field($model, 'canalCode')->dropDownList($dataCanal) ?><span class="text-danger text-clear" id="error-canalCode"></span>
        </div>

        <div class="col-md-4"> 
            <div class="row p-3 mb-2 bg-info text-white">        
                <div class="col-lg-4">

                     
                    <input type="checkbox" name="chkTodosSincro" id="chkTodosSincro" class="form-control-" onclick="marcarTodosSincro(this);" />
                    <label class="form-check" for="chkTodosSincro" style="font-size: 11px" >Marcar todo</label>
                    
                </div>
                <div class="col-lg-4" style="font-size: 12px" align="right">Buscar Región:</div>
                <div class="col-lg-4">
                    <input id="searchTerm" type="text" class="form-control" onkeyup="doSearch()" />
                </div>
                 
            </div>
            <div class="row">     
                 <div class="col-md-12" style="height:100px;overflow:auto;">

                    <table id="hdTerritorios">
                        <?php $dataTerritorio = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->where('TerritoryID!=-2')->orderby('TerritoryID ASC')->all(), 'TerritoryID', 'Description'); ?>
                        <!--thead>
                            <th>Verificar</th>
                            <th>Territorio</th>
                            
                        </thead--> 
                        <tbody id="tableDetalle">
                            <?php foreach ($dataTerritorio as $key => $val) { ?>
                            <tr>
                                <td width="10%">
                                    <input type="checkbox" value="<?= $key.'=>'.$val ?>"  id="<?= $key ?>" onclick="serializaCheck(this.value)" class="selectCheboxTerritorios">
                                </td>
                                <td width="80%">
                                     <label class="form-check-label" for="<?= $key ?>"><b><?= ($val); ?></b></label>
                                </td>
                                 <td width="10%">
                                    <span id="chec-<?=$key?>"></span>
                                </td>
                            </tr>
                            
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="well">
                        <p id="p-resultado"> </p>  
                    </div>
                </div>
                
            </div>       
                    

            
            
        </div> 
        
        <div class="col-md-4">  
           <?php 
                //$dataClienteDosificacion = \yii\helpers\ArrayHelper::map(backend\models\Grupoclientedocificacion::find()->all(), 'id', 'nombre'); 
                //array_unshift($dataClienteDosificacion,"Todos");
               
            ?> 
          
        </div>  

    </div> 

    <div class="panel panel-default">
        <div class="panel-heading">Campos específicos</div>
        <div class="panel-body">

            <div class="row">

                <div class="col-md-4" id="div-montoTotal">   
                    <?= $form->field($model, 'montoTotal')->textInput(['onkeypress'=>'return NumEnteroMonto(event, this)','onkeyup'=>'calculaPorcentaje(this.value)','onkeypress'=>'$("#error-montoTotal").text("")']) ?><span class="text-danger text-clear" id="error-montoTotal"></span>
                </div>

                <div class="col-md-4" id="div-U_reglacantidad">
                    <label id="lblReglaCantidad">Cantidad Compra</label>   
                    <?= $form->field($model, 'U_reglacantidad')->textInput(['onkeypress'=>'return NumEntero(event, this)','onblur'=>'validarCantidad(this.value)'])->label(false) ?><span class="text-danger text-clear" id="error-U_reglacantidad"></span>
                </div>

                <div class="col-md-4" id="div-cantidadMaximaCompra">
                 <label id="lblCantidadMaximaCompra">Cantidad Maxima Compra</label>   
                    <?= $form->field($model, 'cantidadMaximaCompra')->textInput(['onkeypress'=>'return NumEntero(event, this)'])->label(false) ?><span class="text-danger text-clear" id="error-cantidadMaximaCompra"></span>
                </div>

                <div class="col-md-4" id="div-U_reglaunidad"> 
                    <?php //$dateUnidadMedida = \yii\helpers\ArrayHelper::map(backend\models\Unidadesmedida::find()->all(), 'Name', 'Name'); ?>
                    <?= $form->field($model, 'U_reglaunidad')->dropDownList(['UNI' => 'UNI']) ?><span class="text-danger text-clear" id="error-U_reglaunidad">
                    </span>
                </div> 
               
                <div class="col-md-4" id="div-U_limitemaxregalo">
                <label id="lblLimiteMaximo">Límite máximo de Iteraciones (0 = Sin Límite)</label>    
                    <?= $form->field($model, 'U_limitemaxregalo')->textInput(['onkeypress'=>'return NumEntero(event, this)'])->label(false) ?><span class="text-danger text-clear" id="error-U_limitemaxregalo"></span>
                </div> 
           
                <div class="col-md-4" id="div-U_bonificacioncantidad"> <!-- -->
                    <label id="lblCantidadRegalo">Cantidad Regalo</label>
                   <?= $form->field($model, 'U_bonificacioncantidad')->textInput(['onkeypress'=>'return NumDecimal(event, this)','onkeypress'=>'$("#error-U_bonificacioncantidad").text("")'])->label(false) ?><span class="text-danger text-clear" id="error-U_bonificacioncantidad"></span>
                </div>
                <div class="col-md-4" id="div-U_bonificacionunidad">   
                    <?= $form->field($model, 'U_bonificacionunidad')->dropDownList(['UNI' => 'UNI']) ?>
                   <span class="text-danger text-clear" id="error-U_bonificacionunidad"></span>
                </div> 
                
                <div class="col-md-4" id="div-porcentajeDescuento"> 
                  
                    <?= $form->field($model, 'porcentajeDescuento')->textInput(['onkeypress'=>'return NumDecimal(event, this)']) ?><span class="text-danger text-clear" id="error-porcentajeDescuento"></span>
                </div>

                <div class="col-md-4" id="div-U_cantidadbonificacion"> 
                    <?php $dataPocentaje=array('0','1','2','3','4','5','6','7','8','9','10') ?>
                    <?= $form->field($model, 'U_cantidadbonificacion')->dropDownList($dataPocentaje) ?><span class="text-danger text-clear" id="error-U_cantidadbonificacion"></span>
                </div> 
                
            </div> 
            <div class="row">
                 <div class="col-md-4">    
                 <?php $dateReglaT = \yii\helpers\ArrayHelper::map(backend\models\Reglatipo::find()->where('estado=1')->all(), 'nombre', 'nombre'); ?>
                    <?= $form->field($model, 'U_reglatipo')->dropDownList($dateReglaT,['id'=>'U_REGLATIPO']) ?><span class="text-danger text-clear" id="error-U_reglatipo"></span>
                 </div> 
            	 <div class="col-md-8">    
                    <?= $form->field($model, 'U_observacion')->textInput(['maxlength' => true]) ?><span class="text-danger text-clear" id="error-U_observacion"></span>
                 </div> 
            </div>
        </div> 
    </div>
    <?= $form->field($model, 'U_fecha')->hiddenInput(['value' => date('Y-m-d H:m:s')])->label(false); ?>
  
    <?= $form->field($model, 'U_tipo')->hiddenInput(['value' =>'0','id'=>'U_TIPO'])->label(false); ?> 
	
	<?= $form->field($model, 'territorio')->hiddenInput()->label(false); ?> 
	<?= $form->field($model, 'idUsuario')->hiddenInput(['value' =>Yii::$app->session->get('IDUSUARIO')])->label(false); ?> 
	<?= $form->field($model, 'usuario')->hiddenInput(['value' =>Yii::$app->session->get('USUARIO')])->label(false); ?>
   	<?= $form->field($model, 'U_bonificaciontipo')->hiddenInput()->label(false); ?> 
    <?= $form->field($model, 'idReglaBonificacion')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'clienteDosificacion')->hiddenInput(['value'=>'Todos'])->label(false); ?>
     <?= $form->field($model, 'idClienteDosificacion')->hiddenInput(['value'=>0])->label(false);?>

   <input type="hidden" name="IDCABECERA" id="IDCABECERA">
   <input type="hidden" name="CANTIDADDETALLE" id="CANTIDADDETALLE">

    <?php ActiveForm::end(); ?>
    <!---->


</div>
<script>

	getTerritorio();

	function getTerritorio(){
		/*var comboTerritorio = document.getElementById("bonificacionesca-idterritorio");
        var territorio = comboTerritorio.options[comboTerritorio.selectedIndex].text;
        $('#bonificacionesca-territorio').val(territorio);*/
        var territorio=$("#bonificacionesca-territorio" ).val();
        if(territorio!=""){
            var territorio_=territorio.split('@');
            $(".selectCheboxTerritorios").each(function () {    
                for(var i=0;i<territorio_.length;i++){
                    var idTerritorio=territorio_[i].trim().split("=>");
                    console.log(i+" -> "+territorio_[i].trim()+" : "+$(this).val().trim());
                    if(territorio_[i].trim()==$(this).val().trim()){
                        $(this).attr("checked", true);
                        $("#chec-"+idTerritorio[0]).text("ver");
                        break;
                    }
                }
            });
        }
	}


    function serializaCheck(territorio){
        var serializado="";
        $(".selectCheboxTerritorios").each(function () {    
           
            if ($(this).is(':checked')) {
                serializado=serializado+$(this).val()+"@";
                var idTerritorio=$(this).val().split("=>");
                $("#chec-"+idTerritorio[0]).text("ver");
            }
            else{
                 var idTerritorio=$(this).val().split("=>");
                $("#chec-"+idTerritorio[0]).text("");
            } 

        });
        console.log(serializado);
        $('#bonificacionesca-territorio').val(serializado);
    }

    function marcarTodosSincro(control) {
        var valor = false;
        if (control.checked == true) {
            valor = true;
        }
        $(".selectCheboxTerritorios").each(function (index) {    
            $(this).prop('checked', valor);
        });
        serializaCheck();
    }	
   

//getDetalleEspecifico(1);
if($("#bonificacionesca-detalleespecifico").val()!=null){
    getTipoReglaCompra($("#bonificacionesca-detalleespecifico").val());
}
function getDetalleEspecifico(valor){

    $.ajax({
        url: $("#PATH").attr("name") + 'bonificacionesca/detalleespecifico',
        type: 'POST',
        data: 'tipoRegla='+valor,
        success: function (data, status, xhr) {
            var respuesta=JSON.parse(data);
            console.log("DETALLE ESPECIFICO");
            console.log(respuesta);
            var detalleEspecifico = document.getElementById("bonificacionesca-detalleespecifico");
            $("#bonificacionesca-detalleespecifico").empty();
            var option = document.createElement("option");
            option.text ="Seleccionar";
            option.value ="0";
            detalleEspecifico.add(option);

            for (var i = 0; i < respuesta.length; i++) {
                
                option = document.createElement("option");
                option.text = respuesta[i].detalle;
                detalleEspecifico.add(option);
            }
            $("#bonificacionesca-tiporeglacompra").val("");
            ///selecciona pordejecto el detalle especifico en cas de ser 3 
            if(valor==3){
               getTipoReglaCompra(respuesta[0].detalle);
               $("#bonificacionesca-detalleespecifico").val(respuesta[0].detalle);
              // $('#bonificacionesca-detalleespecifico > option[value="'+respuesta[0].detalle+'"]').attr('selected', 'selected');
            }
        },
        error: function (jqXhr, textStatus, errorMessage) {
            //reject(errorMessage);
            if (jqXhr.status === 0) {

               alert('Verifique conexión de red');

            }else if (textStatus === 'timeout') {

                alert('Mucho tiempo en espera, verifique conexión.');

            }

          

        }
    });
}

function getTipoReglaCompra(valor){

    if(valor!='0'){
        $.ajax({
            url: $("#PATH").attr("name") + 'bonificacionesca/tiporeglacompra',
            type: 'POST',
            data: 'detalle='+valor+'&tipoRegla='+$("#bonificacionesca-idbonificaciontipo").val(),
            success: function (data, status, xhr) {
                var respuesta=JSON.parse(data);
                console.log("TIPO REGLA COMPRA");
                console.log(respuesta);
                if(respuesta.length>0){
                    $("#bonificacionesca-tiporeglacompra").val(respuesta[0].tipoReglaCompra);
                }
                else{
                    $("#bonificacionesca-tiporeglacompra").val("");
                }

                
                 // alert(respuesta[0].id);

                var tipoBonificacion = document.getElementById("bonificacionesca-idbonificaciontipo");
                var tipoBonificacionValor = tipoBonificacion.options[tipoBonificacion.selectedIndex].text;
                $("#bonificacionesca-u_bonificaciontipo").val(tipoBonificacionValor);
                $("#bonificacionesca-idreglabonificacion").val(respuesta[0].id);
                //alert(respuesta[0].idTipoRegla);
                if(respuesta[0].idTipoRegla=='1') $("#lblCantidadRegalo").text("Cantidad Regalo");
                else $("#lblCantidadRegalo").text("Porcentaje Regalo");

                switch (respuesta[0].id) {
                    case '1':
                        $('#div-montoTotal').hide();
                        $('#div-U_cantidadbonificacion').hide();
                        $('#div-porcentajeDescuento').hide();

                        $('#bonificacionesca-montototal').val("");

                        $('#div-cantidadMaximaCompra').hide();
                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').show();
                        $('#div-U_bonificacionunidad').hide();
                        $("#lblLimiteMaximo").text("Límite máximo de Iteraciones (0 = Sin Límite)");
                        $("#lblReglaCantidad").text("Cantidad Compra");
                        

                    break;
                    case '2':
                        $('#div-montoTotal').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#bonificacionesca-montototal').val("");

                        $('#div-cantidadMaximaCompra').hide();
                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').show();
                        $("#lblReglaCantidad").text("Cantidad Compra");
                    
                    break;
                  
                   case '3':
                        $('#div-montoTotal').show();

                        $('#div-cantidadMaximaCompra').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#div-U_reglacantidad').hide();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').hide();
                        //lipiar
                       /* $('#bonificacionesca-cantidadmaximacompra').val(0);
                        $('#bonificacionesca-u_reglacantidad').val(0);
                        $('#bonificacionesca-u_reglaunidad').val("UNI");
                        $('#bonificacionesca-u_limitemaxregalo').val(0);
                        $('#bonificacionesca-u_bonificacionunidad').val("UNI");
                        $('#bonificacionesca-u_cantidadbonificacion').val(0);

                  */
                    break;
                    case '4':
                        $('#div-montoTotal').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#bonificacionesca-montototal').val("");

                        $('#div-cantidadMaximaCompra').hide();
                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').show();
                        $("#lblReglaCantidad").text("Cantidad Compra");
                       
                    break;
                    case '5':
                        $('#div-montoTotal').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#bonificacionesca-montototal').val("");
                        $('#div-cantidadMaximaCompra').hide();
                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').show();
                        $("#lblReglaCantidad").text("Cantidad Compra");
                        
                    break;
                    case '6':
                        $('#div-montoTotal').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#div-U_cantidadbonificacion').hide();

                        $('#bonificacionesca-montototal').val("");
                        $('#div-cantidadMaximaCompra').hide();
                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').show();
                        $('#div-U_bonificacionunidad').hide();
                        $("#lblLimiteMaximo").text("Límite máximo de Iteraciones (0 = Sin Límite)");
                        $("#lblReglaCantidad").text("Cantidad Compra");
                       
                    break;
                    case '7':
                        $('#div-montoTotal').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#bonificacionesca-montoTotal').val("");
                        $('#div-cantidadMaximaCompra').hide();
                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').show();
                        $("#lblReglaCantidad").text("Cantidad Compra");
                        
                    break;
                    case '8':
                        $('#div-montoTotal').show();

                        $('#div-cantidadMaximaCompra').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#div-U_reglacantidad').hide();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').hide();
                        
                    break;

                    case '9':
                        $('#div-montoTotal').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#bonificacionesca-montototal').val("");

                        $('#div-U_reglacantidad').show();
                        $('#div-cantidadMaximaCompra').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').show();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').hide();
                        //cambio de label
                        $("#lblLimiteMaximo").text("Compra");
                        $("#lblReglaCantidad").text("Compra Desde");
                        $("#lblCantidadMaximaCompra").text("Compra Hasta");
                        
                    break;
                    case '10':
                       $('#div-montoTotal').hide();
                       $('#div-porcentajeDescuento').hide();
                       $('#bonificacionesca-montototal').val("");

                        $('#div-U_reglacantidad').show();
                        $('#div-cantidadMaximaCompra').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').show();
                        //cambio de label
                        $("#lblLimiteMaximo").text("Compra");
                        $("#lblReglaCantidad").text("Compra Desde");
                        $("#lblCantidadMaximaCompra").text("Compra Hasta");
                        
                    break;
                    case '11':
                        $('#div-montoTotal').hide();
                        $('#div-porcentajeDescuento').hide();
                        $('#bonificacionesca-montototal').val("");
                        $('#div-U_cantidadbonificacion').hide();

                        $('#div-cantidadMaximaCompra').hide();
                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').show();
                        $('#div-U_bonificacionunidad').hide();
                        $("#lblLimiteMaximo").text("Límite máximo de Iteraciones (0 = Sin Límite)");
                        $("#lblReglaCantidad").text("Cantidad Compra");
                       
                    break;

                    case '12':
                       $('#div-montoTotal').hide();
                       $('#div-porcentajeDescuento').hide();
                       $('#bonificacionesca-montototal').val("");

                        $('#div-U_reglacantidad').show();
                        $('#div-cantidadMaximaCompra').hide();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').hide();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').show();
                        $("#lblReglaCantidad").text("Cantidad Compra");
                       
                    break;

                    case '13':
                        $('#div-montoTotal').hide();

                        $('#div-U_cantidadbonificacion').hide();

                        $('#div-cantidadMaximaCompra').hide();

                        $('#bonificacionesca-cantidadmaximacompra').val("");
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').show();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-porcentajeDescuento').show();
                        $("#lblLimiteMaximo").text("Límite máximo de Iteraciones (0 = Sin Límite)");
                        $("#lblReglaCantidad").text("Cantidad Compra");
                        
                    break;


                    default:
                        $('#div-cantidadMaximaCompra').hide();
                        $('#div-U_reglacantidad').show();
                        $('#div-U_reglaunidad').hide();
                        $('#div-U_limitemaxregalo').show();
                        $('#div-U_bonificacionunidad').hide();
                        $('#div-U_cantidadbonificacion').show();
                        $("#lblLimiteMaximo").text("Límite máximo de Iteraciones (0 = Sin Límite)");
                        $("#lblReglaCantidad").text("Cantidad Compra");
                    break;
                }   
                //document.getElementById('bonificacionesca-u_bonificacioncantidad').disabled=false;
                //document.getElementById('bonificacionesca-u_bonificacioncantidad').readOnly = false;
              

            },
            error: function (jqXhr, textStatus, errorMessage) {
                //reject(errorMessage);
                console.log(errorMessage);
            }
        });
    }
    //$('#bonificacionesca-idterritorio').style.display = 'none';
    
}

function calculaPorcentaje(valor){
   
    /*if(valor>=3000){// se asigna el porcentaje de descuento y se bloquea la caja de texto
        $('#bonificacionesca-u_bonificacioncantidad').val(10);
       
    }
    else{
        $('#bonificacionesca-u_bonificacioncantidad').val(0);
    }*/
}

function getTextClieteDosi(clienteDosi){
    console.log(clienteDosi.value);
    var combo = document.getElementById("bonificacionesca-idclientedosificacion");
    var selected = combo.options[combo.selectedIndex].text;
    console.log(selected);


    $("#bonificacionesca-clientedosificacion").val(selected);
}
</script>




