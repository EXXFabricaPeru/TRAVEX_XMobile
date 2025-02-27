<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "poligonodetalle".
 *
 * @property int $id
 * @property int|null $idcabecera
 * @property string|null $latitud
 * @property string|null $longitud
 * @property int|null $usuario
 * @property int|null $status
 * @property string|null $dateUpdate
 *
 * @property Poligonocabecera $idcabecera0
 */
class Poligonodetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poligonodetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idcabecera', 'usuario', 'status'], 'integer'],
            [['dateUpdate'], 'safe'],
            [['latitud', 'longitud'], 'string', 'max' => 255],
            [['idcabecera'], 'exist', 'skipOnError' => true, 'targetClass' => Poligonocabecera::className(), 'targetAttribute' => ['idcabecera' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idcabecera' => 'Idcabecera',
            'latitud' => 'Latitud',
            'longitud' => 'Longitud',
            'usuario' => 'Usuario',
            'status' => 'Status',
            'dateUpdate' => 'Date Update',
        ];
    }

    /**
     * Gets query for [[Idcabecera0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdcabecera0()
    {
        return $this->hasOne(Poligonocabecera::className(), ['id' => 'idcabecera']);
    }
}
