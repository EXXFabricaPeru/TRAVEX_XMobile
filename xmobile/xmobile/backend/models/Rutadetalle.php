<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "rutadetalle".
 *
 * @property int id
 * @property int idcabecera
 * @property string idcliente 
 * @property string cardname  
 * @property int posicion
 * @property string longitud
 * @property string latitud
 * @property int usuario
 * @property int status
 * @property string dateUpdate
 * @property string tipodoc
 * @property string iddoc
 */
class Rutadetalle extends \yii\db\ActiveRecord {
    public static function tableName() {
        return 'rutadetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'id',
            'idcabecera' => 'id del cabecera',
            'idcliente' => 'id del cliente',
            'cardname' => 'cardname',
            'posicion' => 'posicion',
            'longitud' => 'longitud',
            'latitud' => 'latitud',
            'usuario' => 'usuario',
            'status' => 'status',
            'dateUpdate' => 'dateUpdate',
            'tipodoc' => 'tipodoc',
            'iddoc' => 'iddoc',
            'nropicking' => 'nropicking',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
}