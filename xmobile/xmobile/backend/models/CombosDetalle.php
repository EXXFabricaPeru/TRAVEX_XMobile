<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "combosdetalle".
 *
 * @property int $id
 * @property string $ItemCode
 * @property string $Quantity
 * @property string $Warehouse
 * @property string $Price
 * @property string $Currency
 * @property string $IssueMethod
 * @property string $ParentItem
 * @property int $PriceList
 * @property string $ItemType
 * @property string $AdditionalQuantity
 * @property int $ChildNum
 * @property int $VisualOrder
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 *
 * @property Combos $parentItem
 */
class CombosDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'combosdetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Quantity', 'Price', 'AdditionalQuantity'], 'number'],
            [['PriceList', 'ChildNum', 'VisualOrder', 'User', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['ItemCode'], 'string', 'max' => 255],
            [['Warehouse', 'IssueMethod'], 'string', 'max' => 50],
            [['Currency'], 'string', 'max' => 10],
            [['ParentItem'], 'string', 'max' => 100],
            [['ItemType'], 'string', 'max' => 20],
            [['ParentItem'], 'exist', 'skipOnError' => true, 'targetClass' => Combos::className(), 'targetAttribute' => ['ParentItem' => 'TreeCode']],
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
            'Quantity' => 'Quantity',
            'Warehouse' => 'Warehouse',
            'Price' => 'Price',
            'Currency' => 'Currency',
            'IssueMethod' => 'Issue Method',
            'ParentItem' => 'Parent Item',
            'PriceList' => 'Price List',
            'ItemType' => 'Item Type',
            'AdditionalQuantity' => 'Additional Quantity',
            'ChildNum' => 'Child Num',
            'VisualOrder' => 'Visual Order',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentItem()
    {
        return $this->hasOne(Combos::className(), ['TreeCode' => 'ParentItem']);
    }
}
