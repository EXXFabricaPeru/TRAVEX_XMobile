<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cierrecaja".
 *
 * @property int $id
 * @property int $idUser
 * @property string $numeracion
 * @property int $efectivo_bs
 * @property int $efectivo_usd
 * @property int $tarjeta_de_credito_bs
 * @property int $tarjeta_de_credito_usd
 * @property int $cheque_bs
 * @property int $cheque_usd
 * @property int $transferencia_bs
 * @property int $transferencia_usd
 * @property int $gift_card_bs
 * @property int $total_bs
 * @property int $ofertas
 * @property int $pedidos
 * @property int $facturas
 * @property int $facturas_contado
 * @property int $facturas_credito
 * @property int $pagos_recibidos_bs
 * @property int $total_ventas
 * @property string $fechaIni
 * @property string $fechaFin
 */
class Cierrecaja extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cierrecaja';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idUser','efectivo_bs', 'efectivo_usd', 'tarjeta_de_credito_bs', 'tarjeta_de_credito_usd', 'cheque_bs', 'cheque_usd', 'transferencia_bs', 'transferencia_usd', 'gift_card_bs', 'total_bs', 'ofertas', 'pedidos', 'facturas', 'facturas_contado', 'facturas_credito', 'pagos_recibidos_bs', 'total_ventas'], 'integer'],
            [['numeracion','fechaIni', 'fechaFin'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idUser' => 'Id User',
            'numeracion' => 'Numeracion',
            'efectivo_bs' => 'Efectivo Bs',
            'efectivo_usd' => 'Efectivo Usd',
            'tarjeta_de_credito_bs' => 'Tarjeta De Credito Bs',
            'tarjeta_de_credito_usd' => 'Tarjeta De Credito Usd',
            'cheque_bs' => 'Cheque Bs',
            'cheque_usd' => 'Cheque Usd',
            'transferencia_bs' => 'Transferencia Bs',
            'transferencia_usd' => 'Transferencia Usd',
            'gift_card_bs' => 'Gift Card Bs',
            'total_bs' => 'Total Bs',
            'ofertas' => 'Ofertas',
            'pedidos' => 'Pedidos',
            'facturas' => 'Facturas',
            'facturas_contado' => 'Facturas Contado',
            'facturas_credito' => 'Facturas Credito',
            'pagos_recibidos_bs' => 'Pagos Recibidos Bs',
            'total_ventas' => 'Total Ventas',
            'fechaIni' => 'Fecha Ini',
            'fechaFin' => 'Fecha Fin',
        ];
    }
}
