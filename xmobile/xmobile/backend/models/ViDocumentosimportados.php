<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_documentosimportados".
 *
 * @property int $id
 * @property int|null $DocEntry
 * @property string|null $DocNum
 * @property string $DocType
 * @property string|null $DocDate
 * @property string|null $DocDueDate
 * @property string|null $CardCode
 * @property string|null $CardName
 * @property float|null $DocTotal
 * @property int $Status
 * @property string|null $DateUpdate
 * @property string|null $ReserveInvoice
 * @property string|null $PaidtoDate
 * @property string|null $Saldo
 * @property resource|null $Pendiente
 * @property string|null $centrocosto
 * @property string|null $unidadnegocio
 */
class ViDocumentosimportados extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_documentosimportados';
    }
    public static function primaryKey() {
        return ['id'];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'DocEntry', 'Status'], 'integer'],
            [['DocDate', 'DocDueDate', 'DateUpdate'], 'safe'],
            [['DocTotal'], 'number'],
            [['unidadnegocio'], 'string'],
            [['DocNum', 'CardCode', 'CardName', 'centrocosto'], 'string', 'max' => 255],
            [['DocType'], 'string', 'max' => 3],
            [['ReserveInvoice'], 'string', 'max' => 5],
            [['PaidtoDate', 'Saldo'], 'string', 'max' => 12],
            [['Pendiente'], 'string', 'max' => 33],
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
            'DocType' => 'Doc Type',
            'DocDate' => 'Doc Date',
            'DocDueDate' => 'Doc Due Date',
            'CardCode' => 'Card Code',
            'CardName' => 'Card Name',
            'DocTotal' => 'Doc Total',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'ReserveInvoice' => 'Reserve Invoice',
            'PaidtoDate' => 'Paidto Date',
            'Saldo' => 'Saldo',
            'Pendiente' => 'Pendiente',
            'centrocosto' => 'Centrocosto',
            'unidadnegocio' => 'Unidadnegocio',
        ];
    }
}
