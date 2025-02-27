<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;$this->title = 'Cuentascontables';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="cuentascontables-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create">'Crear Cuentascontables' </button>
    </p>
<?php Pjax::begin(['id' => 'Cuentascontables-list']); ?>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model->Name), ['view', 'id' => $model->id]);
        },
    ]) ?>

    <?php Pjax::end(); ?>

</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Cuentascontables/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


