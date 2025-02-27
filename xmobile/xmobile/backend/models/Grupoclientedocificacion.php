<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "grupoclientedocificacion".
 *
 * @property int $id
 * @property string $nombre Nombre
 */
class Grupoclientedocificacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grupoclientedocificacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
        ];
    }
}
