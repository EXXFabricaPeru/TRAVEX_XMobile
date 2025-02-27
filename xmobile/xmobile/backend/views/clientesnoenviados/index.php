<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ClientesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Clientes no enviados');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clientes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            //'id',
            'CardCode',
            'CardName',
            //'CardType',
            //'Address',
            'CreditLimit',
            //'MaxCommitment',
            //'DiscountPercent',
            //'PriceListNum',
            //'SalesPersonCode',
            'Currency',
            //'County',
            //'Country',
            //'CurrentAccountBalance',
            //'NoDiscounts',
            //'PriceMode',
            //'FederalTaxId',
            //'PhoneNumber',
            //'ContactPerson',
            //'PayTermsGrpCode',
            'Latitude',
            'Longitude',
            //'GroupCode',
            //'User',
            //'Status',
            //'DateUpdate',
            'GroupName',
            //'U_XM_DosificacionSocio',
            //'Territory',
            [
                'header' => 'Territorio',
                'format' => 'raw',
                'value' => function($data) {
                    try {
                        $sql = 'SELECT Description FROM territorios WHERE TerritoryID=' . $data->Territory;
                        $resultado = Yii::$app->db->createCommand($sql)->queryOne();
                        return $resultado["Description"];
                    } catch (Exception $e) {
                        return '(no definido)';
                    }
                }
            ],
            //'DiscountRelations',
            //'Mobilecod',
            //'StatusSend',
            //'CardForeignName',
            //'Phone2',
            //'Cellular',
            //'EmailAddress:email',
            //'MailAdress',
            //'Properties1',
            //'Properties2',
            //'Properties3',
            //'Properties4',
            //'Properties5',
            //'Properties6',
            //'Properties7',
            //'FreeText',
            //'img',
            'Industry',
			 ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
