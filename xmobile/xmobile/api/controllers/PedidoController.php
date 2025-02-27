<?php

namespace api\controllers;

use backend\models\Combos;
use backend\models\Unidadesmedida;
use Carbon\Carbon;
use Yii;
use yii\base\Exception;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use backend\models\Cabeceradocumentos;
use backend\models\Detalledocumentos;
use api\traits\Respuestas;

class PedidoController extends ActiveController {

    use Respuestas;

    const IT = 3;

    public $modelClass = 'backend\models\User';

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

    private function estadosDoc($cod) {
        $sql = 'SELECT * FROM cabeceradocumentos WHERE idDocPedido = "' . $cod . '";';
        $resp = Yii::$app->db->createCommand($sql)->queryAll();
        $doctype = 0;
        $DocEntry = 0;
        if (count($resp) > 0) {
            $DocEntry = $resp[0]['DocEntry'];
            switch ($resp[0]['DocType']) {
                case "DOF":
                    $doctype = 23;
                    break;
                case "DOP":
                    $doctype = 17;
                    break;
                case "DFA":
                    $doctype = 13;
                    break;
            }
        } else {
            $doctype = 0;
            $DocEntry = 0;
        }
        return ['doctype' => $doctype, 'docentry' => $DocEntry];
    }

    public function verificador($cod,$cardcode,$docdate) {
        // SIN LIMIT PREVIAMENTE
        $sql = 'SELECT * FROM cabeceradocumentos WHERE idDocPedido = "' .$cod . ' and CardCode="'.$cardcode.'" and DocDate ="'.$docdate.'"  LIMIT 1;';
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionCreate() {
        $datos = Yii::$app->request->post();
        Yii::error("datos de registro (documento): ".json_encode($datos));
        $arr = [];
        foreach ($datos as $pedido) {
            $id = 0;
            $x = $this->verificador($pedido["header"]["idDocPedido"],$pedido["header"]["CardCode"],$pedido["header"]["DocDate"]);
            $idaux = $pedido["header"]["id"];
            $aux2_sql = "";
            if (count($x) > 0) {
                $id = $this->updateHeader($pedido);
            } else {
                $id = $this->registerHeader($pedido);
                $this->registerDetalles($id, $pedido["detalles"], $pedido["header"]["origenclone"], $pedido);
                if($pedido["header"]["DocType"] == "DFA"){
                    $aux2_sql = 'Update  lbcc  set  U_NumeroSiguiente= U_NumeroSiguiente+1 WHERE U_NumeroAutorizacion = "' .$pedido["header"]["U_NumeroAutorizacion"]. '";';
                    $respuestaGuardado = Yii::$app->db->createCommand($aux2_sql)->execute();
                }
            }
            array_push($arr, [
                //"idPedidoUsr" => $pedido["header"]["DocNum"],
                "idPedidoUsr" => $idaux,
                "idPedidoServicio" => $id,
                "DocTypeEnviado" => $pedido["header"]["DocType"],
                "Sentencia_de_Actualizacion" => $aux2_sql,
                "Respuesta" => $respuestaGuardado,
            ]);
        }
        return $this->correcto($arr, "Documentos de Pedidos Registrados");
    }

    public function registerDetalles($id, $detalles, $clone, $pedido) {
        if (count($detalles) > 0) {
            $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $pedido["header"]["fecharegistro"]);
            foreach ($detalles as $lineaPedido) {
                if($lineaPedido["LineTotal"] >0){
                    $doctype = 0;
                    $docentry = 0;
                    if ($clone != '0') {
                        $data = $this->estadosDoc($clone);
                        $doctype = $data['doctype'];
                        $docentry = $data['docentry'];                                           
                    }
                    if ($lineaPedido["unidadid"]==""){
                        $qauxuom="Select Code from vi_productounidad where ItemCode='{$lineaPedido["ItemCode"]}'";
                        $res_qauxuom=Yii::$app->db->createCommand($qauxuom)->queryOne();
                        $lineaPedido["unidadid"]=$res_qauxuom["Code"];
                    }
                    $this->registerSeries($pedido["header"]["idDocPedido"],$lineaPedido["series"],$lineaPedido["ItemCode"],$pedido["usuariodataid"],$lineaPedido["LineNum"]);
                    $this->registerLotes($pedido["header"]["idDocPedido"],$lineaPedido["lotes"],$lineaPedido["ItemCode"],$pedido["usuariodataid"],$lineaPedido["LineNum"]);
                    $linea = new Detalledocumentos();
                    $linea->DocNum = isset($lineaPedido["DocNum"]) ? $lineaPedido["DocNum"] : 0;
                    $linea->LineNum = isset($lineaPedido["LineNum"]) ? $lineaPedido["LineNum"] : 0;
                    $linea->BaseType = isset($lineaPedido["BaseType"]) ? $lineaPedido["BaseType"] : $doctype;
                    $linea->BaseEntry = isset($lineaPedido["BaseEntry"]) ? $lineaPedido["BaseEntry"] : $docentry;
                    $linea->BaseLine = isset($lineaPedido["BaseLine"]) ? $lineaPedido["BaseLine"] : 0;
                    $linea->LineStatus = isset($lineaPedido["LineStatus"]) ? $lineaPedido["LineStatus"] : 0;
                    $linea->ItemCode = isset($lineaPedido["ItemCode"]) ? $lineaPedido["ItemCode"] : 0;
                    $linea->Dscription = isset($lineaPedido["Dscription"]) ? $lineaPedido["Dscription"] : 0;
                    $linea->Quantity = isset($lineaPedido["Quantity"]) ? $lineaPedido["Quantity"] : 0;
                    $linea->OpenQty = isset($lineaPedido["OpenQty"]) ? $lineaPedido["OpenQty"] : 0;
                    $linea->Price = isset($lineaPedido["Price"]) ? $lineaPedido["Price"] : 0;
                    $linea->Currency = isset($lineaPedido["Currency"])?$lineaPedido["Currency"]:'BS';
                    $linea->DiscPrcnt = isset($lineaPedido["DiscPrcnt"]) ? $lineaPedido["DiscPrcnt"] : 0;
                    $linea->LineTotal = isset($lineaPedido["LineTotal"]) ? $lineaPedido["LineTotal"] : 0;
                    $linea->WhsCode = isset($lineaPedido["WhsCode"]) ? $lineaPedido["WhsCode"] : 0;
                    $linea->CodeBars = isset($lineaPedido["CodeBars"]) ? $lineaPedido["CodeBars"] : 0;
                    $linea->PriceAfVAT = isset($lineaPedido["PriceAfVAT"]) ? $lineaPedido["PriceAfVAT"] : 0;
                    $linea->TaxCode = isset($lineaPedido["TaxCode"]) ? $lineaPedido["TaxCode"] : 0;
                    $linea->U_4DESCUENTO = isset($lineaPedido["U_4DESCUENTO"]) ? $lineaPedido["U_4DESCUENTO"] : 0;
                    $linea->U_4LOTE = isset($lineaPedido["U_4LOTE"]) ? $lineaPedido["U_4LOTE"] : 0;
                    $linea->GrossBase = isset($lineaPedido["GrossBase"]) ? $lineaPedido["GrossBase"] : 0;
                    $linea->idDocumento = $pedido["usuariodataid"] . date("y", date_timestamp_get($fecha)) . date("m", date_timestamp_get($fecha)) . $this->numPedido($pedido["header"]["DocNum"]);
                    $linea->fechaAdd = isset($lineaPedido["fechaAdd"]) ? $lineaPedido["fechaAdd"] : 0;
                    $linea->unidadid = isset($lineaPedido["unidadid"]) ? $lineaPedido["unidadid"] : 0;
                    $linea->tc = isset($lineaPedido["tc"]) ? $lineaPedido["tc"] : 0;
                    $linea->idCabecera = $id;
                    $linea->DiscMonetary = isset($lineaPedido['descuentoM']) ? $lineaPedido['descuentoM'] : 0;
                    $linea->LineTotalPay = isset($lineaPedido['LineTotalPay']) ? $lineaPedido['LineTotalPay'] : 0;
                    $linea->SalesUnitLength = 0; //isset($lineaPedido['SalesUnitLength']) ? $lineaPedido['SalesUnitLength'] : 0;
                    $linea->SalesUnitWidth = isset($lineaPedido['SalesUnitWidth']) ? $lineaPedido['SalesUnitWidth'] : 0;
                    $linea->SalesUnitHeight = isset($lineaPedido['SalesUnitHeight']) ? $lineaPedido['SalesUnitHeight'] : 0;
                    $linea->SalesUnitVolume = isset($lineaPedido['SalesUnitVolume']) ? $lineaPedido['SalesUnitVolume'] : 0;
                    $linea->DiscTotalPrcnt = $lineaPedido['DiscTotalPrcnt'];
                    $linea->DiscTotalMonetary = $lineaPedido['DiscTotalMonetary'];
                    $linea->ICET = isset($lineaPedido['icet']) ? $lineaPedido['icet'] : 0;
                    $linea->ICEE = isset($lineaPedido['icee']) ? $lineaPedido['icee'] : 0;
                    $linea->ICEP = isset($lineaPedido['icep']) ? $lineaPedido['icep'] : 0;
					$linea->xMOB_Venta1 = $lineaPedido["xMOB_Venta1"];
					$linea->xMOB_Venta2 = $lineaPedido["xMOB_Venta2"];
					$linea->xMOB_Venta3 = $lineaPedido["xMOB_Venta3"];
					$linea->xMOB_Venta4 = $lineaPedido["xMOB_Venta4"];
					$linea->xMOB_Venta5 = $lineaPedido["xMOB_Venta5"];
                    $linea->TreeType = isset($lineaPedido['TreeType']) ? $lineaPedido['TreeType'] : $this->tipoItem($lineaPedido["ItemCode"]);
                    $linea->save(false);
                    }
                
            }
        }
    }
    public function registerSeries($id,$series,$item,$usr,$linea){
        $contador=count($series);
        $hoy=date('Y-m-d');
        if($contador>0){   
            $db = Yii::$app->db;         
            foreach ($series as $serie){
                $sqlSub = "";
                $sqlSub .= "INSERT INTO seriesmarketing (DocumentId,SystemNumber,SerialNumber,ItemCode,Status,User,DateUpdate,linea) VALUES (";
                $sqlSub .= "'{$id}','{$serie["SystemNumber"]}','{$serie["SerialNumber"]}','{$item}','1','{$usr}','{$hoy}','{$linea}')";                
                $db->createCommand($sqlSub)->execute();
                $sqlSub0 = "";
                $sqlSub0= "INSERT INTO seriesusadas (ItemCode,DistNumber) VALUES (";
                $sqlSub0.= "'{$item}','{$serie["SystemNumber"]}')";
                $db->createCommand($sqlSub0)->execute();
                $sqlSub1 = " Update seriesproductos set Status=0 where ItemCode='" . $item . "' and SerialNumber='" . $serie["SerialNumber"] . "' ";
                $db->createCommand($sqlSub1)->execute();
            }
        }
    }
    public function registerLotes($id,$lotes,$item,$usr,$linea){
        
        $contador=count($lotes);

        $hoy=date('Y-m-d');
        if($contador>0){   
            $db = Yii::$app->db;         
            foreach ($lotes as $lote){
                $sqlSub = "";
                $sqlSub .= "INSERT INTO lotesmarketing (DocumentId,BatchNum,Quantity,ItemCode,linea,User,DateUpdate) VALUES (";
                $sqlSub .= "'{$id}','{$lote["BatchNum"]}','{$lote["Quantity"]}','{$item}','{$linea}','{$usr}','{$hoy}')";                
                Yii::error(" insertar lotes ".$sqlSub);
                $db->createCommand($sqlSub)->execute();
               
            }
        }
    }
    public function updateHeader($pedido) {
        $pedData = $this->verificador($pedido["header"]["idDocPedido"],$pedido["header"]["CardCode"],$pedido["header"]["DocDate"]);
        $id = $pedData[0]['id'];
        $cabecera = Cabeceradocumentos::findOne($id);
        if (isset($pedido["header"]["estado"]) && $pedido["header"]["estado"] == 6)
            $cabecera->estado = 6;
        $cabecera->save(false);
        return $cabecera->id;
    }

    public function registerHeader($pedido) {
        $fechaSend = date("Y-m-d");
        $cabecera = new Cabeceradocumentos();
        $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $pedido["header"]["fecharegistro"]);
        $cabecera->DocNum = $pedido["header"]["DocNum"];
        $cabecera->DocType = $pedido["header"]["DocType"];
        $cabecera->DocDate = $pedido["header"]["DocDate"];
        $cabecera->DocDueDate = $pedido["header"]["DocDueDate"];
        $cabecera->CardCode = $pedido["header"]["CardCode"];
        $cabecera->CardName = $pedido["header"]["CardName"];
        $cabecera->TaxDate = isset($pedido["header"]["TaxDate"]) ? $pedido["header"]["TaxDate"] : date("Y-m-d");
        $cabecera->Address = isset($pedido["header"]["Address"]) ? $pedido["header"]["Address"] : '';
        $cabecera->fecharegistro = $fecha;
        $cabecera->fechasend = $fechaSend;
        $cabecera->idDocPedido = $pedido["header"]["idDocPedido"];
        $cabecera->idUser = $pedido["usuariodataid"];
        $cabecera->gestion = date("Y", date_timestamp_get($fecha));
        $cabecera->mes = date("m", date_timestamp_get($fecha));
        $cabecera->correlativo = $pedido["header"]["id"];
        $cabecera->rowNum = isset($pedido["header"]["rowNum"]) ? $pedido["header"]["rowNum"] : count($pedido["detalles"]);
        $cabecera->DocTotal = $pedido["header"]["DocTotal"];
        $cabecera->DocTotalPay = $pedido["header"]["DocTotalPay"];
        $cabecera->DiscPrcnt = $pedido["header"]["DiscPrcnt"];
        $cabecera->DiscSum = $pedido["header"]["DiscSum"];
        $cabecera->U_4RAZON_SOCIAL = $pedido["header"]["U_4RAZON_SOCIAL"];
        $cabecera->U_4NIT = $pedido["header"]["U_4NIT"];
        $cabecera->PayTermsGrpCode = ($pedido["header"]["PayTermsGrpCode"] == 0) ? -1 : $pedido["header"]["PayTermsGrpCode"];
        $cabecera->TotalDiscPrcnt = isset($pedido["header"]["TotalDiscPrcnt"]) ? $pedido["header"]["TotalDiscPrcnt"] : 0;
        $cabecera->TotalDiscMonetary = isset($pedido["header"]["TotalDiscMonetary"]) ? $pedido["header"]["TotalDiscMonetary"] : 0;
        $cabecera->U_LATITUD = $pedido["header"]["U_LATITUD"];
        $cabecera->U_LONGITUD = $pedido["header"]["U_LONGITUD"];
        $cabecera->U_4MOTIVOCANCELADO = $pedido["header"]["U_4MOTIVOCANCELADO"];
        $cabecera->U_4DOCUMENTOORIGEN = $pedido["header"]["U_4DOCUMENTOORIGEN"];
        $cabecera->U_LB_NumeroAutorizac =isset( $pedido["header"]["U_NumeroAutorizacion"])?$pedido["header"]["U_NumeroAutorizacion"]:0;
        $cabecera->ControlCode =($pedido["header"]["DocType"]=="DFA" ? $pedido["header"]["ControlCode"] :0);
        $cabecera->UNumFactura =($pedido["header"]["DocType"]=="DFA" ? $pedido["header"]["UNumFactura"] :0); 
        $cabecera->SlpCode = isset($pedido["header"]["SlpCode"]) ? $pedido["header"]["SlpCode"] : -1;
        $cabecera->DocCur = isset($pedido["header"]["DocCur"]) ? $pedido["header"]["DocCur"] : null;
        $cabecera->Reserve = isset($pedido["header"]["Reserve"]) ? $pedido["header"]["Reserve"] : 0;
        $cabecera->clone = isset($pedido["header"]["origenclone"]) ? $pedido["header"]["origenclone"] : 0;
        $cabecera->giftcard = isset($pedido["header"]["giftcard"]) ? $pedido["header"]["giftcard"] : 0;
        $cabecera->estado = 3;
        $cabecera->sucursalxId = $pedido["header"]["sucursalId"];
        $cabecera->equipoId = $pedido["header"]["equipoId"];
        $cabecera->papelId = $pedido["header"]["papelId"];
        $cabecera->Comments = $pedido["header"]["Comments"];
        $cabecera->xMOB_Venta1 = $pedido["header"]["xMOB_Venta1"];
        $cabecera->xMOB_Venta2 = $pedido["header"]["xMOB_Venta2"];
        $cabecera->xMOB_Venta3 = $pedido["header"]["xMOB_Venta3"];
        $cabecera->xMOB_Venta4 = $pedido["header"]["xMOB_Venta4"];
        $cabecera->xMOB_Venta5 = $pedido["header"]["xMOB_Venta5"];
        //$this->registerCuotas($pedido["header"]["cuotas"],$pedido["usuariodataid"],$pedido["header"]);
        $cabecera->save(false);
        //Yii::error(" insertar cuotas".json_encode($pedido["header"]["cuotas"]));
        //$this->registerCuotas($pedido["header"]["cuotas"]);
        
        if(isset($pedido["header"]["giftcard"])){
            Yii::error("entra registro giftcard");
            $this->registroGiftcard($pedido["header"]["giftcard"],$pedido["header"]["idDocPedido"],$pedido["usuasriodataid"]);
        }
        
        return $cabecera->id;
    
    }
    public function registerCuotas($cuotas,$usuario,$cabecera) {
       // Yii::error(" insertar cuotas".json_encode($cabecera));       
        $iddocpedido=$cabecera["idDocPedido"];
        $fecharegistro=$cabecera["DocDate"];
        $cliente=$cabecera["CardCode"];
        $total=$cabecera["DocTotalPay"];
        $control=count($cuotas);
        Yii::error(" insertar cuotas".$control);
        $i=0;
        $sum_por=0;
        foreach ($cuotas as $cuota) {
            if($control==$i){
                $porcentage=100-$sum_por;
            }else{
                $porcentage=($cuota['Total']/$total)*100;
                $sum_por=$sum_por+$porcentage;
            }           
            $sqlSub = "";
            $sqlSub .= "INSERT INTO cuotasfactura (iddocpedido,DueDate,Percentage,Total,InstallmentId,usuario,fecharegistro,idcliente) VALUES (";
            $sqlSub .= "'{$iddocpedido}','{$cuota['DueDate']}','{$porcentage}',{$cuota['Total']},'{$cuota['InstallmentId']}','{$usuario}','{$fecharegistro}','{$cliente}');";
           Yii::error(" insertar cuotas" . $sqlSub);
            $db = Yii::$app->db;
            $db->createCommand($sqlSub)->execute();
        }
    } 
    public function actionCancel() {
        $documento = Cabeceradocumentos::find()
                ->where([
                    'idDocPedido' => Yii::$app->request->post('documentId'),
                    'DocType' => Yii::$app->request->post('doctype')
                ])
                ->one();
        if (!is_null($documento)) {
            $documento->estado = 6;
            if ($documento->save(false)) {
                return $this->correcto($documento->toArray());
            }
            return $this->error('Error al actualizar documento', 101);
        }
        return $this->error('Documento no encontrado', 201);
    }
    public function anularDocumento() {
        $documento = Cabeceradocumentos::find()
                ->where([
                    'idDocPedido' => Yii::$app->request->post('documentId'),
                    'DocType' => Yii::$app->request->post('doctype')
                ])
                ->one();
        if (!is_null($documento)) {
            $documento->estado = 6;
            if ($documento->save(false)) {
                return $this->correcto($documento->toArray());
            }
            return $this->error('Error al actualizar documento', 101);
        }
        return $this->error('Documento no encontrado', 201);
    }

    private function numPedido($numero) {
        if ($numero < 10) {
            $codigo = "000{$numero}";
        } else if ($numero < 100) {
            $codigo = "00{$numero}";
        } else if ($numero < 1000) {
            $codigo = "0{$numero}";
        } else {
            $codigo = "{$numero}";
        }
        return $codigo;
    }

    public function actionFinddetalledocumentobydocentry($docEntry) {
        $oDetalle = Detalledocumentos::find()->where(['DocEntry' => $docEntry])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionFindcabeceradocumentobydocentry($docEntry) {
        $oDetalle = Cabeceradocumentos::find()->where(['DocEntry' => $docEntry])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionFinddetalledocumentobyid($id) {
        $oDetalle = Detalledocumentos::find()->where(['id' => $id])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionFindcabeceradocumentobyid($id) {
        $oDetalle = Cabeceradocumentos::find()->where(['id' => $id])->one();
        if (is_object($oDetalle)) {
            return $this->correcto($oDetalle);
        } else {
            return $this->error();
        }
    }

    public function actionClonedocumentopedido() {

        $IdDocumento = Yii::$app->request->post('doc');

        $oPedido = Cabeceradocumentos::find()->where(['id' => $IdDocumento])->one();

        if (is_object($oPedido)) {

            $cardCode = Yii::$app->request->post('cardcode');
            $direccion = Yii::$app->request->post('direccion');
            $listaPrecios = Yii::$app->request->post('listaPrecios');
            $currency = Yii::$app->request->post('currency');
            $descuentos = Yii::$app->request->post('descuentos');

            $data = Yii::$app->db->createCommand("CALL pa_clonarDoc(:Doc, :cliente, :address, :listaPrecio, :moneda, :descuentos, :usuario)")
                    ->bindValue(':Doc', $IdDocumento)
                    ->bindValue(':cliente', $cardCode)
                    ->bindValue(':address', $direccion)
                    ->bindValue(':listaPrecio', $listaPrecios)
                    ->bindValue(':moneda', $currency)
                    ->bindValue(':descuentos', $descuentos)
                    ->bindValue(':usuario', Yii::$app->user->identity->getId())
                    ->queryAll();

            return $this->correcto($data);
        }
        return $this->error();
    }

    public function actionFacturarpedidolista(){
        $datos = Yii::$app->request->post();
        $cliente = $datos["clienteid"];
        $sql = "SELECT  *, '' AS Marcado FROM vi_pedidosfacturas WHERE CardCode='".$cliente."'";
        $resultado = Yii::$app->db->createCommand($sql)->queryAll();
        $resp2 = [];
        /*foreach($resultado as $elem) {
            $sql2 = "SELECT OcrCode2 FROM pedidosproductos WHERE DocNum = '".$elem["DocNum"]."'";
            $arrayCentros = [];
            $centros = Yii::$app->db->createCommand($sql2)->queryAll();
            foreach($centros as $centro){
                if (!in_array($centro["OcrCode2"], $arrayCentros)) {
                    array_push($arrayCentros, $centro["OcrCode2"]);
                }
            }
            $elem["UnidadNegocio"] = implode( ", ", $arrayCentros );
            array_push($resp2, $elem);
        }*/
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }

    private function unidadEntry($unidad) {
        $entry = Unidadesmedida::find()->where(['Code' => $unidad])->one();
        return $entry->getAttribute('AbsEntry');
    }

    private function usuarioId($usuario) {
        if ($usuario < 10) {
            return "000{$usuario}";
        } else if ($usuario < 100) {
            return "00{$usuario}";
        } else if ($usuario < 1000) {
            return "0{$usuario}";
        } else {
            return "{$usuario}";
        }
    }

    private function tipoItem($itemCode) {
        $item = Combos::find()->where(['TreeCode' => $itemCode])->one();
        if ($item) {
            return 'iSalesTree';
        }
        return 'iNotATree';
    }
    private function registroGiftcard($giftcard,$documento,$usuario){
        $aux=explode('+',$giftcard);
        $item=$aux[0];
        $serie=$aux[1];
        $monto=$aux[2];
        $hoy=Date('Y-m-d');
        $status=1;
        $sql="Insert into gifcards (Code,ItemCode,Amount,User,Status,DateUpdate,Documento) values('{$serie}','{$item}','{$monto}','{$usuario}','{$status}','{$hoy}','{$documento}')";
        // Yii::error(" -- > registro giftcard: ".$sql);
        Yii::$app->db->createCommand($sql)->execute();
        // Yii::$app->db->createCommand($sql)->queryAll();

    }

}
