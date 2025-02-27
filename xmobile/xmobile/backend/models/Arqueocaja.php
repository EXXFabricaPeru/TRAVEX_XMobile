<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_arqueocaja".
 *
 * @property int|null $usuario
 * @property string $formaPago Forma de pago
 * @property string|null $detalle
 * @property float|null $tipoCambioDolar Tipo de cambio de dolar
 * @property float|null $monto
 * @property float|null $monto2
 * @property string $fecha Fecha
 * @property float $cambio Cambio
 */
class Arqueocaja extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_arqueocaja';
    }
   public static function primaryKey() {
        return ['id'];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario'], 'integer'],
            [['formaPago', 'fecha', 'cambio'], 'required'],
            [['tipoCambioDolar', 'monto', 'monto2', 'cambio'], 'number'],
            [['fecha'], 'safe'],
            [['formaPago'], 'string', 'max' => 20],
            [['detalle'], 'string', 'max' => 13],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'usuario' => 'Usuario',
            'formaPago' => 'Forma Pago',
            'detalle' => 'Detalle',
            'tipoCambioDolar' => 'Tipo Cambio Dolar',
            'monto' => 'Monto',
            'monto2' => 'Monto2',
            'fecha' => 'Fecha',
            'cambio' => 'Cambio',
        ];
    }
}
