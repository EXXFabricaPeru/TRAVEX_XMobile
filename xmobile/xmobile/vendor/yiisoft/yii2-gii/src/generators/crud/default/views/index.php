<?php
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
echo "<?php\n";
?>
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>
$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
?>

<?php 
$class = $generator->modelClass;
$pks = $class::primaryKey();
$id = $pks[0];
?>

<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?='<?php' ?> echo Yii::$app->urlManager->createAbsoluteUrl(['']);<?='?>'?>"></div>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<p class="text-right"> <button class="btn btn-success" id="btn-create"><?= $generator->generateString('Crear ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?> </button>
    </p>
<?= $generator->enablePjax ? "<?php Pjax::begin(['id' => '".Inflector::camel2words(StringHelper::basename($generator->modelClass))."-list']); ?>\n" : '' ?>
<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            //'" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        if (++$count < 6) {
            echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        } else {
            echo "            //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }
}
?>

             [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data-><?= $id; ?> . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data-><?= $id; ?> . '" ><i class="fas fa-edit text-info"></i></button>'
                                    . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data-><?= $id; ?> . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    . '';
                        }
             ]
        ],
    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>

<?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>

</div>
<?= '<?=' ?>  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?>/Main.js', ['depends' => [yii\web\JqueryAsset::className()]]); <?= '?>' ?> 


