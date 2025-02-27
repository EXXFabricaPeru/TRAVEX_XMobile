<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tipos_estados".
 *
 * @property string $id
 * @property string $nombre
 *
 * @property ConfigUsuarios[] $configUsuarios
 */
class TiposEstados extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipos_estados';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'string', 'max' => 240],
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
        return $this->hasMany(ConfigUsuarios::className(), ['idEstado' => 'id']);
    }
}
