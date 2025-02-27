<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "opciones_documento".
 *
 * @property int $id
 * @property int $numeroDocumento
 * @property string $formatoPapel
 * @property string $estadoDocumento
 * @property int $almacen
 * @property int $opcionImprimir
 * @property int $opcionCancelar
 * @property int $estado
 * @property int $configuracionId
 * @property string $tipoDocuento
 */
class OpcionesDocumento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'opcionesdocumento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*[['numeroDocumento', 'almacen', 'opcionImprimir', 'opcionCancelar', 'estado', 'configuracionId'], 'integer'],
            [['formatoPapel', 'estadoDocumento', 'tipoDocuento'], 'string', 'max' => 255],*/
        ];
    }

}
