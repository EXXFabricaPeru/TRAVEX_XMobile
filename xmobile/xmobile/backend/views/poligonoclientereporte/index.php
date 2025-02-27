<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;$this->title = 'Poligonoclientes';
?>

<style>
    #tabs{
        width: 100% !important;
        display: none;
    }

    #map {
        height: 550px;
        border: 1px solid #000;
    }

    #pickerFecha{
        margin-top: 15px;
        margin-left: 10px;
        position: absolute !important;
        z-index: 1000
    }
    #tblPoligonosR {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblPoligonosR td, #tblPoligonosR th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblPoligonosR tr:nth-child(even){background-color: #f2f2f2;}

    #tblPoligonosR tr:hover {background-color: #ddd;}

    #tblPoligonosR th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
    
    #tblPoligonosF {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblPoligonosF td, #tblPoligonosF th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblPoligonosF tr:nth-child(even){background-color: #f2f2f2;}

    #tblPoligonosF tr:hover {background-color: #ddd;}

    #tblPoligonosF th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
    
    #tblDias {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblDias td, #tblDias th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblDias tr:nth-child(even){background-color: #f2f2f2;}

    #tblDias tr:hover {background-color: #ddd;}

    #tblDias th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
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
        text-align: right;
      /* background-color: #4CAF50;
        color: white;*/
    }
    
    #tblClientes {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #tblClientes td, #tblClientes th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #tblClientes tr:nth-child(even){background-color: #f2f2f2;}

    #tblClientes tr:hover {background-color: #ddd;}

    #tblClientes th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="poligonocliente-index"  style="display:none;">

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Poligonocliente' </button>
    </p>
<?php Pjax::begin(['id' => 'Poligonocliente-list']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'cardcode',
            'cardname',
            'latitud',
            'longitud',
            //'territoryid',
            //'territoryname',
            //'poligonoid',
            //'poligononombre',
            //'posicion',
            //'dia',
            //'vendedor',

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
<div class="col-md-3">
    <div id="tabsrep">
        <ul>            
            <label class="control-label" style="">PARAMETROS DEL REPORTE</label>
        </ul>
        <div class="row">
            <div class="col-md-12">
                <label class="control-label" style="">Territorio</label>
                <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
                <?= Html::dropDownList('ddlTerritorio', null, $arr, ['style' => 'width:100%;', 'id' => 'ddlTerritorio', 'class' => 'form-control']) ?>
                <br />
            </div>
            <div class="col-md-12">
                <button type="button" class="btn btn-success" style="width:100%;" onclick="abrirSeleccionarPoligonos();">Seleccionar polígonos<i class='fas fa-map-marker-alt text-warning'></i></button>
                <div style="height: 130px; overflow-y: scroll;">
                    <table id="tblPoligonosF" width="95%"><tbody></tbody></table>
                </div>
                <br />
            </div>
            <div class="col-md-12">
                <button type="button" class="btn btn-success" style="width:100%;" onclick="Dibujar();">Dibujar <i class='fas fa-edit text-warning'></i></button>
            </div>
            <div class="col-md-12">
                <div style="height: 240px; overflow-y: scroll;">
                    <table width="100%" id="tblVendedores"><thead></thead><tbody></tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-9">
    <div id="map"></div>
    <div id='tabsdia'>
        <table width="100%" id="tblDias">
            <tbody>
                <tr>
                    <td style="text-align:center;"><input type="checkbox" id="chk_LU" checked onclick="CambiarDia(1, this);">&nbsp;LUNES</input></td>
                    <td style="text-align:center;"><input type="checkbox" id="chk_MA" checked onclick="CambiarDia(2, this);">&nbsp;MARTES</input></td>
                    <td style="text-align:center;"><input type="checkbox" id="chk_MI" checked onclick="CambiarDia(3, this);">&nbsp;MIERCOLES</input></td>
                    <td style="text-align:center;"><input type="checkbox" id="chk_JU" checked onclick="CambiarDia(4, this);">&nbsp;JUEVES</input></td>
                    <td style="text-align:center;"><input type="checkbox" id="chk_VI" checked onclick="CambiarDia(5, this);">&nbsp;VIERNES</input></td>
                    <td style="text-align:center;"><input type="checkbox" id="chk_SA" checked onclick="CambiarDia(6, this);">&nbsp;SABADO</input></td>
                    <td style="text-align:center;"><input type="checkbox" id="chk_DO" checked onclick="CambiarDia(7, this);">&nbsp;DOMINGO</input></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div id="divPoligonosR"  title="Seleccionar poligono" style='display: none;'>    
    <?= Html::input('text', 'txtBuscarPoligono', '', $options=['id' => 'txtBuscarPoligono', 'class'=>'form-control', 'style'=>'', 'onkeyup' => 'RapidaPoligono()']) ?>
    <br />
    <div style="height: 220px; overflow-y: scroll;">
        <table id="tblPoligonosR" width="100%">
            <tbody></tbody>
        </table>
    </div>
</div>
<div id="modalClientes" style="display:none;" title="Clientes">
    <table width="100%" id="tblClientes">
        <thead>
            <th>Código</th>
            <th>Nombre</th>
            <th>Direccion</th>
            <th>Calle</th>
            <th>Posición</th>
            <th>Latitud</th>
            <th>Longitud</th>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
    <!--Danae2-->
<div id="modalDocumentos" style="display:none;" title="">
    <div class="row">
        <div class="col-md-6">
            <label class="control-label">Fecha incial</label>
            <input type="date" id="txtVisitaFiniD" style="width:100%;" class="form-control hasDatepicker" />
        </div>
        <div class="col-md-6">
            <label class="control-label">Fecha final</label>
            <input type="date" id="txtVisitaFfinD" style="width:100%;" class="form-control hasDatepicker" />
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-md-12">
            <?php $arr = ['DOF' => 'Oferta', 'DOP' => 'Pedido', 'DFA' => 'Factura', 'DOE' => 'Entrega', 'PAGO' => 'Pago']; ?>
            <?= Html::dropDownList('ddlGeoDocumento', null, $arr, ['prompt' => 'Todos', 'style' => 'width:100%;', 'class' => 'form-control', 'id' => 'ddlGeoDocumento']) ?>
        </div>
    </div>

</div>
<!--Danae2-->
<div id="modalVisitas" style="display:none;" title="Visitas">
    <div class="row">
        <div class="col-md-6">
            <label class="control-label">Fecha incial</label>
            <input type="date" id="txtVisitaFini" style="width:100%;" class="form-control hasDatepicker" />
        </div>
        <div class="col-md-6">
            <label class="control-label">Fecha final</label>
            <input type="date" id="txtVisitaFfin" style="width:100%;" class="form-control hasDatepicker" />
        </div>
    </div>
</div>

<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBAan7nzQ2E8-ax3E8shSumJ7vmkK00hT0"></script> -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50"></script>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Poligonoclientereporte.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/js/GeoRepScript.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<script>
    var map;
    var Poligonos = [];
    var Rutas = [];
    var allMarkers = [];
    var visitasMarkers = [];
    var documentosMarkers = [];
    const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    let labelIndex = 0;
    function iniciar(){
        initMap();
    }

    function initMap(){
        var ubi = {lat: -16.496777, lng: -68.132031};
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonoclientereporte/coordenadasiniciales']); ?>',
            type: 'POST',
            success: (data, status, xhr) => {
                if (status == 'success'){
                    var resultado = JSON.parse(data);
                    ubi.lat = Number(resultado['lat']);
                    ubi.lng = Number(resultado['long']);
                }
                else{
                    console.error("ERROR: ");    
                }
            },
            error: (jqXhr, textStatus, errorMessage) => {
                console.error("ERROR: " + errorMessage);
            }
	    });
        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});
    }

    function abrirSeleccionarPoligonos(){
        var id = $('#ddlTerritorio')[0].value;
        traerPoligonos(id);
    }

    function RapidaPoligono(){
        var valor = $('#txtBuscarPoligono').val().toUpperCase();
        var nombre = '';
        var resultado = false;
        $('#tblPoligonosR tr').each(function () { 
            nombre = $(this).find('td').eq(1).html().toUpperCase();
            resultado = nombre.includes(valor);
            if (resultado) $(this).show();
            else $(this).hide();
        });
    }

    function traerPoligonos(territorio){
        var datos = { Territorio: territorio};
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonoclientereporte/recuperarpoligonos']); ?>',
            type: 'POST',
            data: datos,
            success: (data, status, xhr) => {
                if (status == 'success'){
                    var resultado = JSON.parse(data);
                    llenarTablasBusqueda(resultado, 'POLIGONOS');
                    $( "#divPoligonosR" ).dialog({
                        height: 400,
                        width: 450,
                        modal: true,
                        buttons: {
                            "Seleccionar Todos": SeleccionarTodoPoligono,
                            "Aceptar": AceptarPoligono,
                            "Cerrar": function() {  $( "#divPoligonosR" ).dialog( "close" ); }
                        }
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

    function SeleccionarTodoPoligono(){

    }

    function AceptarPoligono(){ 
        $('#tblPoligonosF tbody').empty();
        $('#tblPoligonosR tbody tr').each(function () {
            SeleccionarPoligono(this);
        });
        $( "#divPoligonosR" ).dialog( "close" );
    }

    function llenarTablasBusqueda(tabla, tipo){
        var fila = '';
        switch(tipo){
            case "POLIGONOS":
                $('#tblPoligonosR tbody > tr').remove();
                var tbl = $('#tblPoligonosR');
                tbl.find('tbody').append(fila);
                for(var i = 0; i < tabla.length; i++){
                    fila =  "<tr><td style='display:none;'>" +  tabla[i]["id"] + "</td><td>" + tabla[i]["nombre"] + "</td>" +
                            "<td style='text-align:center; width:20px;'><input type='checkbox' id='chkp_" + tabla[i]["id"] +  "' /></td></tr>";
                    tbl.find('tbody').append(fila);
                }
            break;
        }
    }

    function SeleccionarPoligono(fila){
        var id = $(fila).find('td').eq(0).html(); 
        var nombre = $(fila).find('td').eq(1).html();
        //var chk = $('#chkp_' + id);// $(fila).find('td').eq(2);
        var chk = $(fila).find('td').eq(2).find('input')[0].checked;        
        if (chk){
            llenarTablasFinal({rid: id, rnom: nombre}, 'POLIGONOS');
        }
    }

    function llenarTablasFinal(registro, tipo){
        var fila = '';
        switch(tipo){
            case "POLIGONOS":                
                var tbl = $('#tblPoligonosF');
                fila =  "<tr style='cursor: pointer;' onclick='ArmarVendedores(this);'><td style='display:none;'>" +  registro.rid + "</td><td>" + registro.rnom + 
                        "</td><td style='text-align:center; width:20px;'><button onclick='eliminarPoligono(" + registro.rid + ")'>" + 
                        "<i class='fas fa-trash-alt text-warning'></i></button></td></tr>";
                tbl.find('tbody').append(fila);
            break;
        }
    }

    function eliminarPoligono(poligono){
        var idtabla = '';
        var tbl = $('#tblPoligonosF');
        $('#tblPoligonosF tbody tr').each(function () {
            idtabla = $(this).find('td').eq(0).html();
            if (poligono == idtabla) $(this).children('td').remove();
        });

        for(var i = 0; i < Poligonos.length; i++){
            if (poligono == Poligonos[i].id) Poligonos[i].poligono.setMap(null);
        }

        for(var i = 0; i < Rutas.length; i++){
            if (poligono == Rutas[i]["id"]){
                var dias = Rutas[i]["dias"];
                for(var j = 0; j < dias.length; j++){
                    var vendedores = dias[j]["vendedores"];
                    for ( var k = 0; k < vendedores.length; k++){
                        vendedores[k]["ruta"].setMap(null);
                    }
                }
            }
        }
    }

    function Dibujar(){        
        var idtabla = '';
        var idpoligonos = [];
        RutasReal = [];
        $('#tblPoligonosF tbody tr').each(function () {
            idtabla = $(this).find('td').eq(0).html();
            //console.log(idtabla)
            idpoligonos.push(idtabla);
           

        });
        var datos = { Poligonos: idpoligonos};
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonoclientereporte/recuperarpoligonosdetalle']); ?>',
            type: 'POST',
            data: datos,
            success: (data, status, xhr) => {
                if (status == 'success'){
                    var resultado = JSON.parse(data);
                    console.log(resultado);
                    DibujarPoligonos(resultado);
                    DibujarRutas(resultado);
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

    function DibujarPoligonos(registros){
        Poligonos = [];
        for (var i = 0; i < registros.length; i++){
            var lcoor = [];
            var color = SetColorPoligono(parseInt(i));
            var pol = registros[i]["detalle"];
            for(var j = 0; j < pol.length; j++){
                var coor = { lat: Number(pol[j]["latitud"]), lng: Number(pol[j]["longitud"]) };
                if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor});
                lcoor.push(coor);
            };
            var bermudaTriangle = new google.maps.Polygon({ paths: lcoor, strokeColor: color, strokeOpacity: 0.8, strokeWeight: 2, fillColor: color, fillOpacity: 0.35 });            
            var poligono = { 
                id: registros[i]["id"],
                detalle: registros[i]["detalle"],
                poligono: bermudaTriangle
            };
            Poligonos.push(poligono);
            bermudaTriangle.setMap(map);
        }
    }

    function DibujarRutas(registros){
        Rutas = [];
        for (var i = 0; i < registros.length; i++){
            var lcoor = [];
            //var color = SetColorPoligono(parseInt(i));
            var ruta = {
                    id: registros[i]["id"],
                    nombre: registros[i]["nombre"],
                    dias: []
                };
            var dias = registros[i]["dias"];
            for (var j = 0; j < dias.length; j++){
                var dia = {
                        id: dias[j]["dia"],
                        nombre: dias[j]["nombre"],
                        vendedores: []
                    };
                var vendedores = dias[j]["vendedores"];
                for (var k = 0; k < vendedores.length; k++){                                        
                    var vendedor = {
                        id: vendedores[k]["vendedor"],
                        nombre: vendedores[k]["nombre"],
                        clientes: [],
                        ruta: armarRuta(vendedores[k]["clientes"], i),
                        puntos: allMarkers,
                    };
                    var clientes = vendedores[k]["clientes"];
                    for (var l = 0; l < clientes.length; l++ ){
                        var cliente = {
                            cardcode: clientes[l]["cardcode"],
                            cardname: clientes[l]["cardname"],
                            nombreDireccion: clientes[l]["nombreDireccion"],
                            calle: clientes[l]["calle"],
                            posicion: clientes[l]["posicion"],
                            latitud: clientes[l]["latitud"],
                            longitud: clientes[l]["longitud"]
                        };
                        vendedor.clientes.push(cliente);
                    }
                    dia.vendedores.push(vendedor);
                }
                ruta.dias.push(dia);
            }
            Rutas.push(ruta);
        }
    }

    function armarRuta(clientes, color){
        allMarkers = [];
        labelIndex = 0;
        var c = SetColorPoligono(color);
        var inicio = { lat: Number(clientes[0]["latitud"]), lng: Number(clientes[0]["longitud"]) };
        var fin = { lat: Number(clientes[clientes.length - 1]["latitud"]), lng: Number(clientes[clientes.length - 1]["longitud"]) };
        var puntos = [];
        var puntostodos = [];
        puntostodos.push(inicio);
        var informacion = 'Codigo: ' + clientes[0]["cardcode"] + "\n";
        informacion = informacion + 'Nombre: ' + clientes[0]["cardname"] + '\n';
        informacion = informacion + 'Direccion: ' + clientes[0]["nombreDireccion"] + "\n";
        informacion = informacion + 'Calle: ' + clientes[0]["calle"] + "\n";
        informacion = informacion + 'Posicion: Inicio del recorrido';
        placeMarker(inicio, c, informacion);
        for (var i = 1; i < clientes.length - 1; i++){
            var punto = {
                location: { lat: Number(clientes[i]["latitud"]), lng: Number(clientes[i]["longitud"]) },
                stopover: true
            };
            puntos.push(punto);
            puntostodos.push(punto);
            informacion = 'Codigo: ' + clientes[i]["cardcode"] + "\n";
            informacion = informacion + 'Nombre: ' + clientes[i]["cardname"] + '\n';
            informacion = informacion + 'Direccion: ' + clientes[i]["nombreDireccion"] + "\n";
            informacion = informacion + 'Calle: ' + clientes[i]["calle"] + "\n";
            informacion = informacion + 'Posicion: ' + clientes[i]["posicion"];
            placeMarker(punto.location, c, informacion);
        }
        puntostodos.push(fin);
        informacion = 'Codigo: ' + clientes[clientes.length - 1]["cardcode"] + "\n";
        informacion = informacion + 'Nombre: ' + clientes[clientes.length - 1]["cardname"] + '\n';
        informacion = informacion + 'Direccion: ' + clientes[i]["nombreDireccion"] + "\n";
        informacion = informacion + 'Calle: ' + clientes[i]["calle"] + "\n";
        informacion = informacion + 'Posicion: Fin del recorrido';
        placeMarker(fin, c, informacion);
        var directionsService = new google.maps.DirectionsService;
        var directionsRenderer = new google.maps.DirectionsRenderer;
        directionsRenderer.setOptions({ 
            polylineOptions: { 
                strokeColor: c 
            },            
            suppressMarkers: true 
        });
        directionsRenderer.setMap(map);
        directionsService.route({
          origin: inicio,
          destination: fin,
          waypoints: puntos,
          optimizeWaypoints: true,
          travelMode: 'WALKING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsRenderer.setDirections(response);
            var route = response.routes[0];
           
          } else {
            alert('Directions request failed due to ' + status);
          }
        });
        return directionsRenderer;
    }

    function placeMarker(location, color, informacion = null) {        
        var tooltip = '';
        if (informacion != null) tooltip = informacion;

        var marker = new google.maps.Marker({
            position: location,
            label: labels[labelIndex++ % labels.length],
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
            //icon: pinSymbol(color),
            title: tooltip,
            id: 0
        });

        allMarkers.push(marker);

        marker.addListener('click', function() {
            //geoAbrirClienteEspecifico(informacion);
            //console.log(informacion);
            //$("#ddlClientes").val(cardcode);
        });
    }

    function pinSymbol(color) {
    return {
        path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#000',
        strokeWeight: 2,
        scale: 1
    };
}

    function SetColorPoligono(tipo)
    {
        var resultado = '';
        switch(tipo){
            case 0: resultado = 'red'; break;
            case 1: resultado = 'blue'; break;
            case 2: resultado = 'yellow'; break;
            case 3: resultado = 'green'; break;
            case 4: resultado = 'brown'; break;
            case 5: resultado = 'purple'; break;
            case 6: resultado = 'black'; break;
            case 7: resultado = 'white'; break;
            case 8: resultado = 'aqua'; break;
            default: resultado = 'azure'; break;
        }
        return resultado;
    }

    function CambiarDia(dia, input){
        var bandera = $(input)[0].checked;
        reDibujarDia(dia, bandera);
    }

    function reDibujarDia(dia, bandera) {
        if (!bandera){
            for(var i = 0; i < Rutas.length; i++)
            {
                var dias = Rutas[i]["dias"];
                for (var j = 0; j < dias.length; j++){
                    if (parseInt(dias[j]["id"]) == dia){
                        var vendedores = dias[j]["vendedores"];
                        for (var k = 0; k < vendedores.length; k++ ) {
                            vendedores[k]["ruta"].setMap(null);
                            var puntos = vendedores[k]["puntos"]; 
                            for(var l = 0; l < puntos.length; l++){
                                puntos[l].setMap(null);
                            }
                        }
                    }
                }
            }
        }
        else{
            for(var i = 0; i < Rutas.length; i++)
            {
                var dias = Rutas[i]["dias"];
                for (var j = 0; j < dias.length; j++){
                    if (parseInt(dias[j]["id"]) == dia){
                        var vendedores = dias[j]["vendedores"];
                        for (var k = 0; k < vendedores.length; k++ ){
                            vendedores[k]["ruta"] = null;
                            vendedores[k]["ruta"] = armarRuta(vendedores[k]["clientes"], i);
                        }
                    }
                }
            }
        }
    }

    function ArmarTablaVendedores(id, nombre){
        var cabecera = "<th style='text-align: center;' >" + nombre + "</th>";
        var tbl = $('#tblVendedores');
        tbl.find('thead').empty();
        tbl.find('tbody').empty();
        tbl.find('thead').append(cabecera);
        for(var i = 0; i < Rutas.length; i++){
            if (Rutas[i]["id"] == id){
                var dias = Rutas[i]["dias"];
                for(var j = 0; j < dias.length; j++){
                    var fila = "<tr onclick='mostrarClientes(this)' style='cursor: pointer;'><td style='display: none;'>" + Rutas[i]["id"] + 
                               "</td><td style='display: none;'>" + dias[j]["id"] + 
                               "</td><td style='display: none;'>0</td><td style='text-align: center;'><b>" + dias[j]["nombre"] + "</b></td></tr>";
                    


                    tbl.find('tbody').append(fila);
                    var vendedores = dias[j]["vendedores"];

                    
                    for (var k = 0; k < vendedores.length; k++){
                        //console.log("vendedores"+vendedores[k]["id"]);
                        //Danae2
                        fila = "<tr style='cursor: pointer;'><td style='display: none;'>" + dias[j]["id"] + 
                               "</td>   <td style='display: none;'>" + vendedores[k]["id"] + "</td><td>" + vendedores[k]["nombre"] + 
                             "<td style='text-align:center; width:20px;'><button onclick='AbrirModalDocumentos(\"<tr>\"+$(this).parent().parent().html()+\"</tr>\")'>" + 
                            "<i class='fas fa-file-alt text-warning'></i></button></td>" +
                            

                            
                            "<td style='text-align:center; width:20px;'><button onclick='AbrirModalVisitas(\"<tr>\"+$(this).parent().parent().html()+\"</tr>\")'>" + 
                            "<i class='fas fa-shipping-fast text-warning'></i></button></td>" +
                           

                               "</td></tr>";
                        tbl.find('tbody').append(fila);
                        //console.log("FILA"+fila);
                    }
                }
            }
        }
    }

    function ArmarVendedores(fila){
        var id = $(fila).find('td').eq(0).html();
        var nombre = $(fila).find('td').eq(1).html();
        ArmarTablaVendedores(id, nombre);
    }

    function mostrarClientes(fila){
        $('#tblClientes tbody').empty();
        var pol = $(fila).find('td').eq(0).html();
        var dia = $(fila).find('td').eq(1).html();
        var tbl = $('#tblClientes');
        for (var i = 0; i < Rutas.length; i++){
            if(pol == Rutas[i]["id"]){
                var dias = Rutas[i]["dias"];
                for(var j = 0; j < dias.length; j++){
                    if (dia == dias[j]["id"]){
                        var vendedores = dias[j]["vendedores"];
                        var clientes = vendedores[0]["clientes"];
                        for(var k = 0; k < clientes.length; k++){
                            var fila = "<tr><td>" + clientes[k]["cardcode"] + "</td>" + 
                                           "<td>" + clientes[k]["cardname"] + "</td>" + 
                                           "<td>" + clientes[k]["nombreDireccion"] + "</td>" + 
                                           "<td>" + clientes[k]["calle"] + "</td>" + 
                                           "<td>" + clientes[k]["posicion"] + "</td>" + 
                                           "<td>" + clientes[k]["latitud"] + "</td>" + 
                                           "<td>" + clientes[k]["longitud"] + "</td></tr>";
                            tbl.find('tbody').append(fila);
                        }
                    }
                }
            }
        }
        $( "#modalClientes" ).dialog({
            height:500,
            width: 850,
            modal: true,
            buttons: {
                "Cerrar": function() {  $( "#modalClientes" ).dialog( "close" ); }
            }
        });
    }
    //Danae2
    function AbrirModalDocumentos(fila){

        console.log("FILA MODAL DOCUMENTO"+fila);

        var iddia = $(fila).find('td').eq(0).html();
        var idven = $(fila).find('td').eq(1).html();
        var tipo = $('#ddlGeoDocumento')[0].value;


        console.log("iddia"+iddia); 
        console.log("idven"+idven);
        console.log("tipo"+tipo);

        $( "#modalDocumentos" ).dialog({
            height:300,
            width: 400,
            modal: true,
            buttons: {
                "Recuperar": function(){ TraerDocumentos(iddia, idven, tipo); /*TraerVisitas(iddia, idven);*/ },
                "Cerrar": function() {  $( "#modalDocumentos" ).dialog( "close" ); }
            }
        });     
    }


    function AbrirModalVisitas(fila){
        var iddia = $(fila).find('td').eq(0).html();
        var idven = $(fila).find('td').eq(1).html();
        console.log("IDDIA"+iddia);
        console.log("IDVENTAS"+idven);
        $( "#modalVisitas" ).dialog({
            height:200,
            width: 400,
            modal: true,
            buttons: {
                "Recuperar": function(){ TraerVisitas(iddia, idven); },
                "Cerrar": function() {  $( "#modalVisitas" ).dialog( "close" ); }
            }
        });
    }

    function TraerVisitas(dia, vendedor){
        var fini = $('#txtVisitaFini').val();
        var ffin = $('#txtVisitaFfin').val();
        var datos = { 
            Vendedor: vendedor,
            FechaInicial: fini,
            FechaFinal: ffin
        };
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonoclientereporte/visitasporvendedor']); ?>',
            type: 'POST',
            data: datos,
            success: (data, status, xhr) => {

                if (status == 'success'){
                    var resultado = JSON.parse(data);
                    //resultado = data;
                    if(resultado == "N")
                      {
                        
                    window.alert("El vendedor no esta asignado a ningun usuario ");
                      }
                      if (resultado.length != 0){
                    for(var i = 0;i < resultado.length; i++){
                        var coordenadas = {lat: Number(resultado[i]["lat"]), lng: Number(resultado[i]["lng"]) };
                        var informacion = resultado[i]["CardCode"] + '\n' + resultado[i]["CardName"];
                        placeMarkerVisitas(coordenadas, '', informacion );
                    }
                }
                    $( "#modalVisitas" ).dialog( "close" );
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

    function TraerDocumentos(dia, vendedor, tipo){
        console.log("TRAER DOCUEMENTO DIA"+dia); 
        console.log("TRAER DOCUEMENTO VENDEDOR"+vendedor);
        console.log("TRAER DOCUEMENTO TIPO"+tipo);

        var fini = $('#txtVisitaFiniD').val();
        var ffin = $('#txtVisitaFfinD').val();
        var tdoc = $('#ddlGeoDocumento')[0].value;
        var datos = { 
            Vendedor: vendedor,
            FechaInicial: fini,
            FechaFinal: ffin,
            tipoDoc: tdoc
        };

        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonoclientereporte/documentosporvendedor']); ?>',
            type: 'POST',
            data: datos,
            success: (data, status, xhr) => {
            
                if (status == 'success'){
                    console.log(data);
                    console.log("DATA DENTRO nro"+ data.length);
                      var resultado = JSON.parse(data);
                   
                      if(resultado == "N")
                      {
                        
                    window.alert("El vendedor no esta asignado a ningun usuario ");
                      }
                  if (resultado.length != 0){
                    for(var i = 0; i < resultado.length; i++){
                       //console.log("RESULTADO"+resultado.length);
                        if (resultado[i]["U_LATITUD"] != null && resultado[i]["U_LATITUD"] != '' && resultado[i]["U_LATITUD"] != '0'){
                        var coordenadas = {lat: Number(resultado[i]["U_LATITUD"]), lng: Number(resultado[i]["U_LONGITUD"]) };
                        var informacion = resultado[i]["CardCode"] + '\n' + 
                                          resultado[i]["CardName"] + '\n' + 
                                          resultado[i]["DocType"] + '\n' + 
                                          resultado[i]["DocDate"];
                                          console.log("INFOPRMACION"+informacion);
                        placeMarkerDocumentos(coordenadas, '', informacion );
                    }
                    }
                }


                    $( "#modalDocumentos" ).dialog( "close" );
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

    function placeMarkerVisitas(location, color, informacion = null) {        
        var tooltip = '';
        if (informacion != null) tooltip = informacion;

        var marker = new google.maps.Marker({
            position: location, 
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
            icon:  { url: "http://maps.google.com/mapfiles/ms/icons/cabs.png" },
            title: tooltip,
            id: 0
        });

        visitasMarkers.push(marker);

        marker.addListener('click', function() {
            //geoAbrirClienteEspecifico(informacion);
            //console.log(informacion);
            //$("#ddlClientes").val(cardcode);
        });
    }

    function placeMarkerDocumentos(location, color, informacion = null) {        
        var tooltip = '';
     //   console.log("LOCATION"+location);

        if (informacion != null) tooltip = informacion;

        var marker = new google.maps.Marker({
            position: location, 
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
            icon:  { url: "http://maps.google.com/mapfiles/ms/icons/shopping.png" },
            title: tooltip,
            id: 0
        });

        documentosMarkers.push(marker);

        marker.addListener('click', function() {
            //geoAbrirClienteEspecifico(informacion);
            //console.log(informacion);
            //$("#ddlClientes").val(cardcode);
        });
    }








    
</script>