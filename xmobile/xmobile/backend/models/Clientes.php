<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "clientes".
 *
 * @property int $id
 * @property string $CardCode Id BussinesPartner
 * @property string $CardName
 * @property string $CardType
 * @property string $Address
 * @property string $CreditLimit
 * @property string $MaxCommitment Deuda Actual *
 * @property string $DiscountPercent
 * @property int $PriceListNum
 * @property string $SalesPersonCode
 * @property string $Currency
 * @property string $County Ciudad
 * @property string $Country Pais
 * @property string $CurrentAccountBalance Saldo actual
 * @property string $NoDiscounts
 * @property string $PriceMode
 * @property string $FederalTaxId
 * @property string $PhoneNumber
 * @property string $ContactPerson
 * @property string $PayTermsGrpCode
 * @property string $Latitude
 * @property string $Longitude
 * @property int $GroupCode
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 * @property string $img
 * @property string $Industry
 * @property string $cliente_Std1
 * @property string $cliente_Std2
 * @property string $cliente_Std3
 * @property string $cliente_Std4
 * @property string $cliente_Std5
 * @property string $cliente_Std6
 * @property string $cliente_Std7
 * @property string $cliente_Std8
 * @property string $cliente_Std9
 * @property string $cliente_Std10
 *
 * @property Cabeceradocumentos[] $cabeceradocumentos
 * @property Monedas $currency
 * @property Clientesgrupo[] $clientesgrupos
 * @property Clientesimagenes[] $clientesimagenes
 * @property Clientessucursales[] $clientessucursales
 */
class Clientes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'clientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
                [['Latitude', 'Longitude'], 'required'],
                // [['PriceListNum', 'GroupCode', 'User', 'Status'], 'integer'],
                // [['DateUpdate'], 'safe'],
                // [['CardCode', 'CardName', 'CardType', 'Address', 'CreditLimit', 'MaxCommitment', 'DiscountPercent', 'SalesPersonCode', 'Currency', 'County', 'Country', 'CurrentAccountBalance', 'NoDiscounts', 'PriceMode', 'FederalTaxId', 'PhoneNumber', 'ContactPerson', 'PayTermsGrpCode', 'Latitude', 'Longitude', 'img', 'Industry'], 'string', 'max' => 255],
                // [['Currency'], 'exist', 'skipOnError' => true, 'targetClass' => Monedas::className(), 'targetAttribute' => ['Currency' => 'Code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'CardCode' => 'Id BussinesPartner',
            'CardName' => 'Card Name',
            'CardType' => 'Card Type',
            'Address' => 'Address',
            'CreditLimit' => 'Credit Limit',
            'MaxCommitment' => 'Deuda Actual *',
            'DiscountPercent' => 'Discount Percent',
            'PriceListNum' => 'Price List Num',
            'SalesPersonCode' => 'Sales Person Code',
            'Currency' => 'Currency',
            'County' => 'Ciudad',
            'Country' => 'Pais',
            'CurrentAccountBalance' => 'Saldo actual',
            'NoDiscounts' => 'No Discounts',
            'PriceMode' => 'Price Mode',
            'FederalTaxId' => 'Federal Tax ID',
            'PhoneNumber' => 'Phone Number',
            'ContactPerson' => 'Contact Person',
            'PayTermsGrpCode' => 'Pay Terms Grp Code',
            'Latitude' => 'Latitude',
            'Longitude' => 'Longitude',
            'GroupCode' => 'Group Code',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'img' => 'img',
            'Industry' => 'Industry',
            'clientes_sdt1' => 'cliente estandar 1',
            'clientes_sdt2' => 'cliente estandar 2',
            'clientes_sdt3' => 'cliente estandar 3',
            'clientes_sdt4' => 'cliente estandar 4',
            'clientes_sdt5' => 'cliente estandar 5',
            'clientes_sdt6' => 'cliente estandar 6',
            'clientes_sdt7' => 'cliente estandar 7',
            'clientes_sdt8' => 'cliente estandar 8',
            'clientes_sdt9' => 'cliente estandar 9',
            'clientes_sdt10' => 'cliente estandar 10'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCabeceradocumentos() {
        return $this->hasMany(Cabeceradocumentos::className(), ['CardCode' => 'CardCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(Monedas::className(), ['Code' => 'Currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientesgrupos() {
        return $this->hasMany(Clientesgrupo::className(), ['GroupCode' => 'GroupCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientesimagenes() {
        return $this->hasMany(Clientesimagenes::className(), ['IdCliente' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientessucursales() {
        return $this->hasMany(Clientessucursales::className(), ['IdCliente' => 'id']);
    }

}
