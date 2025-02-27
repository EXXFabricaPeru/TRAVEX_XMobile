<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "gestionbancos".
 *
 * @property int $id
 * @property string $BankCode
 * @property string $BankName
 * @property int $CountryCode
 */
class Gestionbancos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gestionbancos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['BankCode', 'BankName', 'CountryCode'], 'required'],
            [['CountryCode'], 'integer'],
            [['BankCode'], 'string', 'max' => 10],
            [['BankName'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'BankCode' => 'Bank Code',
            'BankName' => 'Bank Name',
            'CountryCode' => 'Country Code',
        ];
    }
}
