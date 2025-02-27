<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "permisosx".
 *
 * @property int $id
 * @property int $rolexId
 * @property int $accionesId
 *
 * @property Acciones $acciones
 */
class Permisosx extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'permisosx';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['rolexId', 'accionesId'], 'required'],
            [['id', 'rolexId', 'accionesId'], 'integer'],
            [['id'], 'unique'],
            [['accionesId'], 'exist', 'skipOnError' => true, 'targetClass' => Acciones::className(), 'targetAttribute' => ['accionesId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'rolexId' => 'Rolex ID',
            'accionesId' => 'Acciones ID',
        ];
    }

    /**
     * Gets query for [[Acciones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAcciones() {
        return $this->hasOne(Acciones::className(), ['id' => 'accionesId']);
    }

}
