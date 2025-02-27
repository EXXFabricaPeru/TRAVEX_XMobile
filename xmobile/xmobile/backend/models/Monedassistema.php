<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "monedassistema".
 *
 * @property int $id
 * @property string $CurrencyLocal
 * @property string $CurrencySystem
 * @property string $CurrecyOther
 * @property int $Status
 * @property string $DateUpdate
 */
class Monedassistema extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monedassistema';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Status'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['CurrencyLocal', 'CurrencySystem', 'CurrecyOther'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'CurrencyLocal' => 'Currency Local',
            'CurrencySystem' => 'Currency System',
            'CurrecyOther' => 'Currecy Other',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}
