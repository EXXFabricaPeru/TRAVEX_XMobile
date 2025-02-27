<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bancos".
 *
 * @property int $id
 * @property string $codigo
 * @property string $cuenta
 * @property string $nombre
 */
class Bancos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bancos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'cuenta', 'nombre'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'cuenta' => 'Cuenta',
            'nombre' => 'Nombre',
        ];
    }
}
