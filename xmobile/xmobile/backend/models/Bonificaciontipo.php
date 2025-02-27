<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bonificaciontipo".
 *
 * @property int $id
 * @property string|null $descripcion
 * @property string|null $tipoReglaCompra
 * @property string|null $tipoRegla
 * @property string|null $detalle
 */
class Bonificaciontipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonificaciontipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion'], 'string'],
            [['tipoReglaCompra'], 'string', 'max' => 100],
            [['tipoRegla', 'detalle'], 'string', 'max' => 150],
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
            'tipoReglaCompra' => 'Tipo Regla Compra',
            'tipoRegla' => 'Tipo Regla',
            'detalle' => 'Detalle',
        ];
    }
}
