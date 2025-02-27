<?php

namespace api\controllers;

use backend\models\Clientes;
use Yii;
use backend\models\Servislayer;
use Carbon\Carbon;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Sincronizar;
use backend\models\Sapenvio;


class ClientesmovilController extends ActiveController
{

    public $modelClass = 'backend\models\Usuario';

    use Respuestas;

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate(){
        $datos = Yii::$app->request->post();
        Yii::error("DATA envion cliente movil :: " . json_encode($datos));     
        //$serviceLayer = new Servislayer();
        $result = [];

        //verifica configuracion
        $configuracion = Yii::$app->db->createCommand("select parametro,valor from configuracion where estado=1")->queryAll();            
        foreach ($configuracion as $key => $value) {
            if($value['parametro']=='FEX') $cnf_fex=$value['valor'];
            if($value['parametro']=='CanalVenta') $cnf_canalVenta=$value['valor'];
            if($value['parametro']=='ClientesLocalizacion') $cnf_clienteLocal=$value['valor'];
        }
        $cnf_usuario=["cnf_fex"=>$cnf_fex,"cnf_canalVenta"=>$cnf_canalVenta,"cnf_clienteLocal"=>$cnf_clienteLocal];
        
        foreach ($datos as $cliente) {
            Yii::error("DATA envion cliente movil -->>> " . json_encode($cliente));
            //OBJETO CLIENTES//
            $response=$this->objetoClientes($cliente);

            Yii::error("response Cliente 1--->".json_encode($response));
            if (count($response) == 0) {
                array_push($result, [
                    "cardcode" => 0,
                    "U_XM_Mobilecod" => 0,
                    "respuestaSap"=>0
                ]);
            }                
            else {
                //ACTUALIZA CAMPOS PERSONALIZADO
                $datosClient= $this->actualizaCampos($cliente,$cnf_usuario,$response["id"]);
                // OBJETO DE CONTACTO 
                $contactos = $this->objetoContactos($datosClient,$cliente["ContactPerson"]);
                // OBJETO CLIENTES SUCURSALES 
                $sucursales = $this->objetoSucursales($datosClient,$cliente["SucursalPerson"]);
                

                Yii::error("RESPONSE NEW? :: " . $response['New']);
                Yii::error($datosClient);
                
                if ($response['New'] == 0) {
                    //ACTUALIZA REGISTRO A SAP
                    $respuesta=Sapenvio::clienteUpdate($datosClient,$contactos,$sucursales,$cnf_usuario);
                }else{
                    //REGISTRO NUEVO CLIENTE A SAP
                    $id=$datosClient["id"];
                    Yii::error("nuevo cliente :: " . $id);
                    $respuesta=Sapenvio::cliente($cnf_usuario,$id);    
                }
                Yii::error($respuesta);
                //actualiza RowNum de contactos y de clientes
                $this->ObtenerContactosCliente($respuesta[0]["CardCode"],$response["Mobilecod"]);
                $this-> ObtenerSucursalesCliente($respuesta[0]["CardCode"],$response["Mobilecod"]);
                array_push($result, [
                    "cardcode" => $respuesta[0]["CardCode"],
                    "U_XM_Mobilecod" => $response["Mobilecod"],
                    "respuestaSap"=>$respuesta[0]["mensaje"]
                ]);
            }
        }//for principal
        Yii::error("Respuesta cliente: ");
        Yii::error($result);
        return $this->correcto($result);
    }
    private function actualizaCampos($cliente,$cnf_usuario,$id){

            //ACTUALIZA CAMPOS PERSONALIZADOS
            if($cnf_usuario['cnf_canalVenta']==1){
                $canalVenta=",".
                "codecanal = '" . $cliente["codeCanal"] . "', " .
                "codesubcanal = '" . $cliente["codeSubCanal"] . "', " .
                "codetipotienda = '" . $cliente["codeTipoTienda"] . "', " .
                "cadena = '" . $cliente["cadena"] . "', " .
                "cadenatxt = '" . $cliente["cadenaTxt"] . "', " .
                "cadenaconsolidador = '" . $cliente["codeCadenaConsolidador"] . "'" ;    
            }else{
                $canalVenta="";
            }
            if($cnf_usuario['cnf_fex']==1){
                $camposFex=",".
                "Fex_tipodocumento = '" . $cliente["Fex_tipodocumento"] . "', " .
                "Fex_complemento = '" . $cliente["Fex_complemento"] . "', " .
                "Fex_codigoexcepcion = '" . $cliente["Fex_codigoexcepcion"] . "'" ;
            }else{
                $camposFex =""; 
            }

            /***************CCUS******************* */
             if (!empty($cliente["cuccs"])) {
                Yii::error(" CAMPOS PERSONALIZADOS: ".$cliente["cuccs"]);
                $clienteCcus=json_decode($cliente["cuccs"]);
                foreach ($clienteCcus as $cuc) {
                   $datosCcu1.=", ".$cuc->variablemovil."='".$cuc->code."' ";
                }
                if(count($cliente["cuccs"])>0){
                    $ccucs=$datosCcu1.", cadenaCcus='".$cliente["cuccs"]."'";
                }
                else{
                    $ccucs="";
                }
            }
            else{
                $ccucs="";
            }
            // UPDATE A TODOS LOS CAMPOS PERSONALIZADOS// 
            $up = "UPDATE `clientes` SET cliente_std1 = '" . $cliente["cliente_std1"] . "', " .
                "cliente_std2 = 'N', " .
                "cliente_std3 = '" . $cliente["cliente_std3"] . "', " .
                "cliente_std4 = '" . $cliente["cliente_std4"] . "', " .
                "cliente_std5 = '" . $cliente["cliente_std5"] . "', " .
                "cliente_std6 = '" . $cliente["cliente_std6"] . "', " .
                "cliente_std7 = '" . $cliente["cliente_std7"] . "', " .
                "cliente_std8 = '" . $cliente["cliente_std8"] . "', " .
                "cliente_std9 = '" . $cliente["cliente_std9"] . "', " .
                "cliente_std10 = '" . $cliente["cliente_std10"] . "'".
                $canalVenta.
                $camposFex.
                $ccucs.
                " where id = " . $id;
            Yii::error("DATA Query Update : " . $up);
            $res = Yii::$app->db->createCommand($up)->execute();
            $datos = Yii::$app->db->createCommand("SELECT *, 0 AS New FROM clientes WHERE id =".$id)->queryOne();
            
            Yii::error($datos);
            return $datos;
    }

    private function objetoClientes($cliente){

        if($cliente["Longitude"]==""){
            $cliente["Longitude"]=$cliente["SucursalPerson"][0]->u_lon;
            $cliente["Latitude"]=$cliente["SucursalPerson"][0]->u_lat;
        }
       // Yii::error("Parametros de configuracion: cnf_fex= ".$cnf_usuario['cnf_fex']." - cnf_canalVenta= ".$cnf_usuario['cnf_canalVenta']." - cnf_clienteLocal=".$cnf_usuario['cnf_clienteLocal']);

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
        // $registro->img = "sin imagen";
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
        $registro->Properties7 = 'tNO';*/
        //$registro->Fex_tipodocumento=$cliente["Fex_tipodocumento"];
       // $registro->Fex_complemento=$cliente["Fex_complemento"];
       // $registro->Fex_codigoexcepcion=$cliente["Fex_codigoexcepcion"];

       /* foreach ($diasVisita as $diaVisita) {
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
                case 'Sábado':
                    $registro->Properties6 = 'tYES';
                    break;
                case 'Domingo':
                    $registro->Properties7 = 'tYES';
                    break;
            }
        }*/
        
        $registro->Territory = $cliente["rutaterritorisap"];
        //$registro->Territory = $cliente["SucursalPerson"][0]["u_territorio"];
        if ($cliente["tipoEmpresa"] == '' || $cliente["tipoEmpresa"] == 'undefined' || $cliente["tipoEmpresa"] == '0') {
            $registro->Industry = '-1';
        }else {
            $registro->Industry = $cliente["tipoEmpresa"];
        }

        $fileName="addPhoto.svg";
        if($registro->img && $registro->img!=''){
          
           // $decodificado = base64_decode($registro->img); 
           preg_match("/data:image\/(.*?);/",$registro->img,$image_extension); // extract the image extension
           $image = preg_replace('/data:image\/(.*?);base64,/','',$registro->img); // remove the type part
           $image = str_replace(' ', '+', $image);
           $fileName = $registro->CardCode.'-'.time()."-".uniqid().".jpg"; 
           Yii::error("decode img ->".$image); 
           Yii::error("decode img -> 2".json_encode($registro->img)); 
         
            file_put_contents ("./imgs/cli/".$fileName, base64_decode($image));
        }
       
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
            "'$fileName'," .
            "'$registro->Industry'," .
            "'$registro->CardCode'" .
            ");";
            Yii::error("response Cliente 0--->".$sql);
            $response = Yii::$app->db->createCommand($sql)->queryOne();
        return $response;
    }

    private function objetoContactos($clientes,$clienteContactos){
        $contactos = [];
        if (!empty($clienteContactos)) {
            Yii::error("CONTACTOS CLIENTES: ". json_encode($clienteContactos));

            foreach ($clienteContactos as $contacto) {
                $responseContacto = Yii::$app->db->createCommand("CALL pa_SincronizarContactos(:cardcode,:nombre,'',:telefono,'','','',:comentario,:id,:correo,:titulo,:Mobilecode)", [
                    ":cardcode" => $contacto["cardCode"],
                    ":nombre" => $contacto["nombre"],
                    ":telefono" => $contacto["telefono"],
                    ":comentario" => $contacto["comentario"],
                    ":id" => $clientes["idUser"],
                    ":correo" => $contacto["correo"],
                    ":titulo" => $contacto["titulo"],
                    ":Mobilecode"=>$clientes['Mobilecod']
                ])->execute();

                $aux1 = $contacto["nombre"];
                $aux2 = $clientes['Mobilecod'];
                $RowNum = "";
                $serie = Yii::$app->db->createCommand("SELECT InternalCode FROM contactos WHERE Nombre = '$aux1' and Mobilecode = '$aux2' ")
                ->queryOne();
                $RowNum = $serie['InternalCode'];
                
                $nombreContacto = explode(" ", $contacto["nombre"]);
                Yii::error("InternalCode : ".$RowNum );
                if($RowNum == "")
                {
                    $contactoObjeto = [
                        
                        "CardCode" => $contacto["cardCode"],
                        "Name" =>  $contacto["nombre"],
                        "FirstName" => $nombreContacto[0],
                        "LastName" => $nombreContacto[1],
                        "Phone1" => $contacto["telefono"],
                        "Remarks1" => $contacto["comentario"],
                        "Title" =>substr($contacto["titulo"], 0, 9),
                        "E_Mail" => $contacto["correo"],
                       
                        "Active" => "tYES"
                     ];
                    
                }else{
                    $contactoObjeto = [
                        "InternalCode"=> $RowNum,
                        "CardCode" => $contacto["cardCode"],
                        "Name" =>  $contacto["nombre"],
                        "FirstName" => $nombreContacto[0],
                        "LastName" => $nombreContacto[1],
                        "Phone1" => $contacto["telefono"],
                        "Remarks1" => $contacto["comentario"],
                        "Title" =>substr($contacto["titulo"], 0, 9),
                        "E_Mail" => $contacto["correo"],
                       
                        "Active" => "tYES"
                     ];
                }
                array_push($contactos, $contactoObjeto);
              
                Yii::error(json_encode($contactoObjeto));
            }
        }
        return $contactos;
    }

    private function objetoSucursales($clientes,$clienteSucursal){
        $sucursales = [];
        if (!empty($clienteSucursal)) {
            foreach ($clienteSucursal as $sucursal) {
                Yii::error("response database sucursal->" . json_encode($sucursal));
                $responsesucursal = Yii::$app->db->createCommand("CALL pa_SincronizarSucursalesClientes
                     (:AddresName,:Street,:State,:FederalTaxId,:CreditLimit,:CardCode,:User,:Status,:idDocumento,:TaxCode,:AdresType,:u_zona,:u_lat,:u_lon,:u_territorio,:u_vendedor,:Mobilecode,:RowNum)", [
                    ":AddresName" => $sucursal["AddresName"],
                    ":Street" => $sucursal["Street"],
                    ":State" => $sucursal["State"],
                    ":FederalTaxId" => $sucursal["FederalTaxId"],
                    ":CreditLimit" => $clientes["CreditLimit"],
                    ":CardCode" => $sucursal["CardCode"],
                    ":User" => $sucursal["User"],
                    ":Status" => $sucursal["Status"],
                    ":idDocumento" => $sucursal["idDocumento"],
                    ":TaxCode" => $sucursal["TaxCode"],
                    ":AdresType" => $sucursal["AdresType"],
                    ":u_zona" => $sucursal["u_zona"],
                    ":u_lat" => $sucursal["u_lat"],
                    ":u_lon" => $sucursal["u_lon"],
                    ":u_territorio" => $sucursal["u_territorio"],
                    ":u_vendedor" => $sucursal["u_vendedor"],
                    ":Mobilecode" => $clientes['Mobilecod'],
                    ":RowNum" => $sucursal["LineNum"]
                ])->execute(); 
                $nombresucursal = explode(" ", $sucursal["nombre"]);
                
                $aux1 = $sucursal["AddresName"];
                $aux2 = $clientes['Mobilecod'];
                $aux3 = $sucursal["AdresType"];
                $RowNum = "";
                $serie = Yii::$app->db->createCommand("SELECT RowNum FROM clientessucursales WHERE AddresName = '$aux1' and Mobilecode = '$aux2' and AdresType = '$aux3'  ")
                ->queryOne();
                $RowNum = $serie['RowNum'];
    
                
                if ($sucursal["AdresType"] == "B") 
                {
                    $auxtipo = "bo_BillTo";
                    $city="";
                } 
                else 
                {
                    $auxtipo = "bo_ShipTo";
                    $city=$clientes['ccu2'];
                }
    
                $sucursalObjeto = [
                    "RowNum" => $RowNum,
                    "BPCode" => $clientes['CardCode'],
                    "AddressName" => $sucursal["AddresName"],
                    "AddressName2"=> $sucursal["Street"],
                    "Street" => $sucursal["Street"],
                    "AddressType" => $auxtipo,
                    "City"=>$city,
                    //"U_XM_Latitud" => $sucursal["u_lat"],
                    //"U_XM_Longitud" => $sucursal["u_lon"],
                    //"U_Territorio" => $sucursal["u_territorio"],
                    
                ];
                array_push($sucursales, $sucursalObjeto);
                Yii::error("ClienteSucursal");
                Yii::error("response database sucursal" . json_encode($sucursalObjeto));
            }     
        }
        return $sucursales;
    }


    private function remplaceString($string) {
        if (!is_null($string)) {
            $string=str_replace('\'', '`', $string);
            $string=addslashes($string);
            Yii::error("REMPLAZA VERDAD");
            Yii::error($string);
            return $string;
        }
        Yii::error("REMPLAZA FALSE");
        Yii::error($string);
        return $string;
    }
    private function ObtenerContactosCliente($cardCode,$mobileCode){
        Yii::error("actualizacion contactos ");
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 305,"cliente"=>$cardCode));
        $respuesta = $serviceLayer->executex($data);
        $respuesta = json_decode($respuesta);
        foreach ($respuesta as $puntero) {
            $sql = "UPDATE contactos set CardCode='{$puntero->CardCode}', InternalCode= '{$puntero->CntctCode}' where Mobilecode='{$mobileCode}' and nombre='{$puntero->Name}'";
            Yii::error("actualizacion contactos sql ".$sql);
            Yii::$app->db->createCommand($sql)->execute();
        }
    }
    private function ObtenerSucursalesCliente($cardCode,$mobileCode){
        Yii::error("actualizacionSucursales ");
        $serviceLayer = new Sincronizar();
        $data = json_encode(array("accion" => 56,"CardCode"=>$cardCode));
        $respuesta = $serviceLayer->executex($data);
        $respuesta = json_decode($respuesta);
        foreach ($respuesta as $puntero) {
            $sql = "UPDATE clientessucursales set CardCode='{$puntero->CardCode}', RowNum= '{$puntero->LineNum}' where Mobilecode='{$mobileCode}' and AddresName='{$puntero->AddressName}'";
            Yii::error("actualizacion contactos sql ".$sql);
            Yii::$app->db->createCommand($sql)->execute();
        }
    }
}