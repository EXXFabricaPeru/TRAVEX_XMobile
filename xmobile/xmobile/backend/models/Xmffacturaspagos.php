<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "xmffacturaspagos".
 *
 * @property int $id
 * @property int $idCabecera
 * @property string|null $clienteId
 * @property string|null $nro_recibo
 * @property string|null $documentoId
 * @property string|null $docentry
 * @property float|null $monto
 * @property string|null $CardName
 * @property float|null $saldo
 * @property string|null $nroFactura
 * @property float|null $DocTotal
 * @property float|null $cuota
 */
class Xmffacturaspagos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xmffacturaspagos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['monto', 'saldo', 'DocTotal', 'cuota'], 'number'],
            [['idCabecera'],'integer'],
            [['clienteId', 'nro_recibo', 'documentoId', 'docentry', 'CardName', 'nroFactura'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clienteId' => 'Cliente ID',
            'nro_recibo' => 'Nro Recibo',
            'documentoId' => 'Documento ID',
            'docentry' => 'Docentry',
            'monto' => 'Monto',
            'CardName' => 'Card Name',
            'saldo' => 'Saldo',
            'nroFactura' => 'Nro Factura',
            'DocTotal' => 'Doc Total',
            'cuota' => 'Cuota',
            'idCabecera'=>'idCabecera',
        ];
    }
}
