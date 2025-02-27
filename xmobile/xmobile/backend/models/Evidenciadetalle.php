<?php

namespace backend\models;
use Yii;

/**
 * This is the model class for table "evidenciadetalle".
 *
 * @property string|null $idDocPedido
 * @property string|null $tipo_evidencia
 * @property string|null $ruta
 * @property string|null $nombre
 */
class Evidenciadetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'evidenciadetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idDocPedido'], 'string', 'max' => 255],
            [['idCabecera'], 'integer'],
            [['tipo_evidencia'], 'string', 'max' => 2],
            [['ruta', 'nombre'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idDocPedido' => 'Id Doc Pedido',
            'tipo_evidencia' => 'Tipo Evidencia',
            'ruta' => 'Ruta',
            'nombre' => 'Nombre',
        ];
    }
}
