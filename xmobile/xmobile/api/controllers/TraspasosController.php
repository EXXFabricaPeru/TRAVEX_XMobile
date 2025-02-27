<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\TraspasosCabecera;
use backend\models\TraspasosDetalle;
use backend\models\TraspasosLote;
use backend\models\TraspasosSerie;

class TraspasosController extends ActiveController
{
  use Respuestas;

  public $modelClass = 'backend\models\Usuario';

  /*public function init() {
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
  }*/

  protected function verbs()
  {
    return [
      'index'  => ['GET', 'HEAD'],
      'view'   => ['GET', 'HEAD'],
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

  public function actionIndex(){
    $tipo = Yii::$app->request->post('tipo');
    $usuario = Yii::$app->request->post('usuario');
    $sql = "SELECT t.*, o.WarehouseName AS origen, d.WarehouseName AS destino FROM traspasocabecera t, almacenes o, almacenes d WHERE t.usuariosolicitud = :usuario AND t.origenWarehouse = o.WarehouseCode COLLATE utf8_unicode_ci AND t.destinoWarehouse = d.WarehouseCode COLLATE utf8_unicode_ci";
    if ($tipo == "1") $sql = $sql." AND (t.estado = 1 OR t.estado = 6)";
    //else $sql = $sql." AND (t.estado = 2 OR t.estado = 3 OR t.estado = 4 OR t.estado = 6)";
    else $sql = $sql." AND (t.estado = 3 OR t.estado = 4 OR t.estado = 6)";
    $resultado = Yii::$app->db->createCommand($sql)
                  ->bindValue(':usuario' , $usuario)
                  ->queryAll();
        if (count($resultado) > 0) {
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
  }

  public function actionCreate(){}


  public function actionCreartraspaso(){
    $datos = Yii::$app->request->post();		
    if (!count($datos)){
      return $this->error('Sin datos',201);
    }
    $fechaSend = date("Y-m-d");
    $datosRespuesta = [];
    try {
      //foreach ($datos["cabecera"] as $traspaso) {
        $traspaso = $datos["cabecera"];
        $t = new TraspasosCabecera();
        $t->id = null;
        $t->cardCode = $traspaso["cardCode"];
        $t->origenWarehouse = $traspaso["origenWarehouse"];
        $t->destinoWarehouse = $traspaso["destinoWarehouse"];
        $t->estado = $traspaso["estado"];
        $t->estadodescripcion = $traspaso["estadodescripcion"];
        $t->usuariosolicitud = $traspaso["usuariosolicitud"];
        $t->usuarioaprobacion = $traspaso["usuarioaprobacion"];
        $t->usuariorecepcion = $traspaso["usuariorecepcion"];
        $t->fechasolicitud = $traspaso["fechasolicitud"];
        $t->fechaaprobacion = $traspaso["fechaaprobacion"];
        $t->fecharecepcion = $traspaso["fecharecepcion"];
        $t->comentariosolicitud = $traspaso["comentariosolicitud"];
        $t->comentarioaprobacion = $traspaso["comentarioaprobacion"];
        $t->comentariorecepcion = $traspaso["comentariorecepcion"];
        $t->nombresolicitud = $traspaso["nombresolicitud"];
        $t->nombreaprobacion = $traspaso["nombreaprobacion"];
        $t->nombrerecepcion = $traspaso["nombrerecepcion"];
        $t->usuario = $traspaso["usuario"];
        $t->status = $traspaso["status"];
        $t->dateUpdate = $traspaso["dateUpdate"];
        if($t->save(false)){
          $flags = [];
          foreach ($traspaso["detalles"] as $detalle) {
            $d = new TraspasosDetalle();
            $d->id = null;
            $d->idcabecera = $t["id"];
            $d->itemCode = $detalle["itemCode"];
            $d->origenwarehouse = $detalle["origenwarehouse"];
            $d->destinowarehouse = $detalle["destinowarehouse"];
            $d->unidadmedida = $detalle["unidadmedida"];
            $d->cantidadsolicitada = $detalle["cantidadsolicitada"];
            $d->cantidadaprobada = $detalle["cantidadaprobada"];
            $d->cantidadrecepcionada = $detalle["cantidadrecepcionada"];
            $d->tipoRegistro = $detalle["tipoRegistro"];
            $d->serie = '';//$detalle["serie"];
            $d->usuario = $traspaso["usuario"];
            $d->status = $traspaso["status"];
            $d->dateUpdate = $traspaso["dateUpdate"];
            $d->save(false);
            /*if ($d->save(false)){
              if ($detalle["tipoRegistro"] == "L"){
                foreach($detalle["Lotes"] as $lote){
                  $l = new TraspasoLote();
                  $l->id = null;
                  $l->idDetalle = $d["id"];
                  $l->lote = $lote["lote"];
                  $l->usuario = $traspaso["usuario"];
                  $l->status = $traspaso["status"];
                  $l->dateUpdate = $traspaso["dateUpdate"];
                  $l->save(false);
                }
              }
              else if ($detalle["tipoRegistro"] == "S"){
                foreach($detalle["Series"] as $serie){
                  $s = new TraspasosSerie();
                  $s->id = null;
                  $s->idDetalle = $d["id"];
                  $s->serie = $serie["serie"];
                  $s->usuario = $traspaso["usuario"];
                  $s->status = $traspaso["status"];
                  $s->dateUpdate = $traspaso["dateUpdate"];
                  $s->save(false);
                }
              }
            }*/
          }
        //}
      }
      return $this->correcto($datosRespuesta, "Traspasos Registrados");
    }catch (Exception $e){
        Yii::error('ERROR-TRASPASO:'.$e->getMessage());
        return $this->error($e->getMessage(),100);
      }
  }

  public function actionAprobartraspaso(){
    $datos = Yii::$app->request->post();		
    if (!count($datos)){
      return $this->error('Sin datos',201);
    }
    $fechaSend = date("Y-m-d");
    $datosRespuesta = [];
    try {
        $traspaso = $datos["cabecera"];
          $sql = "UPDATE traspasocabecera SET". 
                " comentarioaprobacion = :comentarioaprobacion,".
                " fechaaprobacion = :fechaaprobacion,".
                " usuarioaprobacion = :usuarioaprobacion,".
                " nombreaprobacion = :nombreaprobacion,".
                " estado = :estado,".
                " estadodescripcion = :estadodescripcion".
                " WHERE id = :id";
          Yii::$app->db->createCommand($sql)
          ->bindValue(':comentarioaprobacion', $traspaso["comentarioaprobacion"])
          ->bindValue(':fechaaprobacion', $traspaso["fechaaprobacion"])
          ->bindValue(':usuarioaprobacion', $traspaso["usuarioaprobacion"])
          ->bindValue(':nombreaprobacion', $traspaso["nombreaprobacion"])
          ->bindValue(':estado', $traspaso["estado"])
          ->bindValue(':estadodescripcion', $traspaso["estadodescripcion"])
          ->bindValue(':id', $traspaso["id"])
          ->execute();

		  $serie = 0;
          foreach($traspaso["detalles"] as $detalle){
            $sql = "UPDATE traspasodetalle SET cantidadaprobada = :cantidadaprobada, cantidadrecepcionada = :cantidadaprobada, serie = :serie WHERE id = :id";
            Yii::$app->db->createCommand($sql)
            ->bindValue(':cantidadaprobada', $detalle["cantidadaprobada"])
			->bindValue(':serie', $serie)
            ->bindValue(':id', $detalle["id"])
            ->execute();
			$serie = $serie + 1;
          }

          foreach($traspaso["lotes"] as $lote){
            $l = new TraspasosLote();
            $l->id = null;
            $l->idDetalle = $lote["idDetalle"];
            $l->lote = $lote["lote"];            
            $l->usuario = $lote["usuario"];
            $l->status = $lote["status"];
            $l->dateUpdate = $lote["dateUpdate"];
            $l->save(false);
          }
        return $this->correcto($datosRespuesta, "Traspasos actualizados");
    }catch (Exception $e){
      Yii::error('ERROR-TRASPASO:'.$e->getMessage());
      return $this->error($e->getMessage(),100);
    }
  }

  public function actionRechazartraspaso(){
    $datos = Yii::$app->request->post();		
    if (!count($datos)){
      return $this->error('Sin datos',201);
    }
    $fechaSend = date("Y-m-d");
    $datosRespuesta = [];
    try {
        $traspaso = $datos["cabecera"];
          $sql = "UPDATE traspasocabecera SET  estado = 7, estadodescripcion = 'Rechazado' WHERE id = :id";
          Yii::$app->db->createCommand($sql)->bindValue(':id', $traspaso["id"])->execute();
          return $this->correcto($datosRespuesta, "Traspaso rechazado");
    }catch (Exception $e){
      Yii::error('ERROR-TRASPASO:'.$e->getMessage());
      return $this->error($e->getMessage(),100);
    }
  }

  public function actionFinalizartraspaso(){
    $datos = Yii::$app->request->post();		
    if (!count($datos)){
      return $this->error('Sin datos',201);
    }
    $FinalizadoParcial = false;
    $fechaSend = date("Y-m-d");
    $datosRespuesta = [];
    try {
        $traspaso = $datos["cabecera"];
        if ($traspaso["estado"] == '2') $FinalizadoParcial = true;
          $sql = "UPDATE traspasocabecera SET". 
                " comentariorecepcion = :comentariorecepcion,".
                " fecharecepcion = :fecharecepcion,". 
                " usuariorecepcion = :usuariorecepcion,".
                " nombrerecepcion = :nombrerecepcion,".
                " estado = :estado,".
                " estadodescripcion = :estadodescripcion".
                " WHERE id = :id";
          Yii::$app->db->createCommand($sql)
          ->bindValue(':comentariorecepcion', $traspaso["comentariorecepcion"])
          ->bindValue(':fecharecepcion', $traspaso["fechaarecepcion"])
          ->bindValue(':usuariorecepcion', $traspaso["usuariorecepcion"])
          ->bindValue(':nombrerecepcion', $traspaso["nombrerecepcion"])
          ->bindValue(':estado', $traspaso["estado"])          
          ->bindValue(':estadodescripcion', $traspaso["estadodescripcion"])
          ->bindValue(':id', $traspaso["id"])
          ->execute();

          foreach($traspaso["detalles"] as $detalle){
            $sql = "UPDATE traspasodetalle SET cantidadrecepcionada = :cantidadrecepcionada WHERE id = :id";
            Yii::$app->db->createCommand($sql)
            ->bindValue(':cantidadrecepcionada', $detalle["cantidadrecepcionada"])
            ->bindValue(':id', $detalle["id"])
            ->execute();
            if ($detalle["tipoRegistro"] == "S"){
              foreach($detalle["Series"] as $s){
                  $serie = new TraspasosSerie();
                  $serie->id 			      = null;
                  $serie->idDetalle     = $detalle["id"];
                  $serie->DocEntry      = $s["DocEntry"];
                  $serie->ItemCode      = $s["ItemCode"];
                  $serie->SerialNumber  = $s["SerialNumber"];
                  $serie->SystemNumber  = $s["SystemNumber"];
                  $serie->AdmissionDate = $s["AdmissionDate"];
                  $serie->User          = $s["User"];
                  $serie->Status        = $s["Status"];
                  $serie->Date          = $s["Date"];
                  $serie->WsCode        = $s["WsCode"];
                  $serie->save();
              }

            }
            if ($detalle["tipoRegistro"] == "L"){
              foreach($detalle["Lotes"] as $l){
                  $lote = new TraspasosLote();
                  $lote->id 			      = null;
                  $lote->idDetalle     = $detalle["id"];
                  $lote->ItemCode = 	$l["ItemCode"];
									$lote->BatchNum =   $l["BatchNum"];
									$lote->WhsCode =    $l["WhsCode"];
									$lote->ItemName =   $l["ItemName"];
									$lote->SuppSerial = $l["SuppSerial"];
									$lote->IntrSerial = $l["IntrSerial"];
									$lote->ExpDate =    $l["ExpDate"];
									$lote->PrdDate =    $l["PrdDate"];
                  $lote->InDate =     $l["InDate"];
                  $lote->Located = 	$l["Located"];
									$lote->Notes =      $l["Notes"];
									$lote->Quantity =   $l["CantidadLote"];
									$lote->BaseType =   $l["BaseType"];
									$lote->BaseEntry =  $l["BaseEntry"];
									$lote->BaseNum =    $l["BaseNum"];
									$lote->BaseLinNum = $l["BaseLinNum"];
									$lote->CardCode =   $l["CardCode"];
									$lote->CardName =   $l["CardName"];
									$lote->CreateDate = $l["CreateDate"];
									$lote->Status =     $l["Status"];
									$lote->Direction =  $l["Direction"];
									$lote->IsCommited = $l["IsCommited"];
									$lote->OnOrder =    $l["OnOrder"];
									$lote->Consig =     $l["Consig"];
									$lote->DataSource = $l["DataSource"];
									$lote->UserSign =   $l["UserSign"];
									$lote->Transfered = $l["Transfered"];
									$lote->Instance =   $l["Instance"];
									$lote->SysNumber =  $l["SysNumber"];
									$lote->LogInstanc = $l["LogInstanc"];
									$lote->UserSign2 =  $l["UserSign2"];
									$lote->UpdateDate = $l["UpdateDate"];
                  $lote->save();
              }              
            }
          }
          if ($FinalizadoParcial == true){
            $sql = "SELECT * FROM traspasocabecera WHERE id = :id";
            $acopiar = Yii::$app->db->createCommand($sql)->bindValue(':id' , $traspaso["id"])->queryOne();
            $t = new TraspasosCabecera();
            $t->id = null;
            $t->cardCode = $acopiar["cardCode"];
            $t->origenWarehouse = $acopiar["origenWarehouse"];
            $t->destinoWarehouse = $acopiar["destinoWarehouse"];
            $t->estado = '4';
            $t->estadodescripcion = 'Aprobado incompleto';
            $t->usuariosolicitud = $acopiar["usuariosolicitud"];
            $t->usuarioaprobacion = $acopiar["usuarioaprobacion"];
            $t->usuariorecepcion = '';
            $t->fechasolicitud = $acopiar["fechasolicitud"];
            $t->fechaaprobacion = $acopiar["fechaaprobacion"];
            $t->fecharecepcion = '';
            $t->comentariosolicitud = $acopiar["comentariosolicitud"];
            $t->comentarioaprobacion = $acopiar["comentarioaprobacion"];
            $t->comentariorecepcion = $acopiar["comentariorecepcion"];
            $t->nombresolicitud = $acopiar["nombresolicitud"];
            $t->nombreaprobacion = $acopiar["nombreaprobacion"];
            $t->nombrerecepcion = '';
            $t->DocEntrySolicitud = $acopiar["DocEntrySolicitud"];
            $t->MensajeSolicitud = $acopiar["MensajeSolicitud"];
            $t->usuario = $acopiar["usuario"];
            $t->status = $acopiar["status"];
            $t->dateUpdate = $acopiar["dateUpdate"];
            if($t->save(false)){
              $sql = "SELECT * FROM traspasodetalle WHERE idcabecera = :id";
              $detacopiar = Yii::$app->db->createCommand($sql)->bindValue(':id' , $traspaso["id"])->queryAll();
              $aprobada = 0;
              foreach($detacopiar as $detalle){
                $aprobada = ((int) $detalle["cantidadaprobada"]) - ((int) $detalle["cantidadrecepcionada"]);
                $d = new TraspasosDetalle();
                $d->id = null;
                $d->idcabecera = $t["id"];
                $d->itemCode = $detalle["itemCode"];
                $d->origenwarehouse = $detalle["origenwarehouse"];
                $d->destinowarehouse = $detalle["destinowarehouse"];
                $d->unidadmedida = $detalle["unidadmedida"];
                $d->cantidadsolicitada = $detalle["cantidadsolicitada"];
                $d->cantidadaprobada = $aprobada;
                $d->cantidadrecepcionada = $aprobada;
                $d->tipoRegistro = $detalle["tipoRegistro"];
                $d->serie = $detalle["serie"];
                $d->usuario = $detalle["usuario"];
                $d->status = $detalle["status"];
                $d->dateUpdate = $detalle["dateUpdate"];
				if ($aprobada > 0) $d->save(false);
              }
            }
          }
        return $this->correcto($datosRespuesta, "Traspasos recepcionados");
    }catch (Exception $e){
      Yii::error('ERROR-TRASPASO:'.$e->getMessage());
      return $this->error($e->getMessage(),100);
    }
  }

  public function actionFindtraspasobyid(){
    $id = Yii::$app->request->post('id');
    $sql = "SELECT * FROM traspasocabecera WHERE id = :id";
    $cabecera = Yii::$app->db->createCommand($sql)
                  ->bindValue(':id' , $id)
                  ->queryOne();
        if (count($cabecera) > 0) {          
          $sql = "SELECT * FROM traspasodetalle WHERE idCabecera = :idcabecera";
          $detalle = Yii::$app->db->createCommand($sql)
                  ->bindValue(':idcabecera' , $id)
                  ->queryAll();

          $lotesfinal = [];
          if (count($detalle) > 0){
            foreach($detalle as $d){
              if ($d["tipoRegistro"] == "L"){
                  if ($cabecera["estado"] != 1){ 
                    $sql = "SELECT * FROM traspasolote WHERE idDetalle = :iddetalle";
                    $lotes = Yii::$app->db->createCommand($sql)
                            ->bindValue(':iddetalle' , $d["id"])
                            ->queryAll();
                    if (count($lotes) > 0){
                      foreach($lotes as $lote){
                        $l = [
                          "idDetalle" => $d["id"],
                          "itemCode" => $d["itemCode"],
                          "Lote" => $lote["lote"],
                          "Seleccionado" => "SI"
                        ];
                        array_push($lotesfinal, $l);
                      }
                  }
                  else{
                    $l = [
                      "idDetalle" => '',
                      "itemCode" => '',
                      "Lote" => '',
                      "Seleccionado" => ''
                    ];
                    array_push($lotesfinal, $l);
                  }
                }
                else{
                  $sql = "SELECT * FROM lotesproductos WHERE ItemCode = :itemcode";
                  $lotes = Yii::$app->db->createCommand($sql)
                            ->bindValue(':itemcode' , $d["itemCode"])
                            ->queryAll();
                  if (count($lotes) > 0){
                    foreach($lotes as $lote){
                      $l = [
                        "idDetalle" => $d["id"],
                        "itemCode" => $d["itemCode"],
                        "Lote" => $lote["BatchNum"],
                        "Seleccionado" => "SI"
                      ];
                      array_push($lotesfinal, $l);
                    }
                  }
                  else{
                    $l = [
                      "idDetalle" => '',
                      "itemCode" => '',
                      "Lote" => '',
                      "Seleccionado" => ''
                    ];
                    array_push($lotesfinal, $l);
                  }
                }
            }
            else{
              $l = [
                "idDetalle" => '',
                "itemCode" => '',
                "Lote" => '',
                "Seleccionado" => ''
              ];
              array_push($lotesfinal, $l);
            }
          }    
          $comentario = $cabecera["comentariosolicitud"];
          if ($cabecera["estado"]>1 && $cabecera["estado"]<5) $comentario = $cabecera['comentarioaprobacion'];

          $resultado = [
            "id" => $cabecera["id"],
            "comentario" => $comentario,
            "origenWarehouse" => $cabecera["origenWarehouse"],
            "destinoWarehouse" => $cabecera["destinoWarehouse"],
            "estado" => $cabecera["estado"],
            "estadodescripcion" => $cabecera["estadodescripcion"],
            "usuariosolicitud" => $cabecera["usuariosolicitud"],
            "usuarioaprobacion" => $cabecera["usuarioaprobacion"],
            "usuariorecepcion" => $cabecera["usuariorecepcion"],
            "fechasolicitud" => $cabecera["fechasolicitud"],
            "fechaaprobacion" => $cabecera["fechaaprobacion"],
            "fecharecepcion" => $cabecera["fecharecepcion"],
            "comentariosolicitud" => $cabecera["comentariosolicitud"],
            "comentarioaprobacion" => $cabecera["comentarioaprobacion"],
            "comentariorecepcion" => $cabecera["comentariorecepcion"],
            "nombresolicitud" => $cabecera["nombresolicitud"],
            "nombreaprobacion" => $cabecera["nombreaprobacion"],
            "nombrerecepcion" => $cabecera["nombrerecepcion"],
            "usuario" => $cabecera["usuario"],
            "status" => $cabecera["status"],
            "dateUpdate" => $cabecera["dateUpdate"],
            "detalle" => $detalle,
            "lotes" => $lotesfinal
          ];
          return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
      }
  }
   public function actionReportetraspasos(){
    $tipo = Yii::$app->request->post('estado');
    $usuario = Yii::$app->request->post('usuario');
    $origen = Yii::$app->request->post('origen');
    $destino = Yii::$app->request->post('destino');
    $inicio = Yii::$app->request->post('fechainicio');
    $fin = Yii::$app->request->post('fechafin');
    $sucursal = Yii::$app->request->post('sucursal');
    $sqlUsuario = '';
    if ($usuario != '0') $sqlUsuario = ' AND t.usuariosolicitud='.$usuario;
    $sqlOrigen = '';
    if ($origen != '') $sqlOrigen = " AND t.origenWarehouse = '".$origen."'";
    $sqlDestino = '';
    if ($destino != '') $sqlDestino = " AND t.destinoWarehouse = '".$destino."'";
    
    $sql = "SELECT t.*, o.WarehouseName AS origen, d.WarehouseName AS destino".
          " FROM traspasocabecera t, almacenes o, almacenes d ".
          " WHERE t.estado = 1".
          " AND t.origenWarehouse = (o.WarehouseCode COLLATE utf8_unicode_ci)".
          " AND t.destinoWarehouse = (d.WarehouseCode COLLATE utf8_unicode_ci)".
          $sqlUsuario.$sqlOrigen.$sqlDestino.
          " AND t.fechasolicitud >= '".$inicio."' AND t.fechasolicitud <= '".$fin."'".
          " ORDER BY t.fechasolicitud, t.origenWarehouse";
          ;    
    $cabeceras = Yii::$app->db->createCommand($sql)->queryAll();
    $resultado = [];
        if (count($cabeceras) > 0) {
          foreach($cabeceras as $cabecera){
            $c = [
              'id' => $cabecera["id"],
              'origenWarehouse' => $cabecera["origenWarehouse"],
              'destinoWarehouse' => $cabecera["destinoWarehouse"],
              'estado' => $cabecera["estado"],
              'estadodescripcion' => $cabecera["estadodescripcion"],
              'usuariosolicitud' => $cabecera["usuariosolicitud"],
              'usuarioaprobacion' => $cabecera["usuarioaprobacion"],
              'usuariorecepcion' => $cabecera["usuariorecepcion"],
              'fechasolicitud' => $cabecera["fechasolicitud"],
              'fechaaprobacion' => $cabecera["fechaaprobacion"],
              'fecharecepcion' => $cabecera["fecharecepcion"],
              'comentariosolicitud' => $cabecera["comentariosolicitud"],
              'comentarioaprobacion' => $cabecera["comentarioaprobacion"],
              'comentariorecepcion' => $cabecera["comentariorecepcion"],
              'nombresolicitud' => $cabecera["nombresolicitud"],
              'nombreaprobacion' => $cabecera["nombreaprobacion"],
              'nombrerecepcion' => $cabecera["nombrerecepcion"],
              'usuario' => $cabecera["usuario"],
              'status' => $cabecera["status"],
              'dateUpdate' => $cabecera["dateUpdate"],
              'DocEntrySolicitud' => $cabecera["DocEntrySolicitud"],
              'DocEntryTraspaso' => $cabecera["DocEntryTraspaso"],
              'MensajeSolicitud' => $cabecera["MensajeSolicitud"],
              'MensajeTraspaso' => $cabecera["MensajeTraspaso"],
              'origen' => $cabecera["origen"],
              'destino' => $cabecera["destino"],
              'operadores' => 0,
              'lineas' => 0,
              'detalle' => []
            ];
            $sqlDetalle = 'SELECT t.*, p.ItemName FROM traspasodetalle t, productos p WHERE idcabecera = '.$c["id"].' AND t.itemCode=(p.ItemCode COLLATE utf8_unicode_ci)';
            $detalles = Yii::$app->db->createCommand($sqlDetalle)->queryAll();
            $contador = 0;
            if (count($detalles) > 0) {
              foreach($detalles as $detalle){
                array_push($c["detalle"],$detalle);
                $contador = $contador + 1;
              }
              $c["operadores"] = $contador;
              $c["lineas"] = $contador;
              array_push($resultado, $c);
            }
        }
        if (count($resultado > 0)){
          $sqlusuario = 'SELECT * FROM user Where id='.$usuario;
          $rUsuario = Yii::$app->db->createCommand($sqlusuario)->queryOne();
          $sqlSucurssal = 'SELECT * FROM sucursalx Where id='.$sucursal;
          $rSucursal = Yii::$app->db->createCommand($sqlSucurssal)->queryOne();
          $reporte = [
            'usuario' => $rUsuario["username"],
            'sucursal' => $rSucursal["nombre"],
            'inicial' => $inicio,
            'final' => $fin,
            'traspasos' => $resultado
          ];
          
          return $this->correcto($reporte, 'OK');
        }
        else return $this->correcto([], "No se encontro Datos", 201);
      }
      else return $this->correcto([], "No se encontro Datos", 201);
    }
}
