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
        <div class="col-md-6">  
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
            <?= $form->field($model, 'territoryid')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-territoryid"></span>
        </div>        
        <div class="col-md-6">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-nombre"></span>
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

    $('document').ready(function(){
        initMap();
    });

    function initMap() {
        idmarker = 1;
        var ubi = {lat: -16.496777, lng: -68.132031};
        map = new google.maps.Map(document.getElementById('map'), {zoom: 15, center: ubi});
        google.maps.event.addListener(map, 'click', function(event) { placeMarker(event.latLng); });        
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
    }

    function placeMarker(location) {
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
</script>
