<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bonificacion_de1".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property string|null $U_ID_bonificacion
 * @property string|null $U_regla
 */
class Bonificacionde1 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonificacion_de1';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Code', 'Name', 'U_ID_bonificacion', 'U_regla'], 'string', 'max' => 255],
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
            'U_ID_bonificacion' => 'U Id Bonificacion',
            'U_regla' => 'U Regla',
        ];
    }
}
