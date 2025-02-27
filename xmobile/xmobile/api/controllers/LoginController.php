<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\User;
use backend\models\Usuariolog;
use backend\models\Monedas;
use backend\models\Tiposcambio;
use backend\models\TipoCambioParalelo;
use backend\models\Gestionbancos;
use backend\models\Versionequipo;
use backend\models\Auxcamposusuario;
use backend\models\v2\Clientes;
use backend\models\hana;
class LoginController extends ActiveController {

    public $STATUS_LOGIN = true;

    use Respuestas;

    public $modelClass = 'backend\models\User';

    public function actions() {
        $actions = parent::actions();
        unset($actions['index']); //get
        unset($actions['view']); //get/parm
        unset($actions['create']); //POST
        unset($actions['update']); //PUT
        unset($actions['delete']); //DELETE
        return $actions;
    }

    public function validatePass($clave, $hash) {
        return Yii::$app->security->validatePassword($clave, $hash);
    }

    public function actionCreate() {
		$arrx =  [[	  "idUser"=> "0",
					    "GroupNumber"=> "-1",
						"PaymentTermsGroupName"=> "Contado",
						"StartFrom"=> "pdt_None",
						"NumberOfAdditionalMonths"=> "0",
						"NumberOfAdditionalDays"=> "0",
						"CreditLimit"=> "0.00",
						"GeneralDiscount"=> "0.00",
						"InterestOnArrears"=> "0.00",
						"PriceListNo"=> "1",
						"LoadLimit"=> "0.00",
						"OpenReceipt"=> "oip_No",
						"DiscountCode"=> null,
						"DunningCode"=> null,
						"BaselineDate"=> "bld_TaxDate",
						"NumberOfInstallments"=> null,
						"NumberOfToleranceDays"=> "0",
						"U_UsaLc"=> "0",
						"User"=> "1",
						"DateUpdated"=> "2020-09-15",
						"Status"=> "1",
						"NumberLine"=> null,
						"MaxCuotas"=> null,
						"DistEq"=> null
			]];
        $data = Yii::$app->request->post();
		//return $data;
        //valida si el equipo borro la memoria//
        Yii::error("DATOS MOVI LOGIN");
        Yii::error(json_encode($data));
        $sql_version = "SELECT valor2 FROM configuracion WHERE parametro = 'versionmovil' and valor=1 and estado=1";
        $dataVersion = Yii::$app->db->createCommand($sql_version)->queryOne();
        if(isset($dataVersion['valor2'])){
            if($dataVersion['valor2']!=$data['version'])
                return ['estado'=>403,'mensaje' => 'Sin acceso, actualice la versión del Xmobile.']; 
            
            /*$dataVersionequipo = Versionequipo::find()
            ->where([
              "equipo" => $data['plataformaEmei'],
              "estado" => 'activo',
              "version" => $dataVersion['valor2']
            ])
            ->one();
            Yii::error("DATOS VERSION MOVIL:");
            Yii::error($dataVersionequipo);

            if(!count($dataVersionequipo)>0)
               return ['estado'=>405,'mensaje' => 'Sin acceso, primero elimine la información local del aplicativo.'];*/
        }
        //fin - valida si el equipo borro la memoria//

        $user = new User();
        $resp = $user->login($data);
        if (count($resp) == 1) {
            $STATUS_LOGIN = $this->validatePass($data['usuarioClaveUsuario'], $resp[0]['password_hash']);
            if ($STATUS_LOGIN) {
				$bancos = Gestionbancos::find()->asArray()->all();
                $sql = "SELECT * FROM configuracion WHERE parametro = 'cambio_paralelo'";
                $sqlcp = "SELECT * FROM tipocambioparalelo Order by DateUpdate Desc";
                $cambioParalelo = false;
                $fecha = date('Y-m-d');
                
                $sqlCambio = Yii::$app->db->createCommand($sql)->queryOne();

                $sql_factvenc = "SELECT * FROM configuracion WHERE parametro = 'control_fact_vencidas'";
                $aux_ctrl_fact_ven = Yii::$app->db->createCommand($sql_factvenc)->queryOne();
                $sql_pagofacturas = "SELECT * FROM configuracion WHERE parametro = 'pagoenfacturas'";
                $aux_ctrl_pagofacturas = Yii::$app->db->createCommand($sql_pagofacturas)->queryOne();
                $sql_redondeo = "SELECT * FROM configuracion WHERE parametro = 'redondeomonedalocal'";
                $aux_ctrl_redondeo = Yii::$app->db->createCommand($sql_redondeo)->queryOne();
                $sql_ctrl_condPago="SELECT * FROM configuracion WHERE parametro = 'condicionPago'";
                $aux_ctrl_condPag = Yii::$app->db->createCommand($sql_ctrl_condPago)->queryOne();
                $sql_ctrl_formaPago="SELECT * FROM configuracion WHERE parametro = 'formapagorestringido'";
                $aux_ctrl_formaPago = Yii::$app->db->createCommand($sql_ctrl_formaPago)->queryOne();
                $sql_ctrl_listaPrecios="SELECT * FROM configuracion WHERE parametro = 'listapreciosSAP'";
                $aux_ctrl_listaPrecios = Yii::$app->db->createCommand($sql_ctrl_listaPrecios)->queryOne();
                $sql_ctrl_contrato="SELECT * FROM configuracion WHERE parametro = 'contrato'";
                $aux_ctrl_contrato = Yii::$app->db->createCommand($sql_ctrl_contrato)->queryOne();

                $sql_maneja_per= "SELECT * FROM configuracion WHERE parametro = 'perManejaPercepcion'";
                $aux_maneja_per = Yii::$app->db->createCommand($sql_maneja_per)->queryOne();

                if ($sqlCambio["valor"] == "1") $cambioParalelo = true;
                //$auxiliar_condiciones_pago = $arrx;//$user->condicionespago($resp[0]["idUsuario"]);
                $auxiliar_condiciones_pago = $user->condicionespago($resp[0]["idUsuario"]);
                /* Yii::info("tipo de cambio");
                Yii::info("--->".json_encode($auxiliar_condiciones_pago)); */

                //$q_uso_fex="SELECT * FROM configuracion WHERE parametro ='FEX'";
                $q_uso_fex = "SELECT fex FROM equipox WHERE uuid = '".$data['plataformaEmei']."' ";
                $resp_uso_fex = Yii::$app->db->createCommand($q_uso_fex)->queryOne();
                $uso_fex= $resp_uso_fex["fex"];

                $sql_descuentos_especiales = "SELECT * FROM configuracion WHERE parametro = 'descuentosSAP'";
                $aux_descuentos_especiales = Yii::$app->db->createCommand($sql_descuentos_especiales)->queryOne();
                $sql_editar_precio = "SELECT * FROM configuracion WHERE parametro = 'editPrice'";
                $aux_editar_precio = Yii::$app->db->createCommand($sql_editar_precio)->queryOne();
                $sql_limit_inf = "SELECT * FROM configuracion WHERE parametro = 'priceValidateMin'";
                $aux_limit_inf = Yii::$app->db->createCommand($sql_limit_inf)->queryOne();
                $sql_limit_sup= "SELECT * FROM configuracion WHERE parametro = 'priceValidateMax'";
                $aux_limit_sup = Yii::$app->db->createCommand($sql_limit_sup)->queryOne();

                $sql_ctrl_lote= "SELECT * FROM configuracion WHERE parametro = 'lote'";
                $aux_ctrl_lote= Yii::$app->db->createCommand($sql_ctrl_lote)->queryOne();

                $contador_aux=count($auxiliar_condiciones_pago);
                if($contador_aux==0){
                    $auxiliar_condiciones_pago=$user->condicionespago2();
                    $auxiliar_condiciones_pago[0]["idUser"]=$resp[0]["idUsuario"];
                }
                $resp[0]["config"] = $user->config($resp[0]["idUsuario"]);
				$resp[0]["config"][0]["camposclientes"] = $user->camposdinamicosCliente(); 
                $resp[0]["monedas"] = Monedas::find()->orderby('Type asc')->all();
                $resp[0]["condicionespago"] =$auxiliar_condiciones_pago;
                //modificado lista de precios segun cliente//

                $sql_listaP = "SELECT valor FROM configuracion WHERE parametro = 'listapreciosSAP'";
                $dataListP = Yii::$app->db->createCommand($sql_listaP)->queryOne();
                if($dataListP['valor']==0){
                    $resp[0]["listaprecios"] = $user->listapreciosx($resp[0]["idUsuario"]);
                }
                else{
                    $sql_aux_cnf_territorios="select GROUP_CONCAT(idTerritorio) as territorios from usuariomovilterritoriodetalle where idUserMovil=".$resp[0]["idUsuario"]." group by idUserMovil" ;
                    $aux_cnf_territorios = Yii::$app->db->createCommand($sql_aux_cnf_territorios)->queryOne();
                    $aux_cnfu_trr=$aux_cnf_territorios["territorios"];
                    //consulta directa a sap
                    $campos = "distinct (\"ListNum\") as \"PriceListNo\" ";
                    $condicion = "where \"Territory\" in(".$aux_cnfu_trr.")";
                    $clienes = new Clientes();
                    $listaPreciosClienteSap=$clienes->obtenerCamposEspecificosLista($campos,$condicion);
                    $idListNun="";
                    foreach ($listaPreciosClienteSap  as  $value) {
                        $idListNun.=$value['PriceListNo'].",";
                    }
                    $idListNun=substr($idListNun, 0, -1);

                    $sql_lista_precio="select * from listaprecios where PriceListNo in(".$idListNun.")" ;
                    $data_lista_precio = Yii::$app->db->createCommand($sql_lista_precio)->queryAll();

                    $resp[0]["listaprecios"] = $data_lista_precio;
                }

                //ASIGNAR UN DATO AL NUMERO DE AUTORIZACION//
                if($uso_fex==1){
                    $dataDosifica= $user->docificacion($resp[0]["equipoId"]);
                    foreach ($dataDosifica as $key => $value) {
                        $dataDosifica[$key]['U_NumeroAutorizacion']=$resp[0]["equipoId"];
                    }
                }
                else{
                    $dataDosifica= $user->docificacion($resp[0]["equipoId"]);
                }

                $resp[0]["almacenes"] = $user->almacenes($resp[0]["sucursalxId"]);
                $resp[0]["docificacion"] = ($resp[0]["equipoId"] != '') ? $dataDosifica : [];
                $resp[0]["cuentascontables"] = ($resp[0]["equipoId"] != '') ? $user->cuentasContables($resp[0]["equipoId"]) : [];
                $resp[0]["menu"] = $user->accesos($resp[0]["idUsuario"]);               
                $resp[0]["pagoenfactura"] = $aux_ctrl_pagofacturas["valor"];
                $resp[0]["redondeo"] = $aux_ctrl_redondeo["valor"];
                /// te llega el cambio de SAP
                $resp[0]["cambioparalelo"] = $cambioParalelo; 
                $resp[0]["canal"] =1;                
               


                $resp[0]["gestionbancos"] = $bancos;                
                $resp[0]["empresa"] = Yii::$app->db->createCommand("SELECT * FROM empresa")->queryAll(); 
                $resp[0]["tipoTarjeta"] = Yii::$app->db->createCommand("SELECT * FROM tipotarjetas")->queryAll();
                $resp[0]["ctrl_fv"]=$aux_ctrl_fact_ven["valor"];
                $resp[0]["ctrl_conPago"]=$aux_ctrl_condPag["valor"];
                $resp[0]["ctrl_formaPago"]=$aux_ctrl_formaPago ["valor"];
                $resp[0]["config"][0]["ctrl_listaPrecios"]=$aux_ctrl_listaPrecios["valor"];
                $resp[0]["ctrl_listaPrecios"]=$aux_ctrl_listaPrecios["valor"];
                $resp[0]["ctrl_contrato"]=$aux_ctrl_contrato["valor"];
                $resp[0]["ctrl_grp_prods"]=1;
                $resp[0]["ctrl_lote"]=$aux_ctrl_lote["valor"];
                $resp[0]["descuentosEspeciales"]= $aux_descuentos_especiales["valor"];
                $resp[0]["editPrice"]= $aux_editar_precio["valor"];
                $resp[0]["priceValidateMin"]= $aux_limit_inf["valor"];
                $resp[0]["priceValidateMax"]=  $aux_limit_sup["valor"];
                $resp[0]["perManejaPercepcion"]=  $aux_maneja_per["valor"];

                //$timezone="America/La_Paz";
                date_default_timezone_set("America/La_Paz");
                //$dt=new datetime("now",new datetimezone($timezone));
                //date("Y/m/d H:i:s",(strtotime($dateTimeUTC)+$dt->getOffset()));
                $fecha = date('Y-m-d');
                $sql_tp="Select * from  vi_tiposcambio where ExchangeRateDate>='{$fecha}'";
                Yii::error("TIPOCAMBIO: ".$sql_tp);
                $resp[0]["tiposcambio"] = Yii::$app->db->createCommand($sql_tp)->queryAll();
                $sqlcp = "SELECT * FROM tipocambioparalelo Order by DateUpdate Desc";
                $sqlScp = "SELECT * FROM vi_cambioparalelo0 where fecha>=CURDATE() Order by DateUpdate ASC";

                $this->UsuarioLog($data['usuarioNombreUsuario'],$data['plataformaEmei'],$resp[0]["idUsuario"]);

                // te llega el tipo de cmabio paraleelo  que puede ser igual al tipo cambio de sap
                if ($cambioParalelo) $resp[0]["tipocambioparalelo"] =Yii::$app->db->createCommand($sqlcp)->queryOne(); //TipoCambioParalelo::find()->where(['fecha' => $fecha])->one();
                else $resp[0]["tipocambioparalelo"] = Yii::$app->db->createCommand($sqlScp)->queryOne();
                
                 
                 /**********Validacion Disponible************* */
                 $validaciondisponible = "SELECT valor FROM configuracion WHERE parametro = 'validaciondisponible' and estado=1";
                 $validaciondisponible = Yii::$app->db->createCommand($validaciondisponible)->queryOne();
                 $resp[0]["validaciondisponible"] =$validaciondisponible['valor'];

                 /*******************DATOS DE CONFIGURACION - CAMPOS ******************* */
                 $canalVenta = "SELECT valor FROM configuracion WHERE parametro = 'CanalVenta' and estado=1";
                 $canalVenta = Yii::$app->db->createCommand($canalVenta)->queryOne();

                 $camposCcus = "SELECT valor FROM configuracion WHERE  parametro = 'CamposCcus' and estado=1";
                 $camposCcus = Yii::$app->db->createCommand($camposCcus)->queryOne();

                 $resp[0]["usaCamposCanales"] =$canalVenta['valor'];
                 $resp[0]["usaCamposCcus"] =$camposCcus['valor'];

                 //si usa campos CCus se envia la data de creacion
                 $camposUsuario=array();
                 if($camposCcus['valor']==1){
                   
                    $auxcamposusuario = Auxcamposusuario::find()
                    ->where("estado=1")
                    //->with('aux_camposusuariodata')
                    ->all();
                    foreach ($auxcamposusuario as $key => $value) {
                    
                        $resultadoData = Yii::$app->db->createCommand("SELECT code,name FROM aux_camposusuariodata WHERE estado=1  and id_aux_camposusuario=".$value['id'])->queryAll();
                        $camposUsuario[$key]["objeto"] = $value['objeto'];
                        $camposUsuario[$key]["usar"]=$value['visible'];
                        $camposUsuario[$key]["label"]=$value["label"];
                        $camposUsuario[$key]["variablemovil"]=$value["variablemovil"];
                        $camposUsuario[$key]["values"] =$resultadoData;
    
                    }
                 }
                 $resp[0]["camposUsuario"] =$camposUsuario;




                 /* CAMPOS DINAMICOS DE USUARIO */
                /*$sql_camposusuarios = "SELECT multiCamposUsuarios FROM usuarioconfiguracion WHERE idUser ='".$resp[0]["idUsuario"]."'";
                $aux_camposusuarios = Yii::$app->db->createCommand($sql_camposusuarios)->queryOne();
                $aux_camposusuarios = $aux_camposusuarios["multiCamposUsuarios"];
                Yii::error("CAMPOS DE USUARIO: ");
                $aux_camposusuarios = json_decode($aux_camposusuarios, true);
                */
                //recuperar automaticamente los campos de usuario para todos
                $sql_camposusuarios = "SELECT id FROM camposusuarios";
                $aux_camposusuarios = Yii::$app->db->createCommand($sql_camposusuarios)->queryAll();
                $camposusuario = [];

                foreach ($aux_camposusuarios as $key => $value) {
                    $sql_camp = "SELECT c.Id,c.Objeto,REPLACE(c.Nombre, ' ', '') as Nombre,c.tipocampo,c.longitud,'' as lista,c.Label, cm.nombre as cmidd, o.tabla,o.tabla_xmobile, c.relacionado,c.flagrelacion,c.documento,c.usatablasap,c.tablasap FROM camposusuarios c inner join camposusuario_camposmidd cm on c.campmidd = cm.id inner join objetostablas o on o.id = c.objeto WHERE c.id ='".$value['id']."' and c.Status = '1'";
                    $aux_resul = Yii::$app->db->createCommand($sql_camp)->queryOne();
                    array_push($camposusuario,$aux_resul);
                };

                foreach ($camposusuario as $key => $value) {
                    if($value["tipocampo"] == 1){
                        if($value["usatablasap"] == 1){
                            $hana=New hana;
                            $sql_hana='Select * from "'.$value["tablasap"].'"';
                            $aux_resul =$hana->ejecutarconsultaAll($sql_hana);
                            //se obtine la realcion de la cabecera y el detalle 
                            foreach ($aux_resul as $keyD => $valueD) {
                                $aux_resul[$keyD]['cabecera']=$value['relacionado'];
                            }
                            $camposusuario[$key]["lista"] = $aux_resul;
                            //$camposusuario[$key]["lista"]["cabecera"] = $value['relacionado'];
                        }else{
                            $sql_camp = "SELECT * FROM listacamposusuarios WHERE IdcampoUsuario ='".$value["Id"]."' and Status = '1'";
                            $aux_resul = Yii::$app->db->createCommand($sql_camp)->queryAll();
                            $camposusuario[$key]["lista"] = $aux_resul;
                        }
                        
                    }
                };


                $resp[0]["campodinamicos"] =$camposusuario;

                 /***************DATOS FEX****************** */
                 $fex_url="SELECT valor2 FROM configuracion WHERE parametro in('FEX_LEYENDA','FEX_URL')";
                 $fex_url_dato = Yii::$app->db->createCommand($fex_url)->queryAll();
                 $leyendafex = $fex_url_dato[0]["valor2"];
                 $rutafex = $fex_url_dato[1]["valor2"];
                 
                 // se obtiene url del SIAT
                 $fex_url_siat="SELECT valor2 FROM configuracion WHERE parametro='FEX_URL_SIAT' and estado=1";
                 $fex_url_siat_dato = Yii::$app->db->createCommand($fex_url_siat)->queryOne();
                 $rutafexsiat = $fex_url_siat_dato["valor2"];

                 

                 $usa_consolidador_sql="SELECT valor FROM configuracion WHERE parametro='usa_consolidador' and estado=1";
                 $usa_consolidador = Yii::$app->db->createCommand($usa_consolidador_sql)->queryOne();

                 $resp[0]["uso_fex"]=$uso_fex; // ver linea 131
                 $resp[0]["usa_consolidador"]=$usa_consolidador["valor"];  
                 
                 $resp[0]["fex_tipoDocumento"] = Yii::$app->db->createCommand("SELECT * from fex_tipodocumento")->queryAll();
                 $resp[0]["fex_codigoexcepcion"] = Yii::$app->db->createCommand("SELECT * from fex_codigoexcepcion")->queryAll();
                 $resp[0]["motivoanulacion"] = Yii::$app->db->createCommand("SELECT * from motivosanulacion")->queryAll();
                 
                 $resp[0]["fex_leyenda"] = $leyendafex;
                 $resp[0]["fex_url"] = $rutafex;
                 $resp[0]["fex_url_siat"]=$rutafexsiat;

                 //nuevos parametros de configuracion
                 $facturaReserva="SELECT valor FROM configuracion WHERE parametro='usaFacturaReserva' and estado=1";
                 $facturaReservaD = Yii::$app->db->createCommand($facturaReserva)->queryOne();
                 $leyendaVisor="SELECT valor FROM configuracion WHERE parametro='usaLeyendaVisor' and estado=1";
                 $leyendaVisorD = Yii::$app->db->createCommand($leyendaVisor)->queryOne();

                 $creacionClientesA="SELECT valor FROM configuracion WHERE parametro='creacionClientesActivos' and estado=1";
                 $creacionClientesAD = Yii::$app->db->createCommand($creacionClientesA)->queryOne();

                 $resp[0]["usaFacturaReserva"] = $facturaReservaD["valor"];
                 $resp[0]["usaLeyendaVisor"]=$leyendaVisorD["valor"];
                 $resp[0]["creacionClientesActivos"]=$creacionClientesAD["valor"];
   
                 //para el layaut de impresion
                 $layautConfig="SELECT valor FROM configuracion WHERE parametro='templateLayautImprecion' and estado=1";
                 $layautConfigData = Yii::$app->db->createCommand($layautConfig)->queryOne();
                 $resp[0]["layautConfig"]=$layautConfigData["valor"];

                 $nitNumeroSql="SELECT valor FROM configuracion WHERE parametro='validanitnumerico' and estado=1";
                 $nitNumeroData = Yii::$app->db->createCommand($nitNumeroSql)->queryOne();
                 $resp[0]["validanitnumerico"]=$nitNumeroData["valor"];

                 $usa_redondeoSql="SELECT valor FROM configuracion WHERE parametro='usa_redondeo' and estado=1";
                 $usa_redondeoData = Yii::$app->db->createCommand($usa_redondeoSql)->queryOne();
                 $resp[0]["usa_redondeo"]=$usa_redondeoData["valor"];
                 
                 Yii::error("datos login: ".json_encode($resp)); 

                return $this->correcto($resp, "Usuario encontrado", 200);
            } else {
                return ['mensaje' => 'Usuario o password no son validos'];
            }
        } else {
            return ['mensaje' => 'Sus credenciales no son validas'];
        }
    }

    public function UsuarioLog($user,$plataforma,$idUsuario){
        /*****SE INSERTA EL USUAARIO A UN REPOSITORIO DE INGRESOS*****/

        $usuariolog =  new Usuariolog();
        $usuariolog->load(Yii::$app->request->post());
        $usuariolog->fecha=date('Y-m-d'); 
        $usuariolog->fechaIngreso=date('Y-m-d H:i:s');
        $usuariolog->usuario=$user;
        $usuariolog->idUsuario=$idUsuario;
        $usuariolog->ipAddress="127.0.0.1";
        $usuariolog->codigo=$plataforma;
        $usuariolog->save();       
        Yii::error("INSERTA TABLA USUARIOLOG: ".$plataforma);

        /*****FIN INSERTA EL USUAARIO A UN REPOSITORIO DE INGRESOS*****/
    }

}
