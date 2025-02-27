<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuariocentrodecosto".
 *
 * @property int $id
 * @property int|null $iduser
 * @property string|null $PrcCode
 */
class Usuariocentrodecosto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuariocentrodecosto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'iduser'], 'integer'],
            [['PrcCode'], 'string', 'max' => 255],
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
            'iduser' => 'Iduser',
            'PrcCode' => 'Prc Code',
        ];
    }
}
