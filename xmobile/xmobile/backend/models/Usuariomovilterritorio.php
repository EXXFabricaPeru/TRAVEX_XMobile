<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuariomovilterritorio".
 *
 * @property int $id
 * @property int|null $idUser
 * @property string|null $user
 * @property int|null $idTerritorio
 * @property string|null $territorio
 * @property int|null $idUserRegister
 * @property string|null $userRegister
 * @property string|null $fechaSistema
 * @property string|null $fechaUpdate
 */
class Usuariomovilterritorio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuariomovilterritorio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idUser', 'idUserRegister'], 'integer'],
            [['fechaSistema','fechaUpdate'], 'safe'],
            [['user', 'territorio', 'userRegister','idTerritorio'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idUser' => 'Usuario',
            'user' => 'Usuario Vendedor',
            'idTerritorio' => 'Territorio',
            'territorio' => 'Territorio/Región',
            'idUserRegister' => 'Id User Register',
            'userRegister' => 'User Register',
            'fechaSistema' => 'Fecha Sistema',
			'fechaUpdate' => 'Fecha de Registro',
        ];
    }
}
