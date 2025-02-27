<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_poligonocabecera".
 *
 * @property int|null $territoryid
 * @property string|null $Description
 * @property string|null $nombre
 * @property int $id
 */
class Vipoligonocabecera extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_poligonocabecera';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['territoryid', 'id'], 'integer'],
            [['Description', 'nombre'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'territoryid' => 'Territoryid',
            'Description' => 'Description',
            'nombre' => 'Nombre',
            'id' => 'ID',
        ];
    }
}
