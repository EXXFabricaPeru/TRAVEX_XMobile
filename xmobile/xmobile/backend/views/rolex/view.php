<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Permisosx;
?>

<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
<div class="row">
    <div class="col-md-4">
        <h4> <b>ROL   <?= $model->nombre ?> </b> </h4>
        <span id="idview" data-id="<?= $model->id ?>"></span>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                //  'id',
                'nombre',
                'descripcion:ntext',
                'tipo',
            //  'user',
            ],
        ])
        ?>   
    </div>
    <div class="col-md-8">
        <h4><b>Acciones ::: <?= $model->nombre ?></b></h4>
        <div class="row">
            <?php foreach ($listController as $key) { ?>
                <div class="col-md-6">
                    <li class="list-group-item">
                        <label class="pure-material-checkbox">
                            <?php
                            $r = "";
                            $resp = Permisosx::find()->where(["rolexId" => $model->id, "accionesId" => $key->id])->all();
                            if (count($resp) == 1)
                                $r = "checked";
                            else
                                $r = "";
                            ?>
                            <input type="checkbox"  class="selectChebox" <?= $r; ?> value="<?= $key->id; ?>">
                            <span><?= $key->nombre; ?></span> 
                        </label>
                    </li>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Viewrol.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 