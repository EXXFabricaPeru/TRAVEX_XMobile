<?php

namespace backend\modules\admin\models;

use Yii;

/**
 * This is the model class for table "localidad".
 *
 * @property int $id
 * @property string $nombre Nombre
 * @property string $descripcion Descripcion
 * @property int $estado Estado
 * @property string $fecha Fecha
 *
 * @property Etiquetas[] $etiquetas
 */
class Localidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'localidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'descripcion', 'fecha'], 'required'],
            [['descripcion'], 'string'],
            [['estado'], 'integer'],
            [['fecha'], 'safe'],
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
            'descripcion' => 'Descripcion',
            'estado' => 'Estado',
            'fecha' => 'Fecha',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEtiquetas()
    {
        return $this->hasMany(Etiquetas::className(), ['idlocalidad' => 'id']);
    }
}
