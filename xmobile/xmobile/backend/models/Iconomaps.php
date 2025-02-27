<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "iconomaps".
 *
 * @property int $id
 * @property string $cadena
 * @property int $posicion
 * @property int $estado
 */
class Iconomaps extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iconomaps';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cadena', 'posicion'], 'required'],
            [['cadena'], 'string'],
            [['posicion', 'estado'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cadena' => 'Cadena',
            'posicion' => 'Posicion',
            'estado' => 'Estado',
        ];
    }
}
