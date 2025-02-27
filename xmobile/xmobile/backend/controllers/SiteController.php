<?php

namespace backend\controllers;

use backend\models\Servislayer;
use backend\models\Unidadesmedida;
use backend\models\Configuracionesgenerales;
use Yii;
use backend\models\Sap;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\LoginForm;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'sincronizacion', 'sincronizar', 'restproductosseries'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        if (Yii::$app->db->getTableSchema('configuracionesgenerales', true) === null) {
            $model = new Configuracionesgenerales();
            $model->actionappx();
            return $this->redirect(['/configuracionesgenerales/index']);
        } else {
            return $this->render('index');
        }
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        Yii::error('VERIFICA LOGIN');
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSincronizacion() {
        set_time_limit(0);
        //ini_set('memory_limit', '2048M');
        Yii::error('INICIO SINCRONIZACION');
        $sap = new Sap();
        /* $sap->gruposUMedida();
          $sap->almacenes();
          $sap->listasPrecios();
          $sap->unidadesMedida();
          $sap->vendedores();
          $sap->clientesGrupos();
          $sap->Monedas();
          $sap->clientes();
          $sap->monedas();
          $sap->productos();
          $sap->lotes();
          $sap->condicionesPagos();
          $sap->territorios();
          $sap->empleadosRoles();
          $sap->empleadosInfo();
          $sap->productosGrupo(); */
        //$sap->lbcc();
        //$sap->descuentosEspeciales();
        //$sap->descuentosGrupo();
        // $sap->leyendas();
        //$sap->motivosAnulacion();
        //$sap->facturas();
        //$sap->indicadoresImpuestos();
        $sap->tipoCambio();
        //$sap->cuentasContables();
        //$sap->productos();
        //$sap->Series();
        //$sap->entregas();
        Yii::error('FIN SINCRONIZACION');
        return $this->render('index');
    }

    /*  public function actionRestproductos() {
      $sap = new Sap();
      return $sap->productos();
      } */

    public function actionRestproductosseries() {
        $sap = new Sap();
        return $sap->seriesProductos();
    }

    public function actionSincronizar() {
       

        set_time_limit(0);
        $datos=Yii::$app->request->post();
        Yii::error("Objeto datos: ");
        Yii::error($datos);
        $sap = new Sap();
        $GUMed = Yii::$app->request->post('GUMed');
        if ($GUMed == 'true')
            $sap->gruposUMedida();
        $Almacen = Yii::$app->request->post('Almacen');
        if ($Almacen == 'true')
            $sap->almacenes();
        $LPrecio = Yii::$app->request->post('LPrecio');
        if ($LPrecio == 'true')
            $sap->listasPrecios();
        $Umed = Yii::$app->request->post('Umed');
        if ($Umed == 'true')
            $sap->unidadesMedida();
        $Vendedor = Yii::$app->request->post('Vendedor');
        if ($Vendedor == 'true')
            $sap->vendedores();
        $GCliente = Yii::$app->request->post('GCliente');
        if ($GCliente == 'true')
            $sap->clientesGrupos();
        $Moneda = Yii::$app->request->post('Moneda');
        if ($Moneda == 'true')
            $sap->Monedas();
        $Cliente = Yii::$app->request->post('Cliente');
        if ($Cliente == 'true')
            $sap->clientes();
            // $sap->Metodocualquiera();
        $Producto = Yii::$app->request->post('Producto');
        if ($Producto == 'true')
            $sap->productos();
        $Lote = Yii::$app->request->post('Lote');
        if ($Lote == 'true')
            $sap->lotes();
        $CPago = Yii::$app->request->post('CPago');
        if ($CPago == 'true')
            $sap->condicionesPagos();
        $Territorio = Yii::$app->request->post('Territorio');
        if ($Territorio == 'true')
            $sap->territorios();
        $REmpleado = Yii::$app->request->post('REmpleado');
        if ($REmpleado == 'true')
            $sap->empleadosRoles();
        $IEmpleado = Yii::$app->request->post('IEmpleado');
        if ($IEmpleado == 'true')
            $sap->empleadosInfo();
        $GProducto = Yii::$app->request->post('GProducto');
        if ($GProducto == 'true')
            $sap->productosGrupo();
        /*$Lbcc = Yii::$app->request->post('Lbcc');
        if ($Lbcc == 'true')
            $sap->lbcc();*/
        $EDescuento = Yii::$app->request->post('EDescuento');
        if ($EDescuento == 'true')
            $sap->descuentosEspeciales();
        $GDescuento = Yii::$app->request->post('GDescuento');
        if ($GDescuento == 'true')
            $sap->descuentosGrupo();
        /*$Leyenda = Yii::$app->request->post('Leyenda');
        if ($Leyenda == 'true')
            $sap->leyendas(); //
        $MAnulacion = Yii::$app->request->post('MAnulacion');
        if ($MAnulacion == 'true')
            $sap->motivosAnulacion(); 
        $Factura = Yii::$app->request->post('Factura');
        if ($Factura == 'true')
            $sap->facturas(); / */
        $IImpuesto = Yii::$app->request->post('IImpuesto');
        if ($IImpuesto == 'true')
            $sap->indicadoresImpuestos();
        $TCambio = Yii::$app->request->post('TCambio');
        if ($TCambio == 'true')
            $sap->tipoCambio();
        $CContable = Yii::$app->request->post('CContable');
        if ($CContable == 'true')
            $sap->cuentasContables();
		/*
        $Serie = Yii::$app->request->post('Serie');
        if ($Serie == 'true')
            $sap->Series();
        $Entrega = Yii::$app->request->post('Entrega');
        if ($Entrega == 'true')
            $sap->entregas(); */ //
        $Empresa = Yii::$app->request->post('Empresa');
        if ($Empresa == 'true')
            $sap->empresa();
        $seriesPro = Yii::$app->request->post('seriesp');
        if ($seriesPro == 'true')
            $sap->seriesProductos();
        $Industrias = Yii::$app->request->post('Industrias');
        if ($Industrias == 'true')
            $sap->industrias();
        $Bonificacion = Yii::$app->request->post('Bonificacion');
        if ($Bonificacion == 'true')
            $sap->Bonificacion();
        $Tarjetas = Yii::$app->request->post('Tarjetas');
        if ($Tarjetas == 'true')
            $sap->tarjetasODBC();
        $Canal = Yii::$app->request->post('Canal');
        if ($Canal == 'true')
            $sap->Canal();

        $Promociones = Yii::$app->request->post('Promociones');
        if ($Promociones == 'true')
            $sap->Promociones();
        ///////////SINCRONIZADORES PROPIOS DE CAMSA/////////////
        $Pcombo = Yii::$app->request->post('Pcombo');
        if ($Pcombo== 'true')
            $sap->combos();
        $PLote = Yii::$app->request->post('PLote');
        if ($PLote== 'true')
            $sap->obtenerProductosLotesODBC();
        $CCierre = Yii::$app->request->post('CCierre');
        if ($CCierre== 'true')
            $sap->CerrarPedidos();
        $Pprecio = Yii::$app->request->post('Pprecio');
        if ($Pprecio== 'true')
            $sap->obtenerProductosPreciosODBC();
        $Palmacen = Yii::$app->request->post('Palmacen');
        if ($Palmacen== 'true')
            $sap->obtenerProductosAlmacenesODBC();
        $TTarjeta= Yii::$app->request->post('TTarjeta');
        if ($TTarjeta== 'true')
            $sap->obtenerTipoTarjetasODBC();
        $CamposUsuario= Yii::$app->request->post('CamposUsuario');
        if ($CamposUsuario== 'true')
            $sap->ObtenerCamposUsuarioAux();
        
        $Electronica = Yii::$app->request->post('Electronica');
        if ($Electronica== 'true');
            $sap->CargaFexCufd(); 

        Yii::error('FIN SINCRONIZACION');
        return $this->render('index');
    }

}
