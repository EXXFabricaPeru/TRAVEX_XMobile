<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "configuracion".
 *
 * @property int $id
 * @property string|null $parametro
 * @property int|null $valor
 * @property string|null $especificacion
 * @property int|null $estado
 * @property string|null $valor2
 * @property string|null $valor3
 * @property string|null $valor4
 * @property int $visible
 */
class Configuracion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['valor', 'estado', 'visible'], 'integer'],
            [['parametro', 'especificacion', 'valor2', 'valor3', 'valor4'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parametro' => 'Parametro',
            'valor' => 'Valor',
            'especificacion' => 'Especificacion',
            'estado' => 'Estado',
            'valor2' => 'Valor2',
            'valor3' => 'Valor3',
            'valor4' => 'Valor4',
            'visible' => 'visible'
        ];
    }
}
