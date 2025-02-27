<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "persona".
 *
 * @property int $idPersona
 * @property string $nombrePersona
 * @property string $apellidoPPersona
 * @property string $apellidoMPersona
 * @property int $estadoPersona
 * @property string $fechaUMPersona
 */
class Persona extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'persona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
			[['nombrePersona','apellidoPPersona', 'apellidoMPersona','documentoIdentidadPersona'], 'required'],
            [['estadoPersona'], 'integer'],
            [['fechaUMPersona'], 'safe'],
            [['nombrePersona', 'apellidoPPersona', 'apellidoMPersona'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idPersona' => 'Persona ID',
            'nombrePersona' => 'Nombre persona',
            'apellidoPPersona' => 'Apellido Paterno',
            'apellidoMPersona' => 'Apellido Materno',
            'estadoPersona' => 'Estado Persona',
            'fechaUMPersona' => 'Ultima modificacion',
        ];
    }
}
