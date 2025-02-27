<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "productosalternativos".
 *
 * @property int $id
 * @property string $ItemCode
 * @property string $ItemCodeAlternative
 * @property string $ItemName
 * @property string $Quantity
 * @property string $WareHouse
 * @property string $Price
 * @property string $Currency
 * @property int $PriceList
 * @property int $ChildNum 
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 * @property string $create_at
 * @property string $update_at
 * @property string ComboCode
 *
 */
class Productosalternativos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productosalternativos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Quantity', 'Price'], 'number'],
            [['PriceList', 'ChildNum', 'User', 'Status'], 'integer'],
            [['ItemCode', 'ItemCodeAlternative', 'ItemName', 'WareHouse', 'ComboCode'], 'string'],
            [['DateUpdate'], 'safe'],
            [['ItemCode', 'ItemCodeAlternative'], 'string', 'max' => 100],
            [['Warehouse'], 'string', 'max' => 50],
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
            'ItemCodeAlternative' => 'Item Code Alternative',
            'Quantity' => 'Quantity',
            'WareHouse' => 'Ware House',
            'Price' => 'Price',
            'Currency' => 'Currency',
            'PriceList' => 'Price List',
            'ChildNum' => 'Child Number',            
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
