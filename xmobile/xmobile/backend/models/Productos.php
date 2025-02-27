<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "productos".
 *
 * @property int $id
 * @property string $ItemCode
 * @property string $ItemName
 * @property string $ItemsGroupsCode
 * @property string $ForeignName
 * @property string $CustomsGroupCode
 * @property string $BarCode
 * @property string $PurchaseItem
 * @property string $SalesItem
 * @property string $InventoryItem
 * @property string $UserText
 * @property int $SerialNum
 * @property string $QuantityOnStock
 * @property string $QuantityOrderedFromVendors
 * @property string $QuantityOrderedByCustomers
 * @property int $ManageSerialNumbers
 * @property int $ManageBatchNumbers
 * @property string $SalesUnit
 * @property double $SalesUnitLength
 * @property double $SaleUnitWidth
 * @property double $SalesUnitHeight
 * @property string $SalesUnitVolume
 * @property string $PurchaseUnit
 * @property string $DefaultWarehouse
 * @property string $ManageStockByWarehouse
 * @property int $ForceSelectionOfSerialNumber
 * @property string $Series
 * @property string $UoMGroupEntry
 * @property string $DefaultSalesUoMEntry
 * @property string $User
 * @property string $Status
 * @property string $DateUpdate
 * @property string $Manufacturer
 * @property string $U_XM_ICEtipo
 * @property double $U_XM_ICEPorcentual
 * @property  double $U_XM_ICEEspecifico
 * @property string $NoDiscounts
 * @property string $producto_std1
 * @property string $producto_std2
 * @property string $producto_std3
 * @property string $producto_std4
 * @property string $producto_std5
 * @property string $producto_std6
 * @property string $producto_std7
 * @property string $producto_std8
 * @property string $producto_std9
 * @property string $producto_std10
 *
 * @property Detalledocumentos[] $detalledocumentos
 * @property Lotes[] $lotes
 * @property Productosalmacenes[] $productosalmacenes
 * @property Productosprecios[] $productosprecios
 * @property string ItemsGroupCode
 * @property float SalesUnitWidth
 */
class Productos extends ActiveRecord
{

  /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*[['SerialNum', 'ManageSerialNumbers', 'ManageBatchNumbers', 'ForceSelectionOfSerialNumber'], 'integer'],
            [['SalesUnitLength', 'SalesUnitWidth', 'SalesUnitHeight'], 'number'],
            [['DateUpdate'], 'safe'],
            [['ItemCode', 'ItemName', 'ItemsGroupCode', 'ForeignName', 'CustomsGroupCode', 'BarCode', 'PurchaseItem', 'SalesItem', 'InventoryItem', 'UserText', 'QuantityOnStock', 'QuantityOrderedFromVendors', 'QuantityOrderedByCustomers', 'SalesUnit', 'SalesUnitVolume', 'PurchaseUnit', 'DefautlWarehouse', 'ManageStockByWarehouse', 'Series', 'UoMGroupEntry', 'DefaultSalesUoMEntry', 'User', 'Status'], 'string', 'max' => 255],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ItemCode' => 'Item Code',
            'ItemName' => 'Item Name',
            'ItemsGroupsCode' => 'Items Groups Code',
            'ForeignName' => 'Foreign Name',
            'CustomsGroupCode' => 'Customs Group Code',
            'BarCode' => 'Bar Code',
            'PurchaseItem' => 'Purchase Item',
            'SalesItem' => 'Sales Item',
            'InventoryItem' => 'Inventory Item',
            'UserText' => 'User Text',
            'SerialNum' => 'Serial Num',
            'QuantityOnStock' => 'Quantity On Stock',
            'QuantityOrderedFromVendors' => 'Quantity Ordered From Vendors',
            'QuantityOrderedByCustomers' => 'Quantity Ordered By Customers',
            'ManageSerialNumbers' => 'Manage Serial Numbers',
            'ManageBatchNumbers' => 'Manage Batch Numbers',
            'SalesUnit' => 'Sales Unit',
            'SalesUnitLength' => 'Sales Unit Length',
            'SaleUnitWidth' => 'Sale Unit Width',
            'SalesUnitHeight' => 'Sales Unit Height',
            'SalesUnitVolume' => 'Sales Unit Volume',
            'PurchaseUnit' => 'Purchase Unit',
            'DefautlWarehouse' => 'Defautl Warehouse',
            'ManageStockByWarehouse' => 'Manage Stock By Warehouse',
            'ForceSelectionOfSerialNumber' => 'Force Selection Of Serial Number',
            'Series' => 'Series',
            'UoMGroupEntry' => 'Uo M Group Entry',
            'DefaultSalesUoMEntry' => 'Default Sales Uo M Entry',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'producto_std1' => 'producto estandar 1',
            'producto_std2' => 'producto estandar 2',
            'producto_std3' => 'producto estandar 3',
            'producto_std4' => 'producto estandar 4',
            'producto_std5' => 'producto estandar 5',
            'producto_std6' => 'producto estandar 6',
            'producto_std7' => 'producto estandar 7',
            'producto_std8' => 'producto estandar 8',
            'producto_std9' => 'producto estandar 9',
            'producto_std10' => 'producto estandar 10'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalledocumentos()
    {
        return $this->hasMany(Detalledocumentos::className(), ['ItemCode' => 'ItemCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLotes()
    {
        return $this->hasMany(Lotes::className(), ['ItemCode' => 'ItemCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosalmacenes()
    {
        return $this->hasMany(Productosalmacenes::className(), ['IdProducto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosprecios()
    {
        return $this->hasMany(Productosprecios::className(), ['IdProducto' => 'id']);
    }

    
}
