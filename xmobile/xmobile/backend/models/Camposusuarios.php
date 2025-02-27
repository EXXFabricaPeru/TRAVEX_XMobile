<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "camposusuarios".
 *
 * @property int $Id
 * @property string|null $Objeto
 * @property string|null $Nombre
 * @property string|null $Tblsap
 * @property string|null $Campsap
 * @property int|null $tipocampo
 * @property int|null $longitud
 * @property string|null $Fechainsert
 * @property string|null $Userinser
 * @property string|null $FechaUpdate
 * @property string|null $UserUpdate
 * @property int|null $Status
 */
class Camposusuarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'camposusuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipocampo', 'longitud', 'Status'], 'integer'],
            [['Fechainsert', 'FechaUpdate'], 'safe'],
            [['Objeto'], 'string', 'max' => 100],
            [['Nombre', 'Tblsap', 'Campsap', 'Userinser', 'UserUpdate','Campmidd','Label'], 'string', 'max' => 50],
            [['Objeto', 'Nombre', 'Tblsap', 'Campsap', 'tipocampo','Status','Campmidd'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Objeto' => 'Objeto',
            'Nombre' => 'Nombre del Campo',
            'Tblsap' => 'Tabla en SAP',
            'Campsap' => 'Campo en SAP',
            'tipocampo' => 'Tipo de campo',
            'longitud' => 'Longitud',
            'Fechainsert' => 'Fechainsert',
            'Userinser' => 'Userinser',
            'FechaUpdate' => 'Fecha Update',
            'UserUpdate' => 'User Update',
            'Status' => 'Status',
            'Campmidd' => 'Campo en el Midd',
            'Label' => 'Label del campo',
        ];
    }
}
