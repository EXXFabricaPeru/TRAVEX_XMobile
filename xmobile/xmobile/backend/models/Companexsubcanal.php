<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "companex_subcanal".
 *
 * @property int $id
 * @property int|null $docEntry
 * @property string|null $canal
 * @property string|null $code
 * @property string|null $name
 * @property string|null $objeto
 * @property string|null $canceled
 */
class Companexsubcanal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'companex_subcanal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['docEntry'], 'integer'],
            [['canal', 'code', 'name', 'objeto', 'canceled'], 'string', 'max' => 255],
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
            'canal' => 'Canal',
            'code' => 'Code',
            'name' => 'Name',
            'objeto' => 'Objeto',
            'canceled' => 'Canceled',
        ];
    }
}
