<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "reimpresion".
 *
 * @property int $id
 * @property string|null $fechahora
 * @property string|null $tipodocumento
 * @property string|null $iddocumento
 * @property int|null $usuario
 * @property int|null $equipo
 */
class Reimpresion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reimpresion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechahora'], 'safe'],
            [['usuario', 'equipo'], 'integer'],
            [['tipodocumento', 'iddocumento'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fechahora' => 'Fechahora',
            'tipodocumento' => 'Tipodocumento',
            'iddocumento' => 'Iddocumento',
            'usuario' => 'Usuario',
            'equipo' => 'Equipo',
        ];
    }
}
