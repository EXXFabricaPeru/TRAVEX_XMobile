<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuario".
 *
 * @property int $id
 * @property int $idusuario
 * @property int $idcondicion
 */
class Usuario_condicionespago extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'usuario_condicionespago';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'id',
            'idUsuario' => 'Id Usuario',
            'idcondicion' => 'Condiciones de pago'
        ];
    }
}