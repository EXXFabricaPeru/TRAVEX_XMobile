<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$this->title = 'Equipoxcuentascontables';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>
<div class="equipoxcuentascontables-index">

    <p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Equipoxcuentascontables' </button>
    </p>
    <?php Pjax::begin(['id' => 'Equipoxcuentascontables-list']); ?>
    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model->id), ['view', 'id' => $model->id, 'equipoxId' => $model->equipoxId]);
        },
    ])
    ?>

<?php Pjax::end(); ?>

</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Equipoxcuentascontables.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 



