<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuariomovilterritoriodetalle".
 *
 * @property int $id
 * @property int $idCabecera
 * @property int $idUserMovil
 * @property string $userMovil
 * @property int $idTerritorio
 * @property string $territorio
 * @property string $estado
 * @property string $fechaUpdate
 */
class Usuariomovilterritoriodetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuariomovilterritoriodetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['id', 'idCabecera', 'idUserMovil', 'userMovil', 'idTerritorio', 'territorio', 'fechaUpdate'], 'required'],
            [['id', 'idCabecera', 'idUserMovil', 'idTerritorio'], 'integer'],
            [['fechaUpdate'], 'safe'],
            [['userMovil', 'territorio'], 'string', 'max' => 100],
            [['estado'], 'string', 'max' => 1],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idCabecera' => 'Id Cabecera',
            'idUserMovil' => 'Id User Movil',
            'userMovil' => 'User Movil',
            'idTerritorio' => 'Id Territorio',
            'territorio' => 'Territorio',
            'estado' => 'Estado',
            'fechaUpdate' => 'Fecha Modifcado',
        ];
    }
}
