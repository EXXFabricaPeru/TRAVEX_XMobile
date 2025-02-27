<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bonificacion_ca".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property string|null $U_tipo
 * @property string|null $U_cliente
 * @property string|null $U_fecha
 * @property string|null $U_fecha_inicio
 * @property string|null $U_fecha_fin
 * @property string|null $U_estado
 * @property string|null $U_entrega
 */
class BonificacionCa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonificacion_ca';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['U_fecha', 'U_fecha_inicio', 'U_fecha_fin'], 'safe'],
            [['Code', 'Name', 'U_tipo', 'U_cliente', 'U_estado', 'U_entrega'], 'string', 'max' => 255],
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
            'U_tipo' => 'U Tipo',
            'U_cliente' => 'U Cliente',
            'U_fecha' => 'U Fecha',
            'U_fecha_inicio' => 'U Fecha Inicio',
            'U_fecha_fin' => 'U Fecha Fin',
            'U_estado' => 'U Estado',
            'U_entrega' => 'U Entrega',
        ];
    }
}
