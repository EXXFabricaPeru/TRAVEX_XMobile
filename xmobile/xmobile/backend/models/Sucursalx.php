<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sucursalx".
 *
 * @property int $id
 * @property string|null $nombre Nombre de la sucursal
 * @property string|null $direccion Dirección
 * @property string|null $descripcion Descripción
 * @property string|null $codigo  Código 
 * @property string|null $empresa Empresa
 * @property string|null $telefonouno Teléfono de ref. Uno 
 * @property string $telefonodos Teléfono de ref. Dos 
 * @property string $pais Pais
 * @property string $ciudad Ciudad
 * @property string|null $leyendauno Leyenda de ref. Uno
 * @property string|null $leyendados Leyenda de ref. Dos
 */
class Sucursalx extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'sucursalx';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['descripcion'], 'string'],
            [['telefonouno', 'nombre', 'direccion', 'pais', 'ciudad'], 'required'],
            [['nombre', 'direccion'], 'string', 'max' => 100],
            [['codigo'], 'string', 'max' => 20],
            [['empresa'], 'string', 'max' => 150],
            [['telefonouno', 'telefonodos'], 'string', 'max' => 15],
            [['pais', 'ciudad'], 'string', 'max' => 50],
            [['leyendauno', 'leyendados'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'direccion' => 'Dirección',
            'descripcion' => 'Descripción ',
            'codigo' => 'Codigo',
            'empresa' => 'Empresa',
            'telefonouno' => 'Teléfono de ref. Uno ',
            'telefonodos' => 'Teléfono de ref. Dos ',
            'pais' => 'Pais',
            'ciudad' => 'Ciudad',
            'leyendauno' => 'Leyenda de ref. Uno',
            'leyendados' => 'Leyenda de ref. Dos',
        ];
    }

}
