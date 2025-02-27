<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Viusuariopersona;
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
    #btn-location {
        position: absolute;
        right: 20px;
        top: 60px;
        z-index: 1;
        padding: 20px;
        border: none;
        border-radius: 4px;
        background-color: rgba(255, 255, 255, 0.8);
        transition: 0.5s;
    }

    #btn-location:hover {
        background-color: rgba(0, 0, 0, 1);
        color: white;
        cursor: pointer;
    }
    
    #tableClientes {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tableClientes td, #tableClientes th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tableClientes tr:nth-child(even){background-color: #f2f2f2;}

    #tableClientes tr:hover {background-color: #ddd;}

    #tableClientes th {
        padding-top: 10px;
        padding-bottom: 10px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>


<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Rutacabecera-form']); ?>
    <input type="hidden" value="<?= $model->id ?>" id="hdId" />
    <div class="row">
        
        <div class="col-md-6">    
            <div class="row">
            
                <div class="col-md-6">    
                    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?> 
                    <span class="text-danger text-clear" id="error-nombre"></span> 
                
                </div> 
              
                <div class="col-md-3">    
                     <?= $form->field($model, 'fecha')->textInput(['autocomplete'=>off]) ?>
                    <span class="text-danger text-clear" id="error-fecha"></span>
                </div>
                <div class="col-md-3">
                </div>
                
            </div>
            <div class="row">

                <div class="col-md-6">     

                    <label>Despachador </label> 
                    <input type="text" size="30"  name="operador" id="operador" value="<?=$model->vendedor?>" list="datalistUser"  value="" class="form-control mayusculas" data-validation="required"  placeholder="Usuario Despachador"> 
                </div> 
                <div class="col-md-3">    
                     <?= $form->field($model, 'fechapicking')->textInput(['autocomplete'=>off]) ?>
                    <span class="text-danger text-clear" id="error-fechapicking"></span>
                </div> 
                <div class="col-md-3"> <br>
                    <input type="button" value="CARGAR DOC." class="btn btn-success" onclick="agregarDocumentos(1);" />
                    <input type="hidden" id="hdCoordenadas" />
                </div>
            </div>
            <div class="row">
                <datalist id="datalistUser">
                    <?php

                    foreach ($modeluser as $key => $value) {
                            echo"<option id='".$value['id']."'  value='".$value['username'].' - '.$value['nombreCompleto']."' > 

                            </option>";                                      
                    }
                    ?>
                </datalist>     
            </div>
            <div class="row">
                <div class="col-md-12">  
                    <!--button id="btn-location" class="fas fa-palette"></button-->
                    <div id="map"></div>
                </div>
            </div>
        </div>  
        <div class="col-md-6">  
            <div class="row">
                 <div class="col-md-12">

                    <hr>
                    <h4>
                        <div class="row">
                            <div class="col-lg-4" style="font-size:15px">Lista de Clientes: <span id="cantidadClientes"> <?=count($detalle)?> </span></div>
                            <div class="col-lg-2" style="font-size:15px" >Buscador:</div>
                            <div class="col-lg-6">
                            <input id="searchTerm" type="text" class="form-control" onkeyup="doSearch()" />
                            </div>
                        </div>
                    </h4>
                   
                </div>
                <div class="col-md-12" style="height:370px;overflow:auto;">  
                    <table width="100%" id="tableClientes" >
                        <thead>
                            <tr>
                                <th>Marcar</th>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>TipoDoc</th>
                                <th>NroDoc</th>
                                <th>Posicion</th>
                                <th>Fecha Vencimiento</th>
                                <th>Nro. Picking</th>
                                <th>Borrar</th>
                            </tr>
                        </thead>
                       
                        <!--<tbody id="tableDetalle"></tbody>-->
                        <?php if ($model->id == null || $model->id == 0 ) { ?>
                            <tbody id="tableDetalle"></tbody>
                        <?php } else { ?>
                            <tbody id="tableDetalle">   
                                <?php $cont = 1;    
                                      $valor = ''; 
                                ?>             
                                <?php foreach ($detalle as $key) { ?>
                                    <?php 
                                        //$carcode = "";
                                        //$cardname= "";                                
                                        //$cliente = backend\models\Clientes::find()->where([ 'CardCode'  => $key->idcliente])->one();
                                        //$cardcode = $cliente->CardCode;
                                       // $cardname = $cliente->CardName;

                                        if($key->posicion=='1')
                                        {
                                            $valor = 'checked'; 
                                        }
                                        else{
                                            $valor = ''; 
                                        }

                                    ?>
                                
                                    
                                        <tr  id="tr-fila-<?=$cont?>" onclick="mostrarEtiqueta('<?=$cont?>')">
                                        <td style="width:10"> <input type="radio" <?= $valor; ?> id="radio_<?=$cont?>" name="name" value="<?= $cardcode; ?>*<?= $key->tipodoc; ?>*<?= $key->iddoc; ?>*<?= $key->latitud; ?>*<?= $key->longitud; ?>*<?= $key->cardname; ?>*<?= $key->tipodoc; ?>"> </td>  
                                        <td style="width:10"> <?= $key->idcliente; ?></td>
                                                                    
                                        <td style="width:10"> <?= $key->cardname; ?> </td>
                                        <td style="width:10"> <?= $key->tipodoc; ?></td>
                                        <td style="width:10"> <?= $key->iddoc; ?></td>
                                        <td style="width:10">  <?= $key->posicion; ?></td>                          

                                        <td style='display: none;'></td>                          
                                        
                                        
                                        <td style='display: none;'><?= $key->latitud; ?></td>
                                        <td style='display: none;'><?= $key->longitud; ?></td>     
                                        <td style='display: none;'><?= $key->idcliente; ?></td>

                                        <td style="width:10"><?= $key->dateUpdate; ?></td>
                                        <td style="width:10"><?= $key->nropicking; ?></td>
                                        <td style="width:10" align="center" >  <button title="Eliminar Fila"  type="button" class="btn-link" value="" onclick="EliminarFila('<?=$cont; ?>')" ><i class="fas fa-trash-alt text-warning"></i></button>  </td> 
                                        </tr>
                                    
                                    
                                <?php $cont++; } ?>
                            </tbody>
                        <?php } ?>
                        
                    </table>
                    <div class="well">
                        <p id="p-resultado"> </p>  
                    </div>
                </div>
            </div>
        </div>
         <?= $form->field($model, 'idvendedor')->hiddenInput()->label(false); ?>
         <?= $form->field($model, 'vendedor')->hiddenInput()->label(false); ?>
    </div>
    <imput type="hidden" id="TRAZORUTA" name="TRAZORUTA" value="0" >
    <?php ActiveForm::end(); ?>
    
</div>
<script>

var map;
    var directionsService;
    var directionsRenderer;
    var myLocation = {lat: -16.496777, lng: -68.132031};
    var btnLocation = document.getElementById("btn-location");
    var primerPunto = false;
    var swRowColor;
    var infowindow ;
    var companyMarker; 

    $('document').ready(function(){
        initMap(0);
    });

    btnLocation.addEventListener('click', function() {
        miUbicaion();
    });

    function initMap(valor) {

        directionsService = new google.maps.DirectionsService;
        directionsRenderer = new google.maps.DirectionsRenderer;
        var ubi = {lat: -16.496777, lng: -68.132031};
        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});
        //new google.maps.Marker({position: ubi, map: map});
        console.log('hola0');
        directionsRenderer.setMap(map);
        var quitarespacios = $('#hdId').val();
        if (quitarespacios != ''){
            $('#hdCoordenadas').val('');                        
            var inicio = '';            
            $('#tableDetalle > tbody  > tr').each(function(index, tr) {
                if (inicio == ""){                    
                     inicio = $(this).find('td:eq(6)').text() + "|" +
                              $(this).find('td:eq(5)').text() + "|" +                              
                              $(this).find('td:eq(7)').text() + "|" +
                              $(this).find('td:eq(8)').text();
                }
                else {
                    inicio = inicio + "*" + $(this).find('td:eq(6)').text() + "|" +
                                            $(this).find('td:eq(5)').text() + "|" +                              
                                            $(this).find('td:eq(7)').text() + "|" +
                                            $(this).find('td:eq(8)').text();
                }
            });
            $('#hdCoordenadas').val(inicio);
            //$('#hdCoordenadas').val(inicio);

            calcularRuta(valor);
        }

        //else{alert('no entro a actualizar');}
    }
    
    function buscarCoordenada(){
        /*
        var vacio = true;
        $('#tableClientes > tbody  > tr').each(function(index, tr) {
            vacio = false;
        }
        */
        var idcliente = $('#hdCliente').val();
        var cliente = { id: idcliente };
        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['ruta/traerdatoscliente']); ?>',
               type: 'POST',
               data: cliente,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                        var resultado = JSON.parse(data);                        
                        var xy = { lat: Number(resultado["Latitude"]), lng: Number(resultado["Longitude"]) };
                        if (!primerPunto){
                            map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: xy});
                            primerPunto = true;
                        }
                        new google.maps.Marker({
                                    position: xy, 
                                    map: map,
                                    zoom: 15,
                                    draggable: true,
                                    animation: google.maps.Animation.DROP,
                                    title: resultado["CardName"],
                                    center: xy
                        });
                        //new google.maps.Marker( { position: xy, map: map, zoom: 15, center: xy, title: resultado["CardName"] });
                        $('#hdCliente').val(
                                                resultado["id"] + "|" + 
                                                resultado["CardCode"] + "|" + 
                                                resultado["CardName"] + "|" + 
                                                resultado["Latitude"] + "|" + 
                                                resultado["Longitude"] 
                                            );
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

   
    function calcularRuta(valor){
        var swCheckRario=false;
        $("input[name=name]").each(function (index) { 
            if($(this).is(':checked')){
               swCheckRario=true;
            }
        });
        if(swCheckRario){
            if($('#tableDetalle').children().length>0){
                primerPunto = false;
                var waypts = [];
                var controlr = [];
                var checkboxArray = document.getElementById('waypoints');      
                

                var table = document.getElementById("tableDetalle");
              
                var contenedor = [];
                var cont  = 1;
                var marcado  = "";
                var marcadox = "";
                var control = "";
                var inicio = "";
                var fin = "";
                console.log('hola1');

                 
                $("input[name=name]").each(function (index) { 
                if($(this).is(':checked')){
                    //alert($(this).val()) ;
                    marcado = $(this).val();
                    contenedor = marcado.split('*');
                    inicio = { lat: Number(contenedor[3]), lng: Number(contenedor[4]),cardCode: contenedor[0],cardName: contenedor[5],posicion: contenedor[6] };
                    console.log('marcadoini: '+contenedor[3].trim()+'-'+contenedor[4].trim());
                    //print(inicio);  

                    marcadox = contenedor[0].trim()+'-'+contenedor[1].trim()+'-'+contenedor[2].trim();
                    console.log('marcadox: '+marcadox);  
                }
                });
                


                //> tbody  > 
                $('#tableDetalle tr').each(function(index, tr) 
                {       
                    console.log('hola2');   
                    control =$(this).find('td:eq(1)').text().trim()+'-'+$(this).find('td:eq(3)').text().trim()+'-'+$(this).find('td:eq(4)').text().trim();
                    console.log('ctronol:'+control);
                    if (marcadox ==control)//inicio == ""  &&
                    { 
                        //inicio = { lat: Number($(this).find('td:eq(7)').text()), lng: Number($(this).find('td:eq(8)').text()) };
                        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: inicio});
                        primerPunto = true;
                        
                        directionsService = new google.maps.DirectionsService;
                        directionsRenderer = new google.maps.DirectionsRenderer;
                        directionsRenderer.setMap(map);

                        controlr.push(
                            {
                                cardcode: $(this).find('td:eq(1)').text().trim(),
                                tipodoc: $(this).find('td:eq(3)').text().trim(),
                                iddoc: $(this).find('td:eq(4)').text().trim(),
                                posicion: 1,
                                distancia :0
                            }
                        );
                    }
                    //else
                    //{
                        fin = { lat: Number($(this).find('td:eq(7)').text()), lng: Number($(this).find('td:eq(8)').text()),cardCode: $(this).find('td:eq(1)').text(),cardName: $(this).find('td:eq(2)').text(),posicion: $(this).find('td:eq(3)').text() };
                        waypts.push({
                        location: fin,
                        stopover: true, 
                        distancia: calcularDistancia(inicio, fin)
                        });     
                    //}
                   
                    if (marcadox !=control)
                    {
                        controlr.push(
                            {
                                cardcode: $(this).find('td:eq(1)').text().trim(),
                                tipodoc: $(this).find('td:eq(3)').text().trim(),
                                iddoc: $(this).find('td:eq(4)').text().trim(),
                                posicion: 0,
                                distancia: calcularDistancia(inicio, fin)
                            }
                        );
                    }
                    

                    cont =  cont+1;
                });


               
                var wy = clone(waypts);
                console.log('hola3');
                wy = wy.sort(function(a, b){
                    return a.distancia - b.distancia;
                });

                console.log('hola4: '+wy.length);

                var xordenado = clone(controlr);
                xordenado = xordenado.sort(function(a, b){
                    return a.distancia - b.distancia;
                });

                console.log('hola5: '+xordenado.length);

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
                        fin = { lat: wy[i].location.lat, lng: wy[i].location.lng, cardCode: wy[i].location.cardCode, cardName: wy[i].location.cardName, posicion: wy[i].location.posicion }; 
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
                      
                        title: "Codigo: " +stations[i].cardCode+ '\n' + 'Nombre: ' + stations[i].cardName+'\n Tipo Doc: '+stations[i].posicion
                    
                    });
                }
                 ////////////////////////////si valor es 0 calcula ruta////////////////////////////////////////
                if(valor==0){
                   
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
                                var vtipodoc = $(this).find('td:eq(3)').text().trim();
                                var viddoc= $(this).find('td:eq(4)').text().trim();

                                for( var i = 0; i < xordenado.length; i++)
                                {  
                                    if(vcardcode == xordenado[i].cardcode &&
                                    vtipodoc == xordenado[i].tipodoc &&
                                    viddoc == xordenado[i].iddoc)
                                    {
                                        console.log("COORDENAS COMPARA:"+ i);
                                        console.log(vcardcode+"-"+vtipodoc+"-"+viddoc);
                                        console.log(xordenado[i].cardcode+"-"+xordenado[i].tipodoc+"-"+xordenado[i].iddoc);


                                        var numero = i+1;
                                        console.log(numero);
                                        $(this).find('td:eq(5)').text(numero);
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

            }
            else{
                alert("Error! no hay lista de clientes ");
            }
        }
        else{
            alert("Marcar cliente inicial");
        }
    }

    function clone(obj){
       if(obj == null || typeof(obj) != 'object')
            return obj;
        var temp = obj.constructor();
        for(var key in obj)
            temp[key] = clone(obj[key]);
        return temp;
    }

    function calcularDistancia(p1, p2) {
        return (google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(p1.lat, p1.lng), new google.maps.LatLng(p2.lat, p2.lng)) / 1000);
    }

    function ArregloRutas(){
        var coordenadas = $('#hdCoordenadas').val();
        var resultado = [];
        var registros = coordenadas.split('*');
                                    
        for(var i=0; i<registros.length; i++){
            var div = registros[i].split('|');
            var res = {pos: div[0], cliente: div[1], lat: div[2], lon: div[3],cardcode:div[4] };
            resultado.push(res);
        }
        return resultado;
    }

    function miUbicaion(){
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {

                myLocation.lat = position.coords.latitude;
                myLocation.lng = position.coords.longitude;

                // I just added a marker for you to verify your location
                var marker = new google.maps.Marker({
                    position: myLocation,
                    map: map
                });

                map.setCenter(myLocation);
            }, function() {
                handleLocationError(true, map.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, map.getCenter());
        }
    }

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
         console.log(browserHasGeolocation ?
            'Error: The Geolocation service failed.' :
            'Error: Your browser doesn\'t support geolocation.');
    }

    function ArregloRutas2(){

        var table = document.getElementById("tableDetalle");
        var resultado = [];

        $('#tableDetalle tr').each(function(index, tr) 
        {       
            var idcliente= $(this).find('td:eq(1)').text().trim();
            var tipodoc= $(this).find('td:eq(3)').text().trim();
            var iddoc = Number($(this).find('td:eq(4)').text().trim());
            var posicion= $(this).find('td:eq(5)').text().trim();
            var lat = Number($(this).find('td:eq(7)').text());
            var lng = Number($(this).find('td:eq(8)').text());
            var cardname = $(this).find('td:eq(2)').text();
            var fechaupdate = $(this).find('td:eq(10)').text();
            var nropicking = $(this).find('td:eq(11)').text();
                        
            var res = {pos: posicion, cliente: idcliente, lat: lat, lon: lng,tipodoc: tipodoc,iddoc: iddoc,cardname:cardname,fechaupdate:fechaupdate,nropicking:nropicking };//

            resultado.push(res);
        });

        console.log(resultado);
        return resultado;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////
     ///////////////////////////////////////////////////////////////////////////////////
    function verificarRegistroUsuario(valor,id){
        var datos={idUsuario:$('#rutacabecera-idvendedor').val(),fechaDespacho:$('#rutacabecera-fecha').val()};
        $.ajax({
           url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['rutacabecera/verificaregistrousuario']); ?>',
           type: 'POST',
           data: datos,
           success: (data, status, xhr) => {
               if (status == 'success'){
                    console.log("Verifica registro: ");
                    console.log(data);
                    var contador=JSON.parse(data);
                    console.log("cantidad de registro: "+contador.length);
                    if(contador.length==0){
                        //se registra la ruta
                        if(valor==0)guardar(0);
                        else actualizar(0);
                        
                    }
                    else{
                        if(id!=contador[0]['id']){

                            var opcion = confirm("El despachador seleccionado ya tiene un registro en esta fecha: "+$('#rutacabecera-fecha').val()+", si acepta se inactivara la ruta: "+contador[0]['nombre']);
                            if (opcion == true) {
                                if(valor==0) guardar(contador[0]['id']);
                                else actualizar(contador[0]['id']);
                            } 
                        }
                        else{
                             if(valor==0) guardar(contador[0]['id']);
                             else actualizar(0);
                        }
                    }
                    //if(contador[]){}
               }
               else{
                    console.error("ERROR AL VERIFICAR REGISTRO USUARIO: ");    
                }
           },
            error: (jqXhr, textStatus, errorMessage) => {
                console.error("ERROR: " + errorMessage);
            }
        });
    }
    //////////////////////////////////////////////


    //////////////////////////////////////////////
    function guardar(idActualizar){
        var swPosicion=0;
        var swCheckRario=false;
        if($('#tableDetalle').children().length>0){

            if($('#tableDetalle').find('td:eq(5)').text().trim()==""){
                swPosicion=1;
            }
            $("input[name=name]").each(function (index) { 
                if($(this).is(':checked')){
                   swCheckRario=true;
                }
            });

            if(swPosicion!=1 && swCheckRario==true){
                console.log('enttroguarddar');
                var vendedor = $('#rutacabecera-vendedor').val();
                var idvendedor = $('#rutacabecera-idvendedor').val();
                var vfecha = $('#rutacabecera-fecha').val();
                var fechapicking = $('#rutacabecera-fechapicking').val();
                var nombre = $('#rutacabecera-nombre').val();
                var lon = ''; 
                var lat = '';
                var tipouser = 'D';
                console.log('tipuse:'+tipouser);
                var rutas = ArregloRutas2();
                var clienteinicial = rutas[0]["cliente"];
                lat = rutas[0]["lat"];
                lon = rutas[0]["lon"];
                var paraguardar = { 
                        id: 0,
                        vendedor: vendedor, 
                        idvendedor: idvendedor, 
                        nombre: nombre,
                        fecha: vfecha, 
                        fechapicking:fechapicking, 
                        idclienteinicial: clienteinicial, 
                        longitud: lon, 
                        latitud: lat, 
                        tipousuario: tipouser,
                        detalle: rutas,
                        idActualizar:idActualizar 
                    };
                

                console.log(paraguardar);
                $.ajax({
                       url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['ruta/create']); ?>',
                       type: 'POST',
                       data: paraguardar,
                       success: (data, status, xhr) => {
                           if (status == 'success'){
                                console.log(data);
                                $('.window').dialog('close');
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
            }else{
                alert("Seleccione un cliente inicial y trace una ruta");
            }
        }
        else{
            alert("Error! no hay lista de clientes ");
        }
    }
   
    function actualizar(idActualizar){
        var swPosicion=0;
        var swCheckRario=false;
        if($('#tableDetalle').children().length>0){
            if($('#tableDetalle').find('td:eq(5)').text().trim()==""){
                swPosicion=1;
            }
            $("input[name=name]").each(function (index) { 
                if($(this).is(':checked')){
                   swCheckRario=true;
                }
            });

            if(swPosicion!=1 && swCheckRario==true){
                console.log('enttro a actualizar');
                var idruta = $('#hdId').val();
                var vendedor= $("#operador").val();//$('#rutacabecera-vendedor').val();
                var idvendedor= $('#rutacabecera-idvendedor').val();
                var vfecha = $('#rutacabecera-fecha').val();
                var fechapicking = $('#rutacabecera-fechapicking').val();
                var nombre = $('#rutacabecera-nombre').val();
                var lon = ''; 
                var lat = '';
                var tipouser = 'D';
                var rutas = ArregloRutas2();        
                console.log(rutas);
                console.log()
                var clienteinicial = rutas[0]["cliente"];
                lat = rutas[0]["lat"];
                lon = rutas[0]["lon"];
                var paraguardar = { 
                                    id: idruta,
                                    vendedor: vendedor, 
                                    idvendedor: idvendedor, 
                                    nombre: nombre,
                                    fecha: vfecha,
                                    fechapicking:fechapicking, 
                                    idclienteinicial: clienteinicial, 
                                    longitud: lon, 
                                    latitud: lat, 
                                    tipousuario: tipouser,
                                    detalle: rutas,
                                    idActualizar:idActualizar 
                                };

                $.ajax({
                       url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['ruta/update']); ?>',
                       type: 'POST',
                       data: paraguardar,
                       success: (data, status, xhr) => {
                           if (status == 'success'){
                                $('.window').dialog('close');
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
            }else{
                alert("Seleccione un cliente inicial y trace una ruta");
            }
        }
        else{
            alert("Error! no hay lista de clientes ");
        }
    }







     //para mostrar vendedor

    function mostrarEtiqueta(id){

        var Row = document.getElementById("tr-fila-"+id);
        cambiarColor(Row);
        var cardCode= $(Row).find('td').eq(1).html(); 
        var cardName= $(Row).find('td').eq(2).html(); 
        var posicion= $(Row).find('td').eq(3).html(); 
        var latitud= $(Row).find('td').eq(7).html(); 
        var longitud= $(Row).find('td').eq(8).html(); 

        ubicacion = { lat:Number(latitud), lng: Number(longitud), cardCode: cardCode, cardName: cardName, posicion: posicion }; 
        infowindow = new google.maps.InfoWindow({
           content: '<b>Código: </b>'+cardCode+'<br><b>Nombre:</b>'+cardName+'<br><b>Tipo Doc:</b> '+posicion
         });
        companyMarker = new google.maps.Marker({ 
            position: ubicacion,
            map: map,
            title:'Código: '+cardCode+'\nNombre:'+cardName+'\nTipo Doc: '+posicion,
            visible:true
        });
        infowindow.open(map,companyMarker);
    }

    var infowindow ;
    var companyMarker; 
    ///cambia de color fila
    function cambiarColor(celda){
        if(swRowColor!=null){
            infowindow.close();
            colorTr = swRowColor.style.backgroundColor;
            swRowColor.style.backgroundColor="#F9FAFC";

        }
        colorTr = celda.style.backgroundColor;
        celda.style.backgroundColor="#F8DC3D";
        swRowColor=celda;       
    }

    function asignaEtiquetas(color){
        var lat = '';
        var lon = '';
        var cod = '';
        var nom = '';
        var direc='';
  
        
        $('#tableDetalle tr').each(function(index, tr) 
        {       
            var lat = Number($(this).find('td:eq(7)').text());
            var lon = Number($(this).find('td:eq(8)').text());           
                        
            var coor = { lat: Number(lat), lng: Number(lon) };
            map = new google.maps.Map(document.getElementById('map'), {zoom: 12, center: coor});
            
        });

        $('#tableDetalle tr').each(function(index, tr) 
        {       
            var cardcode= $(this).find('td:eq(1)').text().trim();
            var typedoc= $(this).find('td:eq(3)').text().trim();
          
         
            var lat = Number($(this).find('td:eq(7)').text());
            var lon = Number($(this).find('td:eq(8)').text());
            var cardname = $(this).find('td:eq(2)').text();
           
                        
            var coor = { lat: Number(lat), lng: Number(lon) };
            console.log("coordenadas:  ");
            console.log(coor);
            placeMarker(coor, color, { CardCode: cardcode, CardName:  cardname,TypeDoc:  typedoc });
        });

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
            tooltip = "Codigo: " + informacion.CardCode + '\n' + 'Nombre: ' + informacion.CardName+'\n Tipo Doc.'+informacion.TypeDoc;
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
      

        

       
    }

</script>
