<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "lineas_pedidos".
 *
 * @property int $id
 * @property int $userId
 * @property int $gestionLinea
 * @property string $mesLinea
 * @property string $correlativoLinea
 * @property int $estado
 * @property string $fechaUMLinea
 * @property string $idDocPedido
 * @property string $docEntry
 * @property int $clienteId
 * @property string $codigoProducto
 * @property string $nombreProducto
 * @property string $precioLinea
 * @property int $cantidadLinea
 *
 * @property Almacenes[] $almacenes
 * @property DocumentosPedidos $cliente
 * @property Unidades[] $unidades
 */
class LineaPedido extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lineas_pedidos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'gestionLinea', 'mesLinea', 'correlativoLinea', 'idDocPedido', 'docEntry', 'clienteId'], 'required'],
            [['userId', 'gestionLinea', 'estado', 'clienteId', 'cantidadLinea'], 'integer'],
            [['fechaUMLinea'], 'safe'],
            [['precioLinea'], 'number'],
            [['mesLinea', 'correlativoLinea', 'idDocPedido', 'docEntry', 'codigoProducto', 'nombreProducto'], 'string', 'max' => 255],
            [['clienteId'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentosPedidos::className(), 'targetAttribute' => ['clienteId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'gestionLinea' => 'Gestion Linea',
            'mesLinea' => 'Mes Linea',
            'correlativoLinea' => 'Correlativo Linea',
            'estado' => 'Estado',
            'fechaUMLinea' => 'Fecha Um Linea',
            'idDocPedido' => 'Id Doc Pedido',
            'docEntry' => 'Doc Entry',
            'clienteId' => 'Cliente ID',
            'codigoProducto' => 'Codigo Producto',
            'nombreProducto' => 'Nombre Producto',
            'precioLinea' => 'Precio Linea',
            'cantidadLinea' => 'Cantidad Linea',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlmacenes()
    {
        return $this->hasMany(Almacenes::className(), ['idLineaPedido' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(DocumentosPedidos::className(), ['id' => 'clienteId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnidades()
    {
        return $this->hasMany(Unidades::className(), ['idLineaPedido' => 'id']);
    }
}
