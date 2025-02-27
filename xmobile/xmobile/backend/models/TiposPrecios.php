<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tipos_precios".
 *
 * @property string $id
 * @property int $nombre
 *
 * @property ConfigUsuarios[] $configUsuarios
 */
class TiposPrecios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipos_precios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'integer'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigUsuarios()
    {
        return $this->hasMany(ConfigUsuarios::className(), ['idTipoPrecio' => 'id']);
    }
}
