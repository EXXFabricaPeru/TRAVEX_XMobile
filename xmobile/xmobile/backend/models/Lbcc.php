<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "lbcc".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property int|null $DocEntry
 * @property string|null $Canceled
 * @property string|null $Object
 * @property string|null $LogInst
 * @property int|null $UserSign
 * @property string|null $Transfered
 * @property string|null $CreateDate
 * @property string|null $CreateTime
 * @property string|null $UpdateDate
 * @property string|null $UpdateTime
 * @property string|null $DataSource
 * @property string|null $U_NumeroAutorizacion
 * @property int|null $U_ObjType
 * @property string|null $U_Estado
 * @property int|null $U_PrimerNumero
 * @property int|null $U_NumeroSiguiente
 * @property int|null $U_UltimoNumero
 * @property int|null $U_Series
 * @property string|null $U_SeriesName
 * @property string|null $U_FechaLimiteEmision
 * @property string|null $U_LlaveDosificacion
 * @property string|null $U_Leyenda
 * @property string|null $U_Leyenda2
 * @property int|null $U_TipoDosificacion
 * @property string|null $U_Sucursal
 * @property string|null $U_EmpleadoVentas
 * @property int|null $U_GrupoCliente
 * @property string|null $U_Actividad
 * @property int|null $User
 * @property int|null $Status
 * @property string|null $DateUpdate
 * @property int|null $equipoId
 * @property int|null $U_GrupoProducto
 * @property int $sincategoria
 */
class Lbcc extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'lbcc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {//,'fex_sucursal','fex_puntoventa'
        return [
            //(0==1)?(
              //[['U_LlaveDosificacion','U_NumeroAutorizacion','U_Actividad','U_FechaLimiteEmision'], 'required']
           // ):([['U_Actividad'], 'required']),
            [['U_PrimerNumero', 'U_UltimoNumero', 'U_Series', 'U_TipoDosificacion', 'equipoId', 'papelId'], 'required'],
            [['DocEntry', 'UserSign', 'U_ObjType', 'U_PrimerNumero', 'U_NumeroSiguiente', 'U_UltimoNumero', 'U_Series', 'U_TipoDosificacion', 'U_GrupoCliente', 'User', 'Status', 'equipoId', 'U_GrupoProducto','fex_sucursal','fex_puntoventa','fex_empresa'], 'integer'],
            [['CreateDate', 'CreateTime', 'UpdateDate', 'UpdateTime', 'U_FechaLimiteEmision', 'DateUpdate'], 'safe'],
           // [['U_LlaveDosificacion', 'U_NumeroAutorizacion'], 'unique'],
            [['Code', 'Name', 'Canceled', 'Object', 'LogInst', 'Transfered', 'DataSource', 'U_NumeroAutorizacion', 'U_SeriesName', 'U_LlaveDosificacion', 'U_Leyenda', 'U_Leyenda2', 'U_Sucursal', 'U_Actividad', 'U_EmpleadoVentas'], 'string', 'max' => 255],
            [['U_Estado'], 'string', 'max' => 10],
            [['facturaOffline','idFexTipoDoc'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'Code' => 'Code',
            'Name' => 'Name',
            'DocEntry' => 'Doc Entry',
            'Canceled' => 'Canceled',
            'Object' => 'Object',
            'LogInst' => 'Log Inst',
            'UserSign' => 'User Sign',
            'Transfered' => 'Transfered',
            'CreateDate' => 'Create Date',
            'CreateTime' => 'Create Time',
            'UpdateDate' => 'Update Date',
            'UpdateTime' => 'Update Time',
            'DataSource' => 'Data Source',
            'U_NumeroAutorizacion' => 'N° Autorización',
            'U_ObjType' => 'U Obj Type',
            'U_Estado' => 'Estado',
            'U_PrimerNumero' => 'Numero inicio',
            'U_NumeroSiguiente' => 'N° Siguiente',
            'U_UltimoNumero' => 'Ultimo Numero',
            'U_Series' => 'Serie',
            'U_SeriesName' => 'Nombre de la Serie',
            'U_FechaLimiteEmision' => 'Limite Emisión',
            'U_LlaveDosificacion' => 'Llave dosificación ',
            'U_Leyenda' => 'U Leyenda',
            'U_Leyenda2' => 'U Leyenda2',
            'U_TipoDosificacion' => 'Tipo dosificación',
            'U_Sucursal' => 'U Sucursal',
            'U_EmpleadoVentas' => 'U Empleado Ventas',
            'U_GrupoCliente' => 'U Grupo Cliente',
            'U_Actividad' => 'Actividad',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'equipoId' => 'Equipo ID',
            'papelId' => 'Tipo de papel',
            'U_GrupoProducto' => 'Grupo de productos',
            'sincategoria'  => 'sin categoria',
            'fex_sucursal' => 'Sucursal',
            'fex_puntoventa' => 'Punto de venta',
            'fex_empresa' => 'Empresa',
            'facturaOffline'=>'Factura Offline',
            'idFexTipoDoc'=>'Tipo Documento'
        ];
    }

}
