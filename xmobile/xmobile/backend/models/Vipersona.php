<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_persona".
 *
 * @property int $idPersona
 * @property string|null $nombreCompleto
 */
class Vipersona extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_persona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idPersona'], 'integer'],
            [['nombreCompleto'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idPersona' => 'Id Persona',
            'nombreCompleto' => 'Nombre Completo',
        ];
    }
}
