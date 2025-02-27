<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "traspasoserie".
 *
 * @property int id;
 * @property int idDetalle;
 * @property int DocEntry;
 * @property string ItemCode;
 * @property string SerialNumber;
 * @property int SystemNumber;
 * @property date AdmissionDate;
 * @property int User;
 * @property int Status;
 * @property date Date;
 * @property string WsCode;
 */
class TraspasosSerie extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traspasoserie';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','idDetalle','DocEntry','SystemNumber','User','Status'], 'integer'],
            [['ItemCode','SerialNumber','WsCode'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'			=> 'id',
            'idDetalle'     => 'idDetalle',
            'DocEntry'      => 'DocEntry',
            'ItemCode'      => 'ItemCode',
            'SerialNumber'  => 'SerialNumber',
            'SystemNumber'  => 'SystemNumber',
            'AdmissionDate' => 'AdmissionDate',
            'User'          => 'User',
            'Status'        => 'Status',
            'Date'          => 'Date',
            'WsCode'        => 'WsCode'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
