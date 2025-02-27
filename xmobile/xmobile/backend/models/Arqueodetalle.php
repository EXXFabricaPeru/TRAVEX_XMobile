<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_arqueodetalle".
 *
 * @property string|null $Transaccion
 * @property string|null $documentoId
 * @property string $clienteId Cliente
 * @property string $formaPago Forma de pago
 * @property float|null $tipoCambioDolar Tipo de cambio de dolar
 * @property float $bs Monto
 * @property float|null $Sus Valor (Moneda dolar)
 * @property string $fecha Fecha
 * @property float $cambio Cambio
 * @property int|null $usuario
 * @property string|null $CardName
 */
class Arqueodetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_arqueodetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clienteId', 'formaPago', 'fecha', 'cambio'], 'required'],
            [['tipoCambioDolar', 'bs', 'Sus', 'cambio'], 'number'],
            [['fecha'], 'safe'],
            [['usuario'], 'integer'],
            [['Transaccion'], 'string', 'max' => 2],
            [['documentoId', 'CardName'], 'string', 'max' => 255],
            [['clienteId'], 'string', 'max' => 100],
            [['formaPago'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Transaccion' => 'Transaccion',
            'documentoId' => 'Documento ID',
            'clienteId' => 'Cliente ID',
            'formaPago' => 'Forma Pago',
            'tipoCambioDolar' => 'Tipo Cambio Dolar',
            'bs' => 'Bs',
            'Sus' => 'Sus',
            'fecha' => 'Fecha',
            'cambio' => 'Cambio',
            'usuario' => 'Usuario',
            'CardName' => 'Card Name',
        ];
    }
}
