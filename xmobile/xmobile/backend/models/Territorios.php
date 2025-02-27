<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "territorios".
 *
 * @property int $id
 * @property int $TerritoryID
 * @property string $Description
 * @property int $LocationIndex
 * @property string $Inactive
 * @property int $Parent
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 */
class Territorios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'territorios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['TerritoryID', 'LocationIndex', 'Parent', 'User', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['Description'], 'string', 'max' => 255],
            [['Inactive'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'TerritoryID' => 'Territory ID',
            'Description' => 'Description',
            'LocationIndex' => 'Location Index',
            'Inactive' => 'Inactive',
            'Parent' => 'Parent',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
