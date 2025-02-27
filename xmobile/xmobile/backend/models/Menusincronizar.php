<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "menusincronizar".
 *
 * @property int $id
 * @property string $nombre
 * @property string $seccion
 * @property string $estado
 */
class Menusincronizar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menusincronizar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'seccion'], 'required'],
            [['nombre', 'seccion'], 'string', 'max' => 100],
            [['estado'], 'string', 'max' => 10],
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
            'seccion' => 'Seccion',
            'estado' => 'Estado',
        ];
    }
}
