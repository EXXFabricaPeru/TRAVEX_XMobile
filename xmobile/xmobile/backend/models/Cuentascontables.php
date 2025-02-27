<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cuentascontables".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property float|null $Balance
 * @property int|null $AccountLevel
 * @property string|null $FatherAccountKey
 * @property string|null $AcctCurrency
 * @property string|null $FormatCode
 * @property string|null $User
 * @property int|null $Status
 * @property string|null $DateUpdate
 */
class Cuentascontables extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cuentascontables';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Balance'], 'number'],
            [['AccountLevel', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['Code', 'Name', 'FatherAccountKey', 'FormatCode', 'User'], 'string', 'max' => 255],
            [['AcctCurrency'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Code' => 'Code',
            'Name' => 'Name',
            'Balance' => 'Balance',
            'AccountLevel' => 'Account Level',
            'FatherAccountKey' => 'Father Account Key',
            'AcctCurrency' => 'Acct Currency',
            'FormatCode' => 'Format Code',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
