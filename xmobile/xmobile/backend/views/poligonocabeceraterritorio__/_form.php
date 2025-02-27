<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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
        width: 100%;
        border: 1px solid #000;
    }

    #pickerFecha{
        margin-top: 15px;
        margin-left: 10px;
        position: absolute !important;
        z-index: 1000
    }

    h4{
        text-transform: uppercase;
        font-size: 12px;
        background-color:#ccc;
        padding: 4px;
        color:#030E38;
    }
    #hdClientes {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        font-size: 13px;
        width: 100%;
    }

    #hdClientes td, #hdClientes th {
       border: 1px solid #ddd;
        padding: 5px;
    }

    #hdClientes tr:nth-child(even){background-color: #f2f2f2;}

    #hdClientes tr:hover {background-color: #ddd;}

    #hdClientes th {
        padding-top: 1px;
        padding-bottom: 1px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
        padding: 5px;
    
    }

    
    #tblVendedores {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblVendedores td, #tblVendedores th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblVendedores tr:nth-child(even){background-color: #f2f2f2;}

    #tblVendedores tr:hover {background-color: #ddd;}

    #tblVendedores th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    } 

    #tblVendedoresSeleccionados {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblVendedoresSeleccionados td, #tblVendedoresSeleccionados th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblVendedoresSeleccionados tr:nth-child(even){background-color: #f2f2f2;}

    #tblVendedoresSeleccionados tr:hover {background-color: #ddd;}

    #tblVendedoresSeleccionados th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }

    #checkboxes label {
        float: left;
    }
    #checkboxes ul {
        margin: 0;
        list-style: none;
        float: left;
    } 

    #myTable tr > *:nth-child(2) {
    display: none;
}   
</style>
<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Poligonocabeceraterritorio-form']); ?>
    <div class="row">
        <div class="col-md-6" style="height:450px;overflow:auto;">  
            <div class="row">
                <div class="col-md-12" >
                    <?= $form->field($model, 'nombreRuta')->textInput() ?>
                    <span class="text-danger text-clear" id="error-nombreRuta"></span>
                </div>
            </div> 
            <div class="row">
                <!--<div class="col-md-4">    
                    <?= $form->field($model, 'fechaRegistro')->textInput(['onchange'=>'getDiaSemana()']) ?>
                    <span class="text-danger text-clear" id="error-fechaRegistro"></span>
                </div>--> 
				
				 <?php
					$dias=['Lunes'=>'Lunes','Martes'=>'Martes','Miercoles'=>'Miercoles','Jueves'=>'Jueves','Viernes'=>'Viernes','Sabado'=>'Sabado','Domingo'=>'Domingo'];
				 ?>
				<div class="col-md-6"> 
                    <?= $form->field($model, 'dia')->dropDownList($dias,["onchange"=>"cargaTerritorio('$model->vendedor')"]) ?>
                    <span class="text-danger text-clear" id="error-dia"></span>
                </div> 
                
                <div class="col-md-5">
                    <label>Vendedor </label> 
                    <input type="text" size="30"  name="operador" id="operador" value="<?=$model->vendedor?>" list="datalistUser" onchange="cargaTerritorio('<?=$model->vendedor?>')" value="" class="form-control mayusculas" data-validation="required"  placeholder="Usuario vendedor">

                    <span class="text-danger text-clear" id="error-operador"></span>
                </div>
                <div class="col-md-1" > 
                    <label> </label> 
                    <button type="button" class="btn btn-success" title="Actualizar Territorios" onclick="cargaTerritorioVendedor()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                            <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                            <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                        </svg>
                    </button>
                </div>
                <datalist id="datalistUser">
                    <?php

                    foreach ($modeluser as $key => $value) {
                            echo"<option id='".$value['id']."'  value='".$value['username'].' - '.$value['nombreCompleto']."' > 

                            </option>";                                      
                    }
                    ?>
                </datalist>
                <table id="tablaUserPersona" style="display:none;">
                    <?php foreach ($modeluser as $valueP) { ?>    
                        <tr>
                            <td><?= $valueP["id"]; ?></td>
                            <td><?= $valueP["username"]; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
           
            <div class="row">
                <div class="col-md-6" > 
                    <h4>Territorios</h4>
                    <div style="height:150px;overflow: auto;">
                        <ul class="list-group" id="checkTerritorios">
                           <?php 
                                $territorioCheck=explode('@', $model->territorio);
                                Yii::error("dsdsdsds");
                                Yii::error($model->territorio);
                                
                                for($i=0;$i<count($territorioCheck)-1;$i++) { 
                                    Yii::error($territorioCheck);
                                    $valor=explode('=>',$territorioCheck[$i]);        
                            ?>
                                <li class="list-group-item">
                                   <input type="checkbox" value="<?=$valor[0].'=>'.$valor[1]?>"  id="checkTerri-<?=$valor[0]?>" checked class="selectCheboxTerritorio" onclick="serializaCheckTerritorio()">
                                   <label class="form-check-label" for="checkTerri-<?=$valor[0]?>"> &nbsp;<?=$valor[1]?></label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <!--<button type="button" class="btn btn-success" onclick="">Listar poligonos</button>
                     --> 
                     
                </div>    
                <div class="col-md-6" >  
                    <h4>Poligonos</h4>
                    <div style="height:150px;overflow: auto;">
                        <ul class="list-group" id="checkPoligonos">

                         <?php 
                                $poligonoCheck=explode('@', $model->poligono);
                                Yii::error("dsdsdsds");
                                Yii::error($model->poligono);
                                
                                for($i=0;$i<count($poligonoCheck)-1;$i++) { 
                                    Yii::error($poligonoCheck);
                                    $valor=explode('=>',$poligonoCheck[$i]);        
                            ?>

                                <li class="list-group-item">
                                <input type="checkbox" value="<?=$valor[0].'=>'.$valor[1]?>"  id="checkPoli-<?=$valor[0]?>" checked class="selectCheboxPoligono" onclick="serializaCheckPoligono()">
                                <label class="form-check-label" for="checkPoli-<?=$valor[0]?>"> &nbsp;<?=$valor[1]?></label>
                                </li>

                            <?php } ?>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-9" > 
                            <button type="button" class="btn btn-success" onclick="ListarClientes()">Cargar poligonos Mapa</button>
                        </div>
                        <div class="col-md-3" > 
                            <button type="button" class="btn btn-success" title="Actualizar Mapa" onclick="cargarPoligono()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                                    <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                                    <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                                 
                </div>
                  
               
            </div>
            <div class="row">
                <div class="col-md-12">

                    <hr>
                    <h4>
                        <div class="row">
                            <div class="col-lg-4">Lista de Clientes: <span id="cantidadClientes"> </span></div>
                            <div class="col-lg-2">Buscador:</div>
                            <div class="col-lg-6">
                            <input id="searchTerm" type="text" class="form-control" onkeyup="doSearch()" />
                            </div>
                        </div>
                    </h4>
                   
                </div>
                <div class="col-md-12" style="height:300px;overflow:auto;">
                    

                    <table id="hdClientes">
                        <thead>
                            <th>Marcar</th>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                          
                            <th>Posición</th>
                            <th>Borrar</th>
                        </thead> 
                        <tbody id="tableDetalle">
                        </tbody>
                    </table>
                    <div class="well">
                        <p id="p-resultado"> </p>  
                    </div>
                </div>
                
            </div> 
        </div>
        <div class="col-md-6">
            <div id="map"></div> 
            
        </div>
            <!-- imput tipo hidden -->
        <?= $form->field($model, 'fechaSistema')->hiddenInput(['value' => date('Y-m-d H:m:s')])->label(false); ?>
		<?= $form->field($model, 'fechaRegistro')->hiddenInput(['value' => date('Y-m-d')])->label(false); ?>
        <?= $form->field($model, 'vendedor')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'territorio')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'idPoligono')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'idTerritorio')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'poligono')->hiddenInput()->label(false); ?>
        <?= $form->field($model, 'idUserRegister')->hiddenInput(['value' =>Yii::$app->session->get('IDUSUARIO')])->label(false); ?> 
        <?= $form->field($model, 'userRegister')->hiddenInput(['value' =>Yii::$app->session->get('USUARIO')])->label(false); ?>
        <?= $form->field($model, 'idVendedor')->hiddenInput()->label(false); ?>
    </div> 

    <?php ActiveForm::end(); ?>

</div>
<!-- ////////INCIO DE LA SECCION DE SCRIPTS////// -->

<script>
    var map;
    var markerGeo;
    var marker;
    var allMarkers = [];
    var coordenadas = [];
    var idmarker;
    var bermudaTriangle;
    var Poligono = [];
    var Clientes = [];
    var ClientesGuardados = [];
    var marker_icon = [];
    var swVerifica=true;
    var swRowColor;
    var swTrazaRuta=false;
    var puntosMarker;
    listarIconos();
    function listarIconos(){

        $.ajax({
            url: $("#PATH").attr("name")+'poligonocabeceraterritorio/listaiconos',
            type: 'POST',
            data: '',
            success: (data, status, xhr) => {
                if (status == 'success'){
                    var result = JSON.parse(data);
                    console.log(result);
                    if(result!=null){
                        for(var i=0;i<result.length-1;i++) {
                            marker_icon.push(result[i].cadena);
                        }
                    }
                }
                else{
                        console.error("ERROR USUARIO TERRITORIO: ");    
                }
            },
            error: (jqXhr, textStatus, errorMessage) => {
                console.error("ERROR: " + errorMessage);
            }
        });

    }

    $('document').ready(function () {
        //cargarDatos();
        initMap(0);        
      
    });
    function initMap(color) {
        idmarker = 1;
        directionsService = new google.maps.DirectionsService;
        directionsRenderer = new google.maps.DirectionsRenderer;
        ////marker personalizado
        
        /// fin de marker personalizado
        
        var ubi = {lat: -16.496777, lng: -68.132031};
        map = new google.maps.Map(document.getElementById('map'), {zoom: 12, center: ubi});
       // google.maps.event.addListener(map, 'click', function(event) { placeMarker(event.latLng); });
        var crear = $('#hdCrear').val();
        //dibujar poligono        
        var lcoor=[];
        console.log("poligono");
        console.log(Poligono);
        var coor="";
        for(var i = 0; i < Poligono.length; i++){
            if(Number(Poligono[i]["latitud"])!=0){
                //var marca = {id:mensaje[i]["id"], lat: Number(mensaje[i]["latitud"]), lon: Number(mensaje[i]["longitud"]) };
                coor = { lat: Number(Poligono[i]["latitud"]), lng: Number(Poligono[i]["longitud"]) };
                //if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor});                
                //placeMarker(coor);
                lcoor.push(coor);
            }
            else{
                console.log("else");
                bermudaTriangle = new google.maps.Polygon({ paths: lcoor, strokeColor: '#FF0000', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#FF0000', fillOpacity: 0.35 });
                bermudaTriangle.setMap(map);
                //////
                lcoor=[];
            }
        
        }        
        console.log("else");
        bermudaTriangle = new google.maps.Polygon({ paths: lcoor, strokeColor: '#FF0000', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#FF0000', fillOpacity: 0.35 });
        bermudaTriangle.setMap(map);

        //google.maps.event.addListener(bermudaTriangle , 'click', isWithinPoly);
        var isWithinPolygon = false;
        /// evita que aparescan los puntos de los clientes para que trace la nueva ruta
       // if(!swTrazaRuta){
            asignaEtiquetas(1);
        //}
        
        /*for (var i = 0; i < Clientes.length; i++){
            var coor = { lat: Number(Clientes[i]["latitud"]), lng: Number(Clientes[i]["longitud"]) };
            placeMarker(coor, 1, { CardCode:  Clientes[i]["cardcode"], CardName:  Clientes[i]["cardname"] });
            //isWithinPolygon = google.maps.geometry.poly.containsLocation(coor, bermudaTriangle); 
            //console.log(isWithinPolygon);
        }*/
    }

    function placeMarker(location, color, informacion = null) {
        var icono;
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
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/black-dot.png" };
                break;                
            case 7:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/brown-dot.png" };
                break;
            default:
                icono = { url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png" };
                break;
        }
        
        var tooltip = '';
        var cardcode = 0;
        if (informacion != null){
            tooltip = "Codigo: " + informacion.CardCode + '\n' + 'Nombre: ' + informacion.CardName+'\n'+informacion.Direccion;
            cardcode = informacion.CardCode;
        }

        var marker = new google.maps.Marker({
            position: location, 
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
            icon: icono,
            title: tooltip,
            id: cardcode
        });
      
        allMarkers.push(marker);

       
    }

    function cargaTerritorioVendedor(){
        var opt = $('option[value="'+$("#operador").val()+'"]');
        var idVendedor=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);

        if(idVendedor!='NO OPTION'){
            $("#poligonocabeceraterritorio-idvendedor").val(idVendedor);
            $("#poligonocabeceraterritorio-vendedor").val($("#operador").val());

            var datos = { idVendedor: idVendedor,dia:$("#poligonocabeceraterritorio-dia").val()};

            $.ajax({
                url: $("#PATH").attr("name")+'poligonocabeceraterritorio/usuarioterritorio',
                type: 'POST',
                data: datos,
                success: (data, status, xhr) => {
                    if(status == 'success'){

                        $("#poligonocabeceraterritorio-idterritorio").empty();
                        $('#poligonocabeceraterritorio-idterritorio').append('<option value="0">Seleccione</option>');
                        var result = JSON.parse(data);
                        console.log(result);
                        if(result!=null){
                            var contenido="";
                            var checkTerritorios= document.getElementById("checkTerritorios");
                        //  alert("territorio guardado: "+result.territorio+" territorio nuevo: "+ $('#poligonocabeceraterritorio-territorio').val());
                            var territorio=result.territorio.split('@'); 

                            for(var i=0;i<territorio.length-1;i++) {
                                //console.log(value);
                                var territori=territorio[i].split('=>');
                                console.log(territori[1]);
                                contenido=contenido+'<li class="list-group-item">'+
                                '<input type="checkbox" value="'+territori[0]+'=>'+territori[1]+'"  id="checkTerri-'+territori[0]+'" class="selectCheboxTerritorio" onclick="serializaCheckTerritorio()">'+
                                '<label class="form-check-label" for="checkTerri-'+territori[0]+'"> &nbsp;'+territori[1]+'</label>'+
                                '</li>';
                                
                            }
                            checkTerritorios.innerHTML = contenido;
                        }
                        //ListarPoligono();   
                    }
                    else{
                            console.error("ERROR USUARIO TERRITORIO: ");    
                        }
                
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });             
        }
        else if($("#operador").val()!=""){

            alert("Vendedor seleccionado no existe");
        }
    }
    function cargaTerritorio(vendedor){
       
        var opt = $('option[value="'+$("#operador").val()+'"]');
        var idVendedor=opt.length ? opt.attr('id') : 'NO OPTION';
       // alert(opt);

        if(idVendedor!='NO OPTION'){
            $("#poligonocabeceraterritorio-idvendedor").val(idVendedor);
            $("#poligonocabeceraterritorio-vendedor").val($("#operador").val());

            var datos = { idVendedor: idVendedor,dia:$("#poligonocabeceraterritorio-dia").val()};
            $.ajax({
                url: $("#PATH").attr("name")+'poligonocabeceraterritorio/verifcaasignacion',
                type: 'POST',
                data: datos,
                success: (data, status, xhr) => {
                    if(status == 'success'){
                        var resultado=JSON.parse(data);
                        console.log(resultado);
                        if(resultado.length==0){// si no hay registros se habila para mostrar los territorios
                            swVerifica=true;
                            if(vendedor==""){/// if nuevo registro
                                //var idVendedor = 25;//$("#poligonocabeceraterritorio-idvendedor").val();
                                var datos = { idVendedor: idVendedor };
                                // this.url = $("#PATH").attr("name");
                                $.ajax({
                                    url: $("#PATH").attr("name")+'poligonocabeceraterritorio/usuarioterritorio',
                                    type: 'POST',
                                    data: datos,
                                    success: (data, status, xhr) => {
                                        if(status == 'success'){

                                            $("#poligonocabeceraterritorio-idterritorio").empty();
                                            $('#poligonocabeceraterritorio-idterritorio').append('<option value="0">Seleccione</option>');
                                            var result = JSON.parse(data);
                                            console.log(result);
                                            if(result!=null){
                                                var contenido="";
                                                var checkTerritorios= document.getElementById("checkTerritorios");
                                            //  alert("territorio guardado: "+result.territorio+" territorio nuevo: "+ $('#poligonocabeceraterritorio-territorio').val());
                                                var territorio=result.territorio.split('@'); 

                                                for(var i=0;i<territorio.length-1;i++) {
                                                    //console.log(value);
                                                    var territori=territorio[i].split('=>');
                                                    console.log(territori[1]);
                                                    contenido=contenido+'<li class="list-group-item">'+
                                                    '<input type="checkbox" value="'+territori[0]+'=>'+territori[1]+'"  id="checkTerri-'+territori[0]+'" class="selectCheboxTerritorio" onclick="serializaCheckTerritorio()">'+
                                                    '<label class="form-check-label" for="checkTerri-'+territori[0]+'"> &nbsp;'+territori[1]+'</label>'+
                                                    '</li>';
                                                    
                                                }
                                                checkTerritorios.innerHTML = contenido;
                                            }
                                            //ListarPoligono();   
                                        }
                                        else{
                                                console.error("ERROR USUARIO TERRITORIO: ");    
                                            }
                                    
                                    },
                                    error: (jqXhr, textStatus, errorMessage) => {
                                        console.error("ERROR: " + errorMessage);
                                    }
                                });
                            }///fin - if nuevo registro
                            else{
                                ///por falson se actualiza el registro
                                // y para eso se tiene que verificar si el vendedor tiene la misma asignacion de clientes
                                var datos = { idVendedor: idVendedor };
                                $.ajax({
                                    url: $("#PATH").attr("name")+'poligonocabeceraterritorio/usuarioterritorio',
                                    type: 'POST',
                                    data: datos,
                                    success: (data, status, xhr) => {
                                        if(status == 'success'){
                                            var result = JSON.parse(data);
                                            console.log(data);
                                            if(result!=null){
                                                var result = JSON.parse(data);
                                                var territorio=result.territorio.split('@'); 
                                                var smsTerritorio="";
                                                $(".selectCheboxTerritorio").each(function (index) { 
                                                    var valor=$(this).val().split('=>');
                                                    var swTerritorio=false;
                                                    for(var i=0;i<territorio.length-1;i++) {
                                                        //console.log(value);
                                                        var territori=territorio[i].split('=>');
                                                        console.log("Territorio");
                                                        console.log(territori[1]);
                                                        console.log(valor[1]);
                                                        if(territori[1]==valor[1]){
                                                          
                                                            swTerritorio=true;
                                                            break;
                                                        }
                                                                                            
                                                    }
                                                    console.log("curso normal");
                                                    if(!swTerritorio){
                                                      
                                                        smsTerritorio=smsTerritorio+'\n- '+valor[1];
                                                      
                                                    }    
                                                    // if ($(this).is(':checked')) {
                                                    
                                                    
                                                    //} 
                                                });
                                                if(smsTerritorio!=""){
                                                    alert("El vendedor seleccionado no tiene asignado \nlos siguientes territorios:"+smsTerritorio+"\nNota: No se podrá actualizar el registro.");
                                                    //document.querySelector('.BTN-REGISTRAR').disabled=false;
                                                    //$('#BTNREGISTRAR').prop('disabled', false);
                                                }

                                               
                                            }
                                            else{
                                                alert("El vendedor seleccionado no tiene territorios asignados");
                                            }
                                            
                                        }
                                        else{
                                                console.error("ERROR USUARIO TERRITORIO: ");    
                                        }
                                    },
                                    error: (jqXhr, textStatus, errorMessage) => {
                                        console.error("ERROR: " + errorMessage);
                                    }
                                });
                            }
                        }
                        else{
                            swVerifica=false;
                           
                            alert("Alerta! el vendedor: "+resultado[0].vendedor+" ya esta asignado el dia: "+resultado[0].dia);
                        }
                    
                    }
                    else{
                        console.error("ERROR USUARIO VERIFICA ASIGNACION: ");    
                    }
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
        }
        else if($("#operador").val()!=""){

            alert("Vendedor seleccionado no existe");
        }
       
    }

    function ListarPoligono(){
        var idTerritorio = $("#poligonocabeceraterritorio-idterritorio").val();
        if(idTerritorio!=""){
            var datos = { idTerritorio: idTerritorio };
            // this.url = $("#PATH").attr("name");
            $.ajax({
                url: $("#PATH").attr("name")+'poligonocabeceraterritorio/poligonocabecera',
                type: 'POST',
                data: datos,
                
                success: (data, status, xhr) => {
                    if (status == 'success'){

                        //$("#poligonocabeceraterritorio-idpoligono").empty();
                        //$('#poligonocabeceraterritorio-idpoligono').append('<option value="0">Seleccione</option>');
                        var result = JSON.parse(data);
                        console.log(result);
                        if(result!=null){
                            var contenido="";
                            var checkPoligonos= document.getElementById("checkPoligonos");
                            for(var i=0;i<result.length;i++) {
                                console.log(result[i].nombre);
                                contenido=contenido+'<li class="list-group-item">'+
                                '<input type="checkbox" value="'+result[i].id+'=>'+result[i].nombre+'"  id="checkPoli-'+result[i].id+'" class="selectCheboxPoligono" onclick="serializaCheckPoligono()">'+
                                '<label class="form-check-label" for="checkPoli-'+result[i].id+'"> &nbsp;'+result[i].nombre+' ('+result[i].Description+')</label>'+
                                '</li>';
                                
                                //var territori=territorio[i].split('=>');
                                // console.log(territori[1]);
                            // $('#poligonocabeceraterritorio-idpoligono').append('<option value="'+result[i].id+'">'+result[i].nombre+'</option>');    
                            }
                            checkPoligonos.innerHTML = contenido;
                        }   
                    }
                    else{
                            console.error("ERROR USUARIO TERRITORIO: ");    
                        }
                
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
        }
        else{
            var checkPoligonos= document.getElementById("checkPoligonos");
            checkPoligonos.innerHTML = "";
            //alert("Seleccione un territorio");
        }

    }

    function getDiaSemana(){
        var fecha = $("#poligonocabeceraterritorio-fecharegistro").val();

        var dias = [
        'Lunes',
        'Martes',
        'Miercoles',
        'Jueves',
        'Viernes',
        'Sabado',
        'Domingo',
        ];
        var numeroDia = new Date(fecha).getDay();
        //dias[numeroDia];
        $("#poligonocabeceraterritorio-dia").val(dias[numeroDia]);
        //alert(dias[numeroDia]);
    }

    function cargarPoligono(){
        initMap(0);
        /*Poligono=[];
        var idPoligono =$("#poligonocabeceraterritorio-idpoligono").val();
        if(idPoligono!=""){
            var datos = { idPoligono: idPoligono };
            // this.url = $("#PATH").attr("name");
            
            $.ajax({
                url: $("#PATH").attr("name")+'poligonocabeceraterritorio/poligonodetalle',
                type: 'POST',
                data: datos,
                
                success: (data, status, xhr) => {
                    if (status == 'success'){
                        var result = JSON.parse(data);
                        console.log(result);
                        var swPoligono=1;
                        if(result!=null){
                            for(var i=0;i<result.length;i++) {
                                console.log("idcabecera: "+result[i].idcabecera);
                                
                                if(result[i].idcabecera==result[i+swPoligono].idcabecera){
                                    Poligono.push({
                                        latitud: result[i].latitud, 
                                        longitud: result[i].longitud
                                    });
                                }else{
                                    console.log("idcabecera: @");
                                    Poligono.push({
                                        latitud: result[i].latitud, 
                                        longitud: result[i].longitud
                                    });
                                    Poligono.push({
                                        latitud: "0", 
                                        longitud: "0"
                                    }); 
                                }
                                if(i==result.length-2){
                                    swPoligono=0;
                                }
                                
                            }
                            ////se inicializa el mapa
                            initMap(0);
                        }  
                        
                    }
                    else{
                            console.error("ERROR USUARIO POLIGONO: ");    
                        }
                
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
       }
       else{
         alert("Seleccione un poligono");
       }
        
        
        //initMap(parseInt(diaCombo));
        
       // MarcarVendedores(1);
        //clientesPordia('1'); */       
    }

 
    function serializaCheckTerritorio(){
        var idTerritorio =""
        var territorio =""
        $(".selectCheboxTerritorio").each(function (index) { 	
            if ($(this).is(':checked')) {
                var valor=$(this).val().split('=>');
                idTerritorio=idTerritorio+valor[0]+"@";
                territorio=territorio+$(this).val()+"@";
                
            } 
        });
        console.log(idTerritorio);
        $('#poligonocabeceraterritorio-idterritorio').val(idTerritorio); 
        $('#poligonocabeceraterritorio-territorio').val(territorio); 
        ListarPoligono();
    }
//////////////////////////////////////////////////////////
    function serializaCheckPoligono(){
        var idPoligono ="";
        var poligono="";
        $(".selectCheboxPoligono").each(function (index) { 	
            if ($(this).is(':checked')) {
                var valor=$(this).val().split('=>');
                idPoligono=idPoligono+valor[0]+"@";
                poligono=poligono+$(this).val()+"@";
            } 
        });
        console.log(idPoligono);
        $('#poligonocabeceraterritorio-idpoligono').val(idPoligono);
        $('#poligonocabeceraterritorio-poligono').val(poligono); 

    }

    ////pasa la js
    
function ListarClientes(){
    swTrazaRuta=false;
    //if( $('#poligonocabeceraterritorio-idpoligono').val()!="" && validaCheckPoligono() ){
    if($('#poligonocabeceraterritorio-idterritorio').val()!='' ){    
        this.url = $("#PATH").attr("name");
        var idTerritorio =$("#poligonocabeceraterritorio-idterritorio").val();
        var idPoligono =$("#poligonocabeceraterritorio-idpoligono").val();
        var dia =$("#poligonocabeceraterritorio-dia").val();
     
        $.ajax({
            url: this.url + 'poligonocabeceraterritorio/listacliente',
            type: 'POST',
            //dataType: 'json',
           
            data: 'territoryid='+ idTerritorio+'&idPoligono='+idPoligono+'&dia='+dia,
            success: function (data) {
                /*console.log($('#idusuario').val());
                console.log(data);*/
                ArrayData = $.parseJSON(data);
                console.log("POLIGONO CLIENTE");
                console.log(ArrayData);
              
                var i=0;
                var contenido="";
                if(ArrayData.length>=1){
                    for(i;i<ArrayData.length;i++){
                        //var valor=ArrayData[i].CardCode;//.split('":"');
                        //console.log(valor);
                        var latitud=ArrayData[i].latitud;
                        var longitud=ArrayData[i].longitud;
                        var swRuta=false;
                        console.log("latitud R : "+ArrayData[i].CardCode+" - "+latitud);
                        if(latitud=='undefined'){
                            latitud='16.5770072';
                            longitud='-68.2001839';
                            swRuta=true;
                        }
                        if(latitud=='0'){
                            latitud='16.5770072';
                            longitud='-68.2001839';
                            swRuta=true;
                        }
                        if(latitud==null){
                            latitud='16.5770072';
                            longitud='-68.2001839';
                            swRuta=true;
                        }
                        if(latitud!=null){
                            
                            var tamanio=latitud.length;
                            var tamanio2=longitud.length;
                            console.log("tamanio :"+tamanio);
                            var latitud1=latitud.substring(0,4);
                            var longitud1=longitud.substring(0,4);
                            console.log("latitud1 :"+latitud1);
                            var latitud2=latitud.substring(4,tamanio);
                            var longitud2=longitud.substring(4,tamanio2);
                            console.log("latitud2 :"+latitud2);
                            latitud2= latitud2.replace('.', '');
                            longitud2= longitud2.replace('.', '');
                            console.log("latitud2 replaze :"+latitud2);
                            latitud=latitud1+''+latitud2;
                            longitud=longitud1+''+longitud2;
                            console.log("latitud :"+latitud);
                             if(latitud1<longitud1){
                                swRuta=true;
                            }
                        }
                      
                        
                        console.log("latitud RP : "+ArrayData[i].CardCode+" - "+latitud);
                        ////validadndo coordenadas
                        
                       
                        
                        var cardName=ArrayData[i].CardName;
                        var tamanioCardName=cardName.length;

                        if(tamanioCardName>35){
                            cardName=cardName.substring(0,35);
                        }
                        var territorio=obtenerValorChekTerritorio(ArrayData[i].territorio);
						
						/*var territorio='-';
						var CardCode='-';
						var cardName='-';
						var latitud='-';
						var longitud='-';
						var swRuta='true';*/
						console.log("ID:  :"+i);
                        var direccion="error";
                        if(i<2000){

                            //if(ArrayData[i].direccion=='' && ArrayData[i].calle==''){
                            if(ArrayData[i].direccion==''){   
                                direccion="Sin dirección de envio";
                            }
                            /*else if(ArrayData[i].direccion!=ArrayData[i].calle && ArrayData[i].calle!=null){
                                direccion=ArrayData[i].direccion+' '+ArrayData[i].calle;
                            }*/
                            else{
                                 direccion=ArrayData[i].direccion;
                            }
                             

                            contenido=contenido+ AdicionarRow((i+1),ArrayData[i].CardCode,cardName,latitud,longitud,direccion,'tableDetalle',ArrayData[i].territorio,ArrayData[i].idPoligono,'',swRuta,territorio);
                            
                        }
                        else{
                            alert("¡Alerta! cantidad de clientes encontrados: "+ArrayData.length+" \n Cantidad máxima de clientes: 2.000 ");
                            break;
                        }
                        
                    }
                    //cargamos mapa
                   
                }

                var items = document.getElementById('tableDetalle');
                items.innerHTML = contenido;

                $("#cantidadClientes").text(i);
                cargarPoligono();
            },
            error: function (jqXhr, textStatus, errorMessage) {
                reject(errorMessage);
            }
        });
    }
    else{
        alert("Seleccione un Poligono");
    }
 }

function ListarClientesGuardados(id){
        this.url = $("#PATH").attr("name");
  
        $.ajax({
            url: this.url + 'poligonocabeceraterritorio/listaclienteguardados',
            type: 'POST',
            //dataType: 'json',
        
            data: 'idCabecera='+ id,
            success: function (data) {
               
                ArrayData = $.parseJSON(data);
                console.log("POLIGONO CLIENTE guardado: "+id);
                console.log(ArrayData);
                
                var contenido="";
                if(ArrayData.length>=1){
                    for(var i=0;i<ArrayData.length;i++){
                        var territorio=obtenerValorChekTerritorio(ArrayData[i].territoryid);
                        contenido=contenido+ AdicionarRow((i+1),ArrayData[i].cardcode,ArrayData[i].cardname,ArrayData[i].latitud,ArrayData[i].longitud,ArrayData[i].calle,'tableDetalle',ArrayData[i].territoryid,ArrayData[i].poligonoid,ArrayData[i].posicion,'0',territorio);

                    }
                    $("#cantidadClientes").text(i);           
                }
                var items = document.getElementById('tableDetalle');
                items.innerHTML = contenido;
                cargarPoligono();
            },
            error: function (jqXhr, textStatus, errorMessage) {
                reject(errorMessage);
            }
        });
    
}

 
function AdicionarRow(idAux,cardcode,cardname,latitud,longitud,calle,tabla,idTerritorio,idPoligono,posicion,observacion,territorio){
    //alert(tabla);
      //  var items = document.getElementById(tabla);
        var valor = '';
        if(idAux == 1 && posicion=='')
        {
            valor = 'checked';
        }
        if(posicion=='1')
        {
            valor = 'checked';
        }
        var swObservacion="";
        var stylo="";
        if(!observacion){
            swObservacion="Coordenadas incorrectas";
            stylo="background-color:#F7E90E;color:#0A0229;";
        }

        
        var contenido = '<tr  id="tr-fila-'+idAux+'" title="'+swObservacion+'"  style="'+stylo+'"  onclick="mostrarEtiqueta('+idAux+')" >  '+
                    '<td style="width:10"> <input type="radio" '+valor+' id="radio_'+idAux+'" name="RadioDetalle" value="'+cardcode+'*'+latitud+'*'+longitud+'*'+cardname+'*'+calle+'"> </td>'+
                    '<td style="width:10"> '+cardcode+'</td>'+
                                                
                    '<td style="width:10"> '+cardname+' </td>'+
                    '<td style="width:10"> '+calle+' </td>'+
                    '<td style="width:10"> '+posicion+' </td>'+                           

                    '<td style="width:10" align="center" >  <button title="Eliminar Fila" type="button" class="btn-link" value="" onclick="EliminarFila('+idAux+')" ><i class="fas fa-trash-alt text-warning"></i></button> '+swObservacion+' </td>'+                           
                    
                    
                    '<td style="" >' + latitud + '</td>'+//latitude
                    '<td style="">' + longitud + '</td>'+ //longitude   
                    '<td style="display: none;">' + idTerritorio + '</td>'+ //longitude   
                    '<td style="display: none;">' + idPoligono + '</td>'+ //longitude  
                    '<td style="display: none;">' + observacion + '</td>'+//latitude  
                    '<td style="display: none;">' + territorio + '</td>'+//territorio    
                  
                    '</tr>';
        return contenido;
        //items.innerHTML =items.innerHTML+ contenido;
        
   }

   function EliminarFila(id){
       //alert(id);
       var Row = document.getElementById("tr-fila-"+id);
       Row.parentNode.removeChild(Row);
       var cantidad= $('#tableDetalle').children().length;
       $("#cantidadClientes").text(cantidad);
      
    }
    //para mostrar vendedor

    function mostrarEtiqueta(id){
        var Row = document.getElementById("tr-fila-"+id);
        cambiarColor(Row);
        var cardCode= $(Row).find('td').eq(1).html(); 
        var cardName= $(Row).find('td').eq(2).html(); 
        var calle= $(Row).find('td').eq(3).html(); 
        var latitud= $(Row).find('td').eq(6).html(); 
        var longitud= $(Row).find('td').eq(7).html(); 

        ubicacion = { lat:Number(latitud), lng: Number(longitud), cardCode: cardCode, cardName: cardName, calle: calle }; 
        var infowindow = new google.maps.InfoWindow({
           content: '<b>Código: </b>'+cardCode+'<br><b>Nombre:</b>'+cardName+'<br><b>Dirección:</b> '+calle
         });
        var companyMarker = new google.maps.Marker({ 
            position: ubicacion,
            map: map,
            title:'Código: '+cardCode+'\nNombre:'+cardName+'\nDirección: '+calle,
            visible:true
        });
        infowindow.open(map,companyMarker);
    }

    ///cambia de color fila
    function cambiarColor(celda){
        if(swRowColor!=null){
            colorTr = swRowColor.style.backgroundColor;
		    swRowColor.style.backgroundColor="#F9FAFC";
        }
        colorTr = celda.style.backgroundColor;
        celda.style.backgroundColor="#F8DC3D";
        swRowColor=celda;   	
	}

    ///// para dibujar ruta

    function calcularRuta(){ 
        
        //if(puntosMarker!=null){
        for (var i = 0; i < allMarkers.length; i++) {
            allMarkers[i].setMap(null);
        }
        allMarkers=[];
    
        var radioCheked=false;
        var swRutaIncorrecta=true;
        $("input[name=RadioDetalle]").each(function (index) { 
            if($(this).is(':checked')){
                radioCheked=true; 
            }
        });
        $('#tableDetalle tr').each(function () {
            if ($(this).find('td').eq(10).html()=='false'){
                swRutaIncorrecta =false;   
            }
        });

        if(swRutaIncorrecta){
            if(radioCheked){
               // if($('#tableDetalle').children().length<=90){
                    if($('#tableDetalle').children().length>0){
                        var primerPunto = false;
                        var waypts = [];
                        var controlr = [];
                        var table = document.getElementById("tableDetalle");
                    
                        var contenedor = [];
                        var cont  = 1;
                        var marcado  = "";
                        var marcadox = "";
                        var control = "";
                        var inicio = "";
                        var fin = "";
                        var arrayRutas=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA']
                        console.log('calcularRuta');

                        
                        $("input[name=RadioDetalle]").each(function (index) { 
                            if($(this).is(':checked')){
                                //alert($(this).val()) ;
                                marcado = $(this).val();
                                contenedor = marcado.split('*');
                                inicio = { lat: Number(contenedor[1]), lng: Number(contenedor[2]),cardCode: contenedor[0],cardName: contenedor[3],calle: contenedor[4]};
                                console.log('marcadoini: '+contenedor[1].trim()+'-'+contenedor[2].trim());
                                //print(inicio);  

                                marcadox = contenedor[0].trim()+'-'+contenedor[1].trim()+'-'+contenedor[2].trim();
                                console.log('marcadox: '+marcadox);  
                                
                            }
                        });

                        $('#tableDetalle tr').each(function(index, tr) 
                        { 
                            if($(this).find('td:eq(10)').text()!='false'){
            
                                console.log('tableDetalle 2');   
                                control =$(this).find('td:eq(1)').text().trim()+'-'+$(this).find('td:eq(6)').text().trim()+'-'+$(this).find('td:eq(7)').text().trim();
                                console.log('ctronol:'+control);
                                if (marcadox ==control)//inicio == ""  &&
                                { 
                                    //inicio = { lat: Number($(this).find('td:eq(7)').text()), lng: Number($(this).find('td:eq(8)').text()) };
                                    ///map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: inicio});
                                    primerPunto = true;
                                    
                                    //directionsService = new google.maps.DirectionsService;
                                    //directionsRenderer = new google.maps.DirectionsRenderer;
                                    directionsRenderer.setMap(map);

                                    controlr.push(
                                        {
                                            cardcode: $(this).find('td:eq(1)').text().trim(),
                                            tipodoc: $(this).find('td:eq(6)').text().trim(),
                                            iddoc: $(this).find('td:eq(7)').text().trim(),
                                            posicion: 1,
                                            distancia :0
                                        }
                                    );
                                }

                                fin = { lat: Number($(this).find('td:eq(6)').text()), lng: Number($(this).find('td:eq(7)').text()),cardCode: $(this).find('td:eq(1)').text(),cardName: $(this).find('td:eq(2)').text(),calle: $(this).find('td:eq(3)').text() };
                                waypts.push({
                                location: fin,
                                stopover: true, 
                                distancia: calcularDistancia(inicio, fin)
                                });     
                            
                            
                                if (marcadox !=control){

                                    controlr.push(
                                        {
                                            cardcode: $(this).find('td:eq(1)').text().trim(),
                                            tipodoc: $(this).find('td:eq(6)').text().trim(),
                                            iddoc: $(this).find('td:eq(7)').text().trim(),
                                            posicion: 0,
                                            distancia: calcularDistancia(inicio, fin)
                                        }
                                    );
                                }
                            }
        
                        });

                        var wy = clone(waypts);
                        console.log('a.distancia - b.distancia');
                        wy = wy.sort(function(a, b){
                            return a.distancia - b.distancia;
                        });

                        console.log('wy.length: '+wy.length);
                        console.log(wy);

                        var xordenado = clone(controlr);
                    // var xordenado = clone(waypts);
                        xordenado = xordenado.sort(function(a, b){
                            return a.distancia - b.distancia;
                        });

                        console.log('xordenado.length: '+xordenado.length);
                        console.log(xordenado);

                    
                        
                        var final = [];
                        
                        for( var i = 1; i < wy.length; i++){
                            if (i < wy.length - 1 ){
                                /*final.push({
                                    location: wy[i].location,
                                    stopover: true
                                });*/

                                final.push(wy[i].location);
                            }
                            else{
                                fin = { lat: wy[i].location.lat, lng: wy[i].location.lng, cardCode: wy[i].location.cardCode, cardName: wy[i].location.cardName, calle: wy[i].location.calle }; 
                            }
                        };
                        // array completo de rutas
                        var stations = clone(final);

                        stations.unshift(inicio);
                        stations.push(fin);
                        console.log("ruta completa");
                        console.log(stations);


                        /*console.log(inicio);
                        console.log(fin);
                        console.log('final77: '+final);
                        console.log(final);

                        directionsService.route({
                        origin: inicio,
                        destination: fin,
                        waypoints: final,
                        ///optimizeWaypoints: true,
                        travelMode: google.maps.TravelMode.DRIVING*/

                        // Zoom and center map automatically by stations (each station will be in visible map area)
                        var lngs = stations.map(function(station) { return station.lng; });
                        var lats = stations.map(function(station) { return station.lat; });
                        map.fitBounds({
                            west: Math.min.apply(null, lngs),
                            east: Math.max.apply(null, lngs),
                            north: Math.min.apply(null, lats),
                            south: Math.max.apply(null, lats),
                        });

                        // Show stations on the map as markers
                        for (var i = 0; i < stations.length; i++) {
              
                            new google.maps.Marker({
                                position: stations[i],
                                map: map,
                                animation: google.maps.Animation.DROP,
                                //icon: marker_icon[i],
                              
                                title: "Codigo: " +stations[i].cardCode+ '\n' + 'Nombre: ' + stations[i].cardName+'\n Dirección: '+stations[i].calle
                            
                            });
                        }

                        // Divide route to several parts because max stations limit is 25 (23 waypoints + 1 origin + 1 destination)
                        for (var i = 0, parts = [], max = 25 - 1; i < stations.length; i = i + max)
                            parts.push(stations.slice(i, i + max + 1));

                        // Service callback to process service results
                        var service_callback = function(response, status) {
                            
                            if (status === 'OK') {

                                var renderer = new google.maps.DirectionsRenderer;
                                renderer.setMap(map);
                                renderer.setOptions({ suppressMarkers: true, preserveViewport: true });
                                renderer.setDirections(response);

                                //todo correcto marco la posicion
                                $('#tableDetalle tr').each(function(index, tr) 
                                {   
                                    var vcardcode = $(this).find('td:eq(1)').text().trim();
                                    var vtipodoc = $(this).find('td:eq(6)').text().trim();
                                    var viddoc= $(this).find('td:eq(7)').text().trim();

                                    for( var i = 0; i < xordenado.length; i++)
                                    {  
                                        if(vcardcode == xordenado[i].cardcode &&
                                        vtipodoc == xordenado[i].tipodoc &&
                                        viddoc == xordenado[i].iddoc)
                                        {
                                            console.log("COORDENAS COMPARA:");
                                            console.log(vcardcode+"-"+vtipodoc+"-"+viddoc);
                                            console.log(xordenado[i].cardcode+"-"+xordenado[i].tipodoc+"-"+xordenado[i].iddoc);


                                            var numero = i+1;
                                            console.log(numero);
                                            $(this).find('td:eq(4)').text(numero);
                                            break;
                                        }
                                    }
                                });
                            } 
                            else {
                                switch (status) {
                                    case 'MAX_WAYPOINTS_EXCEEDED':
                                        alert("Error! al trazar la ruta, contactarse con el administrador. ");
                                        console.log(status);
                                        break;
                                    case 'ZERO_RESULTS':
                                        alert("No se encontro resultados.");
                                        break;
                                    default:
                                        break;
                                }

                            }
                        };

                        // Send requests to service to get route (for stations count <= 25 only one request will be sent)
                        for (var i = 0; i < parts.length; i++) {
                            // Waypoints does not include first station (origin) and last station (destination)
                            var waypoints = [];
                            for (var j = 1; j < parts[i].length - 1; j++)
                                waypoints.push({location: parts[i][j], stopover: false});
                            // Service options
                            var service_options = {
                                origin: parts[i][0],
                                destination: parts[i][parts[i].length - 1],
                                waypoints: waypoints,
                                travelMode: 'WALKING'//google.maps.TravelMode.DRIVING//
                            };
                            // Send request
                            directionsService.route(service_options, service_callback);
                        }
                    }
                    else{
                        alert("No hay clientes en la lista... \n precione el boton Cargar Poligonos Mapa");
                    }
               /* }
                else{
                    alert("Error! la cantidad maxima para trazar la ruta es de 90 puntos.  ");
                }*/
            }
            else{
                alert("Marcar inicio de ruta");
            }
        }
        else{
            alert("Hay direcciones  incorrectas, verificar el detalle de la lista de clientes");
        }
    }
    ////marker personalizados

    function createMarker(position, index) {
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            icon: marker_icon[index]
        });
    }
    // fin de marker personalizados

    function calcularDistancia(p1, p2) {
        return (google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(p1.lat, p1.lng), new google.maps.LatLng(p2.lat, p2.lng)) / 1000);
    }
    function clone(obj){
       if(obj == null || typeof(obj) != 'object')
            return obj;
        var temp = obj.constructor();
        for(var key in obj)
            temp[key] = clone(obj[key]);
        return temp;
    }

    ///funcion para asignar nombre
    function asignaEtiquetas(color){
        var lat = '';
        var lon = '';
        var cod = '';
        var nom = '';
        var direc='';
        var esVisible = false;
        $('#tableDetalle tr').each(function () {
            console.log("tableDetalle");
            esVisible =  $(this).is(":visible");
            if (esVisible){
                cod = $(this).find('td').eq(1).html(); 
                nom = $(this).find('td').eq(2).html(); 
                direc = $(this).find('td').eq(3).html(); 
                lat = $(this).find('td').eq(6).html(); 
                lon = $(this).find('td').eq(7).html(); 
                var coor = { lat: Number(lat), lng: Number(lon) };
                console.log("coordenadas:  ");
                console.log(coor);
                placeMarker(coor, color, { CardCode:  cod, CardName:  nom, Direccion:  direc });
            }
        });
    }


function guardarDetalle(){
    //var detallePoligonoCli=[];
    var detallePoligonoCli="";
    var swPosicion=0;
    $('#tableDetalle tr').each(function(index, tr) 
    {       
        /*detallePoligonoCli.push(
            {
                cardCode: $(this).find('td:eq(1)').text().trim(),
                cardName: $(this).find('td:eq(2)').text().trim(),
                calle: $(this).find('td:eq(3)').text().trim(),
                posicion: $(this).find('td:eq(4)').text().trim(),
                latitud: $(this).find('td:eq(6)').text().trim(),
                longitud: $(this).find('td:eq(7)').text().trim(),
                idTerritorio: $(this).find('td:eq(8)').text().trim(),
                territorio: $(this).find('td:eq(11)').text().trim(),
                idPoligono: $(this).find('td:eq(9)').text().trim(),
                dia:getNumeDia($('#poligonocabeceraterritorio-dia').val()),
                vendedor:$('#poligonocabeceraterritorio-idvendedor').val()
               
            }
        );
        */
        if($(this).find('td:eq(4)').text().trim()==""){
            swPosicion=1;
        }
        detallePoligonoCli=detallePoligonoCli
        +$(this).find('td:eq(1)').text().trim()+'&&'//cardCode
        +$(this).find('td:eq(2)').text().trim()+'&&'//cardName
        +$(this).find('td:eq(3)').text().trim()+'&&'//calle
        +$(this).find('td:eq(4)').text().trim()+'&&'//posicion
        +$(this).find('td:eq(6)').text().trim()+'&&'//latitud
        +$(this).find('td:eq(7)').text().trim()+'&&'//longitud
        +$(this).find('td:eq(8)').text().trim()+'&&'//idTerritorio
        +$(this).find('td:eq(11)').text().trim()+'&&'//territorio
        +$(this).find('td:eq(9)').text().trim()+'&&'//idPoligono
        +getNumeDia($('#poligonocabeceraterritorio-dia').val())+'&&'//dia
        +$('#poligonocabeceraterritorio-idvendedor').val()+'@@';//vendedor

       
    });
    console.log("detallePoligonoCli");
    console.log(detallePoligonoCli);
    var paraguardar = { 
                    fechaRegistro: $('#poligonocabeceraterritorio-fecharegistro').val(),
                    fechaSistema: $('#poligonocabeceraterritorio-fechasistema').val(),
                    dia: $('#poligonocabeceraterritorio-dia').val(),
                    idDia:getNumeDia($('#poligonocabeceraterritorio-dia').val()),
                    idVendedor: $('#poligonocabeceraterritorio-idvendedor').val(),
                    vendedor: $('#poligonocabeceraterritorio-vendedor').val(),
                    idTerritorio: $('#poligonocabeceraterritorio-idterritorio').val(),
                    territorio: $('#poligonocabeceraterritorio-territorio').val(),
                    idPoligono: $('#poligonocabeceraterritorio-idpoligono').val(),
                    poligono: $('#poligonocabeceraterritorio-poligono').val(),
                    idUserRegister: $('#poligonocabeceraterritorio-iduserregister').val(),
                    userRegister: $('#poligonocabeceraterritorio-userregister').val(),
                    nombreRuta: $('#poligonocabeceraterritorio-nombreruta').val(),
                    detallePoligono: detallePoligonoCli
                    
                };
    if(swPosicion!=1){
        if(swVerifica){
            $.ajax({
                url:  $("#PATH").attr("name")+'poligonocabeceraterritorio/create',
                type: 'POST',
                dataType: 'json',
                data: paraguardar,
                    success: (data, status, xhr) => {
                        if (status == 'success'){
                            var result = JSON.parse(data); 
                            console.log(result);  
                            console.log("POSIII");
                            $.toast({
                                heading: 'Success',
                                text: 'El registro fue guardado correctamente..',
                                showHideTransition: 'fade',
                                icon: 'success'
                            });
                        
                            location.reload();
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
            alert("El vendedor seleccionado ya tiene un registro en el dia: "+$('#poligonocabeceraterritorio-dia').val());
        }
    }else{
        alert("Seleccione un cliente inicial y trace una ruta");
    }   
}
///actualizar

function actualizarDetalle(id){
    var detallePoligonoCli="";
    $('#tableDetalle tr').each(function(index, tr) 
    {       
        detallePoligonoCli=detallePoligonoCli
        +$(this).find('td:eq(1)').text().trim()+'&&'//cardCode
        +$(this).find('td:eq(2)').text().trim()+'&&'//cardName
        +$(this).find('td:eq(3)').text().trim()+'&&'//calle
        +$(this).find('td:eq(4)').text().trim()+'&&'//posicion
        +$(this).find('td:eq(6)').text().trim()+'&&'//latitud
        +$(this).find('td:eq(7)').text().trim()+'&&'//longitud
        +$(this).find('td:eq(8)').text().trim()+'&&'//idTerritorio
        +$(this).find('td:eq(11)').text().trim()+'&&'//territorio
        +$(this).find('td:eq(9)').text().trim()+'&&'//idPoligono
        +getNumeDia($('#poligonocabeceraterritorio-dia').val())+'&&'//dia
        +$('#poligonocabeceraterritorio-idvendedor').val()+'@@';//vendedor
    });
   
    var paraguardar = { 
                    id: id,
                    fechaRegistro: $('#poligonocabeceraterritorio-fecharegistro').val(),
                    fechaSistema: $('#poligonocabeceraterritorio-fechasistema').val(),
                    dia: $('#poligonocabeceraterritorio-dia').val(),
                    idDia:getNumeDia($('#poligonocabeceraterritorio-dia').val()),
                    idVendedor: $('#poligonocabeceraterritorio-idvendedor').val(),
                    vendedor: $('#poligonocabeceraterritorio-vendedor').val(),
                    idTerritorio: $('#poligonocabeceraterritorio-idterritorio').val(),
                    territorio: $('#poligonocabeceraterritorio-territorio').val(),
                    idPoligono: $('#poligonocabeceraterritorio-idpoligono').val(),
                    poligono: $('#poligonocabeceraterritorio-poligono').val(),
                    idUserRegister: $('#poligonocabeceraterritorio-iduserregister').val(),
                    userRegister: $('#poligonocabeceraterritorio-userregister').val(),
                    nombreRuta: $('#poligonocabeceraterritorio-nombreruta').val(),
                    detallePoligono: detallePoligonoCli
                    
                };

    console.log(detallePoligonoCli);
    if(swVerifica){
        $.ajax({
        //url: this.url + 'poligonocabeceraterritorio/create',
        url: $("#PATH").attr("name")+'poligonocabeceraterritorio/update',
        type: 'POST',
        data: paraguardar,
            success: (data, status, xhr) => {
                if (status == 'success'){
                    var result = JSON.parse(data); 
                    console.log(result);  
                    console.log("POSIII");
                    $.toast({
                        heading: 'Success',
                        text: 'El registro fue modificado correctamente..',
                        showHideTransition: 'fade',
                        icon: 'success'
                    });
                
                    location.reload();
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
        alert("El vendedor seleccionado ya tiene un registro en el dia: "+$('#poligonocabeceraterritorio-dia').val());
    }
    
}


function getNumeDia(dia){
    switch (dia) {
        case 'Lunes':
            return 1;
        break;
        case 'Martes':
            return 2;
        break;
        case 'Miercoles':
            return 3;
        break;
        case 'Jueves':
            return 4;
        break;
        case 'Viernes':
            return 5;
        break;
        case 'Sabado':
            return 6;
        break;
        case 'Domingo':
            return 7;
        break;
    
        default:
            return 0;
            break;
    }
}

function VerificarCalcularRuta() {
    var posicion="";
    $('#tableDetalle tr').each(function () {
        console.log("tableDetalle");
        esVisible =  $(this).is(":visible");
        if (esVisible){
            posicion = $(this).find('td').eq(4).html();   
        }
    });
    if(posicion!=0 ){

        calcularRuta();
    }
 
 }
 setTimeout(VerificarCalcularRuta,2000);

 function validaCheckPoligono(){
     var swPoligno=false;
    $(".selectCheboxPoligono").each(function (index) { 	
        if ($(this).is(':checked')) {
            swPoligno=true; 
        } 
    });
    return swPoligno;
 }

 function validaCheckTerritorio(){
     var swTerritorio=false;
    $(".selectCheboxTerritorio").each(function (index) { 	
        if ($(this).is(':checked')) {
            swTerritorio= true;
           
        } 
    });
    return swTerritorio;
 }
 //////////////////////////////////////////////////////////////

 function obtenerValorChekTerritorio(id){
    var territorio="-";
    
    $(".selectCheboxTerritorio").each(function (index) { 	
        if ($(this).is(':checked')) {
            
            var valor=$(this).val().split('=>');
            console.log(valor[0]+"==="+id);
            if(valor[0]==id){
                territorio=valor[1];
            } 
        } 
    }); 
    return territorio;
 }

</script>