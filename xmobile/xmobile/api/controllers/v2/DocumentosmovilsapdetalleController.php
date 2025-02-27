<?php

namespace api\controllers\v2;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use api\traits\Respuestas;
use yii\data\ActiveDataProvider;
use backend\models\Usuariosincronizamovil;
use backend\models\Sap;
use backend\models\v2\Documentos;

class DocumentosmovilsapdetalleController extends ActiveController {

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
            $sql1 = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
            $configU = Yii::$app->db->createCommand($sql1)->QueryOne();

            $vendedor=$configU['codEmpleadoVenta'];
            
            $documentos=new Documentos;
            ///$pedido=$documentos->obtenerListaPickingDetalle($vendedor);

            $sap = new Sap();
            $sap->ObtenrPedidosDetalle($vendedor);

            $pedido = Yii::$app->db->createCommand("CALL pa_documentosimportadosdetalleruta('".$usuario."','0','0')")->queryAll();
            
            $facturadeuda=[];//$documentos->obtenerDeudaCLienteTodosContador();//
            $resultado = (object) array_merge(
             (array) $pedido,(array) $facturadeuda);
        }
        else{

            //obtener la configuracion del usuario para realizar uno u otro proceso 
            $documentos=new Documentos;
            $oferta=$documentos->obtenerDetalles(0,$equipo,$usuario,$salto);
            $pedido=$documentos->obtenerDetalles(1,$equipo,$usuario,$salto);
            $factura=$documentos->obtenerDetalles(2,$equipo,$usuario,$salto);
            $entrega=$documentos->obtenerDetalles(3,$equipo,$usuario,$salto);
            $facturadeuda=$documentos->obtenerDetalles(4,$equipo,$usuario,$salto);


            $resultado = (object) array_merge(
            (array) $oferta, (array) $pedido, (array) $factura, (array) $entrega, (array) $facturadeuda);
        
        }
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
            $sql1 = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
            $configU = Yii::$app->db->createCommand($sql1)->QueryOne();

            $vendedor=$configU['codEmpleadoVenta'];
            
            $documentos=new Documentos;
            //$pedido=$documentos->obtenerListaPickingDetalleContador($vendedor);

            $sap = new Sap();
            $sap->ObtenrPedidosDetalle($vendedor);

            $pedido = Yii::$app->db->createCommand("CALL pa_documentosimportadosdetalleruta('".$usuario."','1','0')")->queryAll();
            
            $facturadeuda=[];//$documentos->obtenerDeudaCLienteTodosContador();//
            $resultado = (object) array_merge(
             (array) $pedido,(array) $facturadeuda);

        }else{
            //obtener la configuracion del usuario para realizar uno u otro proceso 
            $documentos=new Documentos;
            $oferta=$documentos->obtenerDetallesContador(0,$equipo,$usuario);
            $pedido=$documentos->obtenerDetallesContador(1,$equipo,$usuario);
            $factura=$documentos->obtenerDetallesContador(2,$equipo,$usuario);
            $entrega=$documentos->obtenerDetallesContador(3,$equipo,$usuario);
            $facturadeuda=$documentos->obtenerDetallesContador(4,$equipo,$usuario);

            $resultado = (object) array_merge(
            (array) $oferta, (array) $pedido, (array) $factura, (array) $entrega, (array) $facturadeuda);
        }
        
        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Documentosmovilsapdetalle');

        $cantidad=0;
        if (count($resultado) > 0) {
            
            foreach ($resultado as $value) {
                $cantidad+=$value['CONTADOR'];
            }
            //return $this->correcto(["contador"=>$cantidad]);
        } 
        
        return $this->correcto(["contador"=>$cantidad]);

    }
}
