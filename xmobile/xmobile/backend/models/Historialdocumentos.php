<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "historialdocumentos".
 *
 * @property int $id
 * @property string|null $fecha
 * @property int|null $usuario
 * @property string|null $otpp
 * @property string|null $cadenaDetalle
 * @property string|null $cadenaPago
 * @property string|null $cadenaCabezera
 */
class Historialdocumentos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'historialdocumentos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha'], 'safe'],
            [['usuario'], 'integer'],
            [['cadenaDetalle', 'cadenaPago', 'cadenaCabezera'], 'string'],
            [['otpp'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fecha' => 'Fecha',
            'usuario' => 'Usuario',
            'otpp' => 'Otpp',
            'cadenaDetalle' => 'Cadena Detalle',
            'cadenaPago' => 'Cadena Pago',
            'cadenaCabezera' => 'Cadena Cabezera',
        ];
    }
}
