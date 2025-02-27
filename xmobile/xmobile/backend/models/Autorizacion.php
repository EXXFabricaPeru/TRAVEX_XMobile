<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "autorizacion".
 *
 * @property int $id
 * @property string|null $autorizacion
 * @property int|null $usuario
 * @property string|null $accion
 */
class Autorizacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'autorizacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario'], 'integer'],
            [['autorizacion'], 'string', 'max' => 255],
            [['accion'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'autorizacion' => 'Autorizacion',
            'usuario' => 'Usuario',
            'accion' => 'Accion',
        ];
    }
}
