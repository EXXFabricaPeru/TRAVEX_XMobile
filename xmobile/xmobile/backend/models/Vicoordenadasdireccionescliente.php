<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_coordenadasdireccionescliente".
 *
 * @property string|null $CardCode Id BussinesPartner
 * @property string|null $CardName
 * @property string|null $latitud
 * @property string|null $longitud
 * @property string|null $territorio
 * @property string|null $tipo
 * @property string|null $direccion
 * @property string|null $calle
 * @property string|null $Properties1
 * @property string|null $Properties2
 * @property string|null $Properties3
 * @property string|null $Properties4
 * @property string|null $Properties5
 * @property string|null $Properties6
 * @property string|null $Properties7
 */
class Vicoordenadasdireccionescliente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_coordenadasdireccionescliente';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['CardCode', 'CardName', 'latitud', 'longitud', 'territorio', 'tipo', 'direccion', 'calle'], 'string', 'max' => 255],
            [['Properties1', 'Properties2', 'Properties3', 'Properties4', 'Properties5', 'Properties6', 'Properties7'], 'string', 'max' => 10],
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
            'Properties1' => 'Properties1',
            'Properties2' => 'Properties2',
            'Properties3' => 'Properties3',
            'Properties4' => 'Properties4',
            'Properties5' => 'Properties5',
            'Properties6' => 'Properties6',
            'Properties7' => 'Properties7',
        ];
    }
}
