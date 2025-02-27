<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "log_envio".
 *
 * @property int $idlog
 * @property string|null $proceso
 * @property string|null $envio
 * @property string|null $respuesta
 * @property string|null $fecha
 * @property string|null $ultimo
 * @property string|null $endpoint
 */
class LogEnvio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_envio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['envio', 'respuesta'], 'string'],
            [['fecha', 'ultimo'], 'safe'],
            [['proceso', 'endpoint'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idlog' => 'Idlog',
            'proceso' => 'Proceso',
            'envio' => 'Envio Objeto Midd',
            'respuesta' => 'Respuesta de SAP',
            'fecha' => 'Fecha',
            'ultimo' => 'Ultimo',
            'endpoint' => 'Endpoint',
            'documento' => 'Codigo',
        ];
    }
}
