<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fex_tipodocumento".
 *
 * @property int $id
 * @property string|null $codigo
 * @property string|null $descripcion
 * @property int|null $codigoSIN
 */
class Fextipodocumentosin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fex_tipodocumentosin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigoSIN'], 'integer'],
            [['codigo', 'descripcion'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'descripcion' => 'Descripcion',
            'codigoSIN' => 'Codigo Sin',
        ];
    }
}
