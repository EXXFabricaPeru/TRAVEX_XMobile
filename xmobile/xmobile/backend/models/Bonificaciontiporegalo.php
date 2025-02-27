<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bonificaciontiporegalo".
 *
 * @property int $id
 * @property string|null $descripcion
 * @property int|null $estado
 */
class Bonificaciontiporegalo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonificaciontiporegalo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'integer'],
            [['descripcion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripcion',
            'estado' => 'Estado',
        ];
    }
}
