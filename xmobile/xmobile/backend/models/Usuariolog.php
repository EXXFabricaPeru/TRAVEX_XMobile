<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuariolog".
 *
 * @property int $id
 * @property string $fecha
 * @property string $fechaIngreso
 * @property string $usuario
 * @property int $idUsuario
 * @property string $ipAddress
 * @property string $codigo
 */
class Usuariolog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuariolog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechaIngreso', 'usuario', 'ipAddress', 'codigo'], 'required'],
            [['fechaIngreso','fecha'], 'safe'],
            [['idUsuario'], 'integer'],
            [['usuario', 'ipAddress'], 'string', 'max' => 50],
            [['codigo'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fecha' => 'Fecha',
            'fechaIngreso' => 'Fecha Hora',
            'usuario' => 'Usuario',
            'idUsuario' => 'ID Usuario',
            'ipAddress' => 'Ip Address',
            'codigo' => 'Plataforma',
        ];
    }
}
