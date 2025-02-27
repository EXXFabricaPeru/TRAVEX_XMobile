<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "xmfmediospagos".
 *
 * @property int $id
 * @property string|null $nro_recibo
 * @property string|null $documentoId
 * @property string|null $formaPago
 * @property float|null $monto
 * @property string|null $numCheque
 * @property string|null $numComprobante
 * @property string|null $numTarjeta
 * @property string|null $bancoCode
 * @property string|null $fecha
 * @property int|null $cambio
 * @property int|null $monedaDolar
 * @property int|null $monedaLocal
 * @property string|null $centro
 * @property string|null $baucher
 * @property string|null $checkdate
 * @property string|null $transferencedate
 * @property int|null $CreditCard
 */
class Xmfmediospagos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xmfmediospagos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['monto'], 'number'],
            [['fecha'], 'safe'],
            [['cambio', 'monedaDolar', 'monedaLocal', 'CreditCard','idCabecera'], 'integer'],
            [['nro_recibo', 'formaPago', 'checkdate', 'transferencedate'], 'string', 'max' => 20],
            [['documentoId'], 'string', 'max' => 100],
            [['numCheque', 'numComprobante', 'numTarjeta', 'bancoCode'], 'string', 'max' => 200],
            [['centro'], 'string', 'max' => 50],
            [['baucher'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nro_recibo' => 'Nro Recibo',
            'documentoId' => 'Documento ID',
            'formaPago' => 'Forma Pago',
            'monto' => 'Monto',
            'numCheque' => 'Num Cheque',
            'numComprobante' => 'Num Comprobante',
            'numTarjeta' => 'Num Tarjeta',
            'bancoCode' => 'Banco Code',
            'fecha' => 'Fecha',
            'cambio' => 'Cambio',
            'monedaDolar' => 'Moneda Dolar',
            'monedaLocal' => 'Moneda Local',
            'centro' => 'Centro',
            'baucher' => 'Baucher',
            'checkdate' => 'Checkdate',
            'transferencedate' => 'Transferencedate',
            'CreditCard' => 'Credit Card',
            'idCabecera'=>'idCabecera',
        ];
    }
}
