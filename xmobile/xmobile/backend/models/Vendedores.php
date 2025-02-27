<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vendedores".
 *
 * @property int $id
 * @property string $SalesEmployeeCode
 * @property string $SalesEmployeeName
 * @property int $EmployeeId
 * @property string $User
 * @property string $Status
 * @property string $DateUpdate
 * @property string $fax
 *
 * @property Usuarioconfiguracion[] $usuarioconfiguracions
 */
class Vendedores extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'vendedores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
                //[['EmployeeId'], 'integer'],
                //[['DateUpdate'], 'safe'],
                //[['SalesEmployeeCode', 'SalesEmployeeName', 'User', 'Status','fax'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'SalesEmployeeCode' => 'Sales Employee Code',
            'SalesEmployeeName' => 'Sales Employee Name',
            'EmployeeId' => 'Employee ID',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
			'fax' => 'fax'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioconfiguracions() {
        return $this->hasMany(Usuarioconfiguracion::className(), ['codEmpleadoVenta' => 'id']);
    }

}
