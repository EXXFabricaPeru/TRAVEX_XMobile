<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Viusuariopersona;
$modeluser=Viusuariopersona::find()->asArray()->all();

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

    <?php $form = ActiveForm::begin(['id' => 'Usuariomovilterritorio-form']); ?>
    <div class="row">
		<div class="col-md-6">  
			<h4>Lista de Usuarios</h4>
				<?php 
					 $arr= array();
	                foreach ($modeluser as $key => $value) {
	                    //array_push($arr, $value['id']);
	                    $arrUser[$value['id']]=$value['nombreCompleto'].' - '.$value['username'];
	                }
				?>
                <select name="idUsuario" id='idUsuario' class='form-control'>
			
				<?php foreach($arrUser As $key=>$value){?>
				  <option value='<?=$key?>'><?=$value?></option>
				<?php }?>
				</select>
			<span class="text-danger text-clear" id="error-idUser"></span>
		</div> 
		<div class="col-md-6">   
			
			 <h4>Lista de Territorios</h4>
			<div style="height:350px;overflow: auto;">
				<?php $arr = ArrayHelper::map(backend\models\Territorios::find()->orderBy('TerritoryID ASC')->all(), 'TerritoryID', 'Description'); ?>
				<ul class="list-group">
					<?php foreach ($arr as $key => $val) { ?>
						<li class="list-group-item">
							<input type="checkbox" value="<?= $key.'=>'.$val ?>"  id="<?= $key ?>" class="selectCheboxTerritorios">
							<label class="form-check-label" for="<?= $key ?>"><b><?= ($val); ?></b></label>
						</li>
					<?php } ?>
				</ul>
			</div>
			
			<span class="text-danger text-clear" id="error-idTerritorio"></span>
		</div>
	</div>
	<?= $form->field($model, 'idUser')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'user')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'idTerritorio')->hiddenInput(['value' => '11'])->label(false)?>
	<?= $form->field($model, 'territorio')->hiddenInput(['value' => 'xxx'])->label(false) ?>
	<?= $form->field($model, 'idUserRegister')->hiddenInput(['value' => Yii::$app->session->get('IDUSUARIO')])->label(false) ?>
	<?= $form->field($model, 'userRegister')->hiddenInput(['value' => Yii::$app->session->get('USUARIO')])->label(false) ?>
	<?= $form->field($model, 'fechaSistema')->hiddenInput()->label(false) ?>
	<?= $form->field($model, 'fechaUpdate')->hiddenInput(['value' => date('Y-m-d')])->label(false) ?>

	<?php ActiveForm::end(); ?>
	
</div>



