<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_coordenadasdirecciones".
 *
 * @property string|null $CardCode Id BussinesPartner
 * @property string|null $CardName
 * @property string|null $latitud
 * @property string|null $longitud
 * @property string|null $territorio
 * @property string|null $tipo
 * @property string|null $direccion
 * @property string|null $calle
 */
class ViCoordenadasdirecciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_coordenadasdirecciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['CardCode', 'CardName', 'latitud', 'longitud', 'territorio', 'tipo', 'direccion', 'calle'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'CardCode' => 'Card Code',
            'CardName' => 'Card Name',
            'latitud' => 'Latitud',
            'longitud' => 'Longitud',
            'territorio' => 'Territorio',
            'tipo' => 'Tipo',
            'direccion' => 'Direccion',
            'calle' => 'Calle',
        ];
    }
}
