<?php

namespace backend\models;

use api\traits\Respuestas;
use backend\helpers\Common;
use backend\helpers\ConexionApi;
use Yii;

/**
 * This is the model class for table "tipoCambioParalelo".
 *
 * @property int id
 * @property int from
 * @property int to
 * @property string $fecha
 * @property string $tipoCambio
 * @property int $User
 * @property int $Status
 * @property string $DateUpdate
 */

class TipoCambioParalelo extends \yii\db\ActiveRecord
{
    use Respuestas;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipoCambioParalelo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','User', 'Status', 'from', 'to'], 'int'],
            [['tipoCambio','fecha', 'DateUpdate'], 'string', 'max' => 255],
            [['fecha'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fecha' => 'Fecha',
            'from' => 'desde',
            'to' => 'a',
            'tipoCambio' => 'Tipo de cambio',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    
}
