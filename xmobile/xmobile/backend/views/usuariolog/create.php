<?php
use yii\helpers\Html;

if(isset($_POST['CONDICION'])){

    $DataServicios = Yii::$app->db->createCommand("CALL pa_obtenerServiciosMovilSincroHora('".$_POST['FECHAINICIO']."','".$_POST['FECHAFIN']."', '".$_POST['IDUSUARIO']."')")->queryAll();
    echo (json_encode ($DataServicios));
}
else{
?>
<div class="usuariolog-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
<?php
}
?>