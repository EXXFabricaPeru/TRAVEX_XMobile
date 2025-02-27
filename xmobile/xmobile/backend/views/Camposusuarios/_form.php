<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'Camposusuarios-form']); ?>
    <div class="row">
        <div class="col-md-6">   
            <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Objetostablas:: find()->all(), 'id', 'Nombre'); ?>
            <?= $form->field($model, 'Objeto')->dropDownList($arr, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-Objeto"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'Nombre')->textInput(['maxlength' => true,'style' => 'text-transform:lowercase;']) ?>
            <span class="text-danger text-clear" id="error-Nombre"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'Label')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-Label"></span>
        </div> 
        <div class="col-md-6">   
            <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\CamposusuarioCamposmidd::find()->where('Status = 2')->all(), 'id', 'Nombre');?>    
            <?= $form->field($model, 'Campmidd')->dropDownList($arrx, ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-Campmidd"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'Tblsap')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-Tblsap"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'Campsap')->textInput(['maxlength' => true]) ?>
            <span class="text-danger text-clear" id="error-Campsap"></span>
        </div> 
        
        <div class="col-md-6">    
            <?=  $form->field($model, 'tipocampo')->dropDownList(['1' => 'Lista', '0' => 'Texto', "2" => "Numerico"], ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-tipocampo"></span>
        </div> 
        <div class="col-md-6">    
            <?= $form->field($model, 'longitud')->textInput() ?>
            <span class="text-danger text-clear" id="error-longitud"></span>
        </div> 

        <div class="col-md-6">    
            <?= $form->field($model, 'Status')->dropDownList(['1' => 'Activo', '0' => 'Inactivo'], ['prompt' => '']); ?>
            <span class="text-danger text-clear" id="error-Status"></span>
        </div> 

        <?= $form->field($model, 'Fechainsert')->hiddenInput(['value' => date("Y-m-d H:m:s")])->label(false); ?>
        <?= $form->field($model, 'FechaUpdate')->hiddenInput(['value' => date("Y-m-d H:m:s")])->label(false); ?>
           </div>
    <?php ActiveForm::end(); ?>

</div>
<script>

$("#camposusuarios-tipocampo").change(function () {
    console.log(this);
    $("#camposusuarios-tipocampo option:selected").each(function () {
        aux = $(this).val();

        if($(this).val() == '1'){
            $('#camposusuarios-longitud').attr('disabled', true);
            $('#camposusuarios-longitud').val('');
        }else{
            $('#camposusuarios-longitud').attr('disabled', false);
            $('#camposusuarios-longitud').val(0);
        }    
    });
});

$("#camposusuarios-objeto").change(function () {

    $("#camposusuarios-objeto option:selected").each(function () {
        aux = $(this).val();
        cargarcampos(aux);
    });
});

$("#camposusuarios-nombre").blur(function(){
    if($("#camposusuarios-nombre").val() == ''){
        $('#error-Nombre').html("*Ingrese el nombre del Campo a crear *");
    }else{
        validanombre($("#camposusuarios-nombre").val());
    }
  });

function cargarcampos(dato){
    $("#camposusuarios-campmidd").empty();
    $.ajax({
        url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['camposusuarios/listamidd']); ?>',
        type: 'POST',
        data: {id: dato},
        success: function (response) {
            console.log(response);
           // $("#camposusuarios-campmidd").html(response).fadeIn();
            for (let i = 0; i < response.length; i++) {
                console.log("s"+response[i]["id"]+"ss"+response[i]["nombre"]);

                $("#camposusuarios-campmidd").append($("<option>", {
                    value: response[i]["id"],
                    text: response[i]["nombre"]
                }));
                
            }
        },
        error: (jqXhr, textStatus, errorMessage) => {
            console.error("ERROR: " + errorMessage);
        }
    });
}

function validanombre(dato){
    $.ajax({
        url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['camposusuarios/validanombre']); ?>',
        type: 'POST',
        data: {nombre: dato},
        success: function (response) {
            if(response[0]['cantidad'] >= 1){
                $('#error-Nombre').html("* Ya existe un campo con el mismo nombre *");
                $("#camposusuarios-nombre").focus();
            }else{
                $('#error-Nombre').html("");
            }
        },
        error: (jqXhr, textStatus, errorMessage) => {
            console.error("ERROR: " + errorMessage);
        }
    });
}

</script>