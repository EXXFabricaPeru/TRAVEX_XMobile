<?php

namespace api\controllers;

use backend\models\Clientes;
use backend\models\Contactos;
use backend\models\Clientessucursales;
use Yii;
use backend\models\Servislayer;
use Carbon\Carbon;
use yii\rest\ActiveController;
use api\traits\Respuestas;

class ClientesimportdosController extends ActiveController {

    public $modelClass = 'backend\models\Usuario';

    use Respuestas;
    /* public function init()
      {
      parent::init();
      \Yii::$app->user->enableSession = false;
      }

      public function behaviors()
      {
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
        $datos = Yii::$app->request->post();

        Yii::error("DATA envion cliente ::: " . json_encode($datos));
        $serviceLayer = new Servislayer();
        $result = [];
        foreach ($datos as $cliente) {
            $registro = new Clientes();
            $registro->id = $cliente["id"];
            $registro->CardCode = $cliente["CardCode"];
            $registro->CardName = $cliente["CardName"];
            $registro->FederalTaxId = $cliente["FederalTaxId"];
            $registro->Address = $cliente["Address"];
            $registro->PhoneNumber = $cliente["PhoneNumber"];
            $registro->PriceListNum = $cliente["PriceListNum"];
            $registro->ContactPerson = $cliente["ContactPerson"];
            $registro->img = $cliente["img"];
            $registro->Latitude = $cliente["Latitude"];
            $registro->Longitude = $cliente["Longitude"];
            $registro->CardType = $cliente["CardType"];
            $registro->CreditLimit = $cliente["CreditLimit"];
            $registro->MaxCommitment = $cliente["MaxCommitment"];
            $registro->DiscountPercent = $cliente["DiscountPercent"];
            $registro->SalesPersonCode = $cliente["SalesPersonCode"];
            $registro->Currency = $cliente["Currency"];
            $registro->County = $cliente["County"];
            $registro->Country = "PE";//$cliente["Country"];
            $registro->CurrentAccountBalance = $cliente["CurrentAccountBalance"];
            $registro->NoDiscounts = $cliente["NoDiscounts"];
            $registro->PriceMode = $cliente["PriceMode"];
            $registro->PayTermsGrpCode = $cliente["PayTermsGrpCode"];
            $registro->GroupCode = $cliente["GroupCode"];
            $registro->User = $cliente["idUser"];
            $registro->Status = $cliente["Status"];
            $registro->DateUpdate = $cliente["DateUpdate"];
            $registro->Phone2 = $cliente["celular"];
            $registro->Cellular = $cliente["pesonacontactocelular"];
            $registro->EmailAddress = $cliente["correoelectronico"];
            $registro->FreeText = $cliente["comentario"];
            $registro->CardForeignName = $cliente["razonsocial"];
            $diasVisita = explode(',', $cliente["diavisita"]);
            /*$registro->Properties1 = 'tNO';
            $registro->Properties2 = 'tNO';
            $registro->Properties3 = 'tNO';
            $registro->Properties4 = 'tNO';
            $registro->Properties5 = 'tNO';
            $registro->Properties6 = 'tNO';
            $registro->Properties7 = 'tNO';
            foreach ($diasVisita as $diaVisita) {
                switch ($diaVisita) {
                    case 'Lunes':
                        $registro->Properties1 = 'tYES';
                        break;
                    case 'Martes':
                        $registro->Properties2 = 'tYES';
                        break;
                    case 'Miercoles':
                        $registro->Properties3 = 'tYES';
                        break;
                    case 'Jueves':
                        $registro->Properties4 = 'tYES';
                        break;
                    case 'Viernes':
                        $registro->Properties5 = 'tYES';
                        break;
                    case 'Sabado':
                        $registro->Properties6 = 'tYES';
                        break;
                    case 'Domingo':
                        $registro->Properties7 = 'tYES';
                        break;
                }
            }*/
            $registro->Territory = $cliente["rutaterritorisap"];
            $registro->Industry = $cliente["tipoEmpresa"];
            $sql = "CALL pa_sincronizarCliente2(" .
                    "'$registro->CardCode'," .
                    "'$registro->CardName'," .
                    "'$registro->FederalTaxId'," .
                    "'$registro->Address'," .
                    "'$registro->PhoneNumber'," .
                    "$registro->PriceListNum" . "," .
                    "''," . //"'$registro->ContactPerson',".
                    "'$registro->Latitude'," .
                    "'$registro->Longitude'," .
                    "'$registro->CardType'," .
                    "$registro->CreditLimit" .
                    ",'$registro->MaxCommitment'," .
                    "$registro->DiscountPercent" . "," .
                    "$registro->SalesPersonCode" . "," .
                    "'$registro->Currency'," .
                    "'$registro->County'," .
                    "'$registro->Country'," .
                    "'$registro->CurrentAccountBalance'," .
                    "'$registro->NoDiscounts'," .
                    "'$registro->PriceMode'," .
                    "'$registro->PayTermsGrpCode'," .
                    "'$registro->GroupCode'," .
                    "'$registro->User'" . "," .
                    "'$registro->Status'," .
                    "'$registro->DateUpdate'," .
                    "'$registro->Cellular'," .
                    "'$registro->EmailAddress'," .
                    "'$registro->FreeText'," .
                    "'$registro->CardForeignName'," .
                    "''," . //"'$registro->Properties1'," .
                    "''," . //"'$registro->Properties2'," .
                    "''," . //"'$registro->Properties3'," .
                    "''," . //"'$registro->Properties4'," .
                    "''," . //"'$registro->Properties5'," .
                    "''," . //"'$registro->Properties6'," .
                    "''," . //"'$registro->Properties7'," .
                    "'$registro->Territory'," .
                    "'$registro->Phone2'," .
                    "'$registro->img'," .
                    "'$registro->Industry'," .
                    "'$registro->CardCode'" .
                    ");";
            Yii::error($sql);
            $response = Yii::$app->db->createCommand($sql)->queryOne();
            Yii::error(json_encode($response));
            if (count($response) == 1) {
                array_push($result, [
                    "cardcode" => 0,
                    "U_XM_Mobilecod" => 0
                ]);
                //return $this->correcto($response, 'Error al sincronizar');
            } else {
                array_push($result, [
                    "cardcode" => $response["CardCode"],
                    "U_XM_Mobilecod" => $response["Mobilecod"]
                ]);
            }

            $personasDeContacto = [];
            $personasDeContactoFINAL = [];
            if (!empty($cliente["ContactPerson"])) {
                foreach ($cliente["ContactPerson"] as $contacto) {
                    //$sql = "CALL pa_sincronizarContactos(:carcode,:nombre,'',:telefono,'','','',:comentario,:id)";
                    //var_dump($sql);
                    $responseContacto = Yii::$app->db->createCommand("CALL pa_sincronizarContactos(:cardcode,:nombre,'',:telefono,'','','',:comentario,:id,:correo,:titulo)", [
                                ":cardcode" => $contacto["cardCode"],
                                ":nombre" => $contacto["nombre"],
                                ":telefono" => $contacto["telefono"],
                                ":comentario" => $contacto["comentario"],
                                ":id" => $cliente["idUser"],
                                ":correo" => $contacto["correo"],
                                ":titulo" => $contacto["titulo"]
                            ])->execute();
                    Yii::error(json_encode($responseContacto));

                    $dividir = explode(' ',$contacto["nombre"]);
                    $nombre = '';
                    $nombre2 = '';
                    $apellido = '';
                    switch(count($dividir)){
                        case 1: $nombre = $dividir[0]; break;
                        case 2: $nombre = $dividir[0]; $apellido = $dividir[1]; break;
                        case 3: $nombre = $dividir[0]; $apellido = $dividir[1]; $nombre2 = $dividir[2]; break;
                    }

                    $nuevoContacto = [
                        "CardCode" => $contacto["cardCode"],
                        "Name" => $contacto["nombre"],
                        "FirstName" => $nombre,
                        "MiddleName" => $nombre2,
                        "LastName" => $apellido,
                        "Phone1" => $contacto["telefono"],
                        "E_mail" => $contacto["correo"],
                        "InternalCode" => null
                    ];                    
                    array_push($personasDeContacto, $nuevoContacto);
                }
                $serviceLayer2 = new Servislayer();
                $serviceLayer2->actiondir = "BusinessPartners('{$registro->CardCode}')";
                $resulta2 = $serviceLayer2->executex();
                $resulta2 = $serviceLayer2->value;
                foreach($resulta2 as $res){
                    foreach($res->ContactEmployees as $con){
                        $Vcon = $con["Name"];
                        foreach($personasDeContacto as $ps){
                            $Ncon = $ps["Name"];
                            if ($Vcon == $Ncon){
                                $cn = [
                                    "CardCode" => $contacto["cardCode"],
                                    "Name" => $contacto["nombre"],
                                    "FirstName" => $nombre,
                                    "MiddleName" => $nombre2,
                                    "LastName" => $apellido,
                                    "Phone1" => $contacto["telefono"],
                                    "E_mail" => $contacto["correo"],
                                    "InternalCode" => $con["InternalCode"]
                                ];
                                array_push($personasDeContactoFINAL, $cn);
                            }
                            else{
                                $cn = [
                                    "CardCode" => $contacto["cardCode"],
                                    "Name" => $contacto["nombre"],
                                    "FirstName" => $nombre,
                                    "MiddleName" => $nombre2,
                                    "LastName" => $apellido,
                                    "Phone1" => $contacto["telefono"],
                                    "E_mail" => $contacto["correo"],
                                    "InternalCode" => null
                                ];
                                array_push($personasDeContactoFINAL, $cn);

                            } 
                        }
                    }
                }
            }
            /************* nueva funcionalidad campos dinámicos**********/
            /*$campos = Yii::$app->db->createCommand("SELECT * FROM `configuracion` WHERE estado = 1 and parametro like '%cliente_std%'")->queryAll();
            //Yii::error("DATA Campos : " . var_dump($campos));
            //Yii::error("DATA response id : " . $response['id']);
            $sql = "UPDATE `clientes` SET ";
            foreach($campos as $row => $val){
                $cmp = $campos[$row]['parametro'];
                $sql = $sql .$cmp = "'".$cliente[$cmp]."', " ;
                //Yii::error("DATA Query : " . $campos[$row]['parametro']); 
            }
            Yii::error("DATA Query : " . $sql);

            //$response['id']*/
            $up = "UPDATE `clientes` SET cliente_std1 = '".$cliente["cliente_std1"]."', ".
            "cliente_std2 = '". $cliente["cliente_std2"]."', ".
            "cliente_std3 = '". $cliente["cliente_std3"]."', ".
            "cliente_std4 = '". $cliente["cliente_std4"]."', ".
            "cliente_std5 = '". $cliente["cliente_std5"]."', ".
            "cliente_std6 = '". $cliente["cliente_std6"]."', ".
            "cliente_std7 = '". $cliente["cliente_std7"]."', ".
            "cliente_std8 = '". $cliente["cliente_std8"]."', ".
            "cliente_std9 = '". $cliente["cliente_std9"]."', ".
            "cliente_std10 = '". $cliente["cliente_std10"]."' where id = ".$response['id'];
            Yii::error("DATA Query : " . $up);
            $res = Yii::$app->db->createCommand($up)->execute();

            /************************************************************/

            /**
             * Crear Objeto Series Clientes
             * 1  Verificar que se utilizara series
             * 2  Mandar el objeto con una serie por defecto en la base de datos
             */
            if ($response['New'] == 1) {
                $clienteNuevo = [
                    "CardCode" => $response["CardCode"],
                    "CardName" => $response["CardName"],
                    "CardType" => 'cCustomer',
                    "Address" => $response["Address"],
                    "CreditLimit" => $response["CreditLimit"],
                    "MaxCommitment" => $response["MaxCommitment"],
                    "DiscountPercent" => $response["DiscountPercent"],
                    "SalesPersonCode" => $response["SalesPersonCode"],
                    "Currency" => $response["Currency"],
                    "County" => $response["County"],
                    "Country" => $response["Country"],
                    "CurrentAccountBalance" => $response["CurrentAccountBalance"],
                    "NoDiscounts" => "tNO",
                    "PriceMode" => "pmGross",
                    "FederalTaxID" => $response["FederalTaxId"],
                    "Phone1" => $response["PhoneNumber"],
                    "PayTermsGrpCode" => "-1",
                    "U_XM_Latitud" => $response["Latitude"],
                    "U_XM_Longitud" => $response["Longitude"],
                    "U_XM_Mobilecod" => $response["Mobilecod"],
                    "GroupCode" => $response["GroupCode"],
                    "Phone2" => $response["Phone2"],
                    "Cellular" => $response["Cellular"],
                    "EmailAddress" => $response["EmailAddress"],
                    "FreeText" => $response["FreeText"] . " Contacto: " . $response["ContactPerson"] . " Usuario Xmobile: " . $response["User"],
                    "CardForeignName" => $response["CardForeignName"],
                    "Territory" => $response["Territory"],
                    /*"Properties1" => $response["Properties1"],
                    "Properties2" => $response["Properties2"],
                    "Properties3" => $response["Properties3"],
                    "Properties4" => $response["Properties4"],
                    "Properties5" => $response["Properties5"],
                    "Properties6" => $response["Properties6"],
                    "Properties7" => $response["Properties7"],*/
                    "ContactEmployees" => $personasDeContactoFINAL
                ];
                $issetSerie = Yii::$app->db->createCommand("SELECT valor FROM configuracion WHERE parametro = 's_cliente'")
                        ->queryOne();
                if ($issetSerie['valor'] == '1') {
                    $serie = Yii::$app->db->createCommand("SELECT valor FROM configuracion WHERE parametro LIKE 's_defecto_cliente'")
                            ->queryOne();
                    unset($clienteNuevo['CardCode']);
                    $clienteNuevo['Series'] = $serie['valor'];
                    $clienteNuevo['Territory'] = "-2";
                    $registro->Territory = "-2";
                }
                /******************* Verifica campos de tabla configuracion ***********************/
                $campos = Yii::$app->db->createCommand("SELECT * FROM `configuracion` WHERE estado = 1 and parametro like '%cliente_std%' and valor4 = 'w'")->queryAll();
                foreach($campos as $row => $val){
                    $cmp = $campos[$row]['valor2'];
                    $campVal = Yii::$app->db->createCommand("SELECT ".$campos[$row]['parametro']." FROM `clientes` WHERE id = ".$response['id'])->queryAll();
                    $value = $campVal[0][$campos[$row]['parametro']];
                    Yii::error("DATA campo y valor : " . $cmp . ' -- '. $value); 
                    $clienteNuevo[$cmp] = $value;
                }
                /**********************************************************************************/
                //var_dump(json_encode($clienteNuevo)); die();
                Yii::error("DATA envion cliente 318");
                Yii::error(json_decode($clienteNuevo));
                $serviceLayer->actiondir = "BusinessPartners";
                Yii::error("OBJETO SERVICE_LAYER" . json_encode($clienteNuevo));
                $clienteSap = $serviceLayer->executePost($clienteNuevo);

                //var_dump($clienteSap); die();
                Yii::error("Respuesta SAP.- " . json_encode($clienteSap));
                $sqlUpdate = "CALL pa_sincronizarCliente2(" .
                        "'$clienteSap->CardCode'," .
                        "'$registro->CardName'," .
                        "'$registro->FederalTaxId'," .
                        "'$registro->Address'," .
                        "'$registro->PhoneNumber'," .
                        "$registro->PriceListNum" . "," .
                        "''," . //"'$registro->ContactPerson',".
                        "'$registro->Latitude'," .
                        "'$registro->Longitude'," .
                        "'$registro->CardType'," .
                        "$registro->CreditLimit" .
                        ",'$registro->MaxCommitment'," .
                        "$registro->DiscountPercent" . "," .
                        "$registro->SalesPersonCode" . "," .
                        "'$registro->Currency'," .
                        "'$registro->County'," .
                        "'$registro->Country'," .
                        "'$registro->CurrentAccountBalance'," .
                        "'$registro->NoDiscounts'," .
                        "'$registro->PriceMode'," .
                        "'$registro->PayTermsGrpCode'," .
                        "'$registro->GroupCode'," .
                        "'$registro->User'" . "," .
                        "'$registro->Status'," .
                        "'$registro->DateUpdate'," .
                        "'$registro->Cellular'," .
                        "'$registro->EmailAddress'," .
                        "'$registro->FreeText'," .
                        "'$registro->CardForeignName'," .
                        "''," . //"'$registro->Properties1'," .
                        "''," . //"'$registro->Properties2'," .
                        "''," . //"'$registro->Properties3'," .
                        "''," . //"'$registro->Properties4'," .
                        "''," . //"'$registro->Properties5'," .
                        "''," . //"'$registro->Properties6'," .
                        "''," . //"'$registro->Properties7'," .
                        "'$registro->Territory'," .
                        "'$registro->Phone2'," .
                        "'$registro->img'," .
                        "'$registro->Industry',";
                $sqlUpdate .= $issetSerie['valor'] == '1' ? "'$clienteSap->U_XM_Mobilecod'" : "'$registro->CardCode'";
                $sqlUpdate .= ");";

               
                //var_dump($sqlUpdate); die();
                if($clienteSap==false){                  
                        Yii::error("-->>> error".$clienteSap);
                        //$responseUpdate = Yii::$app->db->createCommand($sqlUpdate)->queryOne();
                        //Yii::error(json_encode($responseUpdate));
                }else{
                        Yii::error("-->>> sin error error"); 
                        $responseUpdate = Yii::$app->db->createCommand($sqlUpdate)->queryOne();
                        Yii::error(json_encode($responseUpdate));
                        $serie = Yii::$app->db->createCommand("UPDATE `clientes` SET StatusSend = 1  WHERE CardCode = '".$clienteSap->CardCode."'")
                            ->execute();

                }
                    
                
                //var_dump($responseUpdate); die();
                
            } else if ($response['New'] == 0) {
                $datosCliente = [
                    "CardName" => $response["CardName"],
                    "CardType" => 'cCustomer',
                    "Address" => $response["Address"],
                    "CreditLimit" => $response["CreditLimit"],
                    "MaxCommitment" => $response["MaxCommitment"],
                    "DiscountPercent" => $response["DiscountPercent"],
                    "PriceListNum" => $response["PriceListNum"],
                    "SalesPersonCode" => $response["SalesPersonCode"],
                    "Currency" => $response["Currency"],
                    "County" => $response["County"],
                    "Country" => $response["Country"],
                    "CurrentAccountBalance" => $response["CurrentAccountBalance"],
                    "NoDiscounts" => "tNO",
                    "PriceMode" => "pmGross",
                    "FederalTaxID" => $response["FederalTaxId"],
                    "Phone1" => $response["PhoneNumber"],
                    "PayTermsGrpCode" => "-1",
                    "U_XM_Latitud" => $response["Latitude"],
                    "U_XM_Longitud" => $response["Longitude"],
                    "GroupCode" => $response["GroupCode"],
                    "Phone2" => $response["Phone2"],
                    "Cellular" => $response["Cellular"],
                    "EmailAddress" => $response["EmailAddress"],
                    "Territory" => $response["Territory"],
                    "FreeText" => $response["FreeText"] . " Contacto: " . $response["ContactPerson"] . " Usuario Xmobile: " . $response["User"],
                    "CardForeignName" => $response["CardForeignName"],
                    /*"Properties1" => $response["Properties1"],
                    "Properties2" => $response["Properties2"],
                    "Properties3" => $response["Properties3"],
                    "Properties4" => $response["Properties4"],
                    "Properties5" => $response["Properties5"],
                    "Properties6" => $response["Properties6"],
                    "Properties7" => $response["Properties7"],*/
                    "ContactEmployees" => $personasDeContactoFINAL
                ];
                Yii::error("DATA envion cliente 425");
                Yii::error(json_encode($datosCliente));
                $serviceLayer->actiondir = "BusinessPartners('{$registro->CardCode}')";
                $clienteSap = $serviceLayer->executePatchPut('PATCH', $datosCliente);
                if (!isset($clienteSap->error)) {
                    $clienteNuevo = Clientes::find()->where("CardCode = '{$response["CardCode"]}'")->one();
                    $clienteNuevo->StatusSend = 1;
                    $clienteNuevo->save(false);
                }
            }
        }
        return $this->correcto($result);
    }

}
