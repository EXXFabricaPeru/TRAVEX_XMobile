<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "rutacabecera".
 *
 * @property int id
 * @property string nombre
 * @property int idvendedor
 * @property string fecha
 * @property string idclienteinicial
 * @property string latitud
 * @property string longitud
 * @property int usuario
 * @property int status
 * @property string dateUpdate
 * @property string tipousuario
 * @property rutadetalle[] $detalle
 */
class Rutacabecera extends \yii\db\ActiveRecord {
    public static function tableName() {
        return 'rutacabecera';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['nombre','fecha','usuario','vendedor'], 'required'],
            [['idvendedor','usuario'], 'integer'],
            [['latitud','longitud'], 'number'],
            [['dateUpdate','fecha','fechapicking'], 'safe'],
            [['nombre', 'idclienteinicial','vendedor'], 'string', 'max' => 255],
            [['tipousuario'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'id',
            'nombre' => 'Nombre de la Ruta',
            'idvendedor' => 'Despachador',
            'fecha' => 'Fecha Registro',
            'idclienteinicial' => 'Cliente',
            'latitud' => 'Latitud',
            'longitud' => 'Longitud',
            'usuario' => 'Usuario',
            'status' => 'Estado',
            'dateUpdate' => 'dateUpdate',
            'tipousuario' => 'tipousuario',
            'vendedor' => 'Despachador',
            'fechapicking' => 'Fecha de Picking',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
}