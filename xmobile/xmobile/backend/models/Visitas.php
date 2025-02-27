<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "visitas".
 *
 * @property int $id
 * @property string|null $CardCode
 * @property string|null $CardName
 * @property string|null $fecha
 * @property string|null $hora
 * @property string|null $horafin
 * @property string|null $lat
 * @property string|null $lng
 * @property string|null $foto
 * @property int $usuario
 * @property string|null $estadoEnviado
 */
class Visitas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visitas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha', 'hora', 'horafin'], 'safe'],
            [['usuario'], 'integer'],
            [['CardCode', 'CardName', 'lat', 'lng','estadoEnviado'], 'string', 'max' => 100],
            [['foto'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'CardCode' => 'Card Code',
            'CardName' => 'Card Name',
            'fecha' => 'Fecha',
            'hora' => 'Hora',
            'horafin' => 'Horafin',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'foto' => 'Foto',
            'usuario' => 'usuario',
            'estadoEnviado' => 'estadoEnviado'
        ];
    }
}
