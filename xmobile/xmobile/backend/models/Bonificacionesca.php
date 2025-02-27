<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "bonificacion_ca".
 *
 * @property int $id
 * @property string|null $Code
 * @property string|null $Name
 * @property string|null $U_tipo
 * @property string|null $U_cliente
 * @property string|null $U_fecha
 * @property string|null $U_fecha_inicio
 * @property string|null $U_fecha_fin
 * @property string|null $U_estado
 * @property int|null $U_limitemaxregalo
 * @property int|null $U_cantidadbonificacion
 * @property string|null $U_observacion
 * @property string|null $U_reglatipo
 * @property string|null $U_reglaunidad
 * @property int|null $U_reglacantidad
 * @property string|null $U_bonificaciontipo
 * @property string|null $U_bonificacionunidad
 * @property int|null $U_bonificacioncantidad
 * @property int|null $U_reglabonificacion
 * @property string|null $idTerritorio
 * @property string|null $territorio
 * @property int|null $idUsuario
 * @property string|null $usuario
 */
class Bonificacionesca extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonificacion_ca';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['Code', 'Name','U_fecha_inicio','U_fecha_fin','idTerritorio','U_limitemaxregalo','U_bonificacioncantidad','U_reglacantidad'], 'required'],
            [['Code', 'Name','U_fecha_inicio','U_fecha_fin','U_bonificaciontipo','detalleEspecifico','tipoReglaCompra'], 'required'],

            [['U_fecha', 'U_fecha_inicio', 'U_fecha_fin','territorio'], 'safe'],
           // [['U_fecha'], 'date', 'format'=>'d-m-Y'],
		    [['U_bonificacioncantidad'], 'double','min'=>1],
            [['porcentajeDescuento'], 'double'],
            [['montoTotal'], 'double','min'=>1],
           //[['U_bonificacioncantidad'],'format'=>['decimal',1]],
            [['U_cantidadbonificacion', 'U_reglacantidad','U_limitemaxregalo','idTerritorio','idUsuario','idBonificacionTipo','cantidadMaximaCompra','idReglaBonificacion','idClienteDosificacion'], 'integer'],
            [['Code', 'Name', 'U_tipo', 'U_cliente', 'U_estado', 'U_observacion', 'U_reglatipo', 'U_reglaunidad', 'U_bonificaciontipo', 'U_bonificacionunidad', 'U_reglabonificacion'], 'string', 'max' => 255],
			[[ 'usuario','tipoReglaCompra','detalleEspecifico','canalCode','canalName','clienteDosificacion'], 'string', 'max' => 100],
            [['U_reglacantidad'], 'integer', 'min'=>1],
		];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Code' => 'Codigo',
            'Name' => 'Nombre Campaña',
           
            'U_cliente' => 'Grupo Cliente',
            'U_fecha' => 'Fecha Registro',
            'U_fecha_inicio' => 'Fecha Inicio',
            'U_fecha_fin' => 'Fecha Fin',
            'U_estado' => 'Situación',
            'U_limitemaxregalo' => 'Límite máximo de Iteraciones (0 = Sin Límite)',
            'U_cantidadbonificacion' => 'Porcentaje Regalo Extra',
            'U_observacion' => 'Observacion',
            'U_reglatipo' => 'Productos Compra',
            'U_tipo' => 'Tipo',
            'U_reglaunidad' => 'Unidad Compra',
            'U_reglacantidad' => 'Cantidad Compra',
            'U_bonificaciontipo' => 'Tipo de Regalo',
            'U_bonificacionunidad' => 'Unidad Regalo',
            'U_bonificacioncantidad' => '',
            'U_reglabonificacion' => 'Modalidad',
			'idTerritorio' => 'Región',
			'territorio' => 'Región',
			'idUsuario' => 'id Usuario',
			'usuario' => 'usuario',
			'idBonificacionTipo' => 'Tipo de Regalo',
            'tipoReglaCompra' => 'Regla Compra',
            'detalleEspecifico' => 'Detalle Especifico',
            'montoTotal' => 'Monto Total',
            'cantidadMaximaCompra' => 'Cantidad Maxima Compra',
            'idReglaBonificacion' => 'Regla Bonificacion',
            'canalCode' => 'Canal',
            'canalName' => 'canalname',
            'porcentajeDescuento' => 'Porcentaje',
            'clienteDosificacion' => 'Cliente Dosificación',
            'idClienteDosificacion' => 'Cliente Dosificación'
        ];
    }
}
