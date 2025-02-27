<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "poligonocabecera".
 *
 * @property int $id
 * @property string|null $nombre
 * @property int $territoryid
 * @property int|null $usuario
 * @property int|null $status
 * @property string|null $dateUpdate
 *
 * @property Poligonodetalle[] $detalle
 */
class Poligonocabecera extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poligonocabecera';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['nombre'], 'unique'],
            [['territoryid','usuario', 'status'], 'integer'],
            [['dateUpdate'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'territoryid' => 'Territorio',
            'usuario' => 'Usuario',
            'status' => 'Status',
            'dateUpdate' => 'Date Update',
        ];
    }

    /**
     * Gets query for [[Poligonodetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPoligonodetalles()
    {
        return $this->hasMany(Poligonodetalle::className(), ['idcabecera' => 'id']);
    }
}
