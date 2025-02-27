<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_uservendedor".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 * @property string $access_token
 * @property int $idPersona
 * @property int $estadoUsuario
 * @property string $fechaUMUsuario
 * @property string $plataformaUsuario
 * @property string $plataformaPlataforma
 * @property string $plataformaEmei
 * @property int $reset
 * @property string|null $SalesEmployeeCode
 * @property string|null $SalesEmployeeName
 * @property string|null $U_Regional
 */
class Viuservendedor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_uservendedor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'idPersona', 'estadoUsuario', 'reset'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'created_at', 'updated_at', 'access_token', 'idPersona', 'estadoUsuario', 'plataformaUsuario', 'plataformaPlataforma', 'plataformaEmei', 'reset'], 'required'],
            [['access_token'], 'string'],
            [['fechaUMUsuario'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'verification_token', 'plataformaEmei', 'SalesEmployeeCode', 'SalesEmployeeName', 'U_Regional'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['plataformaUsuario', 'plataformaPlataforma'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
            'access_token' => 'Access Token',
            'idPersona' => 'Id Persona',
            'estadoUsuario' => 'Estado Usuario',
            'fechaUMUsuario' => 'Fecha Um Usuario',
            'plataformaUsuario' => 'Plataforma Usuario',
            'plataformaPlataforma' => 'Plataforma Plataforma',
            'plataformaEmei' => 'Plataforma Emei',
            'reset' => 'Reset',
            'SalesEmployeeCode' => 'Sales Employee Code',
            'SalesEmployeeName' => 'Sales Employee Name',
            'U_Regional' => 'Regional',
        ];
    }
}
