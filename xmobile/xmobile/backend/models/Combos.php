<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "combos".
 *
 * @property int $id
 * @property string $TreeCode
 * @property string $TreeType
 * @property string $Quantity
 * @property int $PriceList
 * @property string $Warehouse
 * @property string $PlanAvgProdSize
 * @property string $HideBOMComponentsInPrintout
 * @property string $ProductDescription
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 *
 * @property Combosdetalle[] $combosdetalles
 */
class Combos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'combos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Quantity', 'PlanAvgProdSize'], 'number'],
            [['PriceList', 'User', 'Status'], 'integer'],
            [['ProductDescription'], 'string'],
            [['DateUpdate'], 'safe'],
            [['TreeCode', 'TreeType'], 'string', 'max' => 100],
            [['Warehouse'], 'string', 'max' => 50],
            [['HideBOMComponentsInPrintout'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'TreeCode' => 'Tree Code',
            'TreeType' => 'Tree Type',
            'Quantity' => 'Quantity',
            'PriceList' => 'Price List',
            'Warehouse' => 'Warehouse',
            'PlanAvgProdSize' => 'Plan Avg Prod Size',
            'HideBOMComponentsInPrintout' => 'Hide Bom Components In Printout',
            'ProductDescription' => 'Product Description',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCombosdetalles()
    {
        return $this->hasMany(Combosdetalle::className(), ['ParentItem' => 'TreeCode']);
    }
}
