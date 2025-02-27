<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "tipoactividades".
 *
 * @property int $id
 * @property string $descripcion
 */
class Tipoactividades extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipoactividades';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['descripcion'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Description'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
