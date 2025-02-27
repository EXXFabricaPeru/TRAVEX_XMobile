<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fex_sucursal".
 *
 * @property int $id
 * @property string|null $fexcompany
 * @property int|null $idpuntoventa
 * @property string|null $descripcion
 */
class FexSucursales extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fex_sucursal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['DocEntry','telefono'],'integer'],
            [['Code','NumSucursal','NombreSucursal','Ubicacion','direccion'],'string','max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'DocEntry' => 'Doc Entry',
            'Code' => 'Código',
            'NumSucursal' => 'Número Sucursal',
            'NombreSucursal' => 'Nombre Sucursal',
            'Ubicacion' => 'Ubicación',
            'direccion' => 'Dirección',
            'telefono' => 'Teléfono'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
}
?>