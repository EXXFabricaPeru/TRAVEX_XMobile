<?php

namespace backend\modules\admin\models;

use Yii;

/**
 * This is the model class for table "etiquetas".
 *
 * @property int $id
 * @property string $clave Clave
 * @property string $valor Valor
 * @property int $estado Estado
 * @property string $fecha Fecha
 * @property int $idlocalidad Localidad
 *
 * @property Localidad $localidad
 */
class Etiquetas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'etiquetas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clave', 'valor', 'estado', 'idlocalidad'], 'required'],
            [['valor'], 'string'],
            [['estado', 'idlocalidad'], 'integer'],
            [['fecha'], 'safe'],
            [['clave'], 'string', 'max' => 255],
            [['idlocalidad'], 'exist', 'skipOnError' => true, 'targetClass' => Localidad::className(), 'targetAttribute' => ['idlocalidad' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clave' => 'Clave',
            'valor' => 'Valor',
            'estado' => 'Estado',
            'fecha' => 'Fecha',
            'idlocalidad' => 'Localidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocalidad()
    {
        return $this->hasOne(Localidad::className(), ['id' => 'idlocalidad']);
    }
}
