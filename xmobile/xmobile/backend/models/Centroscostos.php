<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "centroscostos".
 *
 * @property int $idcentro
 * @property string|null $PrcCode
 * @property string|null $PrcName
 */
class Centroscostos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'centroscostos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PrcCode', 'PrcName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idcentro' => 'Idcentro',
            'PrcCode' => 'Prc Code',
            'PrcName' => 'Prc Name',
        ];
    }
}
