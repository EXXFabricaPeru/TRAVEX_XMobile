<?php

namespace api\controllers;

use yii;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Usuarioconfiguracion;

class ConfiguserController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Usuarioconfiguracion';

    /* public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'tokenParam' => 'access-token',
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    } */

    protected function verbs() {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate() {
        Yii::error(json_encode(Yii::$app->request->post()));
        $configUser = Usuarioconfiguracion::find()
                ->where(['idUser' => Yii::$app->request->post('idUser')])
                ->one();
        if (is_null($configUser)) {
            $configUser = new Usuarioconfiguracion();
        }
        $config = $this->configuracionUsuario($configUser, Yii::$app->request, true);
        if ($config) {
            return $this->correcto($configUser, 'Configuracion registrada');
        }

        return $this->error();
    }

    public function actionUpdate() {
        Yii::error(json_encode(Yii::$app->request->post()));
        $configUser = Usuarioconfiguracion::find()
                ->where(['idUser' => Yii::$app->request->post('idUser')])
                ->one();
        $config = $this->configuracionUsuario($configUser, Yii::$app->request, false);
        if ($config["estado"]) {
            return $this->correcto([$config["modelo"]], 'Registro actualizado');
        }

        return $this->error();
    }

    public function actionView($id) {
        $configUsuario = Usuarioconfiguracion::find()
                ->where(['idUser' => $id])
                ->one();
        if (!is_null($configUsuario)) {
            return $this->correcto($configUsuario);
        }
        return $this->error('No encontrado', 201);
    }

    private function almacenes($almacenes) {
        if (!is_null($almacenes)) {
            if (!empty($almacenes)) {
                if (strpos($almacenes, '\'') === false) {
                    $vAlmacenes = explode(',', $almacenes);
                    $nAlmacenes = '';
                    foreach ($vAlmacenes as $value) {
                        $nAlmacenes .= "'$value',";
                    }
                    $nAlmacenes = substr($nAlmacenes, 0, strlen($nAlmacenes) - 1);
                    return $nAlmacenes;
                } else {
                    return $almacenes;
                }
            }
        }
        return 0;
    }

    private function configuracionUsuario($modelo, $datos, $flag) {
        $modelo->idEstado = $datos->post('idEstado', 0);
        $modelo->idTipoPrecio = $datos->post('idTipoPrecio', 0);
        $modelo->idTipoImpresora = $datos->post('idTipoImpresora', 0);
        $modelo->ruta = $datos->post('ruta', 0);
        $modelo->ctaEfectivo = $datos->post('ctaEfectivo', 0);
        $modelo->ctaCheque = $datos->post('ctaCheque', 0);
        $modelo->ctaTransferencia = $datos->post('ctaTransferencia', 0);
        $modelo->ctaFcEfectivo = $datos->post('ctaFcEfectivo', 0);
        $modelo->ctaFcCheque = $datos->post('ctaFcCheque', 0);
        $modelo->ctaFcTransferencia = $datos->post('ctaFcTransferencia', 0);
        $modelo->sreOfertaVenta = $datos->post('sreOfertaVenta', 0);
        $modelo->sreOrdenVenta = $datos->post('sreOrdenVenta', 0);
        $modelo->sreFactura = $datos->post('sreFactura', 0);
        $modelo->sreFacturaReserva = $datos->post('sreFacturaReserva', 0);
        $modelo->sreCobro = $datos->post('sreCobro', 0);
        $modelo->flujoCaja = $datos->post('flujoCaja', 0);
        $modelo->modInfTributaria = $datos->post('modInfTributaria', 0);
        $modelo->codEmpleadoVenta = $datos->post('codEmpleadoVenta');
        $modelo->codVendedor = $datos->post('codVendedor');
        $modelo->nombre = $datos->post('nombre');
        $modelo->almacenes = $this->almacenes($datos->post('almacenes'));
        $modelo->modMoneda = $datos->post('modMoneda', 0);
        $modelo->ctaTarjeta = $datos->post('ctaTarjeta');
        $modelo->ctaFcTarjeta = $datos->post('ctaFcTarjeta');
        $modelo->crearCliente = $datos->post('crearCliente');
        $modelo->moneda = $datos->post('moneda');
        $modelo->territorio = $datos->post('territorio');
        $modelo->grupoCliente = $datos->post('grupoCliente');
        $modelo->listaPrecios = $datos->post('listaPrecios');
        $modelo->descuentos = $datos->post('descuentos');
        $modelo->totalDescuento = $datos->post('totalDescuento');
        $modelo->totalDescuentoDocumento = $datos->post('totalDescuentoDocumento');
        $modelo->editarDocumento = $datos->post('editarDocumento');
        $modelo->aperturaCaja = $datos->post('aperturaCaja');
        $modelo->cierreCaja = $datos->post('cierreCaja');
        $modelo->condicionPago = $datos->post('condicionPago');
        $multiListaPrecios = $datos->post('multiListaPrecios');
        $multiCamposUsuarios = $datos->post('multiCamposUsuarios');
        
        $mlp = '';
        foreach($multiListaPrecios as $multi){
            if ($mlp == '') $mlp = $multi;
            else $mlp = $mlp.','.$multi;
        }
        $modelo->multiListaPrecios = $mlp;
        $modelo->ctaanticipo = $datos->post('ctaanticipo');
        if ($flag) {
            $modelo->idUser = $datos->post('idUser');
        }
        $this->facturacionUsuario($datos->post('facturacion'),$datos->post('idUser'));
        $this->crearPermisos($datos->post('permisos'), $datos->post('idUser'));
        return [
            "estado" => $modelo->save(false),
            "modelo" => $modelo
        ];
    }

    public function actionFindonebyiduser() {

        $configuraciones = Usuarioconfiguracion::find()
                ->where(['idUser' => Yii::$app->request->post('idUser')])
                ->one();

        if (is_object($configuraciones)) {
            return $this->correcto($configuraciones->toArray());
        }

        return $this->error();
    }
    private function facturacionUsuario($factura,$iduser){
        if ($factura['id'] == '0' && $factura['id'] == 0){
            $sqlSub ="";
            $sqlSub .= "INSERT INTO `lbcc`(`U_NumeroAutorizacion`, `U_ObjType`, `U_Estado`,  `U_PrimerNumero`,  `U_NumeroSiguiente`,  `U_UltimoNumero`, `U_Series`, `U_FechaLimiteEmision`, `U_LlaveDosificacion`, `U_Leyenda`, `U_Leyenda2`, `U_EmpleadoVentas`, `User`) VALUES (";
            $sqlSub .= "'{$factura["autorizacion"]}','13','Y','{$factura["primernumero"]}','{$factura["siguientenumero"]}','{$factura["ultimonumero"]}','{$factura["serie"]}','{$factura["fechalimite"]}','{$factura["llave"]}','{$factura["leyenda1"]}','{$factura["leyenda2"]}','{$factura["vendedornombre"]}','{$iduser}');";
            Yii::error("REgistro de LBCC: ".$sqlSub);
            $db = Yii::$app->db;
            $db->createCommand($sqlSub)->execute();
        }
        else{
            $sql = "UPDATE lbcc SET U_NumeroAutorizacion = :autorizacion, U_PrimerNumero = :primero, U_NumeroSiguiente = :siguiente, U_UltimoNumero = :ultimo, U_Series = :serie, U_FechaLimiteEmision = :fecha, U_LlaveDosificacion = :llave, U_Leyenda = :leyenda1, U_Leyenda2 = :leyenda2, U_EmpleadoVentas = :empleado  WHERE id = :id";
            $db = Yii::$app->db;
            $db->createCommand($sql)
            ->bindValue(':autorizacion', $factura["autorizacion"])
            ->bindValue(':primero', $factura["primernumero"])
            ->bindValue(':siguiente', $factura["siguientenumero"])
            ->bindValue(':ultimo', $factura["ultimonumero"])
            ->bindValue(':serie', $factura["serie"])
            ->bindValue(':fecha', $factura["fechalimite"])
            ->bindValue(':llave', $factura["llave"])
            ->bindValue(':leyenda1', $factura["leyenda1"])
            ->bindValue(':leyenda2', $factura["leyenda2"])
            ->bindValue(':empleado', $factura["vendedornombre"])
            ->bindValue(':id', $factura["id"])
            ->execute();
        }
    }

    private function crearPermisos($permisosData, $iduser){
        $fecha = date("Y-m-d");
        $sql = 'DELETE FROM permisos WHERE IdUser = :usuario';
        $db = Yii::$app->db;
        $db->createCommand($sql)
            ->bindValue(':usuario', $iduser)
            ->execute();
        foreach($permisosData as $permiso){
            $sqlins = 'INSERT INTO `permisos`(`IdMenuPlatform`, `IdUser`, `Key`, `User`, `Status`, `DateUpdate`) VALUES (';
            $sqlins .= "1,'{$iduser}','{$permiso}',2,1,'{$fecha}');";
            $db->createCommand($sqlins)->execute();
        }
    }
}
