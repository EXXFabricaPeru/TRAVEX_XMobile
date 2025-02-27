<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "traspasoserie".
 *
 * @property int id;
 * @property int idDetalle;
 * @property string ItemCode;
 * @property string BatchNum;
 * @property string WhsCode;
 * @property string ItemName;
 * @property string SuppSerial;
 * @property string IntrSerial;
 * @property string ExpDate;
 * @property string PrdDate;
 * @property string InDate;
 * @property string Located;
 * @property string Notes;
 * @property string Quantity;
 * @property string BaseType;
 * @property string BaseEntry;
 * @property string BaseNum;
 * @property string BaseLinNum;
 * @property string CardCode;
 * @property string CardName;
 * @property string CreateDate;
 * @property string Status;
 * @property string Direction;
 * @property string IsCommited;
 * @property string OnOrder;
 * @property string Consig;
 * @property string DataSource;
 * @property string UserSign;
 * @property string Transfered;
 * @property string Instance;
 * @property string SysNumber;
 * @property string LogInstanc;
 * @property string UserSign2;
 * @property string UpdateDate;
 */
class TraspasosLote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traspasolote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','idDetalle'], 'integer'],
            [['ItemCode' ,'BatchNum' ,'WhsCode' ,'ItemName' ,'SuppSerial' ,'IntrSerial' ,'ExpDate' ,'PrdDate' ,'InDate' ,'Located' ,'Notes' ,'Quantity' ,'BaseType' ,'BaseEntry' ,'BaseNum' ,'BaseLinNum' ,'CardCode' ,'CardName' ,'CreateDate' ,'Status' ,'Direction' ,'IsCommited' ,'OnOrder' ,'Consig' ,'DataSource' ,'UserSign' ,'Transfered' ,'Instance' ,'SysNumber' ,'LogInstanc' ,'UserSign2' ,'UpdateDate'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idDetalle' => 'detalle',
            'ItemCode'=>'ItemCode',
            'BatchNum'=>'BatchNum',
            'WhsCode' =>'WhsCode', 
            'ItemName'=>'ItemName',
            'SuppSerial'=>'SuppSerial',
            'IntrSerial'=>'IntrSerial',
            'ExpDate' =>'ExpDate', 
            'PrdDate' =>'PrdDate', 
            'InDate'=>'InDate',
            'Located' =>'Located', 
            'Notes' =>'Notes', 
            'Quantity'=>'Quantity',
            'BaseType'=>'BaseType',
            'BaseEntry' =>'BaseEntry',
            'BaseNum' =>'BaseNum',
            'BaseLinNum'=>'BaseLinNum',
            'CardCode'=>'CardCode',
            'CardName'=>'CardName',
            'CreateDate'=>'CreateDate',
            'Status'=>'Status',
            'Direction' =>'Direction' ,
            'IsCommited'=>'IsCommited',
            'OnOrder' =>'OnOrder' ,
            'Consig'=>'Consig',
            'DataSource'=>'DataSource',
            'UserSign'=>'UserSign',
            'Transfered'=>'Transfered',
            'Instance'=>'Instance',
            'SysNumber' =>'SysNumber' ,
            'LogInstanc'=>'LogInstanc',
            'UserSign2' =>'UserSign2' ,
            'UpdateDate'=>'UpdateDate'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
