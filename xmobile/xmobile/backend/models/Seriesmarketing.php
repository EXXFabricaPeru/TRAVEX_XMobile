<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "seriesmarketing".
 *
 * @property int $id
 * @property string $DocumentId
 * @property int $SystemNumber
 * @property string $SerialNumber
 * @property string $ItemCode
 * @property int $Status
 * @property int $User
 * @property string $DateUpdate
 */
class Seriesmarketing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seriesmarketing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['SystemNumber', 'Status', 'User'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['DocumentId', 'SerialNumber', 'ItemCode'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'DocumentId' => 'Document ID',
            'SystemNumber' => 'System Number',
            'SerialNumber' => 'Serial Number',
            'ItemCode' => 'Item Code',
            'Status' => 'Status',
            'User' => 'User',
            'DateUpdate' => 'Date Update',
        ];
    }
}
