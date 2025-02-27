
<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\Objetostablas;
$this->title = 'Camposusuarios';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de Eliminar el dato?</b></p>
</div>

<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<div class="row">
    <div class="col-md-12">
        <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Campo</button>
        </p>
        <?php Pjax::begin(['id' => 'camposusuarios-list']); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'header'=>'Sta',
                        'format' => 'raw',
                        'value'=>function($data){
                            if($data->Status == '1'){
                                return '<span class="label label-success">Activo</span>';
                            }else{
                                return '<span class="label label-danger">Inactivo</span>';
                            }
                        }
                    ],
                    [
                        'attribute' => 'Objeto',
                        'value' => function($data) {
                            return Objetostablas::findOne($data->Objeto)->Nombre;
                        }
                    ],
                    'Nombre',
                    'Label',
                    'Tblsap',
                    'Campsap',
                    [
                        'attribute' => 'tipocampo',
                        'filter' => array("1" => "Lista", "0" => "Texto", "2" => "Numerico"),
                        'value' => function($data) {
                            switch ($data->tipocampo) {
                                case ('1') : return 'Lista';
                                case ('0') : return 'Texto';
                                case ('2') : return 'Numerico';
                            }
                        }
                    ],
                    //'longitud',
                    //'Fechainsert',
                    //'Userinser',
                    //'FechaUpdate',
                    //'UserUpdate',
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
                            switch ($data->tipocampo) {
                                case ('1') : return ''
                                . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->Id . '" ><i class="fas fa-edit text-info"></i></button>'
                                . '<a href="/companexTest/xmobile/backend/web/index.php?r=camposusuarios%2Fview&amp;id='. $data->Id .'" title="Ver" aria-label="Ver" data-pjax="0"><span  class="fas fa-align-justify"></span></a>'
                                . '';
                                default : return ''
                                . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->Id . '" ><i class="fas fa-edit text-info"></i></button>'
                                . '';
                            }

                        }
                    ],
                ],
            ]); ?>

        <?php Pjax::end(); ?>
    </div>
</div>
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Camposusuarios.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
