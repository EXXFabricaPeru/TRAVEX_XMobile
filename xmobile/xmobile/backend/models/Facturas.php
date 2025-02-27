<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "facturas".
 *
 * @property int $id
 * @property int $DocEntry
 * @property string $DocNum
 * @property string $DocDate
 * @property string $DocDueDate
 * @property string $CardCode
 * @property string $CardName
 * @property string $DocTotal
 * @property string $DocCurrency
 * @property string $JournalMemo
 * @property int $PaymentGroupCode
 * @property string $DocTime
 * @property int $Series
 * @property string $TaxDate
 * @property string $CreationDate
 * @property string $UpdateDate
 * @property int $FinancialPeriod
 * @property string $UpdateTime
 * @property string $U_LB_NumeroFactura
 * @property string $U_LB_NumeroAutorizac
 * @property string $U_LB_FechaLimiteEmis
 * @property string $U_LB_CodigoControl
 * @property string $U_LB_EstadoFactura
 * @property string $U_LB_RazonSocial
 * @property int $U_LB_TipoFactura
 * @property string $User
 * @property int $Status
 * @property string $DateUpdate
 * @property string $U_XMB_repartidor
 * @property string $U_XMB_AUX1
 */
class Facturas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'facturas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['DocEntry', 'PaymentGroupCode', 'Series', 'FinancialPeriod', 'U_LB_TipoFactura', 'Status'], 'integer'],
            [['DocDate', 'DocDueDate', 'DocTime', 'TaxDate', 'CreationDate', 'UpdateDate', 'UpdateTime', 'U_LB_FechaLimiteEmis', 'U_LB_CodigoControl', 'DateUpdate'], 'safe'],
            [['DocTotal'], 'number'],
            [['DocNum', 'CardCode', 'CardName', 'JournalMemo', 'U_LB_NumeroFactura', 'U_LB_NumeroAutorizac', 'U_LB_RazonSocial', 'User', 'U_XMB_repartidor', 'U_XMB_AUX1'], 'string', 'max' => 255],
            [['DocCurrency', 'U_LB_EstadoFactura'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'DocEntry' => 'Doc Entry',
            'DocNum' => 'Doc Num',
            'DocDate' => 'Doc Date',
            'DocDueDate' => 'Doc Due Date',
            'CardCode' => 'Card Code',
            'CardName' => 'Card Name',
            'DocTotal' => 'Doc Total',
            'DocCurrency' => 'Doc Currency',
            'JournalMemo' => 'Journal Memo',
            'PaymentGroupCode' => 'Payment Group Code',
            'DocTime' => 'Doc Time',
            'Series' => 'Series',
            'TaxDate' => 'Tax Date',
            'CreationDate' => 'Creation Date',
            'UpdateDate' => 'Update Date',
            'FinancialPeriod' => 'Financial Period',
            'UpdateTime' => 'Update Time',
            'U_LB_NumeroFactura' => 'U Lb Numero Factura',
            'U_LB_NumeroAutorizac' => 'U Lb Numero Autorizac',
            'U_LB_FechaLimiteEmis' => 'U Lb Fecha Limite Emis',
            'U_LB_CodigoControl' => 'U Lb Codigo Control',
            'U_LB_EstadoFactura' => 'U Lb Estado Factura',
            'U_LB_RazonSocial' => 'U Lb Razon Social',
            'U_LB_TipoFactura' => 'U Lb Tipo Factura',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'U_XMB_repartidor' => 'U XMB repartidor',
            'U_XMB_AUX1' => 'U XMB AUX1',
        ];
    }

    public function getFacturasproductos(){
      return $this->hasMany(Facturas::className(),["DocEntry" => "DocEntry"]);
    }
}
