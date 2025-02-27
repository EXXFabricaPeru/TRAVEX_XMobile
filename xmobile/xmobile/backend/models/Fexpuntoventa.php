<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fex_puntoventa".
 *
 * @property int $id
 * @property int|null $fexcompany
 * @property int|null $idpuntoventa
 * @property string|null $descripcion
 * @property int|null $idsucursal
 */
class Fexpuntoventa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fex_puntoventa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fexcompany', 'idpuntoventa', 'idsucursal'], 'integer'],
            [['descripcion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fexcompany' => 'Fexcompany',
            'idpuntoventa' => 'Idpuntoventa',
            'descripcion' => 'Descripcion',
            'idsucursal' => 'Idsucursal',
        ];
    }
}
