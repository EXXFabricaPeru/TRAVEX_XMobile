<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "entregasdetalle".
 *
 * @property int id
 * @property int LineNum
 * @property string ItemCode
 * @property string ItemDescription
 * @property string Price
 * @property string TaxCode
 * @property int UoMEntry
 * @property string UoMCode
 * @property int IdCabecera
 * @property int DocEntry
 * @property int Usuario
 * @property int Status
 * @property string DateUpdate
 */
class Entregasdetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entregasdetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','LineNum','UoMEntry','IdCabecera','DocEntry','Usuario','Status'], 'integer'],
            [['ItemCode','ItemDescription','Price','TaxCode','UoMcode','DateUpdate'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'LineNum' => 'LineNum',
            'ItemCode' => 'ItemCode',
            'ItemDescription' => 'ItemDescription',
            'Price' => 'Price',
            'TaxCode' => 'TaxCode',
            'UoMEntry' => 'UoMEntry',
            'UoMCode' => 'UoMCode',
            'IdCabecera' => 'IdCabecera',
            'DocEntry' => 'DocEntry'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
