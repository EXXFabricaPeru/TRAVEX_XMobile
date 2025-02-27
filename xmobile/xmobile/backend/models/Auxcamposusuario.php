<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "aux_camposusuario".
 *
 * @property int $id
 * @property string|null $objeto
 * @property string|null $label
 * @property string|null $visible
 * @property string|null $camposap
 * @property string|null $tablasap
 * @property string|null $condicion
 * @property string|null $code origen
 * @property string|null $name origen
 * @property string|null $tabla origen
 * @property string|null $cond
 * @property int|null $estado
 * @property string|null $variablemovil
 *
 * @property AuxCamposusuariodata[] $auxCamposusuariodatas
 */
class Auxcamposusuario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'aux_camposusuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'integer'],
            [['objeto', 'label', 'visible', 'camposap', 'tablasap', 'condicion', 'code', 'name', 'tabla', 'cond', 'variablemovil'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'objeto' => 'Objeto',
            'label' => 'Label',
            'visible' => 'Visible',
            'camposap' => 'Camposap',
            'tablasap' => 'Tablasap',
            'condicion' => 'Condicion',
            'code' => 'Code',
            'name' => 'Name',
            'tabla' => 'Tabla',
            'cond' => 'Cond',
            'estado' => 'Estado',
            'variablemovil' => 'Variablemovil',
        ];
    }

    /**
     * Gets query for [[AuxCamposusuariodatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuxCamposusuariodatas()
    {
        return $this->hasMany(AuxCamposusuariodata::className(), ['id_aux_camposusuario' => 'id']);
    }
}
