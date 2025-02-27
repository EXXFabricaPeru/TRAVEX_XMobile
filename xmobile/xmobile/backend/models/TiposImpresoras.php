<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tipos_impresoras".
 *
 * @property string $id
 * @property string $nombre
 *
 * @property ConfigUsuarios[] $configUsuarios
 */
class TiposImpresoras extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipos_impresoras';
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
        return $this->hasMany(ConfigUsuarios::className(), ['idTipoImpresora' => 'id']);
    }
}
