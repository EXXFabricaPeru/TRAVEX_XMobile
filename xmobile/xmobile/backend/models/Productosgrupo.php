<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "productosgrupo".
 *
 * @property int $id
 * @property string|null $PriceDifferencesAccount
 * @property string|null $StockInflationAdjustAccount
 * @property string|null $ExchangeRateDifferencesAccount
 * @property string|null $IncreasingAccount
 * @property string|null $StockInflationOffsetAccount
 * @property string|null $PurchaseOffsetAccount
 * @property string|null $WIPMaterialVarianceAccount
 * @property string|null $PurchaseAccount
 * @property string|null $ReturningAccount
 * @property string|null $CostInflationAccount
 * @property string|null $ExpensesAccount
 * @property string|null $RevenuesAccount
 * @property string|null $TransfersAccount
 * @property string|null $CostInflationOffsetAccount
 * @property string|null $InventoryAccount
 * @property string|null $DecreaseGLAccount
 * @property int|null $Number
 * @property string|null $GoodsClearingAccount
 * @property string|null $IncreaseGLAccount
 * @property string|null $ForeignRevenuesAccount
 * @property string|null $WIPMaterialAccount
 * @property string|null $ShippedGoodsAccount
 * @property string|null $ExemptRevenuesAccount
 * @property string|null $DecreasingAccount
 * @property string|null $VATInRevenueAccount
 * @property string|null $VarianceAccount
 * @property string|null $EUExpensesAccount
 * @property string|null $ForeignExpensesAccount
 * @property string|null $GroupName
 * @property string|null $NegativeInventoryAdjustmentAccount
 * @property string|null $WHIncomingCenvatAccount
 * @property string|null $WHOutgoingCenvatAccount
 * @property string|null $StockInTransitAccount
 * @property string|null $WipOffsetProfitAndLossAccount
 * @property string|null $InventoryOffsetProfitAndLossAccount
 * @property string|null $PurchaseBalanceAccount
 * @property int|null $User
 * @property int|null $Status
 * @property string|null $DateUpdate
 */
class Productosgrupo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productosgrupo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Number', 'User', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['PriceDifferencesAccount', 'StockInflationAdjustAccount', 'ExchangeRateDifferencesAccount', 'IncreasingAccount', 'StockInflationOffsetAccount', 'PurchaseOffsetAccount', 'WIPMaterialVarianceAccount', 'PurchaseAccount', 'ReturningAccount', 'CostInflationAccount', 'ExpensesAccount', 'RevenuesAccount', 'TransfersAccount', 'CostInflationOffsetAccount', 'InventoryAccount', 'DecreaseGLAccount', 'GoodsClearingAccount', 'IncreaseGLAccount', 'ForeignRevenuesAccount', 'WIPMaterialAccount', 'ShippedGoodsAccount', 'ExemptRevenuesAccount', 'DecreasingAccount', 'VATInRevenueAccount', 'VarianceAccount', 'EUExpensesAccount', 'ForeignExpensesAccount', 'GroupName', 'NegativeInventoryAdjustmentAccount', 'WHIncomingCenvatAccount', 'WHOutgoingCenvatAccount', 'StockInTransitAccount', 'WipOffsetProfitAndLossAccount', 'InventoryOffsetProfitAndLossAccount', 'PurchaseBalanceAccount'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'PriceDifferencesAccount' => 'Price Differences Account',
            'StockInflationAdjustAccount' => 'Stock Inflation Adjust Account',
            'ExchangeRateDifferencesAccount' => 'Exchange Rate Differences Account',
            'IncreasingAccount' => 'Increasing Account',
            'StockInflationOffsetAccount' => 'Stock Inflation Offset Account',
            'PurchaseOffsetAccount' => 'Purchase Offset Account',
            'WIPMaterialVarianceAccount' => 'Wip Material Variance Account',
            'PurchaseAccount' => 'Purchase Account',
            'ReturningAccount' => 'Returning Account',
            'CostInflationAccount' => 'Cost Inflation Account',
            'ExpensesAccount' => 'Expenses Account',
            'RevenuesAccount' => 'Revenues Account',
            'TransfersAccount' => 'Transfers Account',
            'CostInflationOffsetAccount' => 'Cost Inflation Offset Account',
            'InventoryAccount' => 'Inventory Account',
            'DecreaseGLAccount' => 'Decrease Gl Account',
            'Number' => 'Number',
            'GoodsClearingAccount' => 'Goods Clearing Account',
            'IncreaseGLAccount' => 'Increase Gl Account',
            'ForeignRevenuesAccount' => 'Foreign Revenues Account',
            'WIPMaterialAccount' => 'Wip Material Account',
            'ShippedGoodsAccount' => 'Shipped Goods Account',
            'ExemptRevenuesAccount' => 'Exempt Revenues Account',
            'DecreasingAccount' => 'Decreasing Account',
            'VATInRevenueAccount' => 'Vat In Revenue Account',
            'VarianceAccount' => 'Variance Account',
            'EUExpensesAccount' => 'Eu Expenses Account',
            'ForeignExpensesAccount' => 'Foreign Expenses Account',
            'GroupName' => 'Group Name',
            'NegativeInventoryAdjustmentAccount' => 'Negative Inventory Adjustment Account',
            'WHIncomingCenvatAccount' => 'Wh Incoming Cenvat Account',
            'WHOutgoingCenvatAccount' => 'Wh Outgoing Cenvat Account',
            'StockInTransitAccount' => 'Stock In Transit Account',
            'WipOffsetProfitAndLossAccount' => 'Wip Offset Profit And Loss Account',
            'InventoryOffsetProfitAndLossAccount' => 'Inventory Offset Profit And Loss Account',
            'PurchaseBalanceAccount' => 'Purchase Balance Account',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
