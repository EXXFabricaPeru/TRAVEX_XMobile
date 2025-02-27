<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuariopersona".
 *
 * @property string $idPersona
 * @property string $nombrePersona
 * @property string $apellidoPPersona
 * @property string $apellidoMPersona
 * @property int $estadoPersona
 * @property string $fechaUMPersona
 * @property string $documentoIdentidadPersona
 *
 * @property User[] $users
 */
class Usuariopersona extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'usuariopersona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
           // [['apellidoPPersona', 'nombrePersona', 'apellidoMPersona', 'documentoIdentidadPersona'], 'required'],
            [['apellidoPPersona', 'nombrePersona'], 'required'],
            [['estadoPersona'], 'integer'],
            [['fechaUMPersona'], 'safe'],
            [['documentoIdentidadPersona'], 'unique'],
            [['nombrePersona', 'apellidoPPersona', 'apellidoMPersona', 'documentoIdentidadPersona'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'idPersona' => 'Id Persona',
            'nombrePersona' => 'Nombre',
            'apellidoPPersona' => 'Apellido paterno',
            'apellidoMPersona' => 'Apellido Materno',
            'estadoPersona' => 'Estado',
            'fechaUMPersona' => 'Modificado',
            'documentoIdentidadPersona' => 'Documento Identidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['idPersona' => 'idPersona']);
    }

}
