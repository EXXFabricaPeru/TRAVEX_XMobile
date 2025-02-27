<?php

namespace backend\models;

use Yii;


/**
 * This is the model class for table "acciones".
 *
 * @property int $id
 * @property string $cod Codigo
 * @property string $nombre Nombre
 */
class Acciones extends \yii\db\ActiveRecord
{
 
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'acciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cod', 'nombre'], 'required'],
            [['cod'], 'string', 'max' => 50],
            [['nombre'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cod' => 'Cod',
            'nombre' => 'Nombre',
        ];
    }
}
