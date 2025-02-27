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
    <span id="exxisapp" name="<?= $exitx ?>"></span>
    <?php $form = ActiveForm::begin(['id' => 'Usuarioconfiguracion-form']); ?>

    <div class="row">
        <div class="col-md-6">
            <h4>Configuración Contable</h4>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'aperturaCaja')->checkbox() ?>
                    <?= $form->field($model, 'idUser')->hiddenInput()->label(false); ?>
                    <span class="text-danger text-clear" id="error-aperturaCaja"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'cierreCaja')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-cierreCaja"></span>
                </div>
            </div>
            <h4>Creación de Clientes</h4>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?php $arr = ArrayHelper::map(backend\models\Listaprecios::find()->all(), 'PriceListNo', 'PriceListName'); ?>
                    <?= $form->field($model, 'listaPrecios')->dropDownList($arr, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-listaPrecios"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                    <?php $arrx = ArrayHelper::map(backend\models\Clientesgrupo::find()->all(), 'Code', 'Name'); ?>
                    <?= $form->field($model, 'grupoCliente')->dropDownList($arrx, ['prompt' => '']) ?>
                    <span class="text-danger text-clear" id="error-grupoCliente"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                    <?php $arrterri = ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'); ?>
                    <?= $form->field($model, 'territorio')->dropDownList($arrterri, ['prompt' => '','onchange'=>'cambiaGrupoDisificacion(this.value)']) ?>
                    <span class="text-danger text-clear" id="error-territorio"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                    <?php $arrmodenda = ArrayHelper::map(backend\models\Monedas::find()->all(), 'Code', 'Name'); ?>
                    <?= $form->field($model, 'moneda')->dropDownList($arrmodenda, ['prompt' => '']) ?>
                    <span class="text-danger text-clear" id="error-moneda"></span>
                </div>
                <!--div class="col-xs-6 col-md-6">
                    <?php $arrgrupo = ArrayHelper::map(backend\models\Grupoclientedocificacion::find()->all(), 'id', 'nombre'); ?>
                    <?= $form->field($model, 'grupoClienteDosificacion')->dropDownList($arrgrupo, ['prompt' => '']) ?>
                    <span class="text-danger text-clear" id="error-grupoClienteDosificacion"></span>
                </div-->
                <?= $form->field($model, 'grupoClienteDosificacion')->hiddenInput(['value'=>0])->label(false) ?>
            </div>
            <h4>Configuración en Ventas</h4>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'descuentos')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-descuentos"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'editarDocumento')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-editarDocumento"></span>
                </div>
                <div class="col-xs-3  col-md-3">
                    <?= $form->field($model, 'totalDescuentoDocumento')->textInput(['placeholder' => '0.0%']) ?>
                    <span class="text-danger text-clear" id="error-totalDescuentoDocumento"></span>
                </div>
                <div class="col-xs-3  col-md-3">
                    <?= $form->field($model, 'totalDescuento')->textInput(['maxlength' => true, 'placeholder' => '0.0%']) ?>
                    <span class="text-danger text-clear" id="error-totalDescuento"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?php $arr = ArrayHelper::map(backend\models\Vendedores::find()->orderby('SalesEmployeeName')->all(), 'SalesEmployeeCode', 'SalesEmployeeName'); ?>
                    <?= $form->field($model, 'codEmpleadoVenta')->dropDownList($arr); ?>
                    <span class="text-danger text-clear" id="error-codEmpleadoVenta"></span>
                </div>
            </div>
            <h4>otras Configuraciones</h4>
            <div class="row">
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'idEstado')->checkbox(); ?>
                    <?= $form->field($model, 'idTipoPrecio')->checkbox() ?>
                    <?= $form->field($model, 'modInfTributaria')->checkbox() ?>
                    <?= $form->field($model, 'modMoneda')->checkbox() ?>
                    <?= $form->field($model, 'anularfacturas')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-anularfacturas"></span>
                    <?= $form->field($model, 'anularcobros')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-anularcobros"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'modlitaprecios')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-modlitaprecios"></span>
                    <?= $form->field($model, 'accessstock')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-accessstock"></span>
                    <?php if ($verCC == '1') { ?>
                        <label class="control-label">Centro de Costos</label>
                        <div>
                            <?php $arr = ArrayHelper::map(backend\models\Centroscostos::find()->all(), 'PrcCode', 'PrcName'); ?>
                            <?= Html::dropDownList('ddlCC', $cc[0]["PrcCode"], $arr, ['style' => 'width:100%;', 'id' => 'ddlCC', 'class' => 'form-control']) ?>
                        </div>
                    <?php } ?>
                    <?= $form->field($model, 'anularentregas')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-anularentregas"></span>
                </div>
            </div>
            <h4 class=""> Asignación de series</h4>
            <div class="row">
                <div class="col-xs-4 col-md-4">
                    <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Series::find()->where(['Document' => 2])->all(), 'Series', 'Name'); ?>
                    <?= $form->field($model, 'seriesCliente')->dropDownList($arrx, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-seriesCliente"></span>
                </div>
                <div class="col-xs-4 col-md-4">
                    <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Series::find()->where(['Document' => 23])->all(), 'Series', 'Name'); ?>
                    <?= $form->field($model, 'seriesOferta')->dropDownList($arrx, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-seriesOferta"></span>
                </div>
                <div class="col-xs-4 col-md-4">
                    <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Series::find()->where(['Document' => 17])->all(), 'Series', 'Name'); ?>
                    <?= $form->field($model, 'seriesPedido')->dropDownList($arrx, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-seriesPedido"></span>
                </div>
                <div class="col-xs-4 col-md-4">
                    <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Series::find()->where(['Document' => 24])->all(), 'Series', 'Name'); ?>
                    <?= $form->field($model, 'seriesPago')->dropDownList($arrx, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-seriesPago"></span>
                </div>

                <div class="col-xs-4 col-md-4">
                    <?php $arrx = \yii\helpers\ArrayHelper::map(backend\models\Series::find()->where(['Document' => 15])->all(), 'Series', 'Name'); ?>
                    <?= $form->field($model, 'seriesEntrega')->dropDownList($arrx, ['prompt' => '']); ?>
                    <span class="text-danger text-clear" id="error-seriesEntrega"></span>
                </div>
                
            </div>
            <h4 class=""> Tipo Usuario</h4>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'TipoUsuario')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-TipoUsuario"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'zonaFranca')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-zonaFranca"></span>
                </div>
                
            </div>

        </div>
        <div class="col-md-6">
            <h4> Configuraciones MOVILES</h4>
            <legend>Documentos</legend>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoOferta')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoOferta"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoFactura')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoFactura"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoEntrega')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoEntrega"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoPedido')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoPedido"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoAnularPedido')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoAnularPedido"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoAnularOferta')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoAnularOferta"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoCopiarPedido')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoCopiarPedido"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoCopiarOferta')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoCopiarOferta"></span>
                </div>
            </div>
            <legend>Pagos y descuentos</legend>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoPagosAnticipados')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoPagosAnticipados"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoPagosFacturasLocales')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoPagosFacturasLocales"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoPagoFacturasImportadas')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoPagoFacturasImportadas"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'descuentosDocumento')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-descuentosDocumento"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'descuentosLinea')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-descuentosLinea"></span>
                </div>
            </div>
            <legend>Validaciones</legend>
            <div class="row">
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'validarStock')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-validarStock"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'controlarModificarListaPrecios')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-controlarModificarListaPrecios"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'controlarCambioMoneda')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-controlarCambioMoneda"></span>
                </div>
            </div>
            <legend>Clientes</legend>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoCrearClientes')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoCrearClientes"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoEditarClientes')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoEditarClientes"></span>
                </div>
            </div>
            <legend>Importaciones</legend>
            <div class="row">
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoImportarDocumentosOferta')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoImportarDocumentosOferta"></span>
                </div>
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoImportarDocumentosPedido')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoImportarDocumentosPedido"></span>
                </div>
                <div class="col-xs-6  col-md-6">
                    <?= $form->field($model, 'permisoImportarDocumentosFactura')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoImportarDocumentosFactura"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <?= $form->field($model, 'permisoImportarDocumentosEntregas')->checkbox() ?>
                    <span class="text-danger text-clear" id="error-permisoImportarDocumentosEntregas"></span>
                </div>
            </div>
            <h4 class=""> Configuraciones multiples</h4>
            <div class="row ">
                <div class="col-xs-6 col-md-6">
                    <label class="control-label">Condiciones de pago</label>
                    <input type="hidden" id="hdCondiciones"
                           value="<?= isset($textoCondiciones) ? $textoCondiciones : ''; ?>"/>
                    <div style="height:350px;overflow: auto;">
                        <ul class="list-group">
                            <?php foreach ($condiciones as $condicion) { ?>
                                <li class="list-group-item">
                                    <?php
                                    $r = "";
                                    $resp = backend\models\Usuario_condicionespago::find()->where(["idusuario" => $model->idUser, "idcondicion" => $condicion->GroupNumber])->all();
                                    if (count($resp) == 1)
                                        $r = "checked";
                                    else
                                        $r = "";
                                    ?>
                                    <input type="checkbox" <?= $r; ?> value="<?= $condicion->GroupNumber; ?>"
                                           class="selectChebox">
                                    <b><?= $condicion->PaymentTermsGroupName; ?></b>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-6 col-md-6">
                    <label class="control-label"> Lista Precios</label>
                    <div style="height:350px;overflow: auto;">
                        <?php $arr = ArrayHelper::map(backend\models\Listaprecios::find()->all(), 'PriceListNo', 'PriceListName'); ?>
                        <ul class="list-group">
                            <?php foreach ($arr as $key => $val) { ?>
                                <li class="list-group-item">
                                    <input type="checkbox" value="<?= $key ?>" class="selectCheboxListaPrecios">
                                    <b><?= ($val); ?></b>
                                </li>
                            <?php } ?>
                        </ul>
                        <?= $form->field($model, 'multiListaPrecios')->hiddenInput()->label(false); ?>
                        <span class="text-danger text-clear" id="error-multiListaPrecios"></span>
                    </div>
                </div>
                <!--div class="col-xs-4 col-md-4">
                    <label class="control-label"> Campos de Usuarios</label>
                    <div style="height:350px;overflow: auto;">
                        <?php $arr2 = ArrayHelper::map(backend\models\Camposusuarios::find()->all(), 'Id', 'Nombre'); ?>
                        <ul class="list-group">
                            <?php foreach ($arr2 as $key2 => $val2) { ?>
                                <li class="list-group-item">
                                    <input type="checkbox" value="<?= $key2 ?>" class="selectCheboxCamposusuario">
                                    <b><?= ($val2); ?></b>
                                </li>
                            <?php } ?>
                        </ul>
                        
                        <span class="text-danger text-clear" id="error-multiCamposUsuarios"></span>
                    </div>
                </div-->
                <!--OBTENER LOS CAMPOS DE USUARIO AUTOMATICAMENTE-->
                <?= $form->field($model, 'multiCamposUsuarios')->hiddenInput()->label(false); ?>

            </div>

        </div>

    </div>


    <?php ActiveForm::end(); ?>
</div>
<script>
    var array1 = [];

    $('document').ready(function () {
       
        if ($('#hdCondiciones').val() != "" && array1.length == 0) {
           
            var jstring = $('#hdCondiciones').val();
            var div = jstring.split(',');
            for (var i = 0; i < div.length; i++)
                array1.push(div[i]);
        }
    });

    $(".selectChebox").on('click', function () {
    
        if ($(this).is(':checked')) {
            array1.push($(this).val());
        } else {
            var index = array1.indexOf($(this).val());
            if (index > -1)
                array1.splice(index, 1);
        }
        var serializado = JSON.stringify(array1);
        $('#hdCondiciones').val(serializado);
    });

 
   
    $(".selectChebox").each(function (index) { 	
        if ($(this).is(':checked')) {
            array1.push($(this).val());
        } else {
            var index = array1.indexOf($(this).val());
            if (index > -1)
                array1.splice(index, 1);
        }
        var serializado = JSON.stringify(array1);
        $('#hdCondiciones').val(serializado);
    });

    function cambiaGrupoDisificacion(valor){
        if(valor>=1 && valor <=9){
            $('#usuarioconfiguracion-grupoclientedosificacion').val(2);
        }
        else{
            $('#usuarioconfiguracion-grupoclientedosificacion').val(1);
        }
    }
        
</script>

