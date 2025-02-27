<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model backend\models\Pagos */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pagos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="pagos-view">

<h1><?= Html::encode($this->title) ?></h1>

<div class="row">
    <div class="col-md-4">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'nro_recibo',
                'fecha',
                'hora',
                'monto_total',
                'cliente_carcode',
                [
                    'attribute' => 'documentoId',
                    'format' => 'raw',
                    'value' => function($data) {
                        if(is_null($data->documentoId) or $data->documentoId=='null')
                            return '-';
                        else
                            return $data->documentoId;
                    }
                ],
                'razon_social',
                'nit',
                'TransId'
            ],
        ]) ?>
    </div><!--fin class 4-->
    <div class="col-md-8">
        <div class="row panel panel-default" >
            <div class="col-md-12">
            <h4 align='center'>Facturas Pagos</h4>
                <div class="table-responsive">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProviderFP,
                        'filterModel' => $searchModelFP,
                        'columns' => [
                            'nro_recibo',
                            'clienteId',
                            'CardName', 
                            'documentoId',
                            'docentry',
                            'monto',
                            'nroFactura',                        
                        //['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-12">
                <h4 align='center'>Medios de Pago</h4> 
                <div class="table-responsive">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProviderMP,
                        'filterModel' => $searchModelMP,
                        'columns' => [
                            'nro_recibo',
                            'formaPago',
                            'monto',
                            'fecha',
                        //['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>      
    </div><!--fin class 8-->
</div>
<div class="row">
    <h4 align='center'>LOG ENVIO</h4>   
    <div class="table-responsive">
        <?=
        GridView::widget([
            'dataProvider' => $dataProviderlog,
            'filterModel' => $searchModellog,
            'columns' => [
                [
                    'attribute' => 'Estado',
                    'format' => 'raw',
                    'value' => function($data) {
                        //if($data->idlog == 24){
                           // return '<span class="label label-success">Sap</span>';
                       // } else {
                            return '<span class="label label-warning">Midd</span>';
                       // }
                    }
                ],
                'fecha',
                'documento',
                [
                    'attribute' => 'envio',
                    'format' => 'raw',
                    'value' => function($data) {
                        $cantidad=strlen($data->envio);
                        $arr1=explode(',"',$data->envio);
                        $linea=1;
                        $envio="";
                        for($i=0;$i<count($arr1);$i++){
                            if($i==$linea){
                                $envio.='<br>';
                                $linea=$linea+2;
                            }
                            $envio.=',"'.$arr1[$i];
                        }
                        $envio=substr($envio, 2);
                        //$envio=substr($envio, 0,-2);
                        return $envio;
                    }
                ],
                [
                    'attribute' => 'respuesta',
                    'format' => 'raw',
                    'value' => function($data) {
                        $cantidad=strlen($data->respuesta);
                        $arr1=explode(',"',$data->respuesta);
                        $linea=1;
                        $respuesta="";
                        for($i=0;$i<count($arr1);$i++){
                            if($i==$linea){
                                $respuesta.='<br>';
                                $linea=$linea+1;
                            }
                            $respuesta.=',"'.$arr1[$i];
                        }
                        $respuesta=substr($respuesta, 2);
                        //$respuesta=substr($respuesta, 0,-2);
                        return $respuesta;
                    }
                ],
                'endpoint',
        
            //['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
            ],
        ]);
        ?>
    </div>
</div>

</div>
