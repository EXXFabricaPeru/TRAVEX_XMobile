<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sucursalalmacenes".
 *
 * @property int $id
 * @property int $sucursalId Sucursal 
 * @property int $almacenesId Almacen
 * @property string|null $tiempo Registrado
 *
 * @property Sucursalx $sucursal
 */
class Sucursalalmacenes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'sucursalalmacenes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['sucursalId', 'almacenesId'], 'required'],
            [['sucursalId'], 'integer'],
            [['tiempo'], 'safe'],
            [['sucursalId'], 'exist', 'skipOnError' => true, 'targetClass' => Sucursalx::className(), 'targetAttribute' => ['sucursalId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'sucursalId' => 'Sucursal',
            'almacenesId' => 'Almacenes',
            'tiempo' => 'Tiempo',
        ];
    }

    /**
     * Gets query for [[Sucursal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSucursal() {
        return $this->hasOne(Sucursalx::className(), ['id' => 'sucursalId']);
    }

}
