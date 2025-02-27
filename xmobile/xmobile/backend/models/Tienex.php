<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tienex".
 *
 * @property int $id
 * @property int $rolexId
 * @property int $userId
 * @property int|null $accionesId
 * @property string|null $descripcion
 *
 * @property Acciones $acciones
 */
class Tienex extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'tienex';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['rolexId', 'userId'], 'required'],
            [['id', 'rolexId', 'userId', 'accionesId'], 'integer'],
            [['descripcion'], 'string', 'max' => 255],
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
            'userId' => 'User ID',
            'accionesId' => 'Acciones ID',
            'descripcion' => 'Descripcion',
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
