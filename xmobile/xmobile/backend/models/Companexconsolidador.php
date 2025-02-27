<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "companex_consolidador".
 *
 * @property int $id
 * @property int|null $docentry
 * @property string|null $code
 * @property string|null $name
 */
class Companexconsolidador extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'companex_consolidador';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['docentry'], 'integer'],
            [['code', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'docentry' => 'Docentry',
            'code' => 'Code',
            'name' => 'Name',
        ];
    }
}
