<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "motivonoventa".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property string|null $Razon
 * @property int|null $User
 * @property int|null $Status
 * @property string|null $DateUpdate
 */
class Motivonoventa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'motivonoventa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['User', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['Code'], 'string', 'max' => 50],
            [['Name', 'Razon'], 'string', 'max' => 255],
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
            'Razon' => 'Razón',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
