<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "serviciosxmobile".
 *
 * @property int $id
 * @property string $descripcion
 * @property string $estado
 */
class Serviciosxmobile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'serviciosxmobile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'estado'], 'required'],
            [['descripcion'], 'string', 'max' => 100],
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
            'descripcion' => 'Descripcion',
            'estado' => 'Estado',
        ];
    }
}
