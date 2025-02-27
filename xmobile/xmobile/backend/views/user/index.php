<?php
/* MAHH-EXXIS-BOLIVIA */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\Usuariopersona;

$this->title = 'Users';

$datosC = Yii::$app->db->createCommand("SELECT valor from configuracion
          where parametro='campo_adicional_user_midd' and estado=1")->queryOne();
?>
<div class="window" style="display: none"></div>
<div class="windowAccesos" style="display: none"></div>
<div id="windowpdf" style="display: none"></div>
<div id="windowReset" style="display: none"><br/> <p class="text-center"><b>Esta seguro de resetear el password?</b></p></div>
<div id="windowEliminar" style="display: none"> <br/><br/>
    <p class="text-center"><b>Esta seguro de eliminar el dato?</b></p>
</div>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']); ?>"></div>

<div class="row">
    <div class="col-md-12">
		<?php
		if(Yii::$app->session->get('NIVEL')=='2'){
		?>
        <p class="text-right"> <button class="btn btn-success" id="btn-create">Crear User </button>
        </p>
		<?php
		}
		?>
		
        <?php Pjax::begin(['id' => 'User-list']); ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //'id',
                'username',
                //'auth_key',
                // 'password_hash',
                // 'password_reset_token',
                //'status',
                'created_at',
                //'updated_at',
                //'verification_token',
                //'access_token:ntext',
                // 'idPersona',
                [
                   
                    'attribute' => 'idPersona',
                    'filter' => \yii\helpers\ArrayHelper::map(backend\models\Vipersona::find()->asArray()->all(), 'idPersona', 'nombreCompleto'),
                    'value' => function($data) {
                        /*$dataPersona=\backend\models\Vipersona::find()->where('idPersona='.$data->idPersona)->asArray()->one();
                        if (isset($dataPersona['nombreCompleto']))
                            return $dataPersona['nombreCompleto'];
                        else
                            return NULL;*/
                        return $data->nombreCompleto;
                    }

                ],
                [
                    'attribute' => 'estadoUsuario',
                    'filter' => array("1" => "Activo", "0" => "Inactivo", "2" => "Inhabilitado"),
                    'value' => function($data) {
                        switch ($data->estadoUsuario) {
                            case ('1') : return 'Activo';
                            case ('0') : return 'Inactivo';
                            case ('2') : return 'Inhabilitado';
                        }
                    }
                ],
               ($datosC['valor']==1)?(              
                   [
                        'attribute' => 'U_Regional',
                        'filter' => \yii\helpers\ArrayHelper::map(backend\models\Vendedores::find()->where("U_Regional IS NOT NULL")->asArray()->all(), 'U_Regional', 'U_Regional'),
                        'value' => function($data) {

                            return $data->U_Regional;
                            
                        }

                    ]
                                    
                ):([
                    'header'=>'',
                    'value'=>function($data){
                        return "";
                    }
                ]),
                //'estadoUsuario',
              
                //'fechaUMUsuario',
                //'plataformaUsuario',
                //'plataformaPlataforma',
                //'plataformaEmei',
                //'reset',
				   [
                    'header' => 'reset',
                    'format' => 'raw',
                    'value' => function($data) {
						if($data->reset == 1){
							return '<span class="label label-warning resetEventData" id="' . $data->id . '">Reset</span>';
						}else{
							return '<span class="label label-success">Ok</span>';
						}
                    }
                ],
                [
                    'attribute' => 'Acciones',
                    'format' => 'raw',
                    'value' => function($data) {
                        $contenido='';
                        if(Yii::$app->session->get('NIVEL')=='2'){
                                $contenido= '<button title="Eliminar registro" class="btn-link btn-grid-action-delete" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                                . '<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>'
                                . '<button title="Configuraciones del usuario" class="btn-link btn-grid-action-config" value="' . $data->id . '" ><i class="fas fa-cogs  text-success"></i> Movil</button>'
                                . '<button title="Accesos usuario" class="btn-link btn-grid-action-accesos" value="' . $data->id . '" ><i class="fas fa-key text-danger"></i></button>'
                                . '<button title="Configuraciones del usuario MiddleWere" class="btn-link btn-grid-action-config-middle" value="' . $data->id.'@'.$data->username.'" ><i class="fas fa-cogs  text-success"></i> Middle</button>'
								. '<span style="cursor:pointer;" class="label label-warning resetEventData" id="' . $data->id . '">Reset</span>'
								. '<button title="Configuraciones de Territorio" class="btn-link btn-territorio" value="' . $data->id.'" ><i class="fas fa-cogs  text-success"></i> Asignar Terri.</button>'
								//  . '<button title="Documento PDF" class="btn-link btn-grid-action-pdf" value="' . $data->id . '" ><i class="fas fa-file-pdf text-danger"></i></button> '
                                . '';
						}
                        else{
                            $contenido='<button title="Editar registro" class="btn-link btn-grid-action-edit" value="' . $data->id . '" ><i class="fas fa-edit text-info"></i></button>';
                        }

						return  $contenido;
                    }
                ]
            ],
        ]);
        ?>


        <?php Pjax::end(); ?>

    </div>
</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/User.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Usuarioconfiguracion.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Tienex.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Permisosmiddle.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 

<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Usuariomovilterritorio_.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 

