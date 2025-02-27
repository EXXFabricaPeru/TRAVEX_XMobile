<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "seriesproductos".
 *
 * @property int $id
 * @property int $DocEntry
 * @property string $ItemCode
 * @property string $SerialNumber
 * @property int $SystemNumber
 * @property string $AdmissionDate
 * @property int $User
 * @property int $Status
 * @property string $Date
 */
class Seriesproductos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seriesproductos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['DocEntry', 'SystemNumber', 'User', 'Status'], 'integer'],
            [['AdmissionDate', 'Date'], 'safe'],
            [['ItemCode', 'SerialNumber'], 'string', 'max' => 100],
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
            'ItemCode' => 'Item Code',
            'SerialNumber' => 'Serial Number',
            'SystemNumber' => 'System Number',
            'AdmissionDate' => 'Admission Date',
            'User' => 'User',
            'Status' => 'Status',
            'Date' => 'Date',
        ];
    }
}
