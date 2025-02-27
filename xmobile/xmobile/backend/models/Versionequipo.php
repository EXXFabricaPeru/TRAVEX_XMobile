<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "versionequipo".
 *
 * @property int $id
 * @property string|null $fechaRegistro
 * @property int|null $usuario
 * @property string|null $fechaVersion
 * @property string|null $equipo
 * @property string|null $version
 */
class Versionequipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'versionequipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechaRegistro', 'fechaVersion'], 'safe'],
            [['usuario'], 'integer'],
            [['equipo', 'version'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fechaRegistro' => 'Fecha Registro',
            'usuario' => 'Usuario',
            'fechaVersion' => 'Fecha Version',
            'equipo' => 'Equipo',
            'version' => 'Version',
        ];
    }
}
