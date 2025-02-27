<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equipox".
 *
 * @property int $id
 * @property string $equipo Nombre del equipo
 * @property string $uuid UUID
 * @property string|null $keyid KEYID
 * @property string $plataforma Plataforma
 * @property int $estado Estado
 * @property string|null $registrado
 * @property string|null $version
 * @property int $sucursalxId
 */
class Equipox extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'equipox';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['equipo', 'uuid', 'plataforma', 'plataforma'], 'required'],
            [['sucursalxId','fex'], 'integer'],
            [['registrado', 'estado'], 'safe'],
            [['uuid'], 'unique', 'message' => '!ALERTA - El dispositivo ya fue registrado.'],
            [['equipo', 'uuid', 'keyid'], 'string', 'max' => 100],
            [['plataforma'], 'string', 'max' => 50],
            [['version'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'equipo' => 'Equipo',
            'uuid' => 'UUID',
            'keyid' => 'KEYID',
            'plataforma' => 'Plataforma',
            'estado' => 'Estado',
            'registrado' => 'Registrado',
            'version' => 'Versión',
            'sucursalxId' => 'Sucursal',
            'fex' => 'Facturacion Electronica',
        ];
    }

}
