<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equipoxcuentascontables".
 *
 * @property int $id
 * @property int $equipoxId Equipo
 * @property string $cuentaEfectivo Cuenta en efectivo
 * @property string $cuentaCheque Cuenta en cheque
 * @property string|null $cuentaChequeDia Cuenta en cheque Dia
 * @property string|null $cuentaChequeDiferido Cuenta en cheque Diferido
 * @property string $cuentaTranferencia Cuenta en tranferencia
 * @property string $cuentaTarjeta Cuenta en tarjeta
 * @property string $cuentaAnticipos Cuenta en anticipos
 * @property string|null $cuentaEfectivoUSD Cuenta en efectivo USD
 * @property string|null $cuentaChequeUSD Cuenta en cheque USD
 * @property string|null $cuentaChequeDiaUSD Cuenta en cheque Dia USD
 * @property string|null $cuentaChequeDiferidoUSD Cuenta en cheque Diferido USD
 * @property string|null $cuentaTranferenciaUSD Cuenta en tranferencia USD
 * @property string|null $cuentaTarjetaUSD Cuenta en tarjeta USD
 * @property string|null $cuentaAnticiposUSD Cuenta en anticipos USD
 *
 * @property Equipox $equipox
 */
class Equipoxcuentascontables extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'equipoxcuentascontables';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            //[['equipoxId', 'cuentaEfectivo', 'cuentaCheque', 'cuentaTranferencia', 'cuentaTarjeta', 'cuentaAnticipos'], 'required'],
            [['equipoxId', 'cuentaEfectivo', 'cuentaCheque', 'cuentaTranferencia'], 'required'],
            [['equipoxId'], 'integer'],
            [['equipoxId'], 'unique', 'message' => 'Solo puedes asignar una cuenta  contable a cada dispositivo este dispositivo ya fue asignado.'],
            [['cuentaEfectivo', 'cuentaCheque','cuentaChequeDia','cuentaChequeDiferido', 'cuentaTranferencia', 'cuentaTarjeta', 'cuentaAnticipos', 'cuentaEfectivoUSD', 'cuentaChequeUSD','cuentaChequeDiaUSD','cuentaChequeDiferidoUSD', 'cuentaTranferenciaUSD', 'cuentaTarjetaUSD', 'cuentaAnticiposUSD','cuentaClientesRegion'], 'string', 'max' => 60],
            [['equipoxId'], 'exist', 'skipOnError' => true, 'targetClass' => Equipox::className(), 'targetAttribute' => ['equipoxId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'equipoxId' => 'Equipox ID',
            'cuentaEfectivo' => 'Cuenta Efectivo',
            'cuentaCheque' => 'Cuenta Cheque',
            'cuentaChequeDia' => 'Cuenta Cheque Diario',
            'cuentaChequeDiferido ' => 'Cuenta Cheque Diferido',
            'cuentaTranferencia' => 'Cuenta Tranferencia',
            'cuentaTarjeta' => 'Cuenta Tarjeta',
            'cuentaAnticipos' => 'Cuenta Anticipos',
            'cuentaEfectivoUSD' => 'Cuenta Efectivo Usd',
            'cuentaChequeUSD' => 'Cuenta Cheque Usd',
            'cuentaChequeDiaUSD' => 'Cuenta Cheque Diario Usd',
            'cuentaChequeDiferidoUSD' => 'Cuenta Cheque Diferido Usd',
            'cuentaTranferenciaUSD' => 'Cuenta Tranferencia Usd',
            'cuentaTarjetaUSD' => 'Cuenta Tarjeta Usd',
            'cuentaAnticiposUSD' => 'Cuenta Anticipos Usd',
            'cuentaClientesRegion' => 'Cuenta Clientes Región'
        ];
    }

    /**
     * Gets query for [[Equipox]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEquipox() {
        return $this->hasOne(Equipox::className(), ['id' => 'equipoxId']);
    }

}
