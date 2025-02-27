<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "reglatipo".
 *
 * @property int $id
 * @property string $nombre Nombre
 * @property string|null $descripcion Descripción
 */
class Reglatipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reglatipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'string', 'max' => 100],
            [['descripcion'], 'string', 'max' => 150],
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
        ];
    }
}
