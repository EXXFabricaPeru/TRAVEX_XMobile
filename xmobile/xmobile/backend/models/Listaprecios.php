<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "listaprecios".
 *
 * @property int $id
 * @property int $GroupNum
 * @property string $BasePriceList
 * @property int $PriceListNo
 * @property string $PriceListName
 * @property string $DefaultPrimeCurrency
 * @property string $User
 * @property string $Status
 * @property string $DateUpdate
 *
 * @property Productosprecios[] $productosprecios
 */
class Listaprecios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'listaprecios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*  [['GroupNum', 'PriceListNo'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['BasePriceList', 'PriceListName', 'DefaultPrimeCurrency', 'User', 'Status','IsGrossPrice','Active'], 'string', 'max' => 255],
            */
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'GroupNum' => 'Group Num',
            'BasePriceList' => 'Base Price List',
            'PriceListNo' => 'Price List No',
            'PriceListName' => 'Price List Name',
            'DefaultPrimeCurrency' => 'Default Prime Currency',
            'IsGrossPrice' => 'Precio Bruto',
            'Active' => 'Estado',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosprecios()
    {
        return $this->hasMany(Productosprecios::className(), ['IdListaPrecios' => 'id']);
    }
}
