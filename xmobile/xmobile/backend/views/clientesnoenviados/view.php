<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model backend\models\Clientes */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clientes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

function getDiaVisita($dia){
    if($dia=='tYES' || $dia=='Y')return "SI";
    else return "NO";
}
?>
<style>
    img {
        height: 350px;
        width: 300px;
    }
</style>

    <div class="row" align="center">
        <label style="color:#1F0656">LOG RESPUESTA ENVIO</label>
    </div>

    <div class="table-responsive">
        <?php Pjax::begin(); ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProviderle,
            'filterModel' => $searchModelle,
            'columns' => [
                //'idlog',
                'documento',
                'proceso',
                // 'envio:ntext',
                'fecha',
                'respuesta:ntext',
                // 'ultimo',
                // 'endpoint',
        
            //['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
            ],
        ]);
        ?>

        <?php Pjax::end(); ?>

	</div>

<div class="panel panel-default">
  <div class="panel-body">
    <div class="row" align="center">
        <label style="color:#1F0656">DATOS DEL CLIENTE</label>
    </div>
    <hr>
    <form>
    <div class="row form-group">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-2">
                    <label for="" class="">CODIGO</label>
                    <input type="text" value="<?=$model->CardCode?>" class="form-control mayusculas" readonly >   
                </div>
                <div class="col-md-4">
                    <label for="" class="">NOMBRE COMPLETO</label>
                    <input type="text" value="<?=$model->CardName?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-3">
                    <label for="" class="">RAZON SOCIAL</label>
                    <input type="text" value="<?=$model->CardForeignName?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-3">
                    <label for="" class="">CI/NIT</label>
                    <input type="text" value="<?=$model->FederalTaxId?>" class="form-control mayusculas" readonly>   
                </div>
                
            </div>
            <br>
        
            <div class="row">
                <div class="col-md-3">
                    <label for="" class="">CORREO ELECTRONICO</label>
                    <input type="text" value="<?=$model->EmailAddress?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-3">
                    <label for="" class="">TELEFONO</label>
                    <input type="text" value="<?=$model->PhoneNumber?>" class="form-control mayusculas" readonly >   
                </div>
                <div class="col-md-3">
                    <label for="" class="">NRO CELULAR</label>
                    <input type="text" value="<?=$model->Phone2?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-3">
                    <label for="" class="">NRO CELULAR 2</label>
                    <input type="text" value="<?=$model->Cellular?>" class="form-control mayusculas" readonly>   
                </div>
                
            </div>

            <br>
            <div class="row">
                <div class="col-md-3">
                    <label for="" class="">PAIS</label>
                    <input type="text" value="<?=$model->Country?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-3">
                    <label for="" class="">DIRECCION</label>
                    <input type="text" value="<?=$model->Address?>" class="form-control mayusculas" readonly >   
                </div>
                
                <div class="col-md-3">
                    <label for="" class="">LATITUD</label>
                    <input type="text" value="<?=$model->Latitude?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-3">
                    <label for="" class="">LONGITUD</label>
                    <input type="text" value="<?=$model->Longitude?>" class="form-control mayusculas" readonly>   
                </div>
            </div>
            <hr>
            <label>DIAS DE VISITAS:</label>
            
            <div class="row">

                <div class="col-md-2">
                    <label for="" class="">LUNES</label>
                    <input type="text" value="<?=getDiaVisita($model->Properties1)?>" class="form-control mayusculas" readonly >   
                </div>
                <div class="col-md-2">
                    <label for="" class="">MARTES</label>
                    <input type="text" value="<?=getDiaVisita($model->Properties2)?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-2">
                    <label for="" class="">MIERCOLES</label>
                    <input type="text" value="<?=getDiaVisita($model->Properties3)?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-2">
                    <label for="" class="">JUEVES</label>
                    <input type="text" value="<?=getDiaVisita($model->Properties4)?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-2">
                    <label for="" class="">VIERNES</label>
                    <input type="text" value="<?=getDiaVisita($model->Properties5)?>" class="form-control mayusculas" readonly>   
                </div>
                <div class="col-md-2">
                    <label for="" class="">SABADO</label>
                    <input type="text" value="<?=getDiaVisita($model->Properties6)?>" class="form-control mayusculas" readonly>   
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <?php
                if(empty($model->img))$imagen="addPhoto.svg";
                else $imagen=$model->img;

                //class="img-thumbnail"  Html::img(str_replace("backend", "api", \Yii::$app->request->BaseUrl) . '/imgs/cli/' . $data->img . '.jpeg', ['class' => 'imgprod'])
            ?>
            <label for="" class="">FOTOGRAFIA</label>
            <img src="../../api/web/imgs/cli/<?=$imagen?>" alt="<?=$model->CardName?>" >   

        </div>   
    </div>
    
    <?php
    if(count($modelSucursal)>0){
    ?>
    <hr>
    <div class="row" align="center">
        <label style="color:#1F0656">SUCURSALES:</label>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label for="" class="">DIRECCION</label>   
        </div>
        <div class="col-md-3">
            <label for="" class="">CALLE</label>   
        </div>
        <div class="col-md-2">
            <label for="" class="">LATITUD</label>   
        </div>
        <div class="col-md-2">
            <label for="" class="">LONGITUD</label>   
        </div>
        <div class="col-md-2">
            <label for="" class="">TIPO DIRECCION</label>   
        </div>         
    </div>
    <?php
    }
    else{
    ?>
    <hr>
    <div class="row" align="center">
        <label style="color:#1F0656">SUCURSALES: SIN REGISTROS</label>
    </div>   
    <?php
    }
    foreach ($modelSucursal as $key => $value) {
    ?>
    <div class="row">
        <div class="col-md-3">
            <input type="text" value="<?=$value['Street']?>" class="form-control mayusculas" readonly >   
        </div>
        <div class="col-md-3">
            <input type="text" value="<?=$value['AddresName']?>" class="form-control mayusculas" readonly>   
        </div>
        <div class="col-md-2">
            <input type="text" value="<?=$value['u_lat']?>" class="form-control mayusculas" readonly>   
        </div>
        <div class="col-md-2">
            <input type="text" value="<?=$value['u_lon']?>" class="form-control mayusculas" readonly>   
        </div>
        <div class="col-md-2">
            <input type="text" value="<?=$value['AdresType']?>" class="form-control mayusculas" readonly>   
        </div>         
    </div>
    <br>
    <?php
    } 
    ?>
    
    <?php
    if(count($modelContactos)>0){
    ?>
    <hr>
    <div class="row" align="center">
        <label style="color:#1F0656">CONTACTOS:</label>
    </div>
    <div class="row">
        <div class="col-md-3">
            <label for="" class="">NOMBRE</label>   
        </div>
        <div class="col-md-3">
            <label for="" class="">CORREO ELECTRONICO</label>   
        </div>
        <div class="col-md-2">
            <label for="" class="">TELEFONO/CELULAR</label>   
        </div>
        <div class="col-md-2">
            <label for="" class="">TITULO</label>   
        </div>
        <div class="col-md-2">
            <label for="" class="">COMENTARIO</label>   
        </div>
                 
    </div>
    <?php
    }
    else{
    ?>
    <hr>
    <div class="row" align="center">
        <label style="color:#1F0656">CONTACTOS: SIN REGISTROS</label>
    </div>   
    <?php
    }
    foreach ($modelContactos as $key => $value) {
    ?>
    <div class="row">
        <div class="col-md-3">
            <input type="text" value="<?=$value['nombre']?>" class="form-control mayusculas" readonly >   
        </div>
        <div class="col-md-3">
            <input type="text" value="<?=$value['correo']?>" class="form-control mayusculas" readonly>   
        </div>
        <div class="col-md-2">
            <input type="text" value="<?=$value['telefono1']?>" class="form-control mayusculas" readonly>   
        </div>
        <div class="col-md-2">
            <input type="text" value="<?=$value['titulo']?>" class="form-control mayusculas" readonly>   
        </div>
        <div class="col-md-2">
            <input type="text" value="<?=$value['comentarios']?>" class="form-control mayusculas" readonly>   
        </div>         
    </div>
    <br>
    <?php
    } 
    ?>
    

    </form>
  </div>
  <div class="panel-footer">Datos del cliente xmobile V2</div>
</div>


<!--div class="col-md-9">
   <div class="table-responsive">
   <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProviderp,
        'filterModel' => $searchModelp,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            //'clienteId',
            //'formaPago',
				[
				'header'=>'EST',
				'format' => 'raw',
				'value'=>function($data){
					if($data->estadoEnviado == 1){
						return '<span class="label label-success">Sap</span>';
					}else{
						return '<span class="label label-warning">Midd</span>';
					}
				}
			],
            'recibo',
			[
				'attribute'=>'formaPago',
				'filter'=>["PEF"=>"PEF","PCC"=>"PCC","PBT"=>"PBT","PCH"=>"PCH"],
			],
			'monto',
			'fecha',
            'hora',
			'bancoCode',
			'numComprobante',			
			'numTarjeta',
			'numAhorro',
			'numCheque',
			'tipoCambioDolar',
            //'estadoEnviado',
            'equipoId',
            'ccost',
			
            //'id',
           // 'documentoId',
           // 
            //'moneda',
            //'numAutorizacion',
            //'ci',
            //'cambio',
            // 'monedaDolar',
			

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

  
  </div>
</div -->

   
</div>
