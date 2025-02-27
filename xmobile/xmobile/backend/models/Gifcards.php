<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "gifcards".
 *
 * @property string $id
 * @property string $Code
 * @property string $Amount
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 */
class Gifcards extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gifcards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Code', 'Amount', 'User', 'Status', 'DateUpdate'], 'required'],
            [['User', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['Code', 'Amount'], 'string', 'max' => 255],
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
            'Amount' => 'Amount',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */

}
