<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_usuariopersona".
 *
 * @property int $id
 * @property string $username
 * @property string|null $nombreCompleto
 */
class Viusuariopersona extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_usuariopersona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username'], 'required'],
            [['username'], 'string', 'max' => 255],
            [['nombreCompleto'], 'string', 'max' => 511],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'nombreCompleto' => 'Nombre Completo',
        ];
    }
}
