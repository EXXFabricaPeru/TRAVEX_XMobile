<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "propiedadesproductos".
 *
 * @property int $id
 * @property string $ItemCode
 * @property string $propiedad
 * @property string $valor
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 */
class Propiedadesproductos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'propiedadesproductos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['User', 'Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['ItemCode', 'propiedad'], 'string', 'max' => 255],
            [['valor'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ItemCode' => 'Item Code',
            'propiedad' => 'Propiedad',
            'valor' => 'Valor',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
