<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "productosprecios".
 *
 * @property int $id
 * @property int $IdProducto
 * @property int $IdListaPrecios
 * @property int $IdUnidadMedida
 * @property string $Price
 * @property string $Currency
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 *
 * @property Detalledocumentos[] $detalledocumentos
 * @property Productos $producto
 * @property Listaprecios $listaPrecios
 * @property Unidadesmedida $unidadMedida
 */
class Copiaproductosprecios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'copiaproductosprecios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*[['IdProducto', 'IdListaPrecios', 'IdUnidadMedida', 'User', 'Status'], 'integer'],
            [['Price'], 'number'],
            [['DateUpdate'], 'safe'],
            [['Currency'], 'string', 'max' => 255],
            [['IdProducto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['IdProducto' => 'id']],
            [['IdListaPrecios'], 'exist', 'skipOnError' => true, 'targetClass' => Listaprecios::className(), 'targetAttribute' => ['IdListaPrecios' => 'id']],
            [['IdUnidadMedida'], 'exist', 'skipOnError' => true, 'targetClass' => Unidadesmedida::className(), 'targetAttribute' => ['IdUnidadMedida' => 'id']],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'IdProducto' => 'Id Producto',
            'IdListaPrecios' => 'Id Lista Precios',
            'IdUnidadMedida' => 'Id Unidad Medida',
            'Price' => 'Price',
            'Currency' => 'Currency',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalledocumentos()
    {
        return $this->hasMany(Detalledocumentos::className(), ['idProductoPrecio' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Productos::className(), ['ItemCode' => 'ItemCode']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getListaPrecios()
    {
        return $this->hasOne(Listaprecios::className(), ['id' => 'IdListaPrecios']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidadMedida()
    {
        return $this->hasOne(Unidadesmedida::className(), ['id' => 'IdUnidadMedida']);
    }
}
