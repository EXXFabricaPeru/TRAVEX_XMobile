<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "geolocalizacion".
 *
 * @property int id
 * @property int idequipox
 * @property string latitud
 * @property string longitud
 * @property string fecha
 * @property string hora
 * @property int idcliente
 * @property string documentocod
 * @property string tipodoc
 * @property int estado
 * @property string actividad
 * @property string anexo
 * @property int usuario
 * @property int status
 * @property string dateUpdate
 *
 */
class Geolocalizacion extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'geolocalizacion';
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
            'idequipox' => 'equipo',
            'latitud' => 'latitud',
            'longitud' => 'longitud',
            'fecha' => 'fecha',
            'hora' => 'hora',
            'idcliente' => 'idcliente',
            'documentocod' => 'documentocod',
            'tipodoc' => 'tipodoc',
            'estado' => 'estado',
            'actividad' => 'actividad',
            'anexo' => 'anexo',
            'usuario' => 'usuario',
            'status' => 'status',
            'dateUpdate' => 'dateUpdate'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
}