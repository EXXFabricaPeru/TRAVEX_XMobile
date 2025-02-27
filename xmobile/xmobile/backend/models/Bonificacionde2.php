<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bonificacion_de2".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property string|null $U_ID_bonificacion
 * @property string|null $U_regla
 */
class Bonificacionde2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonificacion_de2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Code', 'Name', 'U_ID_bonificacion', 'U_regla'], 'string', 'max' => 255],
            [['Cantidad','Estado'], 'integer'],
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
            'U_ID_bonificacion' => 'ID Bonificacion',
            'U_regla' => 'Regla'

        ];
    }
}
