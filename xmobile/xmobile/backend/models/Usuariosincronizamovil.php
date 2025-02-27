<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "usuariosincroniza".
 *
 * @property int $id
 * @property int $idUsuario
 * @property int $idSucursal
 * @property string $equipo
 * @property string $fecha
 * @property string $fechahora
 * @property string $servicio
 */
class Usuariosincronizamovil extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuariosincronizamovil';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idUsuario', 'idSucursal', 'equipo', 'fecha'], 'required'],
            [['idUsuario', 'idSucursal'], 'integer'],
            [['fecha','fechahora'], 'safe'],
            [['equipo','servicio'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idUsuario' => 'Id Usuario',
            'idSucursal' => 'Id Sucursal',
            'equipo' => 'Equipo',
            'fecha' => 'Fecha',
            'fechahora' => 'Fecha y Hora',
            'servicio' => 'servicio',
        ];
    }
    
    public function actionUsuarioSincronizaMovil($datos,$servicio){

        $this->load(Yii::$app->request->post()); 
        $this->fecha=date('Y-m-d');
        $this->fechahora=date('Y-m-d H:i:s');
        $this->idUsuario=$datos["usuario"];
        $this->idSucursal=$datos["sucursal"];
        $this->equipo=$datos["equipo"];
        $this->servicio=$servicio;

        $this->save();       
      
    }
}
