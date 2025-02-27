<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_rutasventaregion".
 *
 * @property int $id
 * @property string $fechaSistema
 * @property string $fechaRegistro
 * @property string $dia
 * @property int $idDia
 * @property int $idVendedor
 * @property string $vendedor
 * @property string $tipoVendedor
 * @property string $idTerritorio
 * @property string $territorio
 * @property string $idPoligono
 * @property string $poligono
 * @property string $estado
 * @property string $tipo
 * @property int $idUserRegister
 * @property string $userRegister
 * @property string|null $nombreRuta
 * @property string|null $SalesEmployeeCode
 * @property string|null $SalesEmployeeName
 * @property string|null $U_Regional
 */
class Virutasventaregion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_rutasventaregion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idDia', 'idVendedor', 'idUserRegister'], 'integer'],
            [['fechaSistema', 'fechaRegistro', 'dia', 'idVendedor', 'vendedor', 'tipoVendedor', 'territorio', 'poligono', 'estado', 'tipo', 'idUserRegister', 'userRegister'], 'required'],
            [['fechaSistema', 'fechaRegistro'], 'safe'],
            [['dia', 'estado', 'tipo'], 'string', 'max' => 15],
            [['vendedor', 'tipoVendedor', 'idTerritorio', 'idPoligono', 'userRegister'], 'string', 'max' => 150],
            [['territorio', 'poligono', 'nombreRuta', 'SalesEmployeeCode', 'SalesEmployeeName', 'U_Regional'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fechaSistema' => 'Fecha Sistema',
            'fechaRegistro' => 'Fecha Registro',
            'dia' => 'Dia',
            'idDia' => 'Id Dia',
            'idVendedor' => 'Id Vendedor',
            'vendedor' => 'Vendedor',
            'tipoVendedor' => 'Tipo Vendedor',
            'idTerritorio' => 'Id Territorio',
            'territorio' => 'Territorio',
            'idPoligono' => 'Id Poligono',
            'poligono' => 'Poligono',
            'estado' => 'Estado',
            'tipo' => 'Tipo',
            'idUserRegister' => 'Id User Register',
            'userRegister' => 'User Register',
            'nombreRuta' => 'Nombre Ruta',
            'SalesEmployeeCode' => 'Sales Employee Code',
            'SalesEmployeeName' => 'Sales Employee Name',
            'U_Regional' => 'Regional',
        ];
    }
}
