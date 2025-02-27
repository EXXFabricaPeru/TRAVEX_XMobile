<?php

namespace api\controllers\v2;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use backend\models\Usuariosincronizamovil;
use api\traits\Respuestas;
use yii\data\ActiveDataProvider;
use backend\models\Sap;
use backend\models\v2\Documentos;
use backend\models\v2\Convertirutf8data;

class DocumentosmovilsapController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\User';

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
	
	public function actionIndex(){
        /*  
        0 = oferta
        1 = pedito
        2 = factura
        3 = entrega
        4 = factura deuda
        */
        Yii::error("Ingresa Documentos88");
        set_time_limit(0);
        $datos=Yii::$app->request->post();
        Yii::error("usuario: ".$datos['usuario']." Equipo: ".$datos['equipo']." pagina: ".$datos['pagina']." limite: ".$datos['limite']);

        $usuario=$datos['usuario'];
        $equipo=$datos['equipo'];
        $salto=$datos['pagina'];
        $limite=$datos['limite'];

        // verificar configuraiones del midd
        $sql = "SELECT valor FROM configuracion WHERE parametro='listapicking'";
        $configuracion = Yii::$app->db->createCommand($sql)->QueryOne();

        if($configuracion['valor']==1){ //caso companex
            $documentos=new Documentos;
            $sql1 = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
            $configU = Yii::$app->db->createCommand($sql1)->QueryOne();
            $vendedor=$configU['codEmpleadoVenta'];
            
            //*$documentos=new Documentos;
            //$pedido=$documentos->obtenerListaPicking($vendedor);
            
            //SINCRONIZANDO LISTA DE PICKING A MIID//
            $sap = new Sap();
            $sap->ObtenrPedidosCabecera($vendedor);
            $pedido =Yii::$app->db->createCommand("CALL pa_documentosimportadosrutas('".$usuario."','0','".$salto."','0')")->queryAll();
            
            $facturadeuda=$documentos->obtenerDeudaCLienteTodos($usuario);
            $resultado = (object) array_merge(
             (array) $pedido,(array) $facturadeuda);
        }
        else{
            //obtener la configuracion del usuario para realizar uno u otro proceso 
            $documentos=new Documentos;
            $oferta=$documentos->obtenerDocumentos(0,$equipo,$usuario,$salto);
            $pedido=$documentos->obtenerDocumentos(1,$equipo,$usuario,$salto);
            $factura=$documentos->obtenerDocumentos(2,$equipo,$usuario,$salto);
            $entrega=$documentos->obtenerDocumentos(3,$equipo,$usuario,$salto);
            $facturadeuda=$documentos->obtenerDocumentos(4,$equipo,$usuario,$salto);
            $resultado = (object) array_merge(
            (array) $oferta, (array) $pedido, (array) $factura, (array) $entrega, (array) $facturadeuda);
        }

        $resultado = Convertirutf8data::convert_to_utf8_recursively($resultado);
        if (count($resultado) > 0) {
            return $this->correcto($resultado);
        }
        return $this->error('Sin datos',201);
	}

    
    public function actionContador(){
        set_time_limit(0);

        $datos=Yii::$app->request->post();
        Yii::error("usuario: ".$datos['usuario']." Equipo: ".$datos['equipo']." pagina: ".$datos['pagina']." limite: ".$datos['limite']);

        $usuario=$datos['usuario'];
        $equipo=$datos['equipo'];
        $salto=$datos['pagina'];
        $limite=$datos['limite'];

        // verificar configuraiones del midd
        $sql = "SELECT valor FROM configuracion WHERE parametro='listapicking'";
        $configuracion = Yii::$app->db->createCommand($sql)->QueryOne();

        if($configuracion['valor']==1){ //caso companex
            $documentos=new Documentos;
            $sql1 = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
            $configU = Yii::$app->db->createCommand($sql1)->QueryOne();

            $vendedor=$configU['codEmpleadoVenta'];

            //$documentos=new Documentos;
            //$pedido=$documentos->obtenerListaPickingContador($vendedor);

            $sap = new Sap();
            $sap->ObtenrPedidosCabecera($vendedor);

            $pedido = Yii::$app->db->createCommand("CALL pa_documentosimportadosrutas('".$usuario."','1','0','0')")->queryAll();
            
            $facturadeuda=$documentos->obtenerDeudaCLienteTodosContador();
            $resultado = (object) array_merge(
             (array) $pedido,(array) $facturadeuda);

        }else{
            //obtener la configuracion del usuario para realizar uno u otro proceso 
            $documentos=new Documentos;
            $oferta=$documentos->obtenerDocumentosContador(0,$equipo,$usuario);
            $pedido=$documentos->obtenerDocumentosContador(1,$equipo,$usuario);
            $factura=$documentos->obtenerDocumentosContador(2,$equipo,$usuario);
            $entrega=$documentos->obtenerDocumentosContador(3,$equipo,$usuario);
            $facturadeuda=$documentos->obtenerDocumentosContador(4,$equipo,$usuario);


            $resultado = (object) array_merge(
            (array) $oferta, (array) $pedido, (array) $factura, (array) $entrega, (array) $facturadeuda);
            
        }

        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Documentosmovilsap');

        $cantidad=0;
        if (count($resultado) > 0) {
            
            foreach ($resultado as $value) {
                $cantidad+=$value['CONTADOR'];
            }
            
        }
        return $this->correcto(["contador"=>$cantidad]);
        //return $this->correcto($resultado);
        //return $this->error('Sin datos',201);
        
    }

    public function actionDoccliente(){
        Yii::error("Entra actionDoccliente");
        $codigo=Yii::$app->request->post('codigo');
        $documentos=new Documentos;
        $resultado=$documentos->obtenerDocumentosCliente($codigo);

        return $this->correcto($resultado, 'OK');
    }
    
}
