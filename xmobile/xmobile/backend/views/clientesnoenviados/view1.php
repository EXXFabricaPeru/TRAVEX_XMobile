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
?>
<div class="row">
<div class="col-md-3">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'CardCode',
            'CardName',
            'CardType',
            'Address',
            'CreditLimit',
            'MaxCommitment',
            'DiscountPercent',
            'PriceListNum',
            'SalesPersonCode',
            'Currency',
            'County',
            'Country',
            'CurrentAccountBalance',
            'NoDiscounts',
            'PriceMode',
            'FederalTaxId',
            'PhoneNumber',
            'ContactPerson',
            'PayTermsGrpCode',
            'Latitude',
            'Longitude',
            'GroupCode',
            'User',
            'Status',
            'DateUpdate',
            'GroupName',
            'U_XM_DosificacionSocio',
            'Territory',
            'DiscountRelations',
            'Mobilecod',
            'StatusSend',
            'CardForeignName',
            'Phone2',
            'Cellular',
            'EmailAddress:email',
            'MailAdress',
            'Properties1',
            'Properties2',
            'Properties3',
            'Properties4',
            'Properties5',
            'Properties6',
            'Properties7',
            'FreeText',
            'img',
            'Industry',
        ],
    ]) ?>

</div>
<div class="col-md-9">
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
</div>


</div>
