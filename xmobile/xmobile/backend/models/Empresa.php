<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "empresa".
 *
 * @property int $id
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono1
 * @property string $telefono2
 * @property string $nit
 * @property string $pais
 * @property string $ciudad
 * @property string $actividad
 * @property int $usuario
 * * @property int $status
 * @property datetime $dateUpdate
 * @property string lat
 * @property string long
 */
class Empresa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empresa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nombre','direccion','telefono1','telefono2','nit','pais','ciudad','actividad','lat','long'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'nombre',
            'direccion' => 'direccion',
            'telefono1' => 'telefono1',
            'telefono2' => 'telefono2',
            'nit' => 'nit',
            'pais' => 'pais',
            'ciudad' => 'ciudad',
            'actividad' => 'actividad',
            'usuario' => 'usuario',
            'status' => 'status',
            'dateUpdate' => 'dateUpdate',
            'lat' => 'Latitud',
            'long' => 'Longitud'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
