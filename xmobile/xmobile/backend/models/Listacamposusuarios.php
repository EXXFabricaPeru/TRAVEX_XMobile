<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "listacamposusuarios".
 *
 * @property int $Id
 * @property int|null $IdcampoUsuario
 * @property string|null $codigo
 * @property string|null $nombre
 * @property int|null $Status
 */
class Listacamposusuarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'listacamposusuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdcampoUsuario', 'Status'], 'integer'],
            [['codigo'], 'string', 'max' => 10],
            [['nombre'], 'string', 'max' => 100],
            [['codigo', 'nombre', 'Status'], 'required'],
            [['IdcampoUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Camposusuarios::className(), 'targetAttribute' => ['IdcampoUsuario' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdcampoUsuario' => 'Id campo Usuario',
            'codigo' => 'Codigo',
            'nombre' => 'Nombre',
            'Status' => 'Status',
        ];
    }

    public function getSucursal() {
        return $this->hasOne(Camposusuarios::className(), ['id' => 'IdcampoUsuario']);
    }
}
