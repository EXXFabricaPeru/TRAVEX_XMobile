<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "lotes".
 *
 * @property int $id
 * @property string $ItemCode
 * @property string $ItemDescription
 * @property string $ItemStatus
 * @property string $Batch
 * @property string $AdmissionDate
 * @property string $ExpirationDate
 * @property string $Stock
 * @property string $User
 * @property string $Status
 * @property string $DateUpdate
 *
 * @property Productos $itemCode
 */
class Lotes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lotes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['AdmissionDate', 'ExpirationDate', 'DateUpdate'], 'safe'],
            [['ItemCode', 'ItemDescription', 'ItemStatus', 'Batch', 'Stock', 'User', 'Status'], 'string', 'max' => 255],
            [['ItemCode'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['ItemCode' => 'ItemCode']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ItemCode' => 'Item Code',
            'ItemDescription' => 'Item Description',
            'ItemStatus' => 'Item Status',
            'Batch' => 'Batch',
            'AdmissionDate' => 'Admission Date',
            'ExpirationDate' => 'Expiration Date',
            'Stock' => 'Stock',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemCode()
    {
        return $this->hasOne(Productos::className(), ['ItemCode' => 'ItemCode']);
    }
}
