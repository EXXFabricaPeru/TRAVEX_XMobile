<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "unidadesmedida".
 *
 * @property int $id
 * @property string $AbsEntry
 * @property string $Code
 * @property string $Name
 * @property string $User
 * @property string $Status
 * @property string $DateTime
 *
 * @property Productosprecios[] $productosprecios
 */
class Unidadesmedida extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unidadesmedida';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*[['DateTime'], 'safe'],
            [['AbsEntry', 'Code', 'Name', 'User', 'Status'], 'string', 'max' => 255],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'AbsEntry' => 'Abs Entry',
            'Code' => 'Code',
            'Name' => 'Name',
            'User' => 'User',
            'Status' => 'Status',
            'DateTime' => 'Date Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosprecios()
    {
        return $this->hasMany(Productosprecios::className(), ['IdUnidadMedida' => 'id']);
    }
}
