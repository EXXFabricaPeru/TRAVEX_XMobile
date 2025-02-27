<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "monmotivosanulacion".
 *
 * @property int $id
 * @property string $Code
 * @property string $Name
 * @property string $U_TipoAnulacion
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 */
class Motivoanulacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'motivosanulacion';
    }

        /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
           // [['id, User, Status'], 'integer'],
           // [['DateUpdate'], 'safe'],
            [['Code', 'Name', 'U_TipoAnulacion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Code' => 'Codigo',
            'Name' => 'Nombre',
            'U_TipoAnulacion' => 'Tipo de anulacion',
            'User' => 'Usuario',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }
}