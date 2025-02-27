<?php

use yii\helpers\Url;
use yii\helpers\Html;

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
        font-size: 13px;
        background-color:#ccc;
        padding: 4px;
        color:#fff;
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
    <div class="row">
        <div class="col-md-6">
            <label class="control-label">Territorio</label>
            <?= Html::input('text', 'territorio', $model->territoryname, $options=['id' => 'txtTerritorio', 'class'=>'form-control','disabled'=>'disabled', 'style'=>'']) ?>
        </div>
        <div class="col-md-6">
            <label class="control-label">Polígono</label>
            <?= Html::input('text', 'poligono', $model->poligononombre, $options=['id' => 'txtPoligono', 'class'=>'form-control','disabled'=>'disabled', 'style'=>'']) ?>
        </div>
    </div>
    <div class="row"><br /></div>
    <div class="row">
        <div class="col-md-6">
            <label class="control-label">Dias de visita</label>
            <?php $arr = \yii\helpers\ArrayHelper::map($ddlDias, 'Codigo', 'Dia'); ?>
            <?= Html::dropDownList('ddlDias', null, $arr, ['style' => 'width:100%;', 'id' => 'ddlDias', 'class' => 'form-control', 'onchange' => "clientesPordia('0')"]) ?>
        </div>
        <div class="col-md-6">
            <label class="control-label">Inicio del recorrido</label>
            <?php $arr = \yii\helpers\ArrayHelper::map($ddlClientes, 'CardCode', 'CardName'); ?>
            <?= Html::dropDownList('ddlClientes', null, $arr, ['style' => 'width:100%;', 'id' => 'ddlClientes', 'class' => 'form-control']) ?>
        </div>
    </div><div class="row"><br /></div>
    <div class="row">
        <div class="col-md-6">
            <label class="control-label">Días registrados</label>
            <table width="100%" id="tblDiasGuardados">
                <tr><td style="text-align:left;"><?= $DiasGuardados['LU'] ?></td></tr>
                <tr><td style="text-align:left;"><?= $DiasGuardados['MA'] ?></td></tr>
                <tr><td style="text-align:left;"><?= $DiasGuardados['MI'] ?></td></tr>
                <tr><td style="text-align:left;"><?= $DiasGuardados['JU'] ?></td></tr>
                <tr><td style="text-align:left;"><?= $DiasGuardados['VI'] ?></td></tr>
                <tr><td style="text-align:left;"><?= $DiasGuardados['SA'] ?></td></tr>
                <tr><td style="text-align:left;"><?= $DiasGuardados['DO'] ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <label class="control-label">Vendedores seleccionados</label>
            <div  style="height: 100px; overflow-y: scroll;">
                <table id="tblVendedoresSeleccionados">
                    <!-- <thead>
                        <th>Nombre</th>
                        <th style="width:20px;">Supr.</th>
                        <th style="display:none;"></th>
                    </thead> -->
                    <tbody>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-success" onclick="abrirVendedores();">Seleccionar vendedores</button>
            <!--DNE1 -->
            <button type="button" class="btn btn-success" onclick="RecargarClientesYmapa()">Todo los Clientes</button>
        </div>
    </div>
    <div class="row"><br /></div>
    <div class="row">
        <div class="col-md-6">
            <div id="map"></div>
        </div>
        <div class="col-md-6" style="height:500px;overflow:auto;">
        <label class="control-label">Lista de Clientes</label>
            <table id="hdClientes">
                <thead>
                    <th>Código</th>
                    <th>Nombre</th>
                    <!--th>Latitud</th-->
                    <!--th>Longitud</th-->
                    <th style="display:none;"></th>
                    <th style="display:none;"></th>
                    <th style="display:none;"></th>
                    <th style="display:none;"></th>
                    <!--th style="display:none;"></th-->
                    <th style="display:none;"></th>
                    <th style="display:none;"></th>
                    <!--th></th-->
                    <th>Dirección</th>
                    <th>Marcar</th>
                </thead>
                <?php if ($crear == false) { ?>
                    <?php foreach ($clientesGuardados as $cliente) { ?>
                        <tr>
                            <td><?= $cliente["cardcode"]; ?></td>
                            <td><?= $cliente["cardname"]; ?></td>
                            <td><?= $cliente["latitud"]; ?></td>
                            <td><?= $cliente["longitud"]; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <?php foreach ($clientes as $cliente) { ?>
                        <tr>
                            <td><?= $cliente["CardCode"]; ?></td>
                            <td><?= $cliente["CardName"]; ?></td>                        
                            <td style="display:none;"><?= $cliente["latitud"]; ?></td>
                            <td style="display:none;"><?= $cliente["longitud"]; ?></td>
                         
                            <td style="display:none;"><?= $cliente["Properties1"]; ?></td>
                            <td style="display:none;"><?= $cliente["Properties2"]; ?></td>
                            <td style="display:none;"><?= $cliente["Properties3"]; ?></td>
                            <td style="display:none;"><?= $cliente["Properties4"]; ?></td>
                            <td style="display:none;"><?= $cliente["Properties5"]; ?></td>
                            <td style="display:none;"><?= $cliente["Properties6"]; ?></td>
                            <td style="display:none;"><?= $cliente["Properties7"]; ?></td>
                       
                            <td style="display:none;"><?= $cliente["direccion"]; ?></td>
                            <td><?= $cliente["calle"]; ?></td>
                            <td><input type="checkbox" id="chk_<?= $cliente['CardCode'] ?>" checked /></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
    <div class="ui-dialog-buttonset">
        <button type="button" class="btn btn-success" onclick="guardar();">REGISTRAR</button>
        <button type="button" class="btn btn-warning" onclick="cancelar();">OK</button>
    </div>
</div>

<input type="hidden" id="hdCrear" value="'<?= $crear ?>'" />
<input type="hidden" id="hdIdPoligono" value="<?= $cabecera["id"] ?>" />
<input type="hidden" id="hdIdTerrotorio" value="<?= $model["territoryid"] ?>" />
<!-- <div class="modal fade" id="divVendedores" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> -->



<div id="divVendedores"  title="Seleccionar vendedores" style='display: none;'>
    <div id="checkboxes">        
        <?= Html::input('text', 'territorio', '', $options=['id' => 'txtBuscarVendedor', 'class'=>'form-control', 'style'=>'', 'onkeyup' => 'BuscarVendedor()']) ?>
        <br />
        <table id="tblVendedores" width="100%">
            <tbody>


                <?php foreach ($vendedores as $vendedor) {?>
                    
                    <tr onclick="SeleccionarVendedor(this)" style="cursor:pointer;">
                        <td style="display: none;"><input type="checkbox" id="chk_<?= $vendedor["SalesPersonCode"] ?>"></td>
                        <td><?= $vendedor["Nombre"] ?></td>
                        <td style="display:none;"><?= $vendedor["SalesPersonCode"] ?></td>
                <?php } ?>

                
            <tbody>
    </div>
</div>




<table id="hdPoligono" style="display:none;">
    <?php foreach ($detalle as $deta) { ?>    
        <tr>
            <td><?= $deta["latitud"]; ?></td>
            <td><?= $deta["longitud"]; ?></td>
        </tr>
    <?php } ?>
</table>
<table id="hdGuardados" style="display:none;">
    <?php foreach ($clientesGuardados as $deta) { ?>    
        <tr>
            <td><?= $deta["cardcode"]; ?></td>
            <td><?= $deta["dia"]; ?></td>
            <td><?= $deta["posicion"]; ?></td>
        </tr>
    <?php } ?>
</table>
<table id="hdVendedores" style="display:none;">
    <?php foreach ($vendedores as $deta) { ?>    
        <tr>
            <td><?= $deta["SalesPersonCode"]; ?></td>
            <td><?= $deta["Nombre"]; ?></td>
            <td><?= $deta["LU"]; ?></td>
            <td><?= $deta["MA"]; ?></td>
            <td><?= $deta["MI"]; ?></td>
            <td><?= $deta["JU"]; ?></td>
            <td><?= $deta["VI"]; ?></td>
            <td><?= $deta["SA"]; ?></td>
            <td><?= $deta["DO"]; ?></td>
        </tr>
    <?php } ?>
</table>
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
<!-- //////////////////////////////////////////////////////////////INCIO DE LA SECCION DE SCRIPTS/////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
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
    $('document').ready(function () {
        cargarDatos();
        initMap(0);        
        // $( "#divVendedores" ).dialog({
        //     height: 300,
        //     width: 300,
        //     modal: true,
        //     buttons: {
        //         "Seleccionar todos": seleccionarTodoVendedores,
        //         "Aceptar": cerrarVendedores
        //         //"Cerrar": function() {  $( "#divVendedores" ).dialog( "close" ); }
        //     }
        // });            
        // $( "#divVendedores" ).dialog("close");
    });

    function initMap(color) {
        idmarker = 1;
        var ubi = {lat: -16.496777, lng: -68.132031};
        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});
        google.maps.event.addListener(map, 'click', function(event) { placeMarker(event.latLng); });
        var crear = $('#hdCrear').val();
        //dibujar poligono        
        var lcoor=[];
        for(var i = 0; i < Poligono.length; i++){
            //var marca = {id:mensaje[i]["id"], lat: Number(mensaje[i]["latitud"]), lon: Number(mensaje[i]["longitud"]) };
            var coor = { lat: Number(Poligono[i]["latitud"]), lng: Number(Poligono[i]["longitud"]) };
            if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor});                
            //placeMarker(coor);
            lcoor.push(coor);
        }        
        bermudaTriangle = new google.maps.Polygon({ paths: lcoor, strokeColor: '#FF0000', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#FF0000', fillOpacity: 0.35 });
        bermudaTriangle.setMap(map);
        //google.maps.event.addListener(bermudaTriangle , 'click', isWithinPoly);
        var isWithinPolygon = false;
        var lat = '';
        var lon = '';
        var cod = '';
        var nom = '';
        var esVisible = false;
        $('#hdClientes tr').each(function () {
            esVisible =  $(this).is(":visible");
            if (esVisible){
                cod = $(this).find('td').eq(0).html(); 
                nom = $(this).find('td').eq(1).html(); 
                lat = $(this).find('td').eq(2).html(); 
                lon = $(this).find('td').eq(3).html(); 
                var coor = { lat: Number(lat), lng: Number(lon) };
                placeMarker(coor, color, { CardCode:  cod, CardName:  nom });
            }
        });
        /*for (var i = 0; i < Clientes.length; i++){
            var coor = { lat: Number(Clientes[i]["latitud"]), lng: Number(Clientes[i]["longitud"]) };
            placeMarker(coor, 1, { CardCode:  Clientes[i]["cardcode"], CardName:  Clientes[i]["cardname"] });
            //isWithinPolygon = google.maps.geometry.poly.containsLocation(coor, bermudaTriangle); 
            //console.log(isWithinPolygon);
        }*/
    }

     /** @this {google.maps.Polygon} */ 
     function isWithinPoly(event){ 
        var isWithinPolygon = google.maps.geometry.poly.containsLocation(event.latLng, this); 
    } 


    function cargarDatos(){
        $('#hdPoligono tr').each(function () {
            Poligono.push({
                latitud: $(this).find('td').eq(0).html(), 
                longitud: $(this).find('td').eq(1).html()
            });            
        });
        
        $('#hdClientes tr').each(function () {
            Clientes.push({
                cardcode: $(this).find('td').eq(0).html(), 
                cardname: $(this).find('td').eq(1).html(),  
                latitud: $(this).find('td').eq(2).html(), 
                longitud: $(this).find('td').eq(3).html()
            });            
        });

        MarcarVendedores(1);
        clientesPordia('1');        
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
            tooltip = "Codigo: " + informacion.CardCode + '\n' + 'Nombre: ' + informacion.CardName;
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

        marker.addListener('click', function() {
            //geoAbrirClienteEspecifico(informacion);
        
            $("#ddlClientes").val(cardcode);
        });
    }

    function OrdenarCoordenadas(){
        var resultado = [];
        var cardcode = $('#ddlClientes')[0].value;
        var clienteinicial;
        for (var i = 0; i < Clientes.length; i++){
            if (cardcode == Clientes[i]["cardcode"]){
                clienteinicial = Clientes[i];
                break;
            }
        }
        var cod = '';
        var nom = '';
        var lat = '';
        var lon = '';
        var dir = '';
        var cal = '';
        var chk = false;
   
        $('#hdClientes tr').each(function () {
            esVisible =  $(this).is(":visible");
            if (esVisible){
                cod = $(this).find('td').eq(0).html(); 
                if (cod != undefined){
                    nom = $(this).find('td').eq(1).html(); 
                    lat = $(this).find('td').eq(2).html(); 
                   lon = $(this).find('td').eq(3).html();
                   dir = $(this).find('td').eq(10).html();
                   cal = $(this).find('td').eq(11).html(); 
                    chk = $(this).find('td').eq(13).find('input')[0].checked;
                    if (chk){
                        resultado.push({
                            CardCode: cod,
                            CardName: nom,
                            latitud: lat,
                            longitud: lon,
                            direccion: dir,
                            calle: cal,
                            distancia: calcularDistancia({
                                lat: Number(clienteinicial["latitud"]),
                                lng: Number(clienteinicial["longitud"])
                            },{
                                lat: Number(lat),
                                lng: Number(lon)
                            })                            
                        });
                    }
                }
            }
        });

        /*for (var i = 0; i < Clientes.length; i++){
            if (Clientes[i]["cardcode"] != undefined){
                resultado.push({
                    CardCode: Clientes[i]["cardcode"],
                    CardName: Clientes[i]["cardname"],
                    latitud: Clientes[i]["latitud"],
                    longitud: Clientes[i]["longitud"],
                    distancia: calcularDistancia({
                        lat: Number(clienteinicial["latitud"]),
                        lng: Number(clienteinicial["longitud"])
                    },{
                        lat: Number(Clientes[i]["latitud"]),
                        lng: Number(Clientes[i]["longitud"])
                    })
                });
            }
        }*/
        return  resultado.sort(function(a, b){ return a.distancia - b.distancia; });
    }

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

    function guardar(){
        var codVendedor = 0;
        var nomVendedor = '';
        var vendedores = [];
        $('#tblVendedoresSeleccionados tbody tr').each(function () {
            codVendedor = $(this).find('td').eq(2).html();
            nomVendedor = $(this).find('td').eq(0).html();
            vendedores.push({ Codigo: codVendedor, Nombre: nomVendedor });
        });
        
        var poligono =  { Codigo: $('#hdIdPoligono').val(), Nombre: $('#txtPoligono').val() };        
        var clientes = OrdenarCoordenadas();        
        var territorio = { Codigo: $('#hdIdTerrotorio').val(), Nombre: $('#txtTerritorio').val() };        
        var dia = $('#ddlDias')[0].value;

        var nrocli = 0;
        
        for (var i = 0; i < clientes.length; i++){
            nrocli = nrocli + 1;
            }
            if(nrocli == 1){                                    
            window.alert("Debe seleccionar más de un cliente");
          //  location.reload();
            }
                var paraguardar = { 
                Poligono: poligono,
                Territorio: territorio,
                Clientes: clientes,
                Dia: dia,
                Vendedores: vendedores
            };
         console.log(paraguardar);
        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocliente/guardar']); ?>',
               type: 'POST',
               data: paraguardar,
             
               success: (data, status, xhr) => {
                   if (status == 'success'){
//$('.window').dialog('close');
                       // location.reload();
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

    function cancelar(){
        $('.window').dialog('close');
        location.reload();
    }

    function clientesPordia(diaCombo){
        if (diaCombo == '0') diaCombo = $('#ddlDias')[0].value;
   
        var diaTabla = '';
        var columna = parseInt(diaCombo) + 3;
        var codigo = '';
        var nombre = ''
        $('#ddlClientes').empty();
        for (var i = 0; i < allMarkers.length; i++){
            allMarkers[i].setMap(null);
        }
        allMarkers = [];
        $('#hdClientes tr').each(function () {
            diaTabla = $(this).find('td').eq(columna).html();
            
            if (diaTabla != undefined){
                if (diaTabla == 'tNO') $(this).hide();
                else{
                    codigo = $(this).find('td').eq(0).html();
                    nombre = "(" + codigo + ") " + $(this).find('td').eq(1).html();
                   
                    $('#ddlClientes').append($('<option></option>').val(codigo).html(nombre));
                    $(this).show();
                }
            }
        });
    
        MarcarVendedores(parseInt(diaCombo));
        initMap(parseInt(diaCombo));
        SeleccionarPrimero(parseInt(diaCombo));
    }

    function SeleccionarPrimero(dia){        
        var diaTabla = 0;
        var posTabla = 0;
        var cod = '';
        $('#hdGuardados tr').each(function () {            
            cod = $(this).find('td').eq(0).html(); 
            diaTabla = parseInt($(this).find('td').eq(1).html()); 
            posTabla = parseInt($(this).find('td').eq(2).html()); 
            if (dia == diaTabla && posTabla == 1) {
                //seleccionar en el combo
                $("#ddlClientes").val(cod);                
            }
        });
    }

    function MarcarVendedores(dia){
        var codigo = '';
        var nombre = '';
        var valor = '';
        var indice = dia;
 
        $('#tblVendedoresSeleccionados tbody').empty();
        $('#hdVendedores tr').each(function () {            
            codigo = $(this).find('td').eq(0).html();
            nombre = $(this).find('td').eq(1).html();
            valor = $(this).find('td').eq(dia + 1).html();
            if (valor == 'NO') $('#chk_' + codigo)[0].checked = false;
            else{
                $('#chk_' + codigo)[0].checked = true;
                agregarVendedor(codigo, nombre);
            }
        });
    }

    function abrirVendedores(){
          $( "#divVendedores" ).dialog({
            height: 400,
            width: 450,
            modal: true,
            buttons: {
                //"Seleccionar todos": seleccionarTodoVendedores,
                "Aceptar":cerrarVendedores
               // "Cerrar": function() {  $( "#divVendedores" ).dialog( "close" ); }
            }
        });            
        //$( "#divVendedores" ).dialog("close");
        //$( "#divVendedores" ).dialog( "open" );
    }
    function cerrarVendedores() {
        $( "#divVendedores" ).dialog( "close" );
    }

    function seleccionarTodoVendedores(){
        var codigo = '';
        $('#hdVendedores tr').each(function () { 
            codigo = $(this).find('td').eq(0).html();
            $('#chk_' + codigo)[0].checked = true; 
        });
    }

    function BuscarVendedor(){
        var valor = $('#txtBuscarVendedor').val().toUpperCase();
        var nombre = '';
        var resultado = false;
        $('#tblVendedores tr').each(function () { 
            nombre = $(this).find('td').eq(1).html().toUpperCase();
            resultado = nombre.includes(valor);
            if (resultado) $(this).show();
            else $(this).hide();
        });
    }

    function SeleccionarVendedor(fila){  
     
        $(this).children('td').remove();      
        var nombre = $(fila).find('td').eq(1).html(); 
        var id = $(fila).find('td').eq(2).html(); 
        var agregar = true;
        var idtabla = '';
        
       
        $('#tblVendedoresSeleccionados tbody tr').each(function () {
            idtabla = $(this).find('td').eq(2).html();
            if (id == idtabla){
                agregar = false;
            }
        });

        if (agregar){
            agregarVendedor(id, nombre);
    
            $( "#divVendedores" ).dialog( "close" );
        }
     
    }

//DNE1
    function agregarVendedor(id, nombre){
                var fila = "<tr><td>" + nombre + "</td>" +
                        "<td style='text-align:center; width:20px;'><button onclick='ClienteDelVendedor(" + id + ")'>" + 
                            "<i class='fas fa-eye text-warning'></i></button></td>" +
                         "<td style='display: none;'>" + id + 

                        "<td style='text-align:center; width:20px;'><button onclick='eliminarVendedor(" + id + ")'>" + 
                            "<i class='fas fa-trash-alt text-warning'></i></button></td>" +
                         "<td style='display: none;'>" + id +                    
                         "</td> </tr>";
                         
            $("#tblVendedoresSeleccionados").find("tbody").append(fila);
    }

//DNE1
function eliminarVendedor(id){        
        $('#tblVendedoresSeleccionados tbody tr').each(function () {
            idtabla = $(this).find('td').eq(2).html();
            if (id == idtabla){
                    $(this).children('td').remove();
            }
        });

        var poligono =  { Codigo: $('#hdIdPoligono').val(), Nombre: $('#txtPoligono').val() };        
        var clientes = OrdenarCoordenadas();        
        var territorio = { Codigo: $('#hdIdTerrotorio').val(), Nombre: $('#txtTerritorio').val() };        
        var dia = $('#ddlDias')[0].value;
        var idtabla2 = id;
        var paraguardar = { 
                            Poligono: poligono,
                            Territorio: territorio,
                            Clientes: clientes,
                            Dia: dia,
                            Idtabla: idtabla2
                        };
      $.ajax({url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocliente/eliminarvendedor']); ?>',
               type: 'POST',
               data: paraguardar,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                       // $('.window').dialog('close');
                      // location.reload();
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

//DNE1
function ClienteDelVendedor(id){  
      
        var vendedores = [];
        $('#tblVendedoresSeleccionados tbody tr').each(function () {
            codVendedor = $(this).find('td').eq(2).html();
            nomVendedor = $(this).find('td').eq(0).html();
            vendedores.push({ Codigo: codVendedor, Nombre: nomVendedor });
        });
  
        var poligono =  { Codigo: $('#hdIdPoligono').val(), Nombre: $('#txtPoligono').val() }; 
               
        var clientes = OrdenarCoordenadas();   
                
        var territorio = { Codigo: $('#hdIdTerrotorio').val(), Nombre: $('#txtTerritorio').val() }; 
        
        var dia = $('#ddlDias')[0].value;
        var idtabla2 = id;
        var paraguardar = { 
                            Poligono: poligono,
                            Territorio: territorio,
                            Clientes: clientes,
                            Dia: dia,
                            Vendedores: vendedores,
                            Idtabla: idtabla2
                        };
        $.ajax({url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocliente/rutadelvendedor']); ?>',
               type: 'POST',
               data: paraguardar,
                          success: (data, status, xhr) => {
                   
                            if (status == 'success'){
                               var result = JSON.parse(data);
                                
                                 $("#hdClientes tbody tr").hide();
                                    $.each(result, function(key, value) {
                                
                                    $("#hdClientes tbody tr").each(function (index) {
                                   
                                       if($(this).find('td').first().text() == value.cardcode){
                                            $(this).show();
                                        
                                        }
                                    });
                                    });
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
    //DN3
    function RecargarClientesYmapa(){
     
        $('#hdPoligono tr').each(function () {
            Poligono.push({
                latitud: $(this).find('td').eq(0).html(), 
                longitud: $(this).find('td').eq(1).html()
            });            
        });
        
        $('#hdClientes tr').each(function () {
            Clientes.push({
                cardcode: $(this).find('td').eq(0).html(), 
                cardname: $(this).find('td').eq(1).html(),  
                latitud: $(this).find('td').eq(2).html(), 
                longitud: $(this).find('td').eq(3).html()
            });            
        });
         clientesPordiaSeleccionada('1');        
    }
    function clientesPordiaSeleccionada(diaCombo){
        if (diaCombo == '0') diaCombo = $('#ddlDias')[0].value;
        var diaTabla = '';
        var columna = parseInt(diaCombo) + 3;
        var codigo = '';
        var nombre = ''
        $('#ddlClientes').empty();
        for (var i = 0; i < allMarkers.length; i++){
            allMarkers[i].setMap(null);
        }
        allMarkers = [];
        $('#hdClientes tr').each(function () {
            diaTabla = $(this).find('td').eq(columna).html(); 
            if (diaTabla != undefined){
                if (diaTabla == 'tNO') $(this).hide();
                else{
                    codigo = $(this).find('td').eq(0).html();
                    nombre = "(" + codigo + ") " + $(this).find('td').eq(1).html();
                    $('#ddlClientes').append($('<option></option>').val(codigo).html(nombre));
                    $(this).show();
                }
            }
        });
       initMap(parseInt(diaCombo));
 
    }
   //DN3
        $( ".ui-dialog-titlebar-close" ).click(function() {
                 location.reload();
        });

    



</script>