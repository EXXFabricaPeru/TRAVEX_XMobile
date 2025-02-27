<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "companex_canal".
 *
 * @property int $id
 * @property int|null $docEntry
 * @property string|null $code
 * @property string|null $name
 * @property string|null $canceled
 * @property string|null $objeto
 */
class Companexcanal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'companex_canal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['docEntry'], 'integer'],
            [['code', 'name', 'canceled', 'objeto'], 'string', 'max' => 255],
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
            'code' => 'Code',
            'name' => 'Name',
            'canceled' => 'Canceled',
            'objeto' => 'Objeto',
        ];
    }
}
