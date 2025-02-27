<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "traspasocabecera".
 *
* @property int id
* @property string cardCode
* @property string origenWarehouse
* @property string destinoWarehouse
* @property int estado
* @property string estadodescripcion
* @property int usuariosolicitud
* @property int usuarioaprobacion
* @property int usuariorecepcion
* @property datetime fechasolicitud
* @property datetime fechaaprobacion
* @property datetime fecharecepcion
* @property string comentariosolicitud
* @property string comentarioaprobacion
* @property string comentariorecepcion
* @property int usuario
* @property int status
* @property datetime dateUpdate
* @property string DocEntrySolicitud
* @property string DocEntryTraspaso
* @property string MensajeSolicitud
* @property string MensajeTraspaso
 */
class TraspasosCabecera extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traspasocabecera';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','estado','usuariosolicitud','usuarioaprobacion','usuariorecepcion','usuario','status'], 'integer'],
            [['cardCode','estadodescripcion','comentariosolicitud','comentarioaprobacion','comentariorecepcion','origenWarehouse','destinoWarehouse'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'cardCode' => 'cardCode',
            'origenWarehouse' => 'origenWarehouse',
            'destinoWarehouse' => 'destinoWarehouse',
            'estado' => 'estado',
            'usuariosolicitud' => 'usuariosolicitud',
            'usuarioaprobacion' => 'usuarioaprobacion',
            'usuariorecepcion' => 'usuariorecepcion',
            'fechasolicitud' => 'fechasolicitud',
            'fechaaprobacion' => 'fechaaprobacion',
            'fecharecepcion' => 'fecharecepcion',
            'comentariosolicitud' => 'comentariosolicitud',
            'comentarioaprobacion' => 'comentarioaprobacion',
            'comentariorecepcion' => 'comentariorecepcion',
            'usuario' => 'usuario',
            'status' => 'status',
            'dateUpdate' => 'dateUpdate',
            'estadodescripcion' => 'descripcion del estado'
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
