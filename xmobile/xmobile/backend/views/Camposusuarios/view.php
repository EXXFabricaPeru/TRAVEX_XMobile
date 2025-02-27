<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<span id="valueIdcampoUsuario" data-id="<?= $id; ?>"></span>
<div class="row">
    <div class="col-md-4">
        <h4>Campo</h4>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'Id',
                'Objeto',
                'Nombre',
                'Tblsap',
                'Campsap',
                'tipocampo',
                'longitud',
                'Fechainsert',
                'Userinser',
                'FechaUpdate',
                'UserUpdate',
                'Status',
            ],
        ]) ?>
    </div>
    <div class="col-md-8">
        <h4>Opciones de la Lista <?= $id; ?></h4>

        <div class="window" style="display: none"></div>
        <div id="windowpdf" style="display: none"></div>
        <div id="windowEliminar" style="display: none"> <br/><br/>
            <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
        </div>
        <div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
        <div class="listacamposusuarios-index">

            <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Opciones</button>
            </p>
            <?php Pjax::begin(['id' => 'Listacamposusuarios-list']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    'Id',
                    'codigo',
                    'nombre',
                    [
                        'attribute' => 'Status',
                        'filter' => array("1" => "Activo", "0" => "Inactivo"),
                        'value' => function($data) {
                            switch ($data->Status) {
                                case ('1') : return 'Activo';
                                case ('0') : return 'Inactivo';
                            }
                        }
                    ],

                    [
                                'attribute' => 'Acciones',
                                'format' => 'raw',
                                'value' => function($data) {
                                    return ''
                                            . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->Id . '" ><i class="fas fa-edit text-info"></i></button>'
                                            . '';
                                }
                    ]
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Listacamposusuarios.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
