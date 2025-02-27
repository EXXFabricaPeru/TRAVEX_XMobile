<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "entregasdirecciones".
 *
 * @property int id
 * @property string ShipToStreet
 * @property string ShipToCity
 * @property string ShipToCountry
 * @property int IdCabecera
 * @property int DocEntry
 * @property int Usuario
 * @property int Status
 * @property string DateUpdate
 */
class Entregasdirecciones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entregasdirecciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','IdCabecera','DocEntry','Usuario','Status'], 'integer'],
            [['ShipToStreet','ShipToCity','ShipToCountry','DateUpdate'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'ShipToStreet' => 'ShipToStreet',
            'ShipToCity' => 'ShipToCity',
            'ShipToCountry' => 'ShipToCountry',
            'IdCabecera' => 'IdCabecera',
            'DocEntry' => 'DocEntry',
            'Usuario' => 'Usuario',
            'Status' => 'Status',
            'DateUpdate' => 'DateUpdate'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
