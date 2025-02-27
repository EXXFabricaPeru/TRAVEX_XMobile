<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "anulaciondocmovil".
 *
 * @property int $id
 * @property string $fechaRegistro
 * @property string $usuario
 * @property string $docDate
 * @property string $docType
 * @property string $docEntry
 * @property string $motivoAnulacion
 * @property int $estado
 * @property int $idUser
 * @property string $docNum
 * @property string $motivoAnulacionComentario
 */
class Anulaciondocmovil extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'anulaciondocmovil';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechaRegistro', 'usuario', 'docDate', 'docType', 'docEntry'], 'required'],
            [['fechaRegistro', 'docDate'], 'safe'],
            [['estado','idUser'], 'integer'],
            [['usuario', 'docType', 'docEntry','motivoAnulacion','motivoAnulacionComentario','docNum'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fechaRegistro' => 'Fecha Sol. Anulación',
            'usuario' => 'Usuario',
            'docDate' => 'Doc Date',
            'docType' => 'Doc Type',
            'docEntry' => 'Doc Entry',
            'motivoAnulacion' => 'Motivo Anulacion',
            'estado' => 'estado',
            'idUser' => 'Usuario',
            'docNum' => 'Doc. Num.',
            'motivoAnulacionComentario' => 'Motivo Anulacion Comentario',
        ];
    }
}
