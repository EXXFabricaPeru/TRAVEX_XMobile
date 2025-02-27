<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "clientessucursales".
 *
 * @property int $id
 * @property string $AddresName
 * @property string $Street
 * @property string $State
 * @property string $FederalTaxId
 * @property string $CreditLimit U_EXI_LIMCRE campo definido por el usuario
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 * @property int $CardCode
 *
 * @property Clientes $cliente
 * @property  string TaxCode
 */
class Clientessucursales extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'clientessucursales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['User', 'Status','idupdate','RowNum','idCliente'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['AddresName', 'Street', 'State', 'FederalTaxId', 'CreditLimit','CardCode','TaxCode','AdresType','u_zona','u_lat','u_lon','u_territorio','u_vendedor','Mobilecode','CAMPOUSER1','CAMPOUSER2','CAMPOUSER3','CAMPOUSER4','CAMPOUSER5'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'AddresName' => 'Addres Name',
            'Street' => 'Street',
            'State' => 'State',
            'FederalTaxId' => 'Federal Tax ID',
            'CreditLimit' => 'U_EXI_LIMCRE campo definido por el usuario',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'IdCliente' => 'Id Cliente',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente() {
        return $this->hasOne(Clientes::className(), ['CardCode' => 'CardCode']);
    }

}
