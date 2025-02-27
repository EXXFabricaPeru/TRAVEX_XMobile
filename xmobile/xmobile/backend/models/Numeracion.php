<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "numeracion".
 *
 * @property int $id
 * @property int $numcli
 * @property int $numdof
 * @property int $numdoe
 * @property int $numdfa
 * @property int $numdop
 * @property int $numgp
 * @property int $numgpa
 * @property int $numnota
 * @property int $iduser
 */
class Numeracion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'numeracion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numcli', 'numdof', 'numdoe', 'numdfa', 'numdop', 'numgp', 'numgpa','numnota', 'iduser'], 'integer'],
            [['iduser'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'numcli' => 'Clientes',
            'numdof' => 'Ofertas',
            'numdoe' => 'Entregas',
            'numdfa' => 'Facturas',
            'numdop' => 'Pedidos',
            'numgp' => 'Pagos',
            'numgpa' => 'Numgpa',
            'numnota' => 'numnota',
            'iduser' => 'Iduser',
        ];
    }
}
