<?php

namespace backend\models;

use Yii;


/**
 * This is the model class for table "poligonocabeceraterritorio".
 *
 * @property int $id
 * @property string $fechaSistema
 * @property string $fechaRegistro
 * @property string $dia
 * @property int $idDia
 * @property int $idVendedor
 * @property string $vendedor
 * @property string $tipoVendedor
 * @property int $idTerritorio
 * @property string $territorio
 * @property int $idPoligono
 * @property string $poligono
 * @property string $estado
 * @property string $tipo
 * @property int $idUserRegister
 * @property string $userRegister
 * @property string $nombreRuta
 */
class Poligonocabeceraterritorio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poligonocabeceraterritorio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['fechaSistema', 'fechaRegistro', 'dia', 'mes', 'idVendedor', 'vendedor', 'tipoVendedor', 'idTerritorio', 'Territorio', 'idPoligono', 'poligono', 'estado', 'tipo', 'idUserRegister', 'userRegister'], 'required'],
            [['fechaRegistro', 'nombreRuta'], 'required'],
            [['fechaSistema', 'fechaRegistro'], 'safe'],
            [['idVendedor',  'idUserRegister','idDia'], 'integer'],
            [['dia', 'estado', 'tipo'], 'string', 'max' => 15],
            [['vendedor', 'tipoVendedor', 'idTerritorio', 'idPoligono', 'userRegister'], 'string', 'max' => 150],
            [['territorio', 'poligono','nombreRuta'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fechaSistema' => 'Fecha Registro Sistema',
            'fechaRegistro' => 'Fecha Registro Ruta',
            'dia' => 'Dia',
            'idDia' => 'dia ',
            'idVendedor' => 'Vendedor',
            'vendedor' => 'Vendedor',
            'tipoVendedor' => 'Tipo Vendedor',
            'idTerritorio' => 'Id Territorio',
            'territorio' => 'Territorio',
            'idPoligono' => 'Id Poligono',
            'poligono' => 'Region',
            'estado' => 'Estado',
            'tipo' => 'Tipo',
            'idUserRegister' => 'Id User Register',
            'userRegister' => 'User Register',
            'nombreRuta' => 'Nombre de la Ruta',
        ];
    }
}
