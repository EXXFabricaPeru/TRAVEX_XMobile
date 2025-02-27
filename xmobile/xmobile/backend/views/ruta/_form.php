<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Ruta-form']); ?>
    <input type="hidden" value="<?= $model->id ?>" id="hdId" />
    <div class="row">
        <div class="col-md-6">  
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-nombre"></span>
        </div>
        <div class="col-md-6">  
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Vendedores::find()->all(), 'id', 'SalesEmployeeName'); ?>
            <?= $form->field($model, 'idvendedor')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-idvendedor"></span>
        </div>        
    </div>
    <div class="row">
        <div class="col-md-4">  
            <?= $form->field($model, 'fecha')->textInput() ?>
            <span class="text-danger text-clear" id="error-fecha"></span>
        </div>
        <div class="col-md-4">  
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Clientes::find()->where(['<>','Latitude',0])->all(), 'id', 'CardName'); ?>
            <?= $form->field($model, 'idclienteinicial')->dropDownList($arr, ['prompt' => '', 'onchange' => '$("#hdCliente").val(this.value); buscarCoordenada();']); ?>
            <span class="text-danger text-clear" id="error-idclienteinicial"></span>
            <input type="hidden" id="hdCliente">
        </div>
        <div class="col-md-4"> 
            <input type="button" value="AGREGAR A LA RUTA" class="btn btn-success" onclick="agregarCliente();" />
            <input type="hidden" id="hdCoordenadas" />
        </div>
    </div>    
    <div class="row">
        <div class="col-md-8">  
            <button id="btn-location" class="fas fa-palette"></button>
            <div id="map"></div>
        </div>
        <div class="col-md-4">  
            <table width="100%" id="tableClientes">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <?php if ($model->id == null || $model->id == 0 ) { ?>
                    <tbody></tbody>
                <?php } else { ?>
                    <tbody>                    
                        <?php foreach ($detalle as $key) { ?>
                            <?php 
                                $carcode = "";
                                $cardname= "";                                
                                $cliente = backend\models\Clientes::find()->where([ 'id'  => $key->idcliente])->one();
                                $cardcode = $cliente->CardCode;
                                $cardname = $cliente->CardName;
                            ?>
                            <tr>
                                <td style='display: none;'><?= $key->idcliente; ?></td>
                                <td><?= $cardcode; ?></td>
                                <td><?= $cardname; ?></td>
                                <td style='display: none;'><?= $key->latitud; ?></td>
                                <td style='display: none;'><?= $key->longitud; ?></td>
                                <td style='display: none;'><?= $key->posicion; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                <?php } ?>
            </table>
        </div>
    </div>

    <!-- <input type="button" value="REGISTRAR" class="btn btn-success" onclick="guardar();" /> -->
    <?php ActiveForm::end(); ?>

</div>
<script>
    var map;
    var directionsService;
    var directionsRenderer;
    var myLocation = {lat: -16.496777, lng: -68.132031};
    var btnLocation = document.getElementById("btn-location");
    var primerPunto = false;

    $('document').ready(function(){
        initMap();
    });

    btnLocation.addEventListener('click', function() {
        miUbicaion();
    });

    function initMap() {
        directionsService = new google.maps.DirectionsService;
        directionsRenderer = new google.maps.DirectionsRenderer;
        var ubi = {lat: -16.496777, lng: -68.132031};
        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});
        //new google.maps.Marker({position: ubi, map: map});
        
        directionsRenderer.setMap(map);
        var quitarespacios = $('#hdId').val();
        if (quitarespacios != ''){
            $('#hdCoordenadas').val('');                        
            var inicio = '';            
            $('#tableClientes > tbody  > tr').each(function(index, tr) {
                if (inicio == ""){                    
                     inicio = $(this).find('td:eq(5)').text() + "|" +
                              $(this).find('td:eq(0)').text() + "|" +                              
                              $(this).find('td:eq(3)').text() + "|" +
                              $(this).find('td:eq(4)').text() + "|" +
                              $(this).find('td:eq(2)').text();
                }
                else {
                    inicio = inicio + "*" + $(this).find('td:eq(5)').text() + "|" +
                                            $(this).find('td:eq(0)').text() + "|" +                              
                                            $(this).find('td:eq(3)').text() + "|" +
                                            $(this).find('td:eq(4)').text() + "|" +
                                            $(this).find('td:eq(2)').text();
                }
            });
            $('#hdCoordenadas').val(inicio);
            calcularRuta();
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

    function agregarCliente(){
        var cliente = $('#hdCliente').val();
        var splitCliente = cliente.split("|");
        var pos = (document.getElementById('tableClientes').rows.length);
        var fila = "<tr>";
        fila = fila + "<td style='display: none;'>" + splitCliente[0] + "</td>"; //id
        fila = fila + "<td>" + splitCliente[1] + "</td>"; //cardcode
        fila = fila + "<td>" + splitCliente[2] + "</td>"; //cardname
        fila = fila + "<td style='display: none;'>" + splitCliente[3] + "</td>"; //latitude
        fila = fila + "<td style='display: none;'>" + splitCliente[4] + "</td>"; //longitude
        fila = fila + "<td style='display: none;'>" + pos + "</td>"; //posicion
        fila = fila + "</tr>";
        $("#tableClientes").find('tbody').append(fila);        
        var coordenadas = $('#hdCoordenadas').val();        
        if (coordenadas == "") coordenadas = pos + "|" + splitCliente[0] + "|" + splitCliente[3] + "|" + splitCliente[4]+"|"+splitCliente[2];
        else coordenadas = coordenadas + "*" + pos + "|" + splitCliente[0] + "|" + splitCliente[3] + "|" + splitCliente[4]+"|"+splitCliente[2];
        $('#hdCoordenadas').val(coordenadas);
    }

    function calcularRuta(){
        primerPunto = false;
        var waypts = [];
        var checkboxArray = document.getElementById('waypoints');

        var table = document.getElementById("tableClientes");
        var inicio = "";
        var fin = "";

        $('#tableClientes > tbody  > tr').each(function(index, tr) {             
            if (inicio == ""){ 
                inicio = { lat: Number($(this).find('td:eq(3)').text()), lng: Number($(this).find('td:eq(4)').text()) };
                map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: inicio});
                primerPunto = true;
                directionsService = new google.maps.DirectionsService;
                directionsRenderer = new google.maps.DirectionsRenderer;
                directionsRenderer.setMap(map);
            }
            fin = { lat: Number($(this).find('td:eq(3)').text()), lng: Number($(this).find('td:eq(4)').text()) };
            waypts.push({
              location: fin,
              stopover: true, 
              distancia: calcularDistancia(inicio, fin)
            });
        });
        var wy = clone(waypts);
        
        wy = wy.sort(function(a, b){
            return a.distancia - b.distancia;
        });

        var final = [];
        
        for( var i = 1; i < wy.length; i++){
            if (i < wy.length - 1 ){
                final.push({
                    location: wy[i].location,
                    stopover: true
                });
            }
            else{
                fin = { lat: wy[i].location.lat, lng: wy[i].location.lng }; 
            }
        };

        directionsService.route({
          origin: inicio,
          destination: fin,
          waypoints: final,
          optimizeWaypoints: true,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsRenderer.setDirections(response);
            var route = response.routes[0];
           
          } else {
            alert('Directions request failed due to ' + status);
          }
        });
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
            var res = {pos: div[0], cliente: div[1], lat: div[2], lon: div[3],cardname: div[4] };
            resultado.push(res);
        }

        return resultado;
    }

    function guardar(){
        var vendedor = $('#rutacabecera-idvendedor')[0].value;
        var vfecha = $('#rutacabecera-fecha').val();
        var nombre = $('#rutacabecera-nombre').val();
        var lon = ''; 
        var lat = '';
        var rutas = ArregloRutas();
        console.log("detalle77-nuevo");    
        console.log(rutas);

        var clienteinicial = rutas[0]["cliente"];
        lat = rutas[0]["lat"];
        lon = rutas[0]["lon"];
        var paraguardar = { 
                            id: 0,
                            idvendedor: vendedor, 
                            nombre: nombre,
                            fecha: vfecha, 
                            idclienteinicial: clienteinicial, 
                            longitud: lon, 
                            latitud: lat, 
                            detalle: rutas 
                        };

        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['ruta/create']); ?>',
               type: 'POST',
               data: paraguardar,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                        $('.window').dialog('close');
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

    function actualizar(){
        var idruta = $('#hdId').val();
        var vendedor = $('#rutacabecera-idvendedor')[0].value;
        var vfecha = $('#rutacabecera-fecha').val();
        var nombre = $('#rutacabecera-nombre').val();
        var lon = ''; 
        var lat = '';
        var rutas = ArregloRutas();   
        console.log("detalle77-actualiza");    
        console.log(rutas);
       
        var clienteinicial = rutas[0]["cliente"];
        lat = rutas[0]["lat"];
        lon = rutas[0]["lon"];
        var paraguardar = { 
                            id: idruta,
                            idvendedor: vendedor, 
                            nombre: nombre,
                            fecha: vfecha, 
                            idclienteinicial: clienteinicial, 
                            longitud: lon, 
                            latitud: lat, 
                            detalle: rutas 
                        };

        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['ruta/update']); ?>',
               type: 'POST',
               data: paraguardar,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                        $('.window').dialog('close');
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

</script>