<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tipopapel".
 *
 * @property int $id
 * @property string $nombre Nombre
 * @property string|null $descripcion Descripción
 * @property string|null $formato
 */
class Tipopapel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipopapel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'unique'],
            [['nombre'], 'string', 'max' => 100, 'min' => 3],
            [['descripcion'], 'string', 'max' => 150],
            [['formato'], 'integer']
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
            'descripcion' => 'Descripcion',
            'formato' => 'formato'
        ];
    }
}
