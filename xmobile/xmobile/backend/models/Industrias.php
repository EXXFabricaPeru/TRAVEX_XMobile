<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "industrias".
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 *
 * @property Usuarioconfiguracion[] $usuarioconfiguracions
 */
class Industrias extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'Industrias';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
                //[['EmployeeId'], 'integer'],
                //[['DateUpdate'], 'safe'],
                //[['SalesEmployeeCode', 'SalesEmployeeName', 'User', 'Status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
            'User' => 'Usuario',
            'Status' => 'Estado',
            'DateUpdate' => 'Fecha de actualizacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */

}
