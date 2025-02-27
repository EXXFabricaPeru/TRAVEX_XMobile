<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "monedas".
 *
 * @property int $id
 * @property string $Code
 * @property string $Name
 * @property string $DocumentsCode
 * @property string $User
 * @property string $Status
 * @property string $DateUpdate
 *
 * @property Clientes[] $clientes
 * @property Tiposcambio[] $tiposcambios
 * @property Tiposcambio[] $tiposcambios0
 */
class Monedas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monedas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['DateUpdate'], 'safe'],
           // [['Code', 'Name', 'DocumentsCode', 'User', 'Status'], 'string', 'max' => 255],
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
            'DocumentsCode' => 'Documents Code',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientes()
    {
        return $this->hasMany(Clientes::className(), ['Currency' => 'Code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTiposcambios()
    {
        return $this->hasMany(Tiposcambio::className(), ['ExchangeRateFrom' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTiposcambios0()
    {
        return $this->hasMany(Tiposcambio::className(), ['ExchangeRateTo' => 'id']);
    }
}
