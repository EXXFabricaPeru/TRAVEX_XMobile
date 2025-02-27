<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "facturasproductos".
 *
 * @property int $id
 * @property int $LineNum
 * @property string $ItemCode
 * @property string $ItemDescription
 * @property string $Quantity
 * @property string $Price
 * @property string $PriceAfterVAT
 * @property string $Currency
 * @property string $Rate
 * @property string $LineTotal
 * @property string $TaxTotal
 * @property string $UnitPrice
 * @property int $DocEntry
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 * @property int $U_XMB_CANTREP
 * @property string $U_XMB_ALMREP
 * @property string $U_XMB_LOTEREP
 * @property string $U_XMB_SERIEREP
 */
class Facturasproductos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'facturasproductos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'LineNum', 'DocEntry', 'User', 'Status', 'Status'], 'U_XMB_CANTREP'],
            [['Quantity', 'Price', 'PriceAfterVAT', 'Rate', 'LineTotal', 'TaxTotal', 'UnitPrice'], 'number'],
            [['DateUpdate'], 'safe'],
            [['ItemCode', 'ItemDescription', 'U_XMB_ALMREP', 'U_XMB_LOTEREP', 'U_XMB_SERIEREP'], 'string', 'max' => 255],
            [['Currency'], 'string', 'max' => 5],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'LineNum' => 'Line Num',
            'ItemCode' => 'Item Code',
            'ItemDescription' => 'Item Description',
            'Quantity' => 'Quantity',
            'Price' => 'Price',
            'PriceAfterVAT' => 'Price After Vat',
            'Currency' => 'Currency',
            'Rate' => 'Rate',
            'LineTotal' => 'Line Total',
            'TaxTotal' => 'Tax Total',
            'UnitPrice' => 'Unit Price',
            'DocEntry' => 'Doc Entry',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'U_XMB_CANTREP' => 'U_XMB_CANTREP',
            'U_XMB_ALMREP' => 'U_XMB_ALMREP',
            'U_XMB_LOTEREP' => 'U_XMB_LOTEREP',
            'U_XMB_SERIEREP' => 'U_XMB_SERIEREP',
        ];
    }
}
