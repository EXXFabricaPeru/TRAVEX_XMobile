<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "productosalmacenes".
 *
 * @property int $id
 * @property int $IdProducto
 * @property string $WarehouseCode
 * @property string $InStockCommitedOrdereds
 * @property string $Committed
 * @property string $Ordered
 * @property string $Locked
 * @property string $User
 * @property string $Status
 * @property string $DateUpdate
 *
 * @property Productos $producto
 * @property Almacenes $warehouseCode
 */
class Copiaproductosalmacenes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'copiaproductosalmacenes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*[['IdProducto'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['WarehouseCode', 'InStockCommitedOrdereds', 'Locked', 'User', 'Status'], 'string', 'max' => 255],
            [['IdProducto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['IdProducto' => 'id']],
            [['WarehouseCode'], 'exist', 'skipOnError' => true, 'targetClass' => Almacenes::className(), 'targetAttribute' => ['WarehouseCode' => 'WarehouseCode']],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'IdProducto' => 'Id Producto',
            'WarehouseCode' => 'Warehouse Code',
            'InStockCommitedOrdereds' => 'In Stock Commited Ordereds',
            'Locked' => 'Locked',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Productos::className(), ['id' => 'IdProducto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouseCode()
    {
        return $this->hasOne(Almacenes::className(), ['WarehouseCode' => 'WarehouseCode']);
    }
}
