<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "almacenes".
 *
 * @property int $id
 * @property string|null $Street
 * @property string|null $WarehouseCode
 * @property string|null $State
 * @property string|null $Country
 * @property string|null $City
 * @property string|null $WarehouseName
 * @property string|null $User
 * @property string|null $Status
 * @property string|null $DateUpdate
 *
 * @property Productosalmacenes[] $productosalmacenes
 */
class Almacenes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'almacenes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            //[['DateUpdate'], 'safe'],
            //[['Street', 'WarehouseCode', 'State', 'Country', 'City', 'WarehouseName', 'User', 'Status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'Street' => 'Dirección',
            'WarehouseCode' => 'Codigo',
            'State' => 'Estado',
            'Country' => 'Pais',
            'City' => 'Ciudad',
            'WarehouseName' => 'Nombre',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * Gets query for [[Productosalmacenes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductosalmacenes() {
        return $this->hasMany(Productosalmacenes::className(), ['WarehouseCode' => 'WarehouseCode']);
    }

}
