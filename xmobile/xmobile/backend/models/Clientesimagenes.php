<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "clientesimagenes".
 *
 * @property int $id
 * @property int $IdCliente
 * @property string $Path
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 *
 * @property Clientes $cliente
 */
class Clientesimagenes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientesimagenes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdCliente', 'User', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['Path'], 'string', 'max' => 255],
            [['IdCliente'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['IdCliente' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'IdCliente' => 'Id Cliente',
            'Path' => 'Path',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Clientes::className(), ['id' => 'IdCliente']);
    }
}
