<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\FexSucursales;

?>
<div class="container-fluid">
    <?php $form = ActiveForm::begin(['id' => 'Lbcc-form']); ?>
    <?php 
 
        //$uso_fex=Yii::$app->db->createCommand("SELECT * FROM configuracion WHERE parametro='FEX'")->queryOne();
        if (!isset($model->equipoId)){
            $model->equipoId=Yii::$app->session->get('IDEQUIPO');
        }
        $uso_fex=Yii::$app->db->createCommand("SELECT * FROM equipox WHERE id=".$model->equipoId)->queryOne();
        $uso_fex=$uso_fex["fex"];
        if($uso_fex==1){
            $fexLbbb=Yii::$app->db->createCommand("SELECT * FROM configuracion WHERE parametro='FEX_LBCC' and valor=1")->queryOne();
            if(isset($fexLbbb['valor2'])){
                if($model->U_Actividad==''){
                    $actividad=$fexLbbb['valor2'];
                    $leyenda=$fexLbbb['valor3'];
                    $numeracion=$fexLbbb['valor4'];
                }
                else{
                    $actividad=$model->U_Actividad;
                    $leyenda=$model->U_Leyenda;
                    $numeracion=$model->U_UltimoNumero;
                }
            }else{
                $actividad=$model->U_Actividad;
                $leyenda=$model->U_Leyenda;
                $numeracion=$model->U_UltimoNumero;
            }
        
        }else{
            $actividad=$model->U_Actividad;
            $leyenda=$model->U_Leyenda;
            $numeracion=$model->U_UltimoNumero;
        }
       
       
       
       
       
        if($uso_fex==1)
        { ?>
            <div class="row">
                <div class="col-md-3">  
                    <?= $form->field($model, 'U_Estado')->dropDownList(['1' => 'Activo', '2' => 'Inactivo']); ?>
                    <span class="text-danger text-clear" id="error-U_Estado"></span>
                </div> 
                <div class="col-md-3">   
                    <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Tipopapel::find()->all(), 'id', 'nombre'); ?>
                    <?= $form->field($model, 'papelId')->dropDownList($arr, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-papelId"></span>
                </div>
                <div class="col-md-3">   
                    <?= $form->field($model, 'U_Actividad')->textInput(['value'=>$actividad]) ?>
                    <span class="text-danger text-clear" id="error-U_Actividad"></span>
                </div>
                <div class="col-md-3"> 
                    <?= $form->field($model, 'U_Leyenda')->textInput(['value'=>$leyenda]) ?>
                    <span class="text-danger text-clear" id="error-U_Leyenda"></span>
                </div> 
            </div>
            <div class="row">
                <div class="col-md-4">   
                    <?= $form->field($model, 'U_PrimerNumero')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_PrimerNumero"></span>
                </div> 
                <div class="col-md-4">   
                    <?= $form->field($model, 'U_NumeroSiguiente')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_NumeroSiguiente"></span>
                </div> 
                <div class="col-md-4"> 
                    <?= $form->field($model, 'U_UltimoNumero')->textInput(['value'=>$numeracion]) ?>
                    <span class="text-danger text-clear" id="error-U_UltimoNumero"></span>
                </div> 
            </div>
            <div class="row">
                <div class="col-md-4">  
                    <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Series::find()->where(['Document' => 13])->all(), 'Series', 'Name'); ?>
                    <?= $form->field($model, 'U_Series')->dropDownList($arrx, ['prompt' => '']); ?>
                    <?= $form->field($model, 'U_SeriesName')->hiddenInput(['maxlength' => true])->label(false); ?>
                    <span class="text-danger text-clear" id="error-U_Series"></span>
                </div>
                <!-- Campo para Sucursales -->
                <div class="col-md-4">
                    <?php 
                        $arraySucursal=array();
                        $sucursales=FexSucursales::find()->asArray()->all();
                        foreach ($sucursales as $key => $value) {
                            //(isset($value['DocEntry']) || is_null($value['DocEntry'])) ? 
                            $arraySucursal[$value['NumSucursal']]=$value['Code']." - ".$value['NumSucursal']." - ".$value['NombreSucursal'];
                            //$arraySucursal[rand(0,)] ;
                        }
                    ?>
                    <?= $form->field($model,'fex_sucursal')->dropDownList($arraySucursal,['prompt'=>'Seleccione punto de venta','onchange'=>'puntoVentas()']); ?>
                </div>
                <!-- Campo para Puntos de Venta -->
                <div class="col-md-4">
                    <?php $puntosventa=\yii\helpers\ArrayHelper::map(backend\models\PuntoVenta::find()->all(),'idpuntoventa','descripcion'); ?>
                    <?= $form->field($model,'fex_puntoventa')->dropDownList([$model->fex_puntoventa=>$model->fex_puntoventa]); ?>
                </div> 
                <div class="col-md-4">
                    <?php $fextipodocumento=\yii\helpers\ArrayHelper::map(backend\models\Fextipodocumentosin::find()->all(),'codigo','descripcion'); ?>
                    <?= $form->field($model,'idFexTipoDoc')->dropDownList($fextipodocumento,['prompt'=>'Seleccione Tipo Doc.']); ?>
                </div> 
            </div> 
            <div class="row">
                <div class="col-md-4">   
                    <?= $form->field($model, 'equipoId')->hiddenInput(['value' => Yii::$app->session->get('IDEQUIPO')])->label(false); ?>
                    <span class="text-danger text-clear" id="error-equipoId"></span>
                </div>
            </div>
            <?php if ($grupoCliente == true && $grupoProducto == true) { ?>
                <div class="row">
                    <div class="col-md-6">   
                        <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Grupoclientedocificacion::find()->all(), 'id', 'nombre'); ?>
                        <?= $form->field($model, 'U_GrupoCliente')->dropDownList($arrx, ['prompt' => '']); ?>
                        <span class="text-danger text-clear" id="error-U_GrupoCliente"></span>
                    </div>        
                    <div class="col-md-6">   
                        <?php 
                          //$arrx = \yii\helpers\ArrayHelper::map(backend\models\Productosgrupo::find()->all(), 'Number', 'GroupName'); 
                          $arrx = \yii\helpers\ArrayHelper::map(backend\models\Grupoproductodocificacion::find()->all(), 'id', 'nombre');
                          ?>
                        <?= $form->field($model, 'U_GrupoProducto')->dropDownList($arrx, ['prompt' => '']); ?>
                        <span class="text-danger text-clear" id="error-U_GrupoProducto"></span>
                    </div>
                </div>
            <?php } ?>
            <?php if ($facturaoffline==true) { ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'facturaOffline')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-facturaOffline"></span>
                </div>
            </div>
            <?php } ?>
            
    <?php    }
        else
        { ?>        
            <div class="row">
                <div class="col-md-6">  
                    <?= $form->field($model, 'U_NumeroAutorizacion')->textInput(['maxlength' => true]) ?>
                    <span class="text-danger text-clear" id="error-U_NumeroAutorizacion"></span>
                </div> 
                <div class="col-md-3">  
                    <?= $form->field($model, 'U_Estado')->dropDownList(['1' => 'Activo', '2' => 'Inactivo']); ?>
                    <span class="text-danger text-clear" id="error-U_Estado"></span>
                </div> 
                <div class="col-md-3">   
                    <?php $arr = \yii\helpers\ArrayHelper::map(backend\models\Tipopapel::find()->all(), 'id', 'nombre'); ?>
                    <?= $form->field($model, 'papelId')->dropDownList($arr, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-papelId"></span>
                </div> 
            </div> 
            <div class="row">
                <div class="col-md-4">   
                    <?= $form->field($model, 'U_PrimerNumero')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_PrimerNumero"></span>
                </div> 
                <div class="col-md-4">   
                    <?= $form->field($model, 'U_NumeroSiguiente')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_NumeroSiguiente"></span>
                </div> 
                <div class="col-md-4"> 
                    <?= $form->field($model, 'U_UltimoNumero')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_UltimoNumero"></span>
                </div> 
            </div>
            <div class="row">
                <div class="col-md-4">  
                    <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Series::find()->where(['Document' => 13])->all(), 'Series', 'Name'); ?>
                    <?= $form->field($model, 'U_Series')->dropDownList($arrx, ['prompt' => '']); ?>
                    <?= $form->field($model, 'U_SeriesName')->hiddenInput(['maxlength' => true])->label(false); ?>
                    <span class="text-danger text-clear" id="error-U_Series"></span>
                </div>
                <div class="col-md-5">   
                    <?= $form->field($model, 'U_Actividad')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_Actividad"></span>
                </div> 
                <div class="col-md-3">  
                    <?= $form->field($model, 'U_FechaLimiteEmision')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_FechaLimiteEmision"></span>
                </div> 
            </div> 
            <div class="row">
                <div class="col-md-6">   
                    <?= $form->field($model, 'U_LlaveDosificacion')->textInput(['maxlength' => true]) ?>
                    <span class="text-danger text-clear" id="error-U_LlaveDosificacion"></span>
                </div> 
                <div class="col-md-6"> 
                    <?= $form->field($model, 'U_Leyenda')->textInput() ?>
                    <span class="text-danger text-clear" id="error-U_Leyenda"></span>
                </div>
            </div>

           
            <div class="row">
                <div class="col-md-6">   
                    <?= $form->field($model, 'equipoId')->hiddenInput(['value' => Yii::$app->session->get('IDEQUIPO')])->label(false); ?>
                    <span class="text-danger text-clear" id="error-equipoId"></span>
                </div>       
            </div>
            <?php if ($grupoCliente == true && $grupoProducto == true) { ?>
                <div class="row">
                    <div class="col-md-6">   
                        <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Grupoclientedocificacion::find()->all(), 'id', 'nombre'); ?>
                        <?= $form->field($model, 'U_GrupoCliente')->dropDownList($arrx, ['prompt' => '']); ?>
                        <span class="text-danger text-clear" id="error-U_GrupoCliente"></span>
                    </div>        
                    <div class="col-md-6">   
                        <?php 
                            //$arrx = \yii\helpers\ArrayHelper::map(backend\models\Productosgrupo::find()->all(), 'Number', 'GroupName');
                            $arrx = \yii\helpers\ArrayHelper::map(backend\models\Grupoproductodocificacion::find()->all(), 'id', 'nombre');
                         ?>
                        <?= $form->field($model, 'U_GrupoProducto')->dropDownList($arrx, ['prompt' => '']); ?>
                        <span class="text-danger text-clear" id="error-U_GrupoProducto"></span>
                    </div>
                </div>
            <?php } ?>
            <?php if ($facturaoffline==true) { ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'facturaOffline')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-facturaOffline"></span>
                </div>
            </div>
            <?php } ?>
<?php  }
    ?>
    
    <?php ActiveForm::end(); ?>
</div>
<script>
    puntoVentas();
    function puntoVentas(){
        if ($("#lbcc-fex_sucursal").length ) {
            var id=$('#lbcc-fex_sucursal').val();
			var idPuntoVenta=$('#lbcc-fex_puntoventa').val();//R77
			console.log("idPuntoVenta: "+idPuntoVenta);
            if(id==''){
                id=-1;
            }
            $.ajax({
                url: $("#PATH").attr("name")+'lbcc/obtienepuntoventa',
                type: 'POST',
                data: 'id='+id,
                success: (data, status, xhr) => {
                    if(status == 'success'){
                        var resultado=JSON.parse(data);
                        console.log(resultado);
                        selectActual=document.getElementById("lbcc-fex_puntoventa");
                        selectActual.length=0;
                        for (let index = 0; index < resultado.length; index++) {
                            var nuevaOpcion=document.createElement("option"); 
                            nuevaOpcion.value=resultado[index].idpuntoventa; 
                            nuevaOpcion.innerHTML=resultado[index].descripcion;
                            selectActual.appendChild(nuevaOpcion); 
                            
                        } 
						if(idPuntoVenta!=null)$('#lbcc-fex_puntoventa').val(idPuntoVenta);//R77
                    }
                    else{
                        console.error("ERROR AL OPTENER PUNTO DE VENTA: ");    
                    }
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
        }   
    }
    
</script>
