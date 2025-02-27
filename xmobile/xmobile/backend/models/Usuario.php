<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuario".
 *
 * @property int $idUsuario
 * @property string $nombreUsuario
 * @property string $claveUsuario
 * @property int $idPersona
 * @property int $estadoUsuario
 * @property string $fechaUMUsuario
 * @property string $plataformaUsuario
 * @property string $plataformaPlataforma
 * @property string $plataformaEmei
 */
class Usuario extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['idPersona', 'estadoUsuario'], 'integer'],
            [['fechaUMUsuario'], 'safe'],
            [['nombreUsuario', 'claveUsuario'], 'string', 'max' => 255],
            [['plataformaUsuario'], 'string', 'max' => 1],
            [['plataformaPlataforma', 'plataformaEmei'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'idUsuario' => 'Id Usuario',
            'nombreUsuario' => 'Nombre del usuario',
            'claveUsuario' => 'Contraseña',
            'idPersona' => 'Persona ID',
            'estadoUsuario' => 'Estado de usuario',
            'fechaUMUsuario' => 'Ultima modificacion',
            'plataformaUsuario' => 'Tipo de plataforma',
            'plataformaPlataforma' => 'Plataforma',
            'plataformaEmei' => 'Emei',
        ];
    }

}
