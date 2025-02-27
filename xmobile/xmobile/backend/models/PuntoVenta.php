<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fex_puntoventa".
 *
 * @property int $id
 * @property string|null $fexcompany
 * @property int|null $idpuntoventa
 * @property string|null $descripcion
 */
class PuntoVenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fex_puntoventa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fexcompany','idpuntoventa'],'integer'],
            [['descripcion'],'string','max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fexcompany' => 'Fex Company',
            'idpuntoventa' => 'Id punto venta',
            'descripcion' => 'Descripción'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
}
?>