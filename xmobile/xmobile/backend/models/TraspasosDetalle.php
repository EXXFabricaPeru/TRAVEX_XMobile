<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "traspasodetalle".
 *
* @property int id
* @property int idcabecera
* @property string cardCode
* @property string origenwarehouse
* @property string destinowarehouse
* @property string unidadmedida
* @property int cantidadsolicitada
* @property int cantidadaprobada
* @property int cantidadrecepcionada
* @property string tipoRegistro
* @property string serie
* @property int usuario
* @property int status
* @property datetime dateUpdate
 */
class TraspasosDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traspasodetalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','idcabecera','cantidadsolicitada','cantidadaprobada','cantidadrecepcionada','usuario','status'], 'integer'],
            [['cardCode','origenwarehouse','destinowarehouse','unidadmedida','tipoRegistro','serie'], 'string', 'max' => 1000]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idcabecera' => 'cabecera',
            'origenwarehouse' => 'origenwarehouse',
            'destinowarehouse' => 'destinowarehouse',
            'unidadmedida' => 'unidadmedida',
            'cantidadsolicitada' => 'cantidad solicitada',
            'cantidadaprobada' => 'cantidad aprobada',
            'cantidadrecepcionada' => 'cantidad recepcionada',
            'tipoRegistro' => 'tipo de registro',
            'serie' => 'serie',
            'usuario' => 'usuario',
            'status' => 'status',
            'dateUpdate' => 'dateUpdate',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
  
}
