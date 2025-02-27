<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "companex_cadena".
 *
 * @property int $id
 * @property int|null $docEntry
 * @property string|null $tipotienda
 * @property string|null $code
 * @property string|null $name
 * @property string|null $canceled
 * @property string|null $objeto
 */
class Companexcadena extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'companex_cadena';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'docEntry'], 'integer'],
            [['tipotienda', 'code', 'name', 'canceled', 'objeto'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'docEntry' => 'Doc Entry',
            'tipotienda' => 'Tipotienda',
            'code' => 'Code',
            'name' => 'Name',
            'canceled' => 'Canceled',
            'objeto' => 'Objeto',
        ];
    }
}
