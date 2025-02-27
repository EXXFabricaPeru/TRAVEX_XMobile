<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Clientesterritorio-form']); ?>
    <div class="row">
        <div class="col-md-6">
       		<div class="row">      
                <div class="col-md-8">
                    <label>Territorio</label> 
                    <input type="text" size="30"  name="txtterritorio" id="txtterritorio"  list="dataTerritorio"  class="form-control mayusculas" data-validation="required"  placeholder="Seleccione Territorio">
                </div>
                <div class="col-md-4">
                    <br> 
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
            <hr>
            <div class="row">
            	<div class="col-md-12">
			       	<div class="content">
				        <div id="map"></div>
				    </div>
		        </div>
            </div>
        </div>
        <div class="col-md-6">
       		<div class="row">      
                <div class="col-md-8">
                    <label>Territorio</label> 
                    <input type="text" size="30"  name="txtterritorio" id="txtterritorio"  list="dataTerritorio"  class="form-control mayusculas" data-validation="required"  placeholder="Seleccione Territorio">
                </div>
                <div class="col-md-4">
                    <br> 
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
            <hr>
            <div class="row">
        		<div class="col-md-12" id="resultados">
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
        	</div>
    	</div>	
    </div>


    <?php ActiveForm::end(); ?>

</div>


<script>
	var map;
    var markerGeo;
    $('document').ready(function(){
      	initMap();  
    });
	
	function initMap() {
		console.log("Inicia el mapa")
        var ubi = {lat: -16.496777, lng: -68.132031};
        directionsService = new google.maps.DirectionsService;
        directionsRenderer = new google.maps.DirectionsRenderer;

        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});
        directionsRenderer.setMap(map);
        //google.maps.event.addListener(map, 'click', function(event) { placeMarker(event.latLng); });        
    }
</script>