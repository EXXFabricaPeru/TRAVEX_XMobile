<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "documentohtml".
 *
 * @property int $id
 * @property string|null $idDocPedido
 * @property string|null $html
 */
class Documentohtml extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documentohtml';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['html'], 'string'],
            [['idDocPedido'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idDocPedido' => 'Id Doc Pedido',
            'html' => 'Html',
        ];
    }
}
