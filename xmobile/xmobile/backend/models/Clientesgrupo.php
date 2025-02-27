<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "clientesgrupo".
 *
 * @property int $id
 * @property string $Code
 * @property string $Name
 * @property string $Type
 * @property int $User
 * @property string $Status
 * @property string $DateUpdate
 * @property int $GroupCode
 *
 * @property Clientes $groupCode
 */
class Clientesgrupo extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'clientesgrupo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
                /* [['id'], 'required'],
                  [['id', 'User', 'GroupCode'], 'integer'],
                  [['DateUpdate'], 'safe'],
                  [['Code', 'Name', 'Type', 'Status'], 'string', 'max' => 255],
                  [['id'], 'unique'], */
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'Code' => 'Code',
            'Name' => 'Name',
            'Type' => 'Type',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'GroupCode' => 'Group Code',
        ];
    }

}
