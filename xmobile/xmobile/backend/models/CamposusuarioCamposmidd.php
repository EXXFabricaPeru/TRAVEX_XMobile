<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "camposusuario_camposmidd".
 *
 * @property int $id
 * @property int|null $idobjeto
 * @property string|null $Nombre
 * @property int|null $Status
 */
class CamposusuarioCamposmidd extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'camposusuario_camposmidd';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idobjeto', 'Status'], 'integer'],
            [['Nombre'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idobjeto' => 'Idobjeto',
            'Nombre' => 'Nombre',
            'Status' => 'Status',
        ];
    }
}
