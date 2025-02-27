<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_obtienedocumentosanulados".
 *
 * @property string $fechaRegistro
 * @property string $docDate
 * @property string|null $docEntry
 * @property string $docType
 * @property int|null $estado
 * @property string|null $docNum
 * @property string|null $motivoAnulacion
 * @property string $origen
 * @property string|null $usuario
 * @property int|null $idUser
 */
class Viobtienedocumentosanulados extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_obtienedocumentosanulados';
    }
    public static function primaryKey() {
        return ['id'];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechaRegistro', 'docDate'], 'safe'],
            [['estado', 'idUser'], 'integer'],
            [['docEntry', 'docType', 'docNum', 'motivoAnulacion','motivoAnulacionComentario', 'usuario'], 'string', 'max' => 255],
            [['origen'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fechaRegistro' => 'Fecha Registro',
            'docDate' => 'Doc Date',
            'docEntry' => 'Doc Entry',
            'docType' => 'Doc Type',
            'estado' => 'Estado',
            'docNum' => 'Doc Num',
            'motivoAnulacion' => 'Motivo Anulacion',
            'motivoAnulacionComentario' => 'Motivo Anulacion Comentario',
            'origen' => 'Origen',
            'usuario' => 'Usuario',
            'idUser' => 'Id User',
        ];
    }
}
