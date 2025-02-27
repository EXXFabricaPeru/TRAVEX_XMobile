<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "permisosmiddle".
 *
 * @property int $id
 * @property int $idUsuario
 * @property string $userName
 * @property int $nivel
 * @property string $descripcionNivel
 * @property string $departamento
 * @property string $idDepartamento
 * @property int $idCargoEmpresa
 * @property string $cargoEmpresa
 * @property string $permisomenu
 * @property string $fechaSistema
 * @property string $permisomenusincro
 */
class Permisosmiddle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permisosmiddle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'userName', 'descripcionNivel', 'idDepartamento', 'cargoEmpresa'], 'required'],
            [['fechaSistema'], 'safe'],
            [['idUsuario', 'nivel', 'idCargoEmpresa','idDepartamento'], 'integer'],
            [['permisomenu','permisomenusincro'], 'string'],
            [['userName', 'descripcionNivel', 'departamento'], 'string', 'max' => 50],
            [['cargoEmpresa'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idUsuario' => 'Id Usuario',
            'userName' => 'Nombre de Usuario',
            'nivel' => 'Rol',
            'descripcionNivel' => 'Descripcion Nivel',
            'idDepartamento' => 'Territorio',
            'departamento' => 'Territorio',
            'idCargoEmpresa' => 'Id Cargo Empresa',
            'cargoEmpresa' => 'Cargo Empresa',
            'permisomenu' => 'Permisomenu',
            'fechaSistema' => 'Fecha Sistema',
            'permisomenusincro' => 'Permisomenusicro',
        ];
    }
}
