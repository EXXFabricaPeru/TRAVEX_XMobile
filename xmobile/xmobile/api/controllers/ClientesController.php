<?php

namespace api\controllers;

use backend\models\Clientes;
use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use api\traits\Respuestas;
use backend\models\Cabeceradocumentos;
use backend\models\Usuariosincronizamovil;
use backend\models\Sap;

class ClientesController extends ActiveController {

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
            //'index' => ['GET', 'HEAD'],
            //'view' => ['GET', 'HEAD'],
            'index' => ['POST'],
            'view' => ['POST'],
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

    public function actionIndex() {
    

    }

    public function actionCreate() {
      $usuario=Yii::$app->request->post('usuario');
      //$salto=Yii::$app->request->post('pagina');
      //$resultado = Yii::$app->db->createCommand("select * from vi_clientes order by cardcode limit 1000 OFFSET {$salto}")->queryAll();

        $resultado = Yii::$app->db->createCommand("CALL pa_obtenerClientes(:usuario,:texto,:terminal,:contador,:salto)")
                ->bindValue(':usuario', Yii::$app->request->post('usuario'))
                ->bindValue(':texto', Yii::$app->request->post('texto', 0))
                ->bindValue(':terminal', Yii::$app->request->post('equipo',0))
                ->bindValue(':contador',0)
                ->bindValue(':salto',Yii::$app->request->post('pagina',0))
                ->queryAll();
        if (count($resultado) > 0) {
           
            return $this->correcto($resultado, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);
    }
    public function actionContador(){
      $usuario=Yii::$app->request->post('usuario');  

      //$resultado=Yii::$app->db->createCommand("Select count(*) as contador from clientes")->queryOne();
      Yii::error("INGRESO CONTADOR CLIENTES: ");
      Yii::error(Yii::$app->request->post());
      $resultado = Yii::$app->db->createCommand("CALL pa_obtenerClientes(:usuario,:texto,:terminal,:contador,:salto)")
      ->bindValue(':usuario', Yii::$app->request->post('usuario'))
      ->bindValue(':texto', 0)
      ->bindValue(':terminal',Yii::$app->request->post('equipo',0))
      ->bindValue(':contador',1)
      ->bindValue(':salto',0)
      ->queryOne();
      
      $usuariosincronizamovil= new Usuariosincronizamovil();
      $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Clientes');
      return $this->correcto($resultado, 'OK'); 
    }
    public function actionLastinserted() {
      $nit = Yii::$app->request->post('nit');
      $ultimo = Clientes::find()
        ->where(['FederalTaxId' => $nit])
        ->all();
      if (count($ultimo) > 0){
        return $this->correcto($ultimo, 'OK');
      }
      return $this->correcto([], "No se encontro Datos", 201);
    }

    public function actionFindonebycardcode($cardCode) {
        $oCliente = Clientes::find()->where(['like', 'CardCode', $cardCode])->one();
        if (is_object($oCliente)) {
            return $this->correcto($oCliente);
        } else {
            return $this->error();
        }
    }

    public function actionUpdateclient(){
		$cardCode = Yii::$app->request->post('cardcode');
		$cliente = Clientes::find()
                  ->where(['CardCode' => Yii::$app->request->post('cardcode')])
                  ->one();
      Yii::$app->db->createCommand("SELECT * FROM clientes WHERE CardCode = :cardcode", [':cardcode' => Yii::$app->request->post('cardcode')])
              ->queryOne();
      if (!is_null($cliente)){
        $cliente->Address = Yii::$app->request->post('address');
        $cliente->PhoneNumber = Yii::$app->request->post('phonenumber');
		    $cliente->FederalTaxId = Yii::$app->request->post('federaltax');
        if ($cliente->save(false)){
          $serviceLayer = new Servislayer();
          $serviceLayer->actiondir = "BusinessPartners('{$cliente->CardCode}')";
          $datos = [
            "Address" => Yii::$app->request->post('address'),
            "Phone1" => Yii::$app->request->post('phonenumber'),
			      "FederalTaxID" => Yii::$app->request->post('federaltax')
          ];
          $serviceLayer->executePatchPut('PATCH',$datos);
          if ($serviceLayer){
            return $this->correcto();
          }
          return $this->error('No se actualizo en SAP');
        }else {
			return $this->error('Error al actualizar cliente');
		}
      }
      return $this->error('No se encontro al Cliente',201);
    }

    function escapeJsonString($value) {
      $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
      $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
      $result = str_replace($escapers, $replacements, $value);
      return $result;
   }
   public function actionConsultasaldoclientesap(){
      $sap= new Sap();
      Yii::error("CONSULTA el saldo SAP: ");
      $codigo=Yii::$app->request->post('codigo');
      $resultado = $sap->Consulta_saldo($codigo);
      Yii::error($resultado);
      
      return $this->correcto($resultado, 'OK');
   }
  public function actionConsultarutclientesap(){
    $sap= new Sap();
    Yii::error("CONSULTA CLIENTE SAP: ");
    $rut=Yii::$app->request->post('rut');
    //$codigo=Yii::$app->request->post('codigo');
    $codigo=0;
    $resultado = $sap->Consulta_rut($rut,$codigo);
    Yii::error($resultado);
    
    return $this->correcto($resultado, 'OK');
  }
  /// consulta directa a movil a sap
  public function actionObtenerclientes() {
    $usuario=Yii::$app->request->post('usuario');
    //$texto= Yii::$app->request->post('texto');
    //$termina=Yii::$app->request->post('equipo');
    $salto=Yii::$app->request->post('pagina');
    //////////////////////////////////////////////////////////////////////////////
    Yii::error("SINCRONIZA ODBC clientes : ");
        
    try {
        $texto = '';
        $insert = '';
        $std='';
        
        $sql = "SELECT * FROM configuracion WHERE parametro LIKE 'cliente_std%' AND estado=1 ORDER BY parametro";
        $parametrosClientes = Yii::$app->db->createCommand($sql)->queryAll();

        $cantidadCliente = count($parametrosClientes);

        if (count($cantidadCliente)){
            for ($c = 0; $c < $cantidadCliente; $c++){
                    $insert .= ','.$parametrosClientes[$c]["parametro"];
                    $std .= ',"'.$parametrosClientes[$c]["valor2"].'"';
            }
        }

        ///////////////////////////////////////////////////////////
        $sql = "SELECT configuracion.valor from configuracion where configuracion.parametro='vendedor'";
        $configuracion = Yii::$app->db->createCommand($sql)->queryOne();

        $sql = "SELECT usuarioconfiguracion.codEmpleadoVenta FROM `user` LEFT JOIN usuarioconfiguracion ON usuarioconfiguracion.idUser = `user`.id WHERE `user`.id=".$usuario;
        $vendedor = Yii::$app->db->createCommand($sql)->queryOne();


        $serviceLayer = new Sincronizar();
        
        Yii::error("SINCRONIZA ODBC CLIENTE MOVIL SAP: ");
        Yii::error("Vendedor: ".$vendedor['codEmpleadoVenta']);
        //$campos='CardCode,CardName,CardType,Address,CreditLimit,MaxCommitment,DiscountPercent,PriceListNum,SalesPersonCode,Currency,County,Country,CurrentAccountBalance,NoDiscounts,PriceMode,FederalTaxId,PhoneNumber,ContactPerson,PayTermsGrpCode,Latitude,Longitude,GroupCode,User,Status,DateUpdate,GroupName,U_XM_DosificacionSocio,Territory,DiscountRelations,Mobilecod,StatusSend,CardForeignName,Phone2,Cellular,EmailAddress,MailAdress,Properties1,Properties2,Properties3,Properties4,Properties5,Properties6,Properties7,FreeText,img,Industry,codecanal,codesubcanal,codetipotienda,cadena'.$insert;
        if($configuracion['valor']=='0'){
           //el vendedor esta ligado a el maestro de socios de negocio por el campo salespersoncode
          $data = json_encode(array("accion" => 1120, "vendedor"=>$vendedor['codEmpleadoVenta'],"salto"=>$salto));
          $respuesta = $serviceLayer->executex($data);
        }
        elseif($configuracion['valor']=='1'){
          //el vendedor se relaciona por territorio en el maestro de clientes
          $data = json_encode(array("accion" => 1121, "vendedor"=>$vendedor['codEmpleadoVenta'],"salto"=>$salto));
          $respuesta = $serviceLayer->executex($data);
        } 
                       
        $respuesta = json_decode($respuesta);

        if (count($respuesta) > 0) {
          return $this->correcto($respuesta, 'OK');
        }
        return $this->correcto([], "No se encontro Datos", 201);                               

      } catch (\Exception $e) {
        Yii::error("Error en sincronizacion de clientes: ",$e);
     
      }         
  }

}
