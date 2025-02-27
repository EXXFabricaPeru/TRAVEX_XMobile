<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "evidenciacabecera".
 *
 * @property string|null $DocEntry
 * @property string|null $idDocPedido
 * @property string|null $fechasend
 * @property int|null $idUser
 * @property string|null $U_LATITUD
 * @property string|null $U_LONGITUD
 * @property string|null $CardCode
 */
class Evidenciacabecera extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'evidenciacabecera';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechasend'], 'safe'],
            [['idUser','id'], 'integer'],
            [['DocEntry', 'idDocPedido', 'U_LATITUD', 'U_LONGITUD', 'CardCode'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'DocEntry' => 'Doc Entry',
            'idDocPedido' => 'Id Doc Pedido',
            'fechasend' => 'Fechasend',
            'idUser' => 'Id User',
            'U_LATITUD' => 'U Latitud',
            'U_LONGITUD' => 'U Longitud',
            'CardCode' => 'Card Code',
        ];
    }
}
