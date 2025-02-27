<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "entregascabecera".
 *
* @property int id
* @property int DocEntry
* @property int DocNum
* @property string DocType
* @property string DocDate
* @property string DocDueDate
* @property string CardCode
* @property string CardName
* @property string DocCurrency
* @property string Reference1
* @property string Reference2
* @property string Comments
* @property string JournalMemo
* @property string Address
* @property string Address2
* @property int SalesPersonCode
* @property int CotactPersonCode
* @property string TaxDate
* @property string ShipToCode
* @property string FederalTaxId
* @property string CreationDate
* @property string UpdateDate
* @property string DocumentStatus
* @property int Usuario
* @property int Status
* @property string DateUpdate
 */
class Entregascabecera extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entregascabecera';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','DocEntry','DocNum','SalesPersonCode','CotactPersonCode','Usuario','Status'], 'integer'],
            [['comentarios','DocType','DocDate','DocDueDate','CardCode','CardName','DocCurrency','Reference1','Reference2',
            'Comments','JournalMemo','Address','Address2','TaxDate','ShipToCode','FederalTaxId',
            'CreationDate','UpdateDate','DocumentStatus'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'DocEntry' => 'DocEntry',
            'DocNum'  => 'DocNum',
            'DocType'  => 'DocType',
            'DocDate'  => 'DocDate',
            'DocDueDate'  => 'DocDueDate',
            'CardCode'  => 'CardCode',
            'CardName'  => 'CardName',
            'DocCurrency'  => 'DocCurrency',
            'Reference1'  => 'Reference1',
            'Reference2'  => 'Reference2',
            'Comments'  => 'Comments',
            'JournalMemo'  => 'JournalMemo',
            'Address'  => 'Address',
            'Address2'  => 'Address2',
            'SalesPersonCode'  => 'SalesPersonCode',
            'CotactPersonCode'  => 'CotactPersonCode',
            'TaxDate'  => 'TaxDate',
            'ShipToCode'  => 'ShipToCode',
            'FederalTaxId'  => 'FederalTaxId',
            'CreationDate'  => 'CreationDate',
            'UpdateDate'  => 'UpdateDate',
            'DocumentStatus'  => 'DocumentStatus',
            'Usuario'  => 'Usuario',
            'Status'  => 'Status',
            'DateUpdate'  => 'DateUpdate'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
