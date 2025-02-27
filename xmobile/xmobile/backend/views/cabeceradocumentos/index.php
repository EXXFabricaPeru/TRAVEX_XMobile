<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\Autocomplete;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CabeceradocumentosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Documentos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cabeceradocumentos-index">
    <div id="windowEliminar" style="display: none"> <br/><br/>
        <p class="text-center"><b>Esta seguro de autorizar la anulación del registro?</b></p>
    </div>


    <?php Pjax::begin(); ?>

    <?php
    // echo $this->render('_search', ['model' => $searchModel]); 
    ?>



    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'header'=>'EST',
                'format' => 'raw',
                'value'=>function($data){
                    if($data->estado == 3 AND $data->canceled==0){
                        return '<span class="label label-success">Sap</span>';
                    }
                   /* else if($data->estadoEnviado == 0){
                        return '<span class="label label-warning">Midd</span>';
                    }*/
                    else if($data->estado == 3 AND $data->canceled==3){
                        return '<span class="label label-danger">Cancel</span>';
                    }/*
                    else {
                        return '<span class="label label-danger">Elim</span>';
                    }*/
                }
            ],  
            [
                'attribute' => 'clone',
                'format' => 'raw',
                //'filter' => ['0' => "Origen", "!0" => "Copia"],
                'value' => function($data) {
                    if ($data->clone == '0') {
                        return '<span class="label label-info">ORIGEN</span>';
                    } else {
                        return '<div class=" alert-warning" role="alert">' . $data->clone . '</div>';
                    }
                }
            ],
            //'idDocPedido',
            [
                'attribute' => 'idDocPedido',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->idDocPedido;
                }
            ],
            'TotalDiscMonetary',
            'TotalDiscPrcnt',
            'DocTotalPay',
            'DocTotal',
            //'DocCur',
            [
                'header' => '$',
                'format' => 'raw',
                'value' => function($data) {
                    return '<span class="label label-warning">' . $data->DocCur . '</span>';
                }
            ],
            'DocEntry',
            //'DocNum',
            'DocDate',
            'CardName',
            [
                'attribute' => 'idUser',
                'format' => 'raw',
                'filter' => ArrayHelper::map(backend\models\User::find()->asArray()->all(), 'id', 'username'),
                'value' => function($data) {
                    $m = backend\models\User::findOne($data->idUser);
                    return $m->username;
                }
            ],
            [
                'attribute' => 'DocType',
                'filter' => ["DFA" => "Factura", "DOP" => "Pedido", "DOE" => "Entrega", "DOF" => "Oferta"],
                'value' => function($data) {
                    switch ($data->DocType) {
                        case('DOF'): return 'Oferta';
                        case('DOP'): return 'Pedido';
                        case('DFA'): return 'Factura';
                        case('DOE'): return 'Entrega';
                    }
                }
            ],
            [
                'attribute' => 'Reserve',
                'filter' => ["1" => "Reserva", "0" => "Deutor",],
                'value' => function($data) {
                    if ($data->DocType != 'DFA') {
                        return '   ';
                    }
                    if ($data->Reserve == '1') {
                        return 'Reserva';
                    } else {
                        return 'Deudor';
                    }
                }
            ],
             [
                'attribute' => '',
                'format' => 'raw',
                'value' => function($data) {
                    if($data->anulaAutorizado==1){
                        return '';
                    }
                    else{
                        return ''
                        . '<button title="Autorizar anulación" class="btn-link btn-grid-action-autoriza" value="' . $data->id . '" ><i class="fas fa-trash-alt text-warning"></i></button> '
                        . '';
                    }
                   
                }
            ],
            // ['class' => 'yii\grid\SerialColumn'],
            //'id',
            //'fecharegistro',
            //'DocType',
            //'canceled',
            //'Printed',
            //'DocStatus',
            /* 	[
              "atrribute" => "DocDate",
              "value" => "DocDate",
              "format" => "raw",
              "filter" => DatePicker::widget([
              "model" => $searchModel,
              "attribute" => "DocDate",
              "clientOptions" => [
              "autoclose" => true,
              "format" => "yyyy-m-d"
              ],
              ])
              ], */
            //'DocDueDate',
            //'NumAtCard',
            //'DiscPrcnt',
            //'DiscSum',
            //'DocRate',
            //'PaidToDate',
            //'Ref1',
            //'Ref2',
            //'Comments',
            //'JrnlMemo',
            //'GroupNum',
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
            //'U_4NIT',
            //'U_4RAZON_SOCIAL',
            //'U_LATITUD',
            //'U_LONGITUD',
            //'U_4SUBTOTAL',
            //'U_4DOCUMENTOORIGEN',
            //'U_4MIGRADOCONCEPTO',
            //'U_4MIGRADO',
            //'PriceListNum',
            //'estadosend',
            //'fechaupdate',
            //'fechasend',
            //'id',
            //'estado',
            //'gestion',
            //'mes',
            //'correlativo',
            //'rowNum',
            //'PayTermsGrpCode',
            //'DocNumSAP',
            //'UNumFactura',
            //'ControlCode',
            //'actsl',
            //'Indicator',
            //'ShipToCode',
            //'ControlAccount',
            //'U_LB_NumeroFactura',
            //'U_LB_EstadoFactura',
            //'U_LB_NumeroAutorizac',
            //'U_LB_TipoFactura',
            //'U_LB_TotalNCND',
            //'giftcard',
            //'CardCode',
            //'Reserve',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        //['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/scripts/documentos.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 