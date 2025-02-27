<?php

namespace api\controllers;

use backend\models\Clientes;
use Yii;
use backend\models\Servislayer;
use Carbon\Carbon;
use yii\rest\ActiveController;
use api\traits\Respuestas;

class ClientesmovilController extends ActiveController {

    public $modelClass = 'backend\models\Usuario';

    use Respuestas;

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
        Yii::error("DATA envion cliente :: " . json_encode($datos));
        
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
            $registro->Country = "PE"; //$cliente["Country"];
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
             $response = Yii::$app->db->createCommand($sql)->queryOne();

            if (count($response) == 1) {
                array_push($result, [
                    "cardcode" => 0,
                    "U_XM_Mobilecod" => 0
                ]);
                // return $this->correcto($response, 'Error al sincronizar');
            } else {
                array_push($result, [
                    "cardcode" => $response["CardCode"],
                    "U_XM_Mobilecod" => $response["Mobilecod"]
                ]);
            }

            // OBJETO DE CONTACTO PARA ACTUALIZAR EN SAP
            $contactos = [];
            if (!empty($cliente["ContactPerson"])) {
                
                foreach ($cliente["ContactPerson"] as $contacto) {
                    $responseContacto = Yii::$app->db->createCommand("CALL pa_sincronizarContactos(:cardcode,:nombre,'',:telefono,'','','',:comentario,:id,:correo,:titulo)", [
                                ":cardcode" => $contacto["cardCode"],
                                ":nombre" => $contacto["nombre"],
                                ":telefono" => $contacto["telefono"],
                                ":comentario" => $contacto["comentario"],
                                ":id" => $cliente["idUser"],
                                ":correo" => $contacto["correo"],
                                ":titulo" => $contacto["titulo"]
                            ])->execute();
                            
                            $nombreContacto = explode(" ", $contacto["nombre"]);
                            $contactoObjeto = [
                                "CardCode" => $contacto["cardCode"],
                                "Name" =>  $contacto["nombre"],
                                "FirstName" => $nombreContacto[0],
                                "LastName" => $nombreContacto[1],
                                "Phone1" => $contacto["telefono"],
                                "Remarks1" => $contacto["comentario"],
                                "Title" => $contacto["titulo"],
                                "E_Mail" => $contacto["correo"],
                                "Active" => "tYES"
                            ];
                            array_push($contactos, $contactoObjeto);
                     Yii::error("RESPUESTA DE CONTACTO ===========> " . json_encode($responseContacto));
                }
                
            }
            Yii::error("OBJETO DE CONTACTOS CREADO ===========> " . json_encode($contactos));

            $up = "UPDATE `clientes` SET cliente_std1 = '" . $cliente["cliente_std1"] . "', " .
                    "cliente_std2 = '" . $cliente["cliente_std2"] . "', " .
                    "cliente_std3 = '" . $cliente["cliente_std3"] . "', " .
                    "cliente_std4 = '" . $cliente["cliente_std4"] . "', " .
                    "cliente_std5 = '" . $cliente["cliente_std5"] . "', " .
                    "cliente_std6 = '" . $cliente["cliente_std6"] . "', " .
                    "cliente_std7 = '" . $cliente["cliente_std7"] . "', " .
                    "cliente_std8 = '" . $cliente["cliente_std8"] . "', " .
                    "cliente_std9 = '" . $cliente["cliente_std9"] . "', " .
                    "cliente_std10 = '" . $cliente["cliente_std10"] . "' where id = " . $response['id'];
            Yii::error("DATA Query : " . $up);
             $res = Yii::$app->db->createCommand($up)->execute();


            // UPDATE CLIENTE 
            Yii::error("RESPONSE NEW? :: " . $response['New']);
            if($response['New'] == 0) {
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
                    "Industry" => $response["Industry"],
                    "FreeText" => $response["FreeText"] . " Contacto: " . $response["ContactPerson"] . " Usuario Xmobile: " . $response["User"],
                    "CardForeignName" => $response["CardForeignName"],
                    /*"Properties1" => $response["Properties1"],
                    "Properties2" => $response["Properties2"],
                    "Properties3" => $response["Properties3"],
                    "Properties4" => $response["Properties4"],
                    "Properties5" => $response["Properties5"],
                    "Properties6" => $response["Properties6"],
                    "Properties7" => $response["Properties7"],*/
                    "ContactEmployees" => $contactos
                ];
                Yii::error("ENTRÓ AL UPDATE :: " . json_encode($datosCliente));
                 $serviceLayer->actiondir = "BusinessPartners('{$registro->CardCode}')";
                 $clienteSap = $serviceLayer->executePatchPut('PATCH', $datosCliente);
                 Yii::error("RESPUESTA SAP :: " . json_encode($clienteSap));
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
