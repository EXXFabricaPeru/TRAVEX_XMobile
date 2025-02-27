<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "promociones".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property string|null $U_CardCode
 * @property int|null $U_CodigoCampania
 * @property float|null $U_ValorGanado
 * @property string|null $U_FechaInicio
 * @property string|null $U_FechaFinal
 * @property int|null $U_DocEntry
 * @property string|null $U_DocType
 * @property string|null $U_FechaMaximoCobro
 * @property float|null $U_ValorSaldo
 * @property float|null $U_Saldo
 */
class Promociones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promociones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['U_CodigoCampania', 'U_DocEntry'], 'integer'],
            [['U_ValorGanado', 'U_ValorSaldo', 'U_Saldo'], 'number'],
            [['U_FechaInicio', 'U_FechaFinal', 'U_FechaMaximoCobro'], 'safe'],
            [['Code', 'Name', 'U_CardCode', 'U_DocType'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Code' => 'Code',
            'Name' => 'Name',
            'U_CardCode' => 'U Card Code',
            'U_CodigoCampania' => 'U Codigo Campania',
            'U_ValorGanado' => 'U Valor Ganado',
            'U_FechaInicio' => 'U Fecha Inicio',
            'U_FechaFinal' => 'U Fecha Final',
            'U_DocEntry' => 'U Doc Entry',
            'U_DocType' => 'U Doc Type',
            'U_FechaMaximoCobro' => 'U Fecha Maximo Cobro',
            'U_ValorSaldo' => 'U Valor Saldo',
            'U_Saldo' => 'U Saldo',
        ];
    }
}
