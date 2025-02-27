<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "grupoproductodocificacion".
 *
 * @property int $id
 * @property string $nombre Grupo
 */
class Grupoproductodocificacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grupoproductodocificacion';
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
