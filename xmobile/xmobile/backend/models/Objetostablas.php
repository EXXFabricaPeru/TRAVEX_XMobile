<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "objetostablas".
 *
 * @property int $id
 * @property string|null $Nombre
 * @property string|null $tabla
 */
class Objetostablas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'objetostablas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Nombre', 'tabla'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Nombre' => 'Nombre',
            'tabla' => 'Tabla',
        ];
    }
}
