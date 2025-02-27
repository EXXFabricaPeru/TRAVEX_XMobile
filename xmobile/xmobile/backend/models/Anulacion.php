<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "anulacion".
 *
 * @property int $id
 * @property string|null $tipodocumento
 * @property string|null $iddocumento
 * @property string|null $fechahora
 * @property int|null $usuario
 * @property int|null $equipo
 */
class Anulacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anulacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechahora'], 'safe'],
            [['usuario','equipo'], 'integer'],
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
            'tipodocumento' => 'Tipodocumento',
            'iddocumento' => 'Iddocumento',
            'fechahora' => 'Fechahora',
            'usuario' => 'Usuario',
			'equipo' => 'equipo'
        ];
    }
}
