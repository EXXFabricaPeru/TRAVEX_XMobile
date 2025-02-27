<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Configuracionesgenerales';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>

<div class="row">
    <div class="col-md-6">
        <!--p class="text-right"> <button class="btn btn-success" id="btn-create">Addicionar </button>
        </p-->
        <?php Pjax::begin(['id' => 'Configuracionesgenerales-list']); ?>
        <?=
        ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item'],
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render("view", ["model" => $model]);
            },
        ])
        ?>
        <?php Pjax::end(); ?>
    </div>
    <div class="col-md-4"></div>
</div>



<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Configuracionesgenerales.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


