<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "contactos".
 *
 * @property int $id
 * @property string $cardCode
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono1
 * @property string $telefono2
 * @property string $celular
 * @property string $tipo
 * @property string $comentarios
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 * @property string $correo
 * @property string $titulo
 *
 * @property Usuarioconfiguracion[] $usuarioconfiguracions
 */
class contactos extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'contactos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
                [['User','idsap','InternalCode','Status'], 'integer'],
                [['DateUpdate'], 'safe'],
                [['cardCode','nombre','direccion','telefono1','telefono2','celular', 'tipo', 'comentarios', 'Mobilecode','titulo','correo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'direccion' => 'Direccion',
            'telefono1' => 'Telefono 1',
            'telefono2' => 'Telefono 2',
            'celular' => 'Celular',
            'tipo' => 'Tipo',
            'comentarios' => 'Comentarios',
            'User' => 'Usuario',
            'Status' => 'Estado',
            'DateUpdate' => 'Fecha de actualizacion',  
            'correo' => 'Correo',
            'titulo' => 'Titulo',          
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */

}
