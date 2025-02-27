<?php

namespace console\controllers;

use backend\models\Configlayer;
use backend\models\Sincronizar;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Yii;
use yii\console\Controller;
use backend\models\Sap;

class SincController extends Controller {

    public $id;
    private $cliente;
    private $conf;
    private $serviceLayer;

    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);
        $this->conf = new Configlayer();
        $this->cliente = new Client([
            'base_uri' => "http://192.168.50.75:8082/xmobile_sinc/",
            'timeout' => 30,
            'verify' => false,
            'cookies' => true
        ]);
        $this->serviceLayer = new Sincronizar();
    }

    public function actionSicronizarsap() {
        set_time_limit(0);
        $sap = new Sap();
        //$sap->CamposUsuario();
		//$this->obtenerBonificacionesSemiautomaticasCA(); // Para Diarios
		//$this->obtenerBonificacionesSemiautomaticasDE1(); // Para Diarios
		//$this->obtenerBonificacionesSemiautomaticasDE2(); // Para Diarios

		// $this->obtenerContactosClientes(); // DIARIO
        // $this->obtenerSucursalClientes(); // DIARIO
		// $this->obtenerProductosCombo();
		$this->obtenerEmpleadosVenta();
        //$this->ObtenrFacturasCabecera();
        //$this->ObtenrFacturasDetalle();
        //$this->ObtenrPedidosCabecera();
        //$this->ObtenrPedidosDetalle();
        //$this->ObtenerSapOfertasCabecera();
        //$this->ObtenerSapOfertasDetalles();
        //$this->ObtenerSapEntregasCabecera();
        //$this->ObtenerSapEntregasDetalles();
       //$this->ObtenerRelUnidMedidaGrupo();
       // $this->ObtenerSapNotasCreditoCabecera();
       // $this->ObtenerSapNotasCreditoDetalles();
        $this->ObtenerRelUnidMedidaGrupo2();
        $this->ObtenerCentrosCosto();
        $this->ObtenerSeriesProductos();
        $this->ObtenerBancos();
        $this->ObtenerAlmacenesSeries();
        //$this->ObtenerLotesProductos();
        $this->ObtenerPagosCuenta();
        $this->ObtenerCuotasPagos();
        $this->ObtenerCuotasFacturas();
        //$this->ObtenerProductosAlternativos();
        //$this->ActualizarSeriesUsadas();
        $this->reEnviarClientesNuevos();

		
        //$this->ObtenerDosificacionParaguay();
        //$this->obtenerProductos();
		
		//$this->ObtenerActividadCliente();
		//$this->ObtenerActividadProducto();
    }

    public function reEnviarClientesNuevos(){// NUEVA
        Yii::error();

    }

    public function obtenerBonificacionesSemiautomaticasCA() {
        Yii::error("SINCRONIZA ODBC: BonificacionesSemiAutomaticas BONIFICACION_CA");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 200));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE bonificacion_ca;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";

                $sqlSub .= "INSERT INTO `bonificacion_ca`(`Code`,`Name`,`U_tipo`,`U_cliente`,`U_fecha`,`U_fecha_inicio`,`U_fecha_fin`,`U_estado`,`U_entrega`,`U_cantidadbonificacion`,`U_observacion`,`U_reglatipo`,`U_reglaunidad`,`U_reglacantidad`,`U_bonificaciontipo`,`U_bonificacionunidad`,`U_bonificacioncantidad`) VALUES (";
                
                $sqlSub .= "'{$punteroSub->Code}', '{$punteroSub->Name}', '{$punteroSub->U_tipo}', '{$punteroSub->U_cliente}', '{$punteroSub->U_fecha}', '{$punteroSub->U_fecha_inicio}', '{$punteroSub->U_fecha_fin}', '{$punteroSub->U_estado}', '{$punteroSub->U_entrega}', '{$punteroSub->U_cantidadbonificacion}', '{$punteroSub->U_observacion}', '{$punteroSub->U_reglatipo}', '{$punteroSub->U_reglaunidad}', '{$punteroSub->U_reglacantidad}', '{$punteroSub->U_bonificaciontipo}', '{$punteroSub->U_bonificacionunidad}', '{$punteroSub->U_bonificacioncantidad}')";
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' bonificacion_ca', $e);
        }
    }

    public function obtenerBonificacionesSemiautomaticasDE1() {
        Yii::error("SINCRONIZA ODBC: BonificacionesSemiAutomaticas bonificacion_de1");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 201));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE bonificacion_de1;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";

                $sqlSub .= "INSERT INTO `bonificacion_de1`(`Code`,`Name`,`U_ID_bonificacion`,`U_regla`) VALUES (";
                $sqlSub .= "'{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->U_ID_bonificacion}','{$punteroSub->U_regla}')";
                //Yii::error("SINCRONIZA bonificacion_de1: ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' bonificacion_de1', $e);
        }
    }

    public function obtenerBonificacionesSemiautomaticasDE2() {
        Yii::error("SINCRONIZA ODBC: BonificacionesSemiAutomaticas bonificacion_de2");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 202));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE bonificacion_de2;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";
                
                $sqlSub .= "INSERT INTO `bonificacion_de2`(`Code`,`Name`,`U_ID_bonificacion`,`U_bonificacion`) VALUES (";
                $sqlSub .= "'{$punteroSub->Code}','{$punteroSub->Name}','{$punteroSub->U_ID_bonificacion}','{$punteroSub->U_bonificacion}')";
                // Yii::error("SINCRONIZA bonificacion_de2: ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' bonificacion_de2', $e);
        }
    }

    public function actionSicronizarsapcopia() {
        set_time_limit(0);
		/*
        $this->ObtenrFacturasCabecera();
        $this->ObtenrFacturasDetalle();
        $this->ObtenrPedidosCabecera();
        $this->ObtenrPedidosDetalle();
        $this->ObtenerSapOfertasCabecera();
        $this->ObtenerSapOfertasDetalles();
        $this->ObtenerSapEntregasCabecera();
        $this->ObtenerSapEntregasDetalles();
        $this->ObtenerSapNotasCreditoCabecera();
        $this->ObtenerSapNotasCreditoDetalles();
        $this->ObtenerCentrosCosto();
        $this->ObtenerBancos();
        $this->ObtenerAlmacenesSeries();
        $this->ObtenerLotesProductos();
        $this->ObtenerPagosCuenta();
        $this->ObtenerCuotasPagos();
        $this->ObtenerCuotasFacturas();
        $this->ObtenerProductosAlternativos();
        
        $this->ActualizarSeriesUsadas();
		*/
        //$this->ObtenerDosificacionParaguay();
        //$this->obtenerProductos();
        $this->ObtenerProductosAlmacenes();
        //$this->ObtenerProductosPrecios();
    }

    private function ObtenrFacturasCabecera() {
        Yii::error("SINCRONIZA ODBC: Facturas Cabecera");
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 1));
        $respuesta = $serviceLayer->executex($data);
        //Yii::error("SINCRONIZA ODBC: Facturas Cabecera respuesta ".$respuesta);
        $respuesta = json_decode($respuesta);
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE facturas;SET FOREIGN_KEY_CHECKS = 1;')->execute();

        foreach ($respuesta as $puntero) {
            $pagado = round($puntero->PaidToDate, 2);
            $saldo = round($puntero->Saldo, 2);
            If ($puntero->JournalMemo == null) {
                $puntero->JournalMemo = "nn";
            }
            // Yii::error("TransId: " . $puntero->TransId . " <---> " . $puntero->U_LB_TipoFactura);
            $sql = "";
            $sql .= "INSERT INTO facturas (id,";
            $sql .= "DocEntry,DocNum,DocDate,DocDueDate";
            $sql .= ",CardCode,CardName,DocTotal,DocCurrency,JournalMemo";
            $sql .= ",PaymentGroupCode,DocTime,Series,TaxDate,CreationDate,UpdateDate";
            $sql .= ",FinancialPeriod,UpdateTime,U_LB_NumeroFactura,U_LB_NumeroAutorizac,U_LB_FechaLimiteEmis";
            $sql .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
            $sql .= ",ReserveInvoice,PaidtoDate,Saldo,TransId,DocStatus,InvStatus,User,Status,DateUpdate";
            $sql .= ") VALUES (DEFAULT,";
            $sql .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
            $sql .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocCurrency}','{$puntero->JournalMemo}'";
            $sql .= ",{$puntero->PaymentGroupCode},'{$puntero->DocTime}',{$puntero->Series},'{$puntero->TaxDate}','{$puntero->CreationDate}','{$puntero->UpdateDate}'";
            $sql .= ",'{$puntero->FinancialPeriod}','{$puntero->UpdateTime}','{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
            $sql .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}','{$puntero->U_LB_TipoFactura}',{$puntero->SalesPersonCode}";
            $sql .= ",'{$puntero->ReserveInvoice}',{$pagado},{$saldo},'','{$puntero->Status}','{$puntero->pedienteEntrega}',";
            $sql .= "1,1,'" . Carbon::today() . "'";
            $sql .= ");";

           //Yii::error("SINCRONIZA ODBC: ".$sql);
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $db->createCommand($sql)->execute();
                $transaction->commit();
                //$this->insertLog2('sincroniza', 'Facturas Cabecera', 'success');
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
                $this->insertLog2('sincroniza', 'Facturas Cabecera', $e);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $this->insertLog2('sincroniza', 'Facturas Cabecera', $e);
                throw $e;
            }
        }


        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenrFacturasDetalle() {
        Yii::error("SINCRONIZA ODBC: Facturas Cuerpo");
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 2));
        $respuesta = $serviceLayer->executex($data);
        $respuesta = json_decode($respuesta);
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE facturasproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();

        foreach ($respuesta as $punteroSub) {
            If ($punteroSub->Rate == null) {
                $punteroSub->Rate = 1;
            }
            $punteroSub->ItemDescription=str_replace("'"," ",$punteroSub->ItemDescription);
            $sqlSub = "";
            $sqlSub .= "INSERT INTO facturasproductos (id,LineNum,ItemCode,ItemDescription,Quantity,Price,PriceAfterVAT,Currency,Rate,LineTotal,TaxTotal,UnitPrice,DocEntry,DocNum,Entregado,OpenQty,User,Status,DateUpdate,WhsCode,OcrCode,OcrCode2,Linestatus,InvStatus,OpenSum) VALUES (DEFAULT,";
            $sqlSub .= "{$punteroSub->LineNum},'{$punteroSub->ItemCode}','{$punteroSub->ItemDescription}',{$punteroSub->Quantity},{$punteroSub->Price},{$punteroSub->PriceAfterVAT},'{$punteroSub->Currency}',{$punteroSub->Rate},{$punteroSub->LineTotal},{$punteroSub->TaxTotal},{$punteroSub->UnitPrice},{$punteroSub->DocEntry},{$punteroSub->DocNum},{$punteroSub->Entregado},{$punteroSub->OpenQty},";
            $sqlSub .= "1,1,'" . Carbon::today() . "','{$punteroSub->WhsCode}','{$punteroSub->OcrCode}','{$punteroSub->OcrCode2}','{$punteroSub->LineStatus}','{$punteroSub->InvntSttus}','{$punteroSub->OpenSum}'";
            $sqlSub .= ");";

            //Yii::error("SINCRONIZA ODBC: ".$sqlSub);
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                //$this->insertLog2('sincroniza', 'Facturas Cuerpo', 'success');
                $db->createCommand($sqlSub)->execute();
                $transaction->commit();
            } catch (\Exception $e) {
                $this->insertLog2('sincroniza', 'Facturas Cuerpo', $e);
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $this->insertLog2('sincroniza', 'Facturas Cuerpo', $e);
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    private function ObtenrPedidosCabecera() {
        Yii::error("SINCRONIZA ODBC: pedidos Cabecera");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 3));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE pedidos ;SET FOREIGN_KEY_CHECKS = 1;')->execute();

            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $sql = "";
                $sql .= "INSERT INTO pedidos (id,";
                $sql .= "DocEntry,DocNum,DocDate,DocDueDate";
                $sql .= ",CardCode,CardName,DocTotal,DocCurrency,JournalMemo";
                $sql .= ",PaymentGroupCode,DocTime,Series,TaxDate,CreationDate,UpdateDate";
                $sql .= ",FinancialPeriod,UpdateTime,U_LB_NumeroFactura,U_LB_NumeroAutorizac,U_LB_FechaLimiteEmis";
                $sql .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
                $sql .= ",ReserveInvoice,DocStatus,InvStatus,User,Status,DateUpdate";
                $sql .= ") VALUES (DEFAULT,";
                $sql .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                $sql .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocCurrency}','{$puntero->JournalMemo}'";
                $sql .= ",{$puntero->PaymentGroupCode},'{$puntero->DocTime}',{$puntero->Series},'{$puntero->TaxDate}','{$puntero->CreationDate}','{$puntero->UpdateDate}'";
                $sql .= ",'{$puntero->FinancialPeriod}','{$puntero->UpdateTime}','{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                $sql .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}','{$puntero->U_LB_TipoFactura}',{$puntero->SalesPersonCode}";
                $sql .= ",'{$puntero->ReserveInvoice}','{$puntero->Status}','{$puntero->pedienteEntrega}',";
                $sql .= "1,1,'" . Carbon::today() . "'";
                $sql .= ");";
                $db = Yii::$app->db;
                $db->createCommand($sql)->execute();
            }
            $this->insertLog2('sincroniza', 'pedidos Cabecera', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'pedidos Cabecera', $e);
        }
    }

    private function ObtenrPedidosDetalle() {
        Yii::error("SINCRONIZA ODBC: pedidos Cuerpo");
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 4));
        $respuesta = $serviceLayer->executex($data);
        // Yii::error("SINCRONIZA ODBC: ".$respuesta);
        $respuesta = json_decode($respuesta);
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE pedidosproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();

        foreach ($respuesta as $punteroSub) {
            If ($punteroSub->Rate == null) {
                $punteroSub->Rate = 1;
            }
            $punteroSub->ItemDescription=str_replace("'"," ",$punteroSub->ItemDescription);
            $sqlSub = "";
            $sqlSub .= "INSERT INTO pedidosproductos (id,LineNum,ItemCode,ItemDescription,Quantity,Price,PriceAfterVAT,Currency,Rate,LineTotal,TaxTotal,UnitPrice,DocEntry,DocNum,Entregado,OpenQty,User, OcrCode, OcrCode2,Status,DateUpdate,WhsCode,Linestatus,InvStatus,OpenSum) VALUES (DEFAULT,";
            $sqlSub .= "{$punteroSub->LineNum},'{$punteroSub->ItemCode}','{$punteroSub->ItemDescription}',{$punteroSub->Quantity},{$punteroSub->Price},{$punteroSub->PriceAfterVAT},'{$punteroSub->Currency}',{$punteroSub->Rate},{$punteroSub->LineTotal},{$punteroSub->TaxTotal},{$punteroSub->UnitPrice},{$punteroSub->DocEntry},{$punteroSub->DocNum},{$punteroSub->Entregado},{$punteroSub->OpenQty},";
            $sqlSub .= "1,'{$punteroSub->OcrCode}','{$punteroSub->OcrCode2}',1,'" . Carbon::today() . "','{$punteroSub->WhsCode}','{$punteroSub->LineStatus}','{$punteroSub->InvntSttus}','{$punteroSub->OpenSum}'";
            $sqlSub .= ");";

            //Yii::error("SINCRONIZA ODBC: ".$sqlSub);
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $db->createCommand($sqlSub)->execute();
                $transaction->commit();
                //$this->insertLog2('sincroniza', 'pedidos Cuerpo', 'success');
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->insertLog2('sincroniza', 'pedidos Cuerpo', $e);
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $this->insertLog2('sincroniza', 'pedidos Cuerpo', $e);
                throw $e;
            }
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerProductosAlternativos() {
        Yii::error("SINCRONIZA ODBC Productos Alternativos : ");
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 5));
        $respuesta = $serviceLayer->executex($data);

        $respuesta = json_decode($respuesta);

        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE productosalternativos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $user = 2;
        $fecha = date("Y-m-d");
        foreach ($respuesta as $punteroSub) {
            $punteroSub->ItemName=str_replace("'"," ",$punteroSub->ItemName);
            $sqlSub = "";
            $sqlSub .= "INSERT INTO productosalternativos (ItemCode,ItemCodeAlternative,ItemName,Quantity,Warehouse,Price,Currency,PriceList,ChildNum,User,Status,DateUpdate, ComboCode,BarCode) VALUES (";
            $sqlSub .= "'{$punteroSub->ITEMCODE}','{$punteroSub->ITEMCODEALTERNATIVE}','{$punteroSub->ItemName}',{$punteroSub->Quantity},'{$punteroSub->Warehouse}',{$punteroSub->Price},'{$punteroSub->Currency}',{$punteroSub->PriceList},{$punteroSub->ChildNum},{$user},1,'{$fecha}','{$punteroSub->COMBOCODE}','{$punteroSub->CodeBars}');";

            //Yii::error("SINCRONIZA productos Alternativos: ".$sqlSub);
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $db->createCommand($sqlSub)->execute();
                $transaction->commit();
                //$this->insertLog2('sincroniza', 'Productos Alternativos', 'Success');
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->insertLog2('sincroniza', 'Productos Alternativos', $e);
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $this->insertLog2('sincroniza', 'Productos Alternativos', $e);
                throw $e;
            }
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function remplaceString($string) {
        if (!is_null($string)) {
            $string=str_replace('\'', '`', $string);
            $string=str_replace("'", '`', $string);
            $string=str_replace('?', ' ', $string);
            return $string;
        }
        return $string;
    }

    private function ObtenerLotesProductos() {
        Yii::error("SINCRONIZA ODBC productos LOTES: ");

        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 35));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            $contador=$respuesta[0]->CANTIDAD;
            Yii::error("SINCRONIZA ODBC PRODUCTOS LOTES cantidad: ".$contador);
            
            $campos='(ItemCode, BatchNum, WhsCode,Quantity,ExpDate,InDate,BaseType,BaseEntry,BaseNum,BaseLinNum,DataSource,Transfered)';

            for($reg= 0; $reg < $contador; $reg+=1000){
                $data = json_encode(array("accion" => 34, "limite"=>1000,"inicio"=>$reg));
                $respuesta = $serviceLayer->executex($data);               
                $respuesta = json_decode($respuesta);
                $valores="";

                foreach ($respuesta as $lote) {
                    // Yii::error("----> " . json_encode($lote));
                  
                        $valores .= "('{$lote->ItemCode}','{$lote->BatchNum}','{$lote->WhsCode}','{$lote->Quantity}','{$lote->Expira}','{$lote->Ingreso}','{$lote->BaseType}','{$lote->BaseEntry}','{$lote->BaseNum}','{$lote->BaseLinNum}','{$lote->DataSource}','{$lote->Transfered}'),";
                    
                }
                $cadena = substr($valores, 0, -1); // quitar comita final
                $sql="INSERT INTO sinc_lotesproductos {$campos}  VALUES {$cadena};";
                //Yii::error("SQL =>" . $sql);
                $db = Yii::$app->db;
                if($reg==0)
                    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sinc_lotesproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;')->execute();
                $resultQuery = $db->createCommand($sql)->execute();

            }


        } catch (\Exception $th) {
            Yii::error("Error en sincronizacion de Productos Lotes x ODBC: ",$e);
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ActualizarSeriesUsadas() {
        Yii::error("SINCRONIZA ODBC Series Usadas : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 7));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE seriesusadas;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE lotesproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");

            foreach ($respuesta as $punteroSub) {

                $item = $punteroSub->ItemCode;
                $serial = $punteroSub->DistNumber;
                $sql = " Update seriesproductos set Status=0 where ItemCode='" . $item . "' and SerialNumber='" . $serial . "' ";
                Yii::$app->db->createCommand($sql)->execute();
                $sql2 = " insert into seriesusadas(ItemCode,DistNumber)VALUES('" . $item . "','" . $serial . "')";
                Yii::$app->db->createCommand($sql2)->execute();
                $giftcard = Yii::$app->db->createCommand("Select valor from configuracion where parametro='giftcard' and valor2='" . $item . "'")->queryone();
                if ($giftcard["valor"]) {
                    $comprobacion = Yii::$app->db->createCommand("Select Code from gifcards where Code='" . $serial . "' and ItemCode='" . $item . "'")->queryone();
                    if (!$comprobacion) {
                        $sql3 = " insert into gifcards(Code,Amount,Status,ItemCode)VALUES('" . $serial . "','" . $giftcard["valor"] . "',1,'" . $item . "')";
                        Yii::error("Agrega gift card: " . $sql3);
                        Yii::$app->db->createCommand($sql3)->execute();
                    }
                }

                // Yii::error("SINCRONIZA series productos : ".$sql2);
               // $db = Yii::$app->db;
                //$transaction = $db->beginTransaction();
            }
            //$this->insertLog2('sincroniza', 'Series Usadas ', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'Series Usadas ', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerPagosCuenta() {
        Yii::error("SINCRONIZA ODBC pagos a cuenta : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 8));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE pagosacuenta;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";
                $sqlSub .= "INSERT INTO `pagosacuenta`(`id`, `DocEntry`, `DocNum`, `DocType`, `DocDate`, `DocDueDate`, `CardCode`, `CardName`, `CashAcct`, `CashSum`, `CashSumFC`, `CreditSum`, `CredSumFC`, `CheckAcct`, `CheckSum`, `CheckSumFC`, `TrsfrAcct`, `TrsfrSum`, `TrsfrSumFC`, `TrsfrDate`, `DocCurr`, `DocRate`, `DocTotal`, `DocTotalFC`, `Ref1`, `JrnlMemo`, `TransId`,`OpenBal`, `usuario`, `status`, `DateUpdate`,`ccost`) VALUES (DEFAULT,";
                $sqlSub .= "'{$punteroSub->DocEntry}','{$punteroSub->DocNum}','{$punteroSub->DocType}','{$punteroSub->DocDate}','{$punteroSub->DocDueDate}','{$punteroSub->CardCode}','{$punteroSub->CardName}','{$punteroSub->CashAcct}','{$punteroSub->CashSum}','{$punteroSub->CashSumFC}','{$punteroSub->CreditSum}','{$punteroSub->CredSumFC}','{$punteroSub->CheckAcct}','{$punteroSub->CheckSum}','{$punteroSub->CheckSumFC}','{$punteroSub->TrsfrAcct}','{$punteroSub->TrsfrSum}','{$punteroSub->TrsfrSumFC}','{$punteroSub->TrsfrDate}','{$punteroSub->DocCurr}','{$punteroSub->DocRate}','{$punteroSub->DocTotal}','{$punteroSub->DocTotalFC}','{$punteroSub->Ref1}','{$punteroSub->JrnlMemo}','{$punteroSub->TransId}','{$punteroSub->OpenBal}',";
                $sqlSub .= $user . ",1,'" . $fecha . "','{$punteroSub->U_UsaLc}');";

               // Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', ' pagos a cuenta', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' pagos a cuenta', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerCentrosCosto() {
        Yii::error("SINCRONIZA ODBC centros de costo: ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 9));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE centroscostos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";
                $sqlSub .= "INSERT INTO `centroscostos`(`PrcCode`, `PrcName`) VALUES (";
                $sqlSub .= "'{$punteroSub->PrcCode}','{$punteroSub->PrcName}')";
                //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', ' centros de costo', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', ' centros de costo', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerSeriesProductos() {
        Yii::error('SINCRONIZACION DE SERIES DE PRODUCTOS POR ODBC');
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion"=>32));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE seriesproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $fecha = date("Y-m-d");

            // Yii::error('Respuesta SINC: ' . json_encode($respuesta));
            foreach($respuesta as $serie){
                $sql = "";
                $sql .= "INSERT INTO `seriesproductos` (`DocEntry`, `ItemCode`, `SerialNumber`, `SystemNumber`, `AdmissionDate`, `User`, `Status`, `Date`, `WsCode`) VALUES (";
                $sql .= "'{$serie->AbsEntry}','{$serie->ItemCode}','{$serie->DistNumber}','{$serie->SysNumber}','{$serie->InDate}','{$serie->UserSign}','{$serie->Status}','{$fecha}','0')";
                $db = Yii::$app->db;
                $db->createCommand($sql)->execute();
                // Yii::error('Respuesta insert: ' . $sql);
            }

        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'series de productos', $e);
        }
    } 

    private function ObtenerCuotasPagos() {
        try {
            Yii::error("SINCRONIZA ODBC cuotas pago : ");
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 10));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE condicionespagocuotas; SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";
                $sqlSub .= "INSERT INTO `condicionespagocuotas`(`GroupNum`, `PymntGroup`, `CTGCode`, `IntsNo`, `InstMonth`, `InstDays`, `InstPrcnt`) VALUES (";
                $sqlSub .= "'{$punteroSub->GroupNum}','{$punteroSub->PymntGroup}','{$punteroSub->CTGCode}','{$punteroSub->IntsNo}','{$punteroSub->InstMonth}','{$punteroSub->InstDays}','{$punteroSub->InstPrcnt}')";
                //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
           // $this->insertLog2('sincroniza', 'cuotas pago ', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'cuotas pago ', $e);
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerCuotasFacturas() {
        Yii::error("SINCRONIZA ODBC cuotas Facturas : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 11));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE facturascuotaspago; SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";
                $sqlSub .= "INSERT INTO `facturascuotaspago`(DocNum,DocType,CardName,InsTotal,DueDate,InstlmntID,InstPrcnt,Paid,Saldo) VALUES (";
                $sqlSub .= "'{$punteroSub->DocNum}','{$punteroSub->DocType}','{$punteroSub->CardName}','{$punteroSub->InsTotal}','{$punteroSub->DueDate}','{$punteroSub->InstlmntID}','{$punteroSub->InstPrcnt}','{$punteroSub->Paid}','{$punteroSub->SALDO}')";
                //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', 'cuotas Facturas', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'cuotas Facturas', $e);
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerAlmacenesSeries() {
        Yii::error("SINCRONIZA ODBC almacenes series : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 12));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            $respuesta = json_decode($respuesta);
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $item = $punteroSub->ItemCode;
                $serial = $punteroSub->DistNumber;
                $sistem = $punteroSub->SysNumber;
                $almacen = $punteroSub->LocCode;
                $sqlSub = "";
                $sql = " Update seriesproductos set WsCode='" . $almacen . "' where ItemCode='" . $item . "' and SerialNumber='" . $serial . "' and SystemNumber ='" . $sistem . "'";
                Yii::$app->db->createCommand($sql)->execute();
                //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', 'almacenes series', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'almacenes series', $e);
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerBancos() {
        Yii::error("SINCRONIZA ODBC bancos : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 13));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE bancos; SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $respuesta = json_decode($respuesta);
            foreach ($respuesta as $punteroSub) {
                $codigo = $punteroSub->BankCode;
                $cuenta = $punteroSub->GLAccount;
                $nombre = $punteroSub->AcctName;

                $sqlSub = "";
                $sql = " Insert into bancos(codigo,cuenta,nombre) values('" . $codigo . "','" . $cuenta . "','" . $nombre . "')";
                Yii::$app->db->createCommand($sql)->execute();
                Yii::error("SINCRONIZA bancos: ".$sql);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', 'bancos', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'bancos', $e);
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerDosificacionParaguay() {
        Yii::error("SINCRONIZA ODBC dosificacion de Paraguay : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 14));
            $respuesta = $serviceLayer->executex($data);

            $respuesta = json_decode($respuesta);

            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE dosificacionparaguay;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            foreach ($respuesta as $punteroSub) {
                $sqlSub = "";
                $sqlSub .= "INSERT INTO `dosificacionparaguay`(`id`, `Descripcion`, `Razon_Social`, `NIT`, `Direccion`, `Sucursal`, `SFC`, `Usuario_Asignado`, `Numero_De_Autorizacion`, `Caracteristicas`, `Actividad_Economica`, `Fecha_De_Inicio`, `Fecha_Limite_De_Emision`, `Leyenda`, `Llave`, `Numero_De_Factura_Inicial`, `Numero_De_Factura_Final`, `Numero_De_Factura_Actual`, `Encabezado1`, `Encabezado2`, `Encabezado3`, `Encabezado4`, `Encabezado5`, `Pie_De_Pagina1`, `Pie_De_Pagina2`, `Pie_De_Pagina3`, `Pie_De_Pagina4`, `Pie_De_Pagina5`, `Observaciones`, `UserSign`, `Tipo`, `Codigo2`, `Estado`, `ICE`, `SitioFacturacion`, `Establecimiento`, `PuntoEmision`) VALUES (DEFAULT,";
                $sqlSub .= "'{$punteroSub->Descripcion}','{$punteroSub->Razon_Social}','{$punteroSub->NIT}','{$punteroSub->Direccion}','{$punteroSub->Sucursal}','{$punteroSub->SFC}','{$punteroSub->Usuario_Asignado}','{$punteroSub->Numero_De_Autorizacion}','{$punteroSub->Caracteristicas}','{$punteroSub->Actividad_Economica}','{$punteroSub->Fecha_De_Inicio}','{$punteroSub->Fecha_Limite_De_Emision}','{$punteroSub->Leyenda}','{$punteroSub->Llave}','{$punteroSub->Numero_De_Factura_Inicial}','{$punteroSub->Numero_De_Factura_Final}','{$punteroSub->Numero_De_Factura_Actual}','{$punteroSub->Encabezado1}','{$punteroSub->Encabezado2}','{$punteroSub->Encabezado3}','{$punteroSub->Encabezado4}','{$punteroSub->Encabezado5}','{$punteroSub->Pie_De_Pagina1}','{$punteroSub->Pie_De_Pagina2}','{$punteroSub->Pie_De_Pagina3}','{$punteroSub->Pie_De_Pagina4}','{$punteroSub->Pie_De_Pagina5}','{$punteroSub->Observaciones}','{$punteroSub->UserSign}','{$punteroSub->Tipo}','{$punteroSub->Codigo2}','{$punteroSub->Estado}','{$punteroSub->ICE}','{$punteroSub->SitioFacturacion}','{$punteroSub->Establecimiento}','{$punteroSub->PuntoEmision}')";
                //$sqlSub .= $user.",1,'".$fecha."');";
                //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', 'dosificacion de Paraguay', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'dosificacion de Paraguay', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerSapOfertasCabecera() {
        Yii::error("SINCRONIZA ODBC: ofertas Cabecera");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 14));
            $respuesta = $serviceLayer->executex($data);
            //Yii::error("SINCRONIZA ODBC: ofertas Cabecera respuesta".$respuesta);
            $respuesta = json_decode($respuesta);
            //Yii::error("SINCRONIZA ODBC: ofertas Cabecera respuesta decode".$respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE SAPOfertas ;SET FOREIGN_KEY_CHECKS = 1;')->execute();

            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $sql = "";
                $sql .= "INSERT INTO SAPOfertas (id,";
                $sql .= "DocEntry,  DocNum,  DocDate,  DocDueDate,  CardCode,  CardName,  DocTotal, ";
                $sql .= "DocTime, Series, TaxDate, UpdateDate, U_LB_NumeroFactura, U_LB_NumeroAutorizac, U_LB_FechaLimiteEmis, ";
                $sql .= "U_LB_CodigoControl, U_LB_EstadoFactura, U_LB_RazonSocial, U_LB_TipoFactura, User, Status, ";
                $sql .= "DateUpdate, ReserveInvoice, SalesPersonCode, PaidtoDate, Saldo,DocStatus,InvStatus,U_LB_NIT,U_xMOB_Codigo ";
                //  $sql .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
                //  $sql .= ",ReserveInvoice,User,Status,DateUpdate";
                $sql .= ") VALUES (DEFAULT,";
                $sql .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                $sql .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocTime}','{$puntero->Series}','{$puntero->TaxDate}'";
                $sql .= ",'{$puntero->UpdateDate}'";
                $sql .= ",'{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                $sql .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}',{$puntero->U_LB_TipoFactura},'{$puntero->User}','{$puntero->Status}','','{$puntero->ReserveInvoice}','{$puntero->SalesPersonCode}','{$puntero->PaidtoDate}','{$puntero->Saldo}','{$puntero->Status}','{$puntero->pedienteEntrega}','{$puntero->U_LB_NIT}','{$puntero->U_xMOB_Codigo}'";
                // $sql .=",'{$puntero->ReserveInvoice}',";
                //  $sql .= "1,1,'".Carbon::today()."'";
                $sql .= ");";

                //Yii::error("SINCRONIZA ODBC: ".$sql);
                $db = Yii::$app->db;

                $db->createCommand($sql)->execute();
                //$this->insertLog2('sincroniza', 'ofertas Cabecera', 'success');
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'ofertas Cabecera', $e);
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerSapOfertasDetalles() {
        Yii::error("SINCRONIZA ODBC: oferta detalle");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 15));
            $respuesta = $serviceLayer->executex($data);
            //Yii::error("SINCRONIZA ODBC: ofertas Cabecera respuesta".$respuesta);
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapofertasdetalle ;SET FOREIGN_KEY_CHECKS = 1;')->execute();

            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $puntero->ItemDescription=str_replace("'"," ",$puntero->ItemDescription);
                $sql = "";
                $sql .= "INSERT INTO sapofertasdetalle (id,";
                $sql .= "DocEntry, LineNum, ItemCode, ItemDescription, Price, Quantity, ";
                $sql .= "Currency, Rate, LineTotal, OpenQty, IdCabecera, Usuario,";
                $sql .= "Status, DateUpdate,UomCode,PriceAfVAT, OcrCode, OcrCode2,WhsCode,GTotal,LineStatus";
                $sql .= ") VALUES (DEFAULT,";
                $sql .= "{$puntero->DocEntry},'{$puntero->LineNum}','{$puntero->ItemCode}','{$puntero->ItemDescription}','{$puntero->Price}','{$puntero->Quantity}'";
                $sql .= ",'{$puntero->Currency}',{$puntero->Rate},{$puntero->LineTotal},{$puntero->OpenQty}";
                $sql .= ",'{$puntero->IdCabecera}','{$puntero->Usuario}','{$puntero->Status}'";
                $sql .= ",'{$puntero->DateUpdate}','{$puntero->UomCode}','{$puntero->PriceAfVAT}','{$punteroSub->OcrCode}','{$punteroSub->OcrCode2}','{$puntero->WhsCode}','{$puntero->GTotal}','{$puntero->LineStatus}'";
                // $sql .=",'{$puntero->ReserveInvoice}',";
                //  $sql .= "1,1,'".Carbon::today()."'";
                $sql .= ");";

                //Yii::error("SINCRONIZA ODBC: ".$sql);
                $db = Yii::$app->db;

                $db->createCommand($sql)->execute();
               // $this->insertLog2('sincroniza', 'oferta detalle', 'success');
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'oferta detalle', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerSapEntregasCabecera() {
        Yii::error("SINCRONIZA ODBC: entregas Cabecera");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 16));
            $respuesta = $serviceLayer->executex($data);
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapentregas ;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            // Yii::error("Respuesta ODBC: " . json_encode($respuesta));

            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $sql = "";
                $sql .= "INSERT INTO sapentregas (id,";
                $sql .= "DocEntry,  DocNum,  DocDate,  DocDueDate,  CardCode,  CardName,  DocTotal, ";
                $sql .= "DocTime, Series, TaxDate, UpdateDate, U_LB_NumeroFactura, U_LB_NumeroAutorizac, U_LB_FechaLimiteEmis, ";
                $sql .= "U_LB_CodigoControl, U_LB_EstadoFactura, U_LB_RazonSocial, U_LB_TipoFactura, User, Status, ";
                $sql .= "DateUpdate, ReserveInvoice, SalesPersonCode, PaidtoDate, Saldo,DocStatus,InvStatus,U_LB_NIT,U_xMOB_Codigo ";
                //  $sql .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
                //  $sql .= ",ReserveInvoice,User,Status,DateUpdate";
                $sql .= ") VALUES (DEFAULT,";
                $sql .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                $sql .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocTime}','{$puntero->Series}','{$puntero->TaxDate}'";
                $sql .= ",'{$puntero->UpdateDate}'";
                $sql .= ",'{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                $sql .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}',{$puntero->U_LB_TipoFactura},'{$puntero->User}','{$puntero->Status}','','{$puntero->ReserveInvoice}','{$puntero->SalesPersonCode}','{$puntero->PaidtoDate}','{$puntero->Saldo}','{$puntero->Status}','{$puntero->pedienteEntrega}','{$puntero->U_LB_NIT}','{$puntero->U_xMOB_Codigo}'";
                // $sql .=",'{$puntero->ReserveInvoice}',";
                //  $sql .= "1,1,'".Carbon::today()."'";
                $sql .= ");";

                //Yii::error("SINCRONIZA ODBC: ".$sql);
                $db = Yii::$app->db;

                $db->createCommand($sql)->execute();
                
            }
            //$this->insertLog2('sincroniza', 'entregas Cabecera', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'entregas Cabecera', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerSapEntregasDetalles() {
        Yii::error("SINCRONIZA ODBC: entregas detalle");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 17));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("RESPUESTA entregas Detalle: " . json_encode($respuesta));
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapentregasdetalle ;SET FOREIGN_KEY_CHECKS = 1;')->execute();

            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $puntero->ItemDescription=str_replace("'"," ",$puntero->ItemDescription);
                $sql = "";
                $sql .= "INSERT INTO sapentregasdetalle (id,";
                $sql .= "DocEntry, LineNum, ItemCode, ItemDescription, Price, Quantity, ";
                $sql .= "Currency, Rate, LineTotal, OpenQty, IdCabecera, Usuario,";
                $sql .= "Status, DateUpdate,UomCode,PriceAfVAT, OcrCode, OcrCode2,WhsCode,GTotal,LineStatus";
                // $sql .= "DateUpdate, ReserveInvoice, SalesPersonCode, PaidtoDate, Saldo ";
                //  $sql .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
                //  $sql .= ",ReserveInvoice,User,Status,DateUpdate";
                $sql .= ") VALUES (DEFAULT,";
                $sql .= "{$puntero->DocEntry},'{$puntero->LineNum}','{$puntero->ItemCode}','{$puntero->ItemDescription}','{$puntero->Price}','{$puntero->Quantity}'";
                $sql .= ",'{$puntero->Currency}','{$puntero->Rate}',{$puntero->LineTotal},{$puntero->OpenQty}";
                $sql .= ",'{$puntero->IdCabecera}','{$puntero->Usuario}','{$puntero->Status}'";
                $sql .= ",'{$puntero->DateUpdate}','{$puntero->UomCode}','{$puntero->PriceAfVAT}', '{$puntero->OcrCode}','{$puntero->OcrCode2}' ,'{$puntero->WhsCode}','{$puntero->GTotal}','{$puntero->LineStatus}'";
                // $sql .=",'{$puntero->ReserveInvoice}',";
                //  $sql .= "1,1,'".Carbon::today()."'";
                $sql .= ");";

               //Yii::error("SINCRONIZA ODBC: ".$sql);
                $db = Yii::$app->db;

                $db->createCommand($sql)->execute();
                
            }
           // $this->insertLog2('sincroniza', 'entregas detalle', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'entregas detalle', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerSapNotasCreditoCabecera() {
        Yii::error("SINCRONIZA ODBC: Notas de crédito Cabecera");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 18));
            $respuesta = $serviceLayer->executex($data);
            //Yii::error("SINCRONIZA ODBC: ofertas Cabecera respuesta".$respuesta);
            $respuesta = json_decode($respuesta);
            //Yii::error("SINCRONIZA ODBC: ofertas Cabecera respuesta decode".$respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE SAPnotasdredito ;SET FOREIGN_KEY_CHECKS = 1;')->execute();

            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $sql = "";
                $sql .= "INSERT INTO SAPnotasdredito (id,";
                $sql .= "DocEntry,  DocNum,  DocDate,  DocDueDate,  CardCode,  CardName,  DocTotal, ";
                $sql .= "DocTime, Series, TaxDate, UpdateDate, U_LB_NumeroFactura, U_LB_NumeroAutorizac, U_LB_FechaLimiteEmis, ";
                $sql .= "U_LB_CodigoControl, U_LB_EstadoFactura, U_LB_RazonSocial, U_LB_TipoFactura, User, Status, ";
                $sql .= "DateUpdate, ReserveInvoice, SalesPersonCode, PaidtoDate, Saldo ";
                //  $sql .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
                //  $sql .= ",ReserveInvoice,User,Status,DateUpdate";
                $sql .= ") VALUES (DEFAULT,";
                $sql .= "{$puntero->DocEntry},'{$puntero->DocNum}','{$puntero->DocDate}','{$puntero->DocDueDate}'";
                $sql .= ",'{$puntero->CardCode}','{$this->remplaceString($puntero->CardName)}',{$puntero->DocTotal},'{$puntero->DocTime}','{$puntero->Series}','{$puntero->TaxDate}'";
                $sql .= ",'{$puntero->UpdateDate}'";
                $sql .= ",'{$puntero->U_LB_NumeroFactura}','{$puntero->U_LB_NumeroAutorizac}','{$puntero->U_LB_FechaLimiteEmis}'";
                $sql .= ",'{$puntero->U_LB_CodigoControl}','{$puntero->U_LB_EstadoFactura}','{$puntero->U_LB_RazonSocial}',{$puntero->U_LB_TipoFactura},'{$puntero->User}','{$puntero->Status}','','{$puntero->ReserveInvoice}','{$puntero->SalesPersonCode}','{$puntero->PaidtoDate}','{$puntero->Saldo}'";
                // $sql .=",'{$puntero->ReserveInvoice}',";
                //  $sql .= "1,1,'".Carbon::today()."'";
                $sql .= ");";

                //Yii::error("SINCRONIZA ODBC: ".$sql);
                $db = Yii::$app->db;

                $db->createCommand($sql)->execute();
                //$this->insertLog2('sincroniza', 'notas credito', 'success');
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'notas credito', $e);
        }

        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function ObtenerSapNotasCreditoDetalles() {
        Yii::error("SINCRONIZA ODBC: Notas de credito detalle");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 19));
            $respuesta = $serviceLayer->executex($data);
            //Yii::error("SINCRONIZA ODBC: ofertas Cabecera respuesta".$respuesta);
            $respuesta = json_decode($respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE sapnotascreditodetalle ;SET FOREIGN_KEY_CHECKS = 1;')->execute();

            foreach ($respuesta as $puntero) {
                If ($puntero->JournalMemo == null) {
                    $puntero->JournalMemo = "nn";
                }
                $sql = "";
                $sql .= "INSERT INTO sapnotascreditodetalle (id,";
                $sql .= "DocEntry, LineNum, ItemCode, ItemDescription, Price, Quantity, ";
                $sql .= "Currency, Rate, LineTotal, OpenQty, IdCabecera, Usuario,";
                $sql .= "Status, DateUpdate";
                // $sql .= "DateUpdate, ReserveInvoice, SalesPersonCode, PaidtoDate, Saldo ";
                //  $sql .= ",U_LB_CodigoControl,U_LB_EstadoFactura,U_LB_RazonSocial,U_LB_TipoFactura,SalesPersonCode";
                //  $sql .= ",ReserveInvoice,User,Status,DateUpdate";
                $sql .= ") VALUES (DEFAULT,";
                $sql .= "{$puntero->DocEntry},'{$puntero->LineNum}','{$puntero->ItemCode}','{$puntero->ItemDescription}','{$puntero->Price}','{$puntero->Quantity}'";
                $sql .= ",'{$puntero->Currency}',{$puntero->Rate},{$puntero->LineTotal},{$puntero->OpenQty}";
                $sql .= ",'{$puntero->IdCabecera}','{$puntero->Usuario}','{$puntero->Status}'";
                $sql .= ",'{$puntero->DateUpdate}'";
                // $sql .=",'{$puntero->ReserveInvoice}',";
                //  $sql .= "1,1,'".Carbon::today()."'";
                $sql .= ");";

                //Yii::error("SINCRONIZA ODBC: ".$sql);
                $db = Yii::$app->db;

                $db->createCommand($sql)->execute();
               // $this->insertLog2('sincroniza', 'notas credito detalle', 'success');
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'notas credito detalle', $e);
        }
        //Yii::error("SINCRONIZA ODBC: ".$respuesta);    
    }

    private function insertLog2($action, $parametros, $error) {
      $sql = "INSERT INTO log_envio(idlog, proceso, envio, respuesta, fecha, ultimo, endpoint) VALUES 
              (DEFAULT,'','" . $parametros . "','" . htmlentities($error, ENT_QUOTES) . "','" . Carbon::now() . "','','" . $action . "')";
      Yii::$app->db->createCommand($sql)->execute();
    }

    private function ObtenerRelUnidMedidaGrupo2(){
        Yii::error("SINCRONIZA ODBC UnidMedidaGrupo : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 40));
            $respuesta = $serviceLayer->executex($data);
            // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE unidadmedidaxgrupo; SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $respuesta = json_decode($respuesta);
            foreach ($respuesta as $punteroSub) {
                $grupo = $punteroSub->UgpEntry;
                $unidad = $punteroSub->UomEntry;
                $unidadbase = $punteroSub->AltQty;
                $unidadcantidad = $punteroSub->BaseQty;
                $sqlSub = "";
                $sql = " Insert into unidadmedidaxgrupo(UgpEntry,UomEntry,AltQty,BaseQty) values('" . $grupo . "','" . $unidad . "','" . $unidadbase . "','".$unidadcantidad."')";
                Yii::$app->db->createCommand($sql)->execute();
                //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();
            }
            //$this->insertLog2('sincroniza', 'bancos', 'success');
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'UnidadMedidaGrupo', $e);
        }

    }


  private function obtenerProductos(){
    Yii::error("SINCRONIZA ODBC Productos : ");
    /*
    try {
        $serviceLayer = new Sincronizar();
        //recuperamos la cantidad de productos
        $data = json_encode(array("accion" => 23));
        $respuesta = json_decode($respuesta);
        $cantidad = $respuesta[0]["CANTIDAD"];
        //fin de cantidad
        $data = json_encode(array("accion" => 20));
        $respuesta = $serviceLayer->executex($data);
        $respuesta = json_decode($respuesta);
        $textoProducto = '';
        $insertProducto = '';
        $sql = "SELECT * FROM configuracion WHERE parametro LIKE 'producto_std%' AND estado=1 ORDER BY parametro";
        $parametrosProducto = Yii::$app->db->createCommand($sql)->queryAll();
        $cantidadProducto = count($parametrosProducto);
        if (count($parametrosProducto)){
            for ($c = 0; $c < $cantidadProducto; $c++){
                if ($textoProducto == ''){
                    $textoProducto = ',{$p->'.$parametrosProducto[$c]["valor2"].'}';
                    //$textoProducto = "'{$p->"."$parametrosProducto[$c]["valor2"]"."}'";
                    //'{$p->U_Centro}',
                    $insertProducto = ",`".$parametrosProducto[$c]["parametro"]."`";
                }
                else {
                    //$textoProducto = $textoProducto.","."'{`$`"."p->".$parametrosProducto[$c]["valor2"]."}'";
                    $textoProducto = $textoProducto.','.'{$p->'.$parametrosProducto[$c]["valor2"].'}';
                    $insertProducto = $insertProducto.',`'.$parametrosProducto[$c]["parametro"].'`';
                }
            }
        }
        $productos = '';
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE copiaproductos;SET FOREIGN_KEY_CHECKS = 1;')->execute();
		//Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $user = 2;
        $status = 1;
        $fecha = date("Y-m-d");        
        foreach ($respuesta as $p) { 
          $miaux_combo = 0;
          $miaux_series = 0;
          $miaux_lotes = 0;
            if($p->TreeType == "iTemplateTree"){
              $miaux_combo = 1;
              //$this->combos(); 
            }                
            else $miaux_combo = 0;    
            if($puntero->ManageSerialNumbers == "tYES") $miaux_series = 1;
            else $miaux_series = 0;
            if($puntero->ManageBatchNumbers == "tYES") $miaux_lotes = 1;
            else $miaux_lotes = 0;
			
			$textoProducto = '';
            $valorDesdeSAP = '';
			$campoDeSAP = '';
            if (count($parametrosProducto)){
              for ($c = 0; $c < $cantidadProducto; $c++){
				$campoDeSAP = $parametrosProducto[$c]["valor2"];
                $valorDesdeSAP = $p->$campoDeSAP;
                if ($textoProducto == ''){
                  $textoProducto = ",'".$valorDesdeSAP."'";
                }
                else{
                  $textoProducto = $textoProducto.",'".$valorDesdeSAP."'";
                }
              }
            }
			
            $sqlSub = "";
            $sqlSub .= "INSERT INTO `copiaproductos`(`id`, `ItemCode`, `ItemName`, `ItemsGroupCode`, `ForeignName`, `CustomsGroupCode`, `BarCode`, `PurchaseItem`, `SalesItem`, `InventoryItem`, `UserText`, `SerialNum`, `QuantityOnStock`, `QuantityOrderedFromVendors`, `QuantityOrderedByCustomers`, `ManageSerialNumbers`, `ManageBatchNumbers`, `SalesUnit`, `SalesUnitLength`, `SalesUnitWidth`, `SalesUnitHeight`, `SalesUnitVolume`, `PurchaseUnit`, `DefaultWarehouse`, `ManageStockByWarehouse`, `ForceSelectionOfSerialNumber`, `Series`, `UoMGroupEntry`, `DefaultSalesUoMEntry`, `User`, `Status`, `DateUpdate`, `Manufacturer`, `NoDiscounts`, `created_at`, `updated_at`, `combo`".$insertProducto.") VALUES (DEFAULT,";
            $sqlSub .= "'{$p->ItemCode}','{$p->ItemName}','{$p->ItmsGrpCod}','{$p->FrgnName}','{$p->CstGrpCode}','{$p->CodeBars}','{$p->PrchseItem}','{$p->SellItem}','{$p->InvntItem}','{$p->UserText}','','{$p->OnHand}','{$p->IsCommited}','{$p->OnOrder}','{$p->ManSerNum}','{$p->ManBtchNum}','{$p->SalUnitMsr}','{$p->SLength1}','{$p->SWidth1}','{$p->BHeight1}','{$p->Svolume}','{$p->BuyUnitMsr}','{$p->DfltWH}','{$p->ByWh}','{$p->EnAstSeri}','{$p->Series}','{$p->UgpEntry}','{$p->SUoMEntry}',{$user},1,'{$fecha}','{$p->FirmCode}','{$p->NoDiscount}','{$p->CreateDate}','{$p->UpdateDate}',{$miaux_combo}".$textoProducto.")";
            //{$p->SERIALNUMBER}
            $db = Yii::$app->db;
            $db->createCommand($sqlSub)->execute();
        }
        $this->insertLog2('sincroniza', 'Productos', 'success');
    } catch (\Exception $e) {
        $this->insertLog2('sincroniza', 'Productos', $e);
    }
    //Yii::error("SINCRONIZA ODBC: ".$respuesta); 
    */       
  }

  private function ObtenerProductosAlmacenes(){
    Yii::error("SINCRONIZA ODBC Productos almacenes : "); 
    /*   
    try {
        $serviceLayer = new Sincronizar();
        //cantidad
        $data = json_encode(array("accion" => 24));
        $respuesta = $serviceLayer->executex($data);        
        $respuesta = json_decode($respuesta);
        $cantidadTotal = $respuesta[0]->CANTIDAD;
        $offset = 0;
        $ciclos = 1;
        $limite = 100;
        if ($cantidadTotal > $limite){
            $ciclos = (int)($cantidadTotal / $limite);
            if ($ciclos < ($cantidadTotal / $limite)) $ciclos = $ciclos + 1;
        }
        echo 'cantidad: '.$cantidadTotal;
        echo '    ciclos: '.$ciclos;
        //fin cantidad
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE copiaproductosalmacenes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        for ($i = 1; $i <= $ciclos; $i++ ){
            echo '    offset: '.$offset;
            $data = json_encode(array("accion" => 21, "inicio" => $offset, "limite" => $limite));
            $respuestauno = $serviceLayer->executex($data);
			echo '    respuestauno: '.$respuestauno;
            $respuestauno = json_decode($respuestauno);
			echo '    Cantidad: '.count($respuestauno);
            //Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE copiaproductosalmacenes;SET FOREIGN_KEY_CHECKS = 1;')->execute();
            $user = 2;
            $fecha = date("Y-m-d");
            $conteo = 0;
            //echo '    inciando registro    ';
            foreach ($respuestauno as $punteroSub) {
                $conteo = $conteo + 1;
                echo '    conteo: '.$conteo;
                $sqlSub = "";
                $sqlSub .= "INSERT INTO `copiaproductosalmacenes`(`id`, `ItemCode`, `WarehouseCode`, `InStock`, `Committed`, `Locked`, `Ordered`, `User`, `Status`, `DateUpdate`) VALUES (DEFAULT,";
                $sqlSub .= "'{$punteroSub->ItemCode}','{$punteroSub->WhsCode}','{$punteroSub->OnHand}','{$punteroSub->IsCommited}','{$punteroSub->Locked}','{$punteroSub->OnOrder}',";
                $sqlSub .= $user.",1,'".$fecha."');";
                $db = Yii::$app->db;
                $db->createCommand($sqlSub)->execute();                
            }
            $offset = $offset + $limite;
        }
        $this->insertLog2('sincroniza', 'productos almacenes', 'success');
    } catch (\Exception $e) {
        $this->insertLog2('sincroniza', 'productos almacenes', $e);
    }
    //Yii::error("SINCRONIZA ODBC: ".$respuesta);
    */
  }

  private function ObtenerProductosPrecios(){
    Yii::error("SINCRONIZA ODBC Productos precios : ");
    /*
    try {
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 22));
        $respuesta = $serviceLayer->executex($data);

        $respuesta = json_decode($respuesta);

        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE copiaproductosprecios;SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $user = 2;
        $fecha = date("Y-m-d");
        foreach ($respuesta as $punteroSub) {
            $sqlSub = "";
            $sqlSub .= "INSERT INTO `copiaproductosprecios`(`id`, `ItemCode`, `IdListaPrecios`, `IdUnidadMedida`, `Price`, `Currency`, `User`, `Status`, `DateUpdate`) VALUES (DEFAULT,";
            $sqlSub .= "'{$punteroSub->ItemCode}','{$punteroSub->PriceList}','{$punteroSub->UomEntry}','{$punteroSub->Price}','{$punteroSub->Currency}',";
            $sqlSub .= $user.",1,'".$fecha."');";
            $db = Yii::$app->db;
            $db->createCommand($sqlSub)->execute();
        }
        $this->insertLog2('sincroniza', 'productos precios', 'success');
    } catch (\Exception $e) {
        $this->insertLog2('sincroniza', 'productos precios', $e);
    }
    //Yii::error("SINCRONIZA ODBC: ".$respuesta);
  }*/
 }
  private  function obtenerEmpleadosVenta(){
    Yii::error("SINCRONIZA ODBC: Empleados venta");
    $serviceLayer = new Sincronizar();
    $data = json_encode(array("accion" => 31));
    $respuesta = $serviceLayer->executex($data);
    //Yii::error("SINCRONIZA ODBC: Facturas Cabecera respuesta ".$respuesta);
    $respuesta = json_decode($respuesta);
    
    foreach ($respuesta as $puntero) {
        $sql = "";
        $sql = "UPDATE vendedores set fax= '{$puntero->Fax}' where SalesEmployeeCode='{$puntero->SlpCode}'";
        Yii::$app->db->createCommand($sql)->execute();
        //Yii::error("SINCRONIZA ODBC: ".$sql);
       
    }    
  }

  private function obtenerContactosClientes() {
      $serviceLayer = new Sincronizar();
      $data = json_encode(array("accion" => 51));
      $respuesta = $serviceLayer->executex($data);
      $respuesta = json_decode($respuesta);
      $fecha = date("Y-m-d");
      Yii::error("SINCRONIZA ODBC: Contactos Clientes: " . json_encode($respuesta));
    foreach ($respuesta as $puntero) {
        $sql = "";
        //$sql = "UPDATE cojnta set fax= '{$puntero->Fax}' where SalesEmployeeCode='{$puntero->SlpCode}'";
        $sql .= "INSERT INTO `contactos`(`id`, `cardCode`, `nombre`, `direccion`, `telefono1`, `telefono2`, `celular`, `tipo`, `comentarios`, `User`, `Status`, `DateUpdate`, `correo`, `titulo`) VALUES (DEFAULT,";
        $sql .= "'{$puntero->CardCode}','{$puntero->Name}','{$puntero->Address}','{$puntero->Phone1}','{$puntero->Phone2}','{$puntero->MobilePhone}','0','{$puntero->Comment}','{$puntero->User}','0','{$fecha}','{$puntero->Mail}','{$puntero->Title}'";
        // Yii::$app->db->createCommand($sql)->execute();
        // Yii::error("SINCRONIZA ODBC CONTACTO linea: ".$sql); FUNCIONANDO 14092020
       
    }
  }

  private function obtenerSucursalClientes() {
      $serviceLayer = new Sincronizar();
      $data = json_encode(array("accion" => 52));
      $respuesta = $serviceLayer->executex($data);
      $respuesta = json_decode($respuesta);
      $fecha = date("Y-m-d");
      Yii::error("SINCRONIZA ODBC: Sucursal Clientes: " . json_encode($respuesta));
    foreach ($respuesta as $puntero) {
        $sql = "";
        //$sql = "UPDATE cojnta set fax= '{$puntero->Fax}' where SalesEmployeeCode='{$puntero->SlpCode}'";
        $sql .= "INSERT INTO `clientessucursales`(`id`,`AddresName`, `Street`, `State`, `FederalTaxId`, `CreditLimit`, `CardCode`, `User`, `Status`, `DateUpdate`, `TaxCode`) VALUES (DEFAULT,";
        $sql .= "'{$puntero->AddressName}','{$puntero->Street}','{$puntero->State}','{$puntero->FederalTaxID}','{$puntero->CreditLimit}','{$puntero->CardCode}','{$puntero->UserSign}','1','{$fecha}','{$puntero->TaxCode}'";
        // Yii::$app->db->createCommand($sql)->execute();
        // Yii::error("SINCRONIZA ODBC SUCURSAL linea: ".$sql); FUNCIONANDO 14092020
       
    }
  }
private function ObtenerRelUnidMedidaGrupo(){
    Yii::error("SINCRONIZA ODBC UnidMedidaGrupo : ");
    try {
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 40));
        $respuesta = $serviceLayer->executex($data);
        // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE unidadmedidaxgrupo; SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $respuesta = json_decode($respuesta);
        foreach ($respuesta as $punteroSub) {
            $grupo = $punteroSub->UgpEntry;
            $unidad = $punteroSub->UomEntry;
            $unidadbase = $punteroSub->AltQty;
            $unidadcantidad = $punteroSub->BaseQty;
            $sqlSub = "";
            $sql = " Insert into unidadmedidaxgrupo(UgpEntry,UomEntry,AltQty,BaseQty) values('" . $grupo . "','" . $unidad . "','" . $unidadbase . "','".$unidadcantidad."')";
            Yii::$app->db->createCommand($sql)->execute();
            //Yii::error("SINCRONIZA pagos a cuenta : ".$sqlSub);
            $db = Yii::$app->db;
            $db->createCommand($sqlSub)->execute();
        }
        //$this->insertLog2('sincroniza', 'bancos', 'success');
    } catch (\Exception $e) {
        $this->insertLog2('sincroniza', 'UnidadMedidaGrupo', $e);
    }
}

private function obtenerProductosCombo() {
    Yii::error("SINCRONIZA ODBC Productos de Combo : ");
try {
    $serviceLayer = new Sincronizar();
    $data = json_encode(array("accion" => 33));
    $respuesta = $serviceLayer->executex($data);
    // Yii::error("SINCRONIZA ODBC Lotes de productos : ".$respuesta);
    Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE combosdetalle; SET FOREIGN_KEY_CHECKS = 1;')->execute();
    $respuesta = json_decode($respuesta);
    foreach ($respuesta as $punteroSub) {
        $Code = $punteroSub->Code;
        $Quantity = $punteroSub->Quantity;
        $Warehouse = $punteroSub->Warehouse;
        $Price = $punteroSub->Price;
        $Currency = $punteroSub->Currency;
        $IssueMthd = $punteroSub->IssueMthd;
        $Father = $punteroSub->Father;
        $PriceList = $punteroSub->PriceList;
        $Type = $punteroSub->Type === "4" ? "pit_Item" : $punteroSub->Type;
        $AddQuantit = $punteroSub->AddQuantit;
        $ChildNum = $punteroSub->ChildNum;
        $VisOrder = $punteroSub->VisOrder;
        $User = 1;
        $Status = 1;
        $DateUpdate = $fecha = date("Y-m-d");
        $ItemComboPrice = $punteroSub->OrigPrice;
        $Descuento = (Float)$ItemComboPrice - (Float)$Price;
        
        $sql = " Insert into combosdetalle(ItemCode,Quantity,Warehouse,Price,Currency,IssueMethod,ParentItem,PriceList,ItemType,AdditionalQuantity,ChildNum,VisualOrder,User,Status,DateUpdate,ItemComboPrice,Descuento) values('" . $Code . "','" . $Quantity . "','" . $Warehouse . "','".$Price."','" . $Currency . "','" . $IssueMthd . "','".$Father."','" . $PriceList . "','" . $Type . "','".$AddQuantit."','" . $ChildNum . "','" . $VisOrder . "','".$User."','" . $Status . "','" . $DateUpdate . "','".$ItemComboPrice."','".$Descuento."')";
        Yii::$app->db->createCommand($sql)->execute();
    }
    //$this->insertLog2('sincroniza', 'bancos', 'success');
} catch (\Exception $e) {
    $this->insertLog2('sincroniza', 'UnidadMedidaGrupo', $e);
}
}


  private function limpiarnombres($nombre){
      $nombre=str_replace("'"," ",$nombre);
      $nombre=str_replace("?"," ",$nombre);
      return $nombre;  
  }
  
  private function ObtenerActividadCliente() {
        Yii::error("SINCRONIZA ODBC actividad de cliente : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 203));
            $respuesta = $serviceLayer->executex($data);  
            if(count($respuesta>0))  {          
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE grupoclientedocificacion; SET FOREIGN_KEY_CHECKS = 1;')->execute();
                $respuesta = json_decode($respuesta);
                foreach ($respuesta as $punteroSub) {
                    $codigo = $punteroSub->Code;
                    $nombre = $punteroSub->Name;

                    $sqlSub = "";
                    $sql = " Insert into grupoclientedocificacion(id,nombre) values('" .$codigo ."','" . $nombre . "')";
                    Yii::$app->db->createCommand($sql)->execute();
                    Yii::error("SINCRONIZA grupoclientedocificacion: ".$sqlSub);
                    $db = Yii::$app->db;
                    $db->createCommand($sqlSub)->execute();
                }
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'grupoclientedocificacion', $e);
        }
    }
	
   private function ObtenerActividadProducto() {
        Yii::error("SINCRONIZA ODBC actividad de producto : ");
        try {
            $serviceLayer = new Sincronizar();
            $data = json_encode(array("accion" => 204));
            $respuesta = $serviceLayer->executex($data); 
             if(count($respuesta>0))  {       
                Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE TABLE grupoproductodocificacion; SET FOREIGN_KEY_CHECKS = 1;')->execute();
                $respuesta = json_decode($respuesta);
                foreach ($respuesta as $punteroSub) {
                    $codigo = $punteroSub->Code;
                    $nombre = $punteroSub->Name;

                    $sqlSub = "";
                    $sql = " Insert into grupoproductodocificacion(id,nombre) values('" .$codigo ."','" . $nombre . "')";
                    Yii::$app->db->createCommand($sql)->execute();
                    Yii::error("SINCRONIZA grupoproductodocificacion: ".$sqlSub);
                    $db = Yii::$app->db;
                    $db->createCommand($sqlSub)->execute();
                }
            }
        } catch (\Exception $e) {
            $this->insertLog2('sincroniza', 'grupoproductodocificacion', $e);
        }
    }
    
}