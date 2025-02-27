<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "xmfcabezerapagos".
 *
 * @property int $id
 * @property string|null $nro_recibo
 * @property int|null $correlativo
 * @property int|null $usuario
 * @property string|null $documentoId
 * @property string|null $fecha
 * @property string|null $hora
 * @property float|null $monto_total
 * @property string|null $tipo
 * @property int|null $otpp
 * @property float|null $tipo_cambio
 * @property string|null $moneda
 * @property string|null $cliente_carcode
 * @property string|null $razon_social
 * @property string|null $nit
 * @property int|null $estado
 * @property int|null $cancelado
 * @property string|null $fechaSistema Fecha de registro al middle
 * @property string|null $tipoTarjeta
 */
class Xmfcabezerapagos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xmfcabezerapagos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['correlativo', 'usuario', 'otpp', 'estado', 'cancelado','idDocumento'], 'integer'],
            [['fecha', 'hora', 'fechaSistema'], 'safe'],
            [['monto_total', 'tipo_cambio'], 'number'],
            [['nro_recibo', 'tipo', 'moneda', 'cliente_carcode', 'nit'], 'string', 'max' => 20],
            [['documentoId', 'razon_social'], 'string', 'max' => 100],
            [['tipoTarjeta'], 'string', 'max' => 255],
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
            'correlativo' => 'Correlativo',
            'usuario' => 'Usuario',
            'documentoId' => 'Documento ID',
            'fecha' => 'Fecha',
            'TransId' => 'DocEntry',
            'hora' => 'Hora',
            'monto_total' => 'Monto Total',
            'tipo' => 'Tipo',
            'otpp' => 'Otpp',
            'tipo_cambio' => 'Tipo Cambio',
            'moneda' => 'Moneda',
            'cliente_carcode' => 'Cliente Carcode',
            'razon_social' => 'Razon Social',
            'nit' => 'Nit',
            'estado' => 'Estado',
            'cancelado' => 'Cancelado',
            'fechaSistema' => 'Fecha Sistema',
            'tipoTarjeta' => 'Tipo Tarjeta',
        ];
    }
    public function getXmffacturaspagos() {
        return $this->hasMany(Xmffacturaspagos::className(), ['idCabecera' => 'id']);//->orderBy(['BaseLine' => SORT_ASC]);
    }
    public function getXmfmediospagos() {
        return $this->hasMany(Xmfmediospagos::className(), ['idCabecera' => 'id']);//->orderBy(['BaseLine' => SORT_ASC]);
    }
}
