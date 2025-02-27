<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model backend\models\Cabeceradocumentos */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Cabeceradocumentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cabeceradocumentos-view">
        <?php Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
<div class="row">
	<div class="col-md-3">
  <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
		    'CardCode',
            'CardName',
            'DocEntry',
			'idDocPedido',
			'DocTotal',
			'DocCur',
			'DocType',
            //'idUser',
            [
                'attribute' => 'Usuario',
                'format' => 'raw',
                'filter' =>'',
                'value' => function($data) {
                    $m = backend\models\User::findOne($data->idUser);
                    return $m->username;
                }
            ],
            //'DocNum',
			'fecharegistro',
            'fechaupdate',
            'fechasend',
			'U_LB_NumeroFactura',
            //'id',
            //'estado',
            [
                        'attribute'=>'ESTADO',
                        'format' => 'raw',
                        'value'=>function($data){
                            if($data->estado == 3 AND $data->canceled==0){
                                return '<span class="label label-success">Sap</span>';
                            }
                           /* else if($data->estadoEnviado == 0){
                                return '<span class="label label-warning">Midd</span>';
                            }*/
                            else if($data->estado == 3 AND $data->canceled==3){
                                return '<span class="label label-danger">Cancelo</span>';
                            }/*
                            else {
                                return '<span class="label label-danger">Elim</span>';
                            }*/
                        }
                    ], 
            //'gestion',
            //'mes',
            'correlativo',
            //'rowNum',
            'DocTotalPay',
            
             [
                'attribute' => 'Condición de pago',
                'format' => 'raw',
                'filter' =>'',
                'value' => function($data) {
                    $condicionespagos = backend\models\Condicionespagos::findOne($data->PayTermsGrpCode);
                    return $condicionespagos->PaymentTermsGroupName;
                }
            ],
            'TotalDiscMonetary',
            'TotalDiscPrcnt',
			'U_4NIT',
            'U_4RAZON_SOCIAL',
            'U_LATITUD',
            'U_LONGITUD',
			'DocDate',
            'DocDueDate',
            'DocNumSAP',
            'UNumFactura',
            'ControlCode',
            //'canceled',
            //'Printed',
            //'DocStatus',
            //'NumAtCard',
            //'DiscPrcnt',
            //'DiscSum',
            //'DocRate',
            //'PaidToDate',
            //'Ref1',
            //'Ref2',
            'Comments',
            //'JrnlMemo',
            //'GroupNum',
            [
                'attribute' => 'Vendedor SAP',
                'format' => 'raw',
                'filter' =>'',
                'value' => function($data) {//SalesEmployeeCode
                    $vendedores = backend\models\Vendedores::find()->where(['SalesEmployeeCode'=>$data->SlpCode])->one();
                    return $vendedores->SalesEmployeeName;
                }
            ],
            //'SlpCode',
            //'Series',
            //'TaxDate',
            //'LicTradNum',
            //'Address',
            //'UserSign',
            //'CreateDate',
            //'UserSign2',
            //'UpdateDate',
            //'U_4MOTIVOCANCELADO',
            //'U_4SUBTOTAL',
            //'U_4DOCUMENTOORIGEN',
            //'U_4MIGRADOCONCEPTO',
            //'U_4MIGRADO',
            //'PriceListNum',
            //'estadosend',

            //'actsl',
            //'Indicator',
            //'ShipToCode',
            //'ControlAccount',
            
            //'U_LB_EstadoFactura',
            //'U_LB_NumeroAutorizac',
            //'U_LB_TipoFactura',
            //'U_LB_TotalNCND',
            //'Reserve',
            //'clone',
            //'giftcard',
        ],
    ]) ?>

	</div>
		<div class="col-md-9">
		
		
		<div id="tabs">
  <ul>
    <li><a href="#tabs-2">Detalle del documento</a></li>
    <li><a href="#tabs-3">Pagos realizados</a></li>
  </ul>
  <div id="tabs-2">
  <div class="table-responsive">
   <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            //'id',
			'ItemCode',
           // 'Dscription',
			[
				'attribute'=>'Dscription',
				'format' => 'raw',
				'value'=>function($data){
					return $data->Dscription;
				}
			],
           // 'DocEntry',
            //'DocNum',
            'Quantity',
            //'OpenQty',
            'Price',
			'DiscPrcnt',
			'LineTotalPay',
			
			'U_4DESCUENTO',		
			'LineTotal',
			//'DiscMonetary',
			'Currency',
            'WhsCode',
		    'LineNum',
            //'BaseType',
            //'BaseEntry',
            //'BaseLine',
			//'LineStatus',
            //'CodeBars',
            //'PriceAfVAT',
            //'TaxCode',
            //'U_4DESCUENTO',
            //'U_4LOTE',
            //'GrossBase',
            //'idDocumento',
            //'fechaAdd',
            //'unidadid',
            //'tc',
            //'idCabecera',
            //'idProductoPrecio',
            //'DiscMonetary',
            //'LineTotalPay',
            //'SalesUnitLength',
            //'SalesUnitWidth',
            //'SalesUnitHeight',
            //'SalesUnitVolume',
            //'DiscTotalPrcnt',
            //'DiscTotalMonetary',
            //'ICET',
            //'ICEE',
            //'ICEP',
            //'TreeType',
            //'actsl',
            //'BaseDocEntry',
            //'BaseDocLine',
            //'BaseDocType',
            //'BaseDocumentReference',
            //'GrossPrice',
            //'WarehouseCode',
            //'CorrectionInvoiceItem',
            //'Status',
            //'Stock',
            //'TargetAbsEntry',

           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
	</div>
  
  </div>
  <div id="tabs-3">
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
			//'numComprobante',			
			'numTarjeta',
			'numAhorro',
			'numCheque',
			'tipoCambioDolar',
            //'estadoEnviado',
            //'equipoId',
            //'baucher',
			
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
		
		
		
		
		
		
		
	 

		
		</div>
</div>
  
</div>


<?php 
  $script = "$('#tabs').tabs();";    
  $this->registerJs($script); 
?>

