<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "objetivosmes".
 *
 * @property int $id
 * @property int $idUser
 * @property int $Month
 * @property int $Year
 * @property string $Amount
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 */
class Objetivosmes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'objetivosmes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idUser', 'Month', 'Year', 'User', 'Status'], 'integer'],
            [['Amount'], 'number'],
            [['DateUpdate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idUser' => 'Id User',
            'Month' => 'Month',
            'Year' => 'Year',
            'Amount' => 'Amount',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
