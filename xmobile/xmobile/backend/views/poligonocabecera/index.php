<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;$this->title = 'Poligonocabeceras';
?>


<div class="window" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowEliminar" style="display: none"> <br/>
   
    <p class="text-center"><b>Esta seguro de eliminar el registro?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>

<div class="poligonocabecera-index">
    <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear Polígono</button></p>

    <?php Pjax::begin(['id' => 'Poligonocabecera-list']); ?>
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		
        'columns' => [

			['class' => 'yii\grid\SerialColumn'],
           // 'id',
            
            [
                'attribute' => 'territoryid',
                'filter' => \yii\helpers\ArrayHelper::map(backend\models\Territorios::find()->all(), 'TerritoryID', 'Description'),
                'value' => function($data) {
					$territorio=\backend\models\Territorios::findOne(["TerritoryID" =>$data->territoryid])->Description;
                    if (isset($territorio))
                        return $territorio;
                    else
                        return NULL;
                }
            ],
			[
                'attribute' => 'nombre',
				'format' => 'raw',
				'value' => function($data) {
					
				   return strtoupper($data->nombre);
				}
            ],
			[
                'attribute' => 'usuario',
				'filter' => \yii\helpers\ArrayHelper::map(backend\models\user::find()->all(), 'id', 'username'),
				'value' => function($data) {
					
				   return \backend\models\user::findOne(["id" =>$data->usuario])->username;
				}
            ]
			
			
			,
               // 'usuario',
               // 'status',
                //'dateUpdate',
                
                [
                        'attribute' => 'Acciones',
                        'format' => 'raw',
                        'value' => function($data) {
                            return ''
                                    . '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                    . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                 //  . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                    ;
                        }
                ]

            ],
        ]); ?>

    <?php Pjax::end(); ?>

</div>


<?php
	function getDia($dia){
		switch ($dia) {
			case '1':
				return 'Lunes';
				break;
			case '2':
				return 'Martes';
				break;
			case '3':
				return 'Miercoles';
				break;
			case '4':
				return 'Jueves';
				break;
			case '5':
				return 'Viernes';
				break;
			case '6':
				return 'Sabado';
				break;
			case '7':
				return 'Domingo';
				break;
			default:
				return '';
				break;
        }
	}
?>



<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Poligonocabecera.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Poligonocliente.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50" async defer></script>
                                                    <!-- AIzaSyCySuZ1D1ZnoE7rnmioW2QM6QFxmRfOf50 -->


