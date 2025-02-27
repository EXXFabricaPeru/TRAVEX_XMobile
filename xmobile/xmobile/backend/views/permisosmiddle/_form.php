<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>
<style>
    h4 {
        text-transform: uppercase;
        font-size: 13px;
        background-color: #f0ad4e;
        padding: 4px;
        color: #fff;
    }

    legend {
        background-color: #999999;
        color: #fff;
        padding: 3px 6px;
        font-size: 10px;
        text-transform: uppercase;
    }
</style>
<div class="container-fluid">

    <?php $form = ActiveForm::begin(['id' => 'Permisosmiddle-form']); ?>
    
    <div class="row">
        <div class="col-md-6"> 
            <h4>Acceso Usuario MiddleWare</h4>
            <div class="row">
               <!-- <div class="col-md-12">  
                 
                    <span class="text-danger text-clear" id="error-idUsuario"></span>
                </div>--> 
                <div class="col-md-6">    
                    <?= $form->field($model, 'userName')->textInput(['maxlength' => true,'id'=>'userName','readonly'=>true]) ?>
                    <span class="text-danger text-clear" id="error-userName"></span>
                </div> 
                <div class="col-md-6">  
                    <?= $form->field($model, 'nivel')->dropDownList(['1' => 'OPERADOR','2' => 'ADMINISTRADOR'],['id'=>'nivel']) ?>
                    <span class="text-danger text-clear" id="error-nivel"></span>
                </div> 
                <!-- row --> 
                <div class="col-md-6">
                    <?php
                     $arrterritorio = ArrayHelper::map(backend\models\Territorios::find()->where('Parent=-2')->all(), 'TerritoryID', 'Description');
                     array_unshift($arrterritorio, "Todos");
                    ?>
                    <?= $form->field($model, 'idDepartamento')->dropDownList($arrterritorio, ['id'=>'idDepartamento']) ?>
                    <span class="text-danger text-clear" id="error-idDepartamento"></span>
                </div> 
                
                <div class="col-md-6">    
                    <?= $form->field($model, 'cargoEmpresa')->textInput(['maxlength' => true]) ?>
                    <span class="text-danger text-clear" id="error-cargoEmpresa"></span>
                </div> 
                <!-- row --> 
                
                <div class="col-md-12"> 
                    
                    <h4>
                        <div class="row"> 
                            <div class="col-md-8">  <label class="" for=""></label>
                                Acceso Sincronizar
                                
                            </div>
                            <div class="col-md-4"> 
                                    <input type="checkbox" name="chkTodosSincro" id="chkTodosSincro" class="form-control-" onclick="marcarTodosSincro(this);" />
                                    <label class="form-check" for="chkTodosSincro">Marcar todos</label> 
                            </div>
                        </div>                      
                    </h4>
                    <?php $dataMenuSincronizar = ArrayHelper::map(backend\models\menusincronizar::find()->orderby('nombre asc')->all(), 'id', 'nombre'); ?>
                    <div style="height:350px;overflow: auto;">
                        <ul class="list-group">
                            <?php foreach ($dataMenuSincronizar as $keySincro => $valSincro) { ?>
                                <li class="list-group-item">
                                    <input type="checkbox" value="<?= $valSincro ?>" onclick="serializaCheckSincro()" id="menusincro-<?= $keySincro ?>" class="selectCheboxMenuSincro">
                                    
                                    <label class="form-check-label" for="menusincro-<?= $keySincro ?>"><b><?= ($valSincro); ?></b></label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div> 

            </div>
        </div>
        <div class="col-md-6"> 
            <h4>
                <div class="row"> 
                    <div class="col-md-8">  <label class="" for=""></label>
                        Acceso Menu MiddleWare
                    </div>
                    <div class="col-md-4"> 
                            <input type="checkbox" name="chkTodosMenu" id="chkTodosMenu" class="form-control-" onclick="marcarTodosMenu(this);" />
                            <label class="form-check" for="chkTodosMenu">Marcar todos</label> 
                    </div>
                </div> 
            </h4>
            <?php $dataMenu = ArrayHelper::map(backend\models\menumiddle::find()->all(), 'id', 'nombreMenu');
             $valor = backend\models\Configuracion::find()->where("parametro='cambio_paralelo'")->one();
            
            ?>

            <ul class="list-group">
                <?php foreach ($dataMenu as $key => $val) { 
                    $activo='';
                    $mensaje='';
                    if($key=='7'){ // ID 7 DEL TIPO DE CAMBIO PARALELO    
                        if($valor['valor']==0){
                            $activo='disabled';// VERIFICA SI EN LAS CONFIGURACION EL TIPO DE CABIO ESTA ACTIVO
                            $mensaje=' - <Font color="Blue">Tipo de cambio paralelo inactivo, según tabla de configuración</Font>';
                        }
                    }
                ?>
                    <li class="list-group-item">
                        <input type="checkbox" value="<?= $val ?>" onclick="serializaCheck()" id="menu-<?= $key ?>" class="selectCheboxMenu" <?=$activo?> >
                        
                        <label class="form-check-label" for="menu-<?= $key ?>"><b><?= ($val); ?></b><?=$mensaje?></label>
                    </li>
                <?php } ?>
            </ul>

        </div>      
    </div>

    <?= $form->field($model, 'fechaSistema')->hiddenInput(['value' => date('Y-m-d H:m:s')])->label(false); ?>
  
    <?= $form->field($model, 'idUsuario')->hiddenInput(['id'=>'idUsuario'])->label(false); ?> 
    <?= $form->field($model, 'descripcionNivel')->hiddenInput(['id'=>'descripcionNivel'])->label(false); ?> 
    <?= $form->field($model, 'permisomenu')->hiddenInput(['id'=>'permisomenu'])->label(false); ?>
    <?= $form->field($model, 'departamento')->hiddenInput(['id'=>'departamento'])->label(false); ?>
    <?= $form->field($model, 'permisomenusincro')->hiddenInput(['id'=>'permisomenusincro'])->label(false); ?>


    <?php ActiveForm::end(); ?>

</div>
<script>


 function serializaCheck(){
    var serializado =""
    $(".selectCheboxMenu").each(function (index) { 	
        if ($(this).is(':checked')) {
            serializado=serializado+$(this).val()+"@";
        } 
    });
    console.log(serializado);
    $('#permisomenu').val(serializado); 

 }

 function serializaCheckSincro(){
    var serializado =""
    $(".selectCheboxMenuSincro").each(function (index) { 	
        if ($(this).is(':checked')) {
            serializado=serializado+$(this).val()+"@";
        } 
    });
    console.log(serializado);
    $('#permisomenusincro').val(serializado); 

 }

function marcarTodosSincro(control) {
    var valor = false;
    if (control.checked == true) {
        valor = true;
    }
    $(".selectCheboxMenuSincro").each(function (index) { 	
        $(this).prop('checked', valor);
    });
    serializaCheckSincro();
}
function marcarTodosMenu(control) {
    var valor = false;
    if (control.checked == true) {
        valor = true;
    }
    $(".selectCheboxMenu").each(function (index) { 	
        $(this).prop('checked', valor);
    });
    serializaCheck();
}

 /*
 $(".selectCheboxMenu").on('click', function () {

    if ($(this).is(':checked')) {
        arrayMenu.push($(this).val());
    } else {
        var index = arrayMenu.indexOf($(this).val());
        if (index > -1)
        arrayMenu.splice(index, 1);
    }
    var serializado = JSON.stringify(arrayMenu);
    console.log(serializado);
    $('#permisomenu').val(serializado);
});*/


        
</script>