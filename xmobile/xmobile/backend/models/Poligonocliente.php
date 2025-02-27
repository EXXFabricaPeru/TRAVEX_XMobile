<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "poligonocliente".
 *
 * @property int $id
 * @property int|null $cardcode
 * @property string|null $cardname
 * @property int|null $territoryid
 * @property string|null $territoryname
 * @property int|null $poligonoid
 * @property string|null $poligononombre
 * @property int|null $posicion
 * @property int|null $dia
 * @property int|null $vendedor
 * @property string|null nombreDireccion
 * @property string|null calle
 * @property int|null idCabecera
 */
class Poligonocliente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'poligonocliente';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cardcode', 'territoryid', 'poligonoid','posicion','dia','vendedor','idCabecera'], 'integer'],
            [['cardname', 'territoryname', 'poligononombre','nombreDireccion'], 'string', 'max' => 255],
            [['calle'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cardcode' => 'Codigo de cliente',
            'cardname' => 'Cliente',
            'territoryid' => 'Id del territorio',
            'territoryname' => 'Territorio',
            'poligonoid' => 'Poligonoid',
            'poligononombre' => 'Poligono',
            'posicion' => 'posicion',
            'dia' => 'Dia',
            'vendedor' => 'Vendedor',
            'nombreDireccion' => 'Direccion',
            'calle' => 'calle',
            'idCabecera'=>'id Cabecera'
        ];
    }
}
