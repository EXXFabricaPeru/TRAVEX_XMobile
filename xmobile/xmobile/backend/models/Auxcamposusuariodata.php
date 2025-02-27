<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "aux_camposusuariodata".
 *
 * @property int $id
 * @property int|null $id_aux_camposusuario
 * @property string|null $code
 * @property string|null $name
 * @property int|null $estado
 *
 * @property AuxCamposusuario $auxCamposusuario
 */
class Auxcamposusuariodata extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'aux_camposusuariodata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_aux_camposusuario', 'estado'], 'integer'],
            [['code', 'name'], 'string', 'max' => 255],
            [['id_aux_camposusuario'], 'exist', 'skipOnError' => true, 'targetClass' => AuxCamposusuario::className(), 'targetAttribute' => ['id_aux_camposusuario' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_aux_camposusuario' => 'Id Aux Camposusuario',
            'code' => 'Code',
            'name' => 'Name',
            'estado' => 'Estado',
        ];
    }

    /**
     * Gets query for [[AuxCamposusuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuxCamposusuario()
    {
        return $this->hasOne(AuxCamposusuario::className(), ['id' => 'id_aux_camposusuario']);
    }
}
