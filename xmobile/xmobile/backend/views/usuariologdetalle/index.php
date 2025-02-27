<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
$this->title = 'Usuariologs';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="usuariolog-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

           // 'id',
            'fechaIngreso',
            'fecha',
            'usuario',
           /// 'idUsuario',
            //'ipAddress',
            'codigo'

        ],
    ]); ?>


</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Usuariolog/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 


