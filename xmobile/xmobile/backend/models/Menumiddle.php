<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "menumiddle".
 *
 * @property int $id
 * @property string $nombreMenu
 * @property string $seccion
 * @property string $estado
 */
class Menumiddle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menumiddle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombreMenu', 'seccion', 'estado'], 'required'],
            [['nombreMenu', 'seccion'], 'string', 'max' => 100],
            [['estado'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombreMenu' => 'Nombre Menu',
            'seccion' => 'Seccion',
            'estado' => 'Estado',
        ];
    }
}
