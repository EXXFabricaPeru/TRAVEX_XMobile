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
    
</style>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Poligonocabecera-form']); ?>
    <input type="hidden" value="<?= $model->id ?>" id="hdId" />
    <div class="row">
        <div class="col-md-5">  
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->orderby('TerritoryID ASC')->all(), 'TerritoryID', 'Description'); ?>
            <?= $form->field($model, 'territoryid')->dropDownList($arr, ['prompt' => '','onchange'=>'camniaEstado(this.value)']); ?>
            <span class="text-danger text-clear" id="error-territoryid"></span>
        </div> 
	
        <div class="col-md-5">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-nombre"></span>
        </div>
		
		<div class="col-md-2" align="center">
		  <label class="form-check-label" for="flexCheckDefault">
			Ver poligonos
		  </label>
		  <br>
          <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" onclick="obtenerPoligonos(this)">
		 
        </div>
    </div>
    <!-- <div class="col-md-12"> 
        <input type="button" value="agregar" class="btn btn-success" onclick="calcular();" />
    </div> -->
    <div class="row">
        <div id="map"></div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<script>
    var map;
    var marker;
    var allMarkers = [];
    var coordenadas = [];
    var idmarker;
    var bermudaTriangle;
	var bermudaTriangle2;
	var Poligono=[];
	var lcoor=[];

    $('document').ready(function(){
        initMap();
    });

    function initMap() {
        idmarker = 1;
       /* var ubi = {lat: -16.406025525333444, lng: -71.53678071457969}; 
        map = new google.maps.Map(document.getElementById('map'), {zoom: 12, center: ubi});
        google.maps.event.addListener(map, 'click', function(event) { placeMarker(event.latLng); });        
		
		// PARA EDITAR EL POLIGONO
		if ($('#hdId').val() != '') {
            var mensaje = <?= $detalle ?>;            
            var lcoor=[];
            for(var i = 0; i < mensaje.length; i++){
                var marca = {id:mensaje[i]["id"], lat: Number(mensaje[i]["latitud"]), lon: Number(mensaje[i]["longitud"]) };
                var coor = { lat: Number(mensaje[i]["latitud"]), lng: Number(mensaje[i]["longitud"]) };
                if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor});                
                placeMarker(coor);
                lcoor.push(coor);
            }
            console.log(allMarkers);
            bermudaTriangle = new google.maps.Polygon({ paths: lcoor, strokeColor: '#FF0000', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#FF0000', fillOpacity: 0.35 });
            bermudaTriangle.setMap(map);
        }
		//FIN EDITAR EL POLIGONO*/
		//directionsService = new google.maps.DirectionsService;
       // directionsRenderer = new google.maps.DirectionsRenderer;
        ////marker personalizado
        
        /// fin de marker personalizado


        
        //var ubi = {lat: -16.5205361, lng: -68.1941184}; 
        var ubicar=ubicacionLugares().split('@');
        console.log('ubicar');
        console.log(ubicar);
        latitud=ubicar[0]*1;
        longitud=ubicar[1]*1;
        

        var ubi = {lat: latitud, lng: longitud}; 
        map = new google.maps.Map(document.getElementById('map'), {zoom: 12, center: ubi});
        google.maps.event.addListener(map, 'click', function(event) { placeMarker(event.latLng); });   
        var crear = $('#hdCrear').val();
        //dibujar poligono        
        var lcoor=[];
		var lcoor2=[];
        console.log("dddddd");
        console.log(Poligono);
        var coor="";
		var swEditar=true;
		
		var mensaje = <?= $detalle ?>;
		console.log(" guradado ");
		console.log(mensaje);
		
		//var colores=['#333FFF','#4FFF33','#0C0F39','#0B958F','#95390B','#0B950D','#F87AE1','#7AF8A4','#FAD95D','#5DC3FA','#633209','#FFCA33'];
		if(mensaje==""){
			
			for( var i=0; i < Poligono.length; i++){
				if(Number(Poligono[i]["latitud"])!=0){
					//var marca = {id:mensaje[i]["id"], lat: Number(mensaje[i]["latitud"]), lon: Number(mensaje[i]["longitud"]) };
					coor = { lat: Number(Poligono[i]["latitud"]), lng: Number(Poligono[i]["longitud"]) };
					//if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor});  

					lcoor.push(coor);
				}
				else{
					
					console.log("else");
					bermudaTriangle2 = new google.maps.Polygon({ paths: lcoor, strokeColor: '#1520B4', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#1520B4', fillOpacity: 0.35 });
					bermudaTriangle2.setMap(map);
					lcoor=[];
				}
			
			}        
			console.log("else");
			bermudaTriangle2 = new google.maps.Polygon({ paths: lcoor, strokeColor: '#1520B4', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#1520B4', fillOpacity: 0.35 });
			bermudaTriangle2.setMap(map);
		}
		else{//para  editar
			
			if (Poligono.length == 0 ) {
				
				//var mensaje = <?= $detalle ?>;
				
				var lcoor=[];
				for(var i = 0; i < mensaje.length; i++){
					var marca = {id:mensaje[i]["id"], lat: Number(mensaje[i]["latitud"]), lon: Number(mensaje[i]["longitud"]) };
					var coor = { lat: Number(mensaje[i]["latitud"]), lng: Number(mensaje[i]["longitud"]) };
					if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 12, center: coor});                
					placeMarker(coor);
					lcoor.push(coor);
				}
				console.log(allMarkers);
				bermudaTriangle = new google.maps.Polygon({ paths: lcoor, strokeColor: '#FF0000', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#FF0000', fillOpacity: 0.35 });
				bermudaTriangle.setMap(map);
			}
			else{
				var swVerifica=false;
				for( var i=0; i < Poligono.length; i++){
					if(Number(Poligono[i]["latitud"])!=0){
						//var marca = {id:mensaje[i]["id"], lat: Number(mensaje[i]["latitud"]), lon: Number(mensaje[i]["longitud"]) };
						coor = { lat: Number(Poligono[i]["latitud"]), lng: Number(Poligono[i]["longitud"]) };
						//if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor});  
						for(var j = 0; j < mensaje.length; j++){
							var coor2 = { lat: Number(mensaje[j]["latitud"]), lng: Number(mensaje[j]["longitud"]) };
							//if (j == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor2}); 
						
							if(mensaje[j]["latitud"]==Poligono[i]["latitud"]){
								console.log("Verdad poligono");
								console.log(Poligono[i]["latitud"]);	
								
								placeMarker(coor2);
								lcoor2.push(coor2);
								swVerifica=true;
								
							}
						}
						if(!swVerifica){
							lcoor.push(coor);
						}//else{
							swVerifica=false;
						//}
						
					}
					else{
						
						console.log("verifica 77");
						console.log(lcoor);
						bermudaTriangle2 = new google.maps.Polygon({ paths: lcoor, strokeColor: '#1520B4', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#1520B4', fillOpacity: 0.35 });
						bermudaTriangle2.setMap(map);
						lcoor=[];
						
						
					}
				}
				        
				console.log("else");
				bermudaTriangle2 = new google.maps.Polygon({ paths: lcoor, strokeColor: '#1520B4', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#1520B4', fillOpacity: 0.35 });
				bermudaTriangle2.setMap(map);
				///para editar//
				console.log("verifica false");
				console.log(lcoor2);
				bermudaTriangle = new google.maps.Polygon({ paths: lcoor2, strokeColor: '#FF0000', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#FF0000', fillOpacity: 0.35 });
				bermudaTriangle.setMap(map);
				
			}		
			
		}

			
		
		////////////////////////PARA EDITAR//////////////////////////////////
		/*if ($('#hdId').val() != '') {
			
            var mensaje = <?= $detalle ?>;
            
            var lcoor=[];
            for(var i = 0; i < mensaje.length; i++){
                var marca = {id:mensaje[i]["id"], lat: Number(mensaje[i]["latitud"]), lon: Number(mensaje[i]["longitud"]) };
                var coor = { lat: Number(mensaje[i]["latitud"]), lng: Number(mensaje[i]["longitud"]) };
                if (i == 0) map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: coor});                
                placeMarker(coor);
                lcoor.push(coor);
            }
            console.log(allMarkers);
            bermudaTriangle = new google.maps.Polygon({ paths: lcoor, strokeColor: '#FF0000', strokeOpacity: 0.8, strokeWeight: 2, fillColor: '#FF0000', fillOpacity: 0.35 });
            bermudaTriangle.setMap(map);
        }*/
    }
	
	function obtenerPoligonos(check){
        allMarkers = [];
		if($(check).is(':checked') ) {
			idTerritorio=$("#poligonocabecera-territoryid").val();
			if(idTerritorio!=""){
				$.ajax({
				url: $("#PATH").attr("name") + 'poligonocabecera/obtienepoligonos',
				type: 'POST',
				data: "idTerritorio="+idTerritorio,
				success: function (data, status, xhr) {
					console.log("poligonos 77");
					console.log(status);
					if(status=='success'){
						var result=JSON.parse(data);	
						var swPoligono=1;
						Poligono=[];				
						for(var i=0; i<result.length;i++){
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
						initMap();
					}
					else{
						alert("No hay poligonos en este territorio.");
					}
				},
				error: function (jqXhr, textStatus, errorMessage) {
					reject(errorMessage);
				}
			});
			}
			else{
				$(check).prop("checked",false);
				alert("Seleccionar un territorio.");
			}
		
			
		}
		else{
			Poligono=[];
			initMap();
		}
	}
	
    function camniaEstado(valor){

        $("#flexCheckDefault").prop("checked",false); 
        Poligono=[];
       
        initMap();
    }

    function ubicacionLugares(){
        var latitud=0;
        var longitud=0;

        var valor=$('select[id=poligonocabecera-territoryid]').val();
         switch (valor) {
            case '1':
                 latitud=-16.4961029;
                 longitud=-68.1091374;
            break;
            case '2':
                 latitud=-17.411712;
                 longitud=-66.1981674;
            break;
            case '3':
                 latitud=-17.7567898;
                 longitud=-63.2918351;
            break;
            case '4':
                 latitud=-17.976925;
                 longitud=-67.1248134;
            break;
            case '5':
                 latitud=-19.0254652;
                 longitud=-65.2793646;
            break;
            case '6':
                 latitud=-11.0345128;
                 longitud=-68.8123574;
            break;
            case '7':
                 latitud=-14.8499789;
                 longitud=-64.9190989;
            break;
            case '8':
                 latitud=-21.5218044;
                 longitud=-64.7604502;
            break;
            case '9':
                latitud=-19.5703487;
                longitud=-65.7821564;
            break;

            case '12':
                latitud=-16.5412365;
                longitud=-68.3298114;
            break;

           default:
                latitud=-16.4961029;
                longitud=-68.1091374;
            break;
        }
        return latitud+'@'+longitud;
    }
    function placeMarker(location) {
        console.log("Ingresa al marrker");
        var marker = new google.maps.Marker({
            position: location, 
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            id: idmarker++
        });
        marker.addListener('click', toggleBounce);
        google.maps.event.addListener(marker, 'dragend', function(evt){
            for (var i = 0; i < allMarkers.length; i++){
                if (marker.id == allMarkers[i]["id"]){
                    allMarkers[i]["lat"] = marker.getPosition().lat();
                    allMarkers[i]["lon"] = marker.getPosition().lng();
                }
            }
            bermudaTriangle.setMap(null);
        });
        var m = {id: marker.id, lat: marker.getPosition().lat(), lon: marker.getPosition().lng() };
        allMarkers.push(m);
    }

    function toggleBounce() {
        if (marker.getAnimation() !== null) {
            marker.setAnimation(null);
        } else {
            marker.setAnimation(google.maps.Animation.BOUNCE);
        }
    }

    function calcular(){
        var coordenadas = [];
        console.log("Coordenadas marker");
        console.log(allMarkers.length);
        for(var i = 0; i < allMarkers.length; i++){
            var coordenada = { lat: allMarkers[i]["lat"], lng: allMarkers[i]["lon"] };
            coordenadas.push(coordenada);
        }
        if (bermudaTriangle != undefined) bermudaTriangle.setMap(null);
        bermudaTriangle = new google.maps.Polygon({
                                                        paths: coordenadas,
                                                        strokeColor: '#FF0000',
                                                        strokeOpacity: 0.8,
                                                        strokeWeight: 2,
                                                        fillColor: '#FF0000',
                                                        fillOpacity: 0.35
                                                    });
        bermudaTriangle.setMap(map);
        google.maps.event.addListener(map, 'overlaycomplete', function(e) {bermudaTriangle = e.overlay; });
    }

    function guardar(){
        var mNombre = $('#poligonocabecera-nombre').val();
        var territoryid = $('#poligonocabecera-territoryid')[0].value;
        var paraguardar = { 
                            id: 0,
                            nombre: mNombre,
                            territorio: territoryid,
                            detalle: allMarkers 
                        };
        console.log(paraguardar);
        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocabecera/create']); ?>',
               type: 'POST',
               data: paraguardar,
               success: (data, status, xhr) => {
                   if (status == 'success'){
                        $('.window').dialog('close');
                        location.reload();  //adición para recargar en la lista los nuevos poligonos creados
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
        var mNombre = $('#poligonocabecera-nombre').val();
        var territoryid = $('#poligonocabecera-territoryid')[0].value;
        var mid = $('#hdId').val();
        var paraguardar = { 
                            id: mid,
                            nombre: mNombre,
                            territorio: territoryid,
                            detalle: allMarkers 
                        };

        $.ajax({
               url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['poligonocabecera/update']); ?>',
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

//Danae3
    $( ".ui-dialog-titlebar-close" ).click(function() {
     //console.log("Cerrrar");
     location.reload();
 });


</script>
