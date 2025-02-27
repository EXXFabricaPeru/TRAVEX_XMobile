<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "permisos".
 *
 * @property int $id
 * @property int $IdMenuPlatform
 * @property int $IdUser
 * @property string $Key
  * @property int $User
 * @property int $Status
 * @property date $DateUpdate
 */
class Permisos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permisos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'IdMenuPlatform', 'IdUser', 'User', 'Status'], 'integer'],
            [['Key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'IdMenuPlatform' => 'id menu',
            'IdUser' => 'usuario',
            'Key' => 'llave',
            'User' => 'usuario',
            'Status' => 'estado',
            'DateUpdate' => 'fecha',
        ];
    }
}
