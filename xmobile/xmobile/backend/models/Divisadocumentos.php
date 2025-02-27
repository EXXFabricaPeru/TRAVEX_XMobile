<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "divisadocumentos".
 *
 * @property int $id
 * @property string $iddocdocumento
 * @property string $CardCode
 * @property string $monedaDe
 * @property string $monedaA
 * @property string $ratio
 * @property string $monto
 * @property string $cambio
 * @property string $usuario
 * @property string $created_at
 * @property string $updated_at
 * @property string $sap
 */
class Divisadocumentos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'divisadocumentos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ratio', 'monto', 'cambio'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['iddocdocumento', 'CardCode', 'monedaDe', 'monedaA', 'usuario', 'sap'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'iddocdocumento' => 'Iddocdocumento',
            'CardCode' => 'Card Code',
            'monedaDe' => 'Moneda De',
            'monedaA' => 'Moneda A',
            'ratio' => 'Ratio',
            'monto' => 'Monto',
            'cambio' => 'Cambio',
            'usuario' => 'Usuario',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'sap' => 'Sap',
        ];
    }
}
