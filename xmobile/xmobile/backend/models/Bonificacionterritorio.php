<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bonificacionterritorio".
 *
 * @property int $id
 * @property int|null $idCabecera
 * @property int|null $idTerritorio
 * @property string|null $descripcion
 */
class Bonificacionterritorio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonificacionterritorio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCabecera', 'idTerritorio'], 'integer'],
            [['territorio'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idCabecera' => 'Id Cabecera',
            'idTerritorio' => 'Id Territorio',
            'territorio' => 'Territorio',
        ];
    }
}
