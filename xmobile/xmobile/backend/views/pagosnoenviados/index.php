<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\PagosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Pagos no enviados');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pagos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php Html::a(Yii::t('app', 'Create Pagos'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'header'=>'EST',
                'format' => 'raw',
                'value'=>function($data){
                    if($data->estado == 2){
                       return '<span class="label label-warning">Midd</span>';
                    }
                    //return '<span class="label label-success">Sap</span>';
                    //return '<span class="label label-warning">Midd</span>';
                    //return '<span class="label label-danger">Cancel</span>';
                    
                }
            ],      
            'nro_recibo',       
            'monto_total',  
            'fecha', 
            'cliente_carcode',
            [
                'attribute' => 'cliente_carcode',
                'format' => 'raw',
                'filter' =>'',
                'value' => function($data) {
                    $datos = backend\models\Clientes::find()->where("CardCode='".$data->cliente_carcode."'")->one();
                    return $datos->CardName;
                }
            ],
            [
                'attribute' => 'otpp',
                'format' => 'raw',
                'filter' =>['1'=>'PAGO AL CONTADO','2'=>'COBRO DE DEUDA','3'=>'PAGO A CUENTA'],
                'value' => function($data) {
                    $mensaje="";
                    switch ($data->otpp) {
                        case '1':
                            $mensaje='PAGO AL CONTADO';
                            break;
                        case '2':
                            $mensaje='COBRO DE DEUDA';
                            break;
                        case '3':
                            $mensaje='PAGO A CUENTA';
                            break;
                        default:
                            # code...
                            break;
                    }
                    return $mensaje;
                }
            ],
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
            [
                'attribute' => 'usuario',
                'format' => 'raw',
                'filter' => ArrayHelper::map(backend\models\User::find()->orderby('username asc')->asArray()->all(), 'id', 'username'),
                'value' => function($data) {
                    $m = backend\models\User::findOne($data->usuario);
                    return $m->username;
                }
            ], 
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
