<?php

use yii\helpers\Url;
?>
<style>
    h4{
        text-transform: uppercase;
        font-size: 13px;
        background-color:#ccc;
        padding: 4px;
        color:#fff;
    }
</style>
<span id="idUser" data-id="<?= $id; ?>"></span>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-4">
            <h4>Asignar rol de usuario</h4>
            <ul class="list-group">
                <?php foreach ($roles as $key) { ?>
                    <li class="list-group-item">
                        <label class="pure-material-checkbox">
                            <?php
                            $r = "";
                            $resp = backend\models\Tienex::find()->where(["rolexId" => $key->id, "userId" => $id])->all();
                            if (count($resp) == 1)
                                $r = "checked";
                            else
                                $r = "";
                            ?>
                            <input type="checkbox" <?= $r; ?>  value="<?= $key->id; ?>" class="selectChebox">
                            <span><?= $key->nombre; ?></span> 
                        </label>
                        <span class="badge">
                            <a target="_blank" href="<?= Url::toRoute(['rolex/view', "id" => $key->id]); ?>"><i class="fas fa-location-arrow" style="color: #fff !important"></i></a> 
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-xs-8">
            <h4>Adicionar accesos</h4>
            <?php //print_r($accionesAdicionales) ?>
            <di1v class="row">
            <?php foreach ($acciones as $accion) { ?>
                <li class="list-group-item">
                <label class="pure-material-checkbox">
                    <?php 
                        $checked = false;
                        for ($i=0;$i<count($accionesActivadas);$i++) {
                            if ($accionesActivadas[$i] === $accion->id) {
                                $checked = true;
                            }
                        }
                        $r = "";
                        $d = "";
                        if ($checked) {
                            $r = "checked";
                            $d = "disabled";
                        } else {
                            $r = "";
                            $d = "";
                        }
                        // --
                        $aditionalChecked = false;
                        for ($i=0;$i<count($accionesAdicionales);$i++) {
                            if ($accionesAdicionales[$i] === $accion->id) {
                                $aditionalChecked = true;
                            }
                        }
                        $a = "";
                        if ($aditionalChecked) {
                            $a = "Checked";
                        } else {
                            $a = "";
                        }
                    ?>
                    <input type="checkbox" class="selectChebox" <?= $a; ?> <?= $r; ?> <?= $d; ?> value="<?= $accion->id; ?>">
                    <span><?= $accion->nombre; ?></span>
                </label>
                </li>
            <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(".selectChebox").on('click', function () {
        if( $(this).is(':checked') ) {
            registrarAccion($(this).val(), $("#idUser").data("id"));
        } else {
            eliminarAccion($(this).val(), $("#idUser").data("id"));
        }
    });

    function eliminarAccion(accionId, userId){
        let accionEliminar = {
            idAccion: accionId,
            idUsuario: userId
        };
        $.ajax({
                url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tienex/borraraccionadicional']); ?>',
                type: 'POST',
                data: $.param(accionEliminar),
                success: (data, status, xhr) => {
                    /* console.log('respuesta eliminar');
                    console.log(data); */
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
    }

    function registrarAccion(accionId, userId) {
        let accionesNuevas = {
            idAccion: accionId,
            idUsuario: userId
        };
        $.ajax({
                url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tienex/nuevaaccionadicional']); ?>',
                type: 'POST',
                data: $.param(accionesNuevas),
                success: (data, status, xhr) => {
                    /* console.log('respuesta');
                    console.log(data); */
                },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
                }
            });
    };

</script>
