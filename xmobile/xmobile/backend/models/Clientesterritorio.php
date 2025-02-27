<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "clientesterritorio".
 *
 * @property int $id
 * @property string|null $CardCode
 * @property string|null $CardName
 * @property int|null $TerritoryId
 * @property string|null $TerritoryName
 */
class Clientesterritorio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientesterritorio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'TerritoryId'], 'integer'],
            [['fechaRegistro'], 'safe'],
            [['CardCode', 'CardName', 'TerritoryName'], 'string', 'max' => 255],
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
            'CardCode' => 'Card Code',
            'CardName' => 'Card Name',
            'TerritoryId' => 'Territory ID',
            'TerritoryName' => 'Territory Name',
        ];
    }
}
