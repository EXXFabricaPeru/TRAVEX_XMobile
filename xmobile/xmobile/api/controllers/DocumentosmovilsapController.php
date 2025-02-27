<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use backend\models\Usuariosincronizamovil;
use api\traits\Respuestas;
use yii\data\ActiveDataProvider;
use backend\models\Sap;
use backend\models\v2\Documentos;

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
        /*$usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $resultado= Yii::$app->db->createCommand("select * from vi_documentosimportadosdetalle2 order by id limit 1000 OFFSET {$salto}")->queryAll();

		/*$sql2 = "SELECT * FROM `vi_documentosimportadosdetalle2`";
        $rx = Yii::$app->db->createCommand($sql2)->queryAll();
		return $this->correcto($rx);
        */
        /*if (count($resultado)){
            return $this->correcto($resultado);
        }
          return $this->error('Sin datos',201);*/
	}
	public function actionCreate(){         
		set_time_limit(0);
        
        $sql = "SELECT valor FROM configuracion WHERE parametro='docImportados' and estado=1";
        $configuracion = Yii::$app->db->createCommand($sql)->QueryOne();

        if($configuracion['valor']==1){
            //CASO COMPANEX//
            $resultado = Yii::$app->db->createCommand("CALL pa_documentosimportados(:usuario,:contador,:salto,:fechaPicking)")
            ->bindValue(':usuario',Yii::$app->request->post('usuario'))
            ->bindValue(':contador',0)
            ->bindValue(':salto',Yii::$app->request->post('pagina',0))
            ->bindValue(':fechaPicking',0)        
            ->queryAll();
        }
        else{
            //CONSULTA DIRECTA A SAP//
            $sap= new Sap();
            $usuario=Yii::$app->request->post('usuario');
            $salto=Yii::$app->request->post('pagina',0);
        
            $sql = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
            $usuario = Yii::$app->db->createCommand($sql)->QueryOne();
            $codEmpleadoVenta = $usuario["codEmpleadoVenta"];
            
            Yii::error("CONSULTA DOCUMENTOS IMPORTADOS SAP: ");
            $resultado = $sap->DocumentosImportados($codEmpleadoVenta,$salto);
            yii::error("codVendedor: ".$codEmpleadoVenta." Salto: ".$salto);
            yii::error($resultado);
        }

        if (count($resultado) > 0) {
            return $this->correcto($resultado);
        }
        return $this->error('Sin datos',201);



	}
    public function actionDetail(){         
		set_time_limit(0);
        $data = Yii::$app->request->post();
        $usuario = $data['usuario'];

        $sql = "SELECT valor FROM configuracion WHERE parametro='docImportados' and estado=1";
        $configuracion = Yii::$app->db->createCommand($sql)->QueryOne();

        if($configuracion['valor']==1){
            $resultado = Yii::$app->db->createCommand("CALL pa_documentosimportadosdetalle(:usuario)")
            ->bindValue(':usuario',$usuario)        
            ->queryAll();
        }
        else{
            $sql = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
            $usuario = Yii::$app->db->createCommand($sql)->QueryOne();
            $codEmpleadoVenta = $usuario["codEmpleadoVenta"];
            $sql = "SELECT * FROM `vi_documentosimportadosmovil` WHERE SalesPersonCode=".$codEmpleadoVenta;
            $resultado = Yii::$app->db->createCommand($sql)->queryAll();
        }
        
        if (count($resultado) > 0) {
            return $this->correcto($resultado);
        }
        return $this->error('Sin datos',201);
	}

    public function actionContador(){
        Yii::error("Entra contador");
        $sql = "SELECT valor FROM configuracion WHERE parametro='docImportados' and estado=1";
        $configuracion = Yii::$app->db->createCommand($sql)->QueryOne();

        if($configuracion['valor']==1){
            $resultado = Yii::$app->db->createCommand("CALL pa_documentosimportados(:usuario,:contador,:salto,:fechaPicking)")
            ->bindValue(':usuario',Yii::$app->request->post('usuario'))
            ->bindValue(':contador',1)
            ->bindValue(':salto',Yii::$app->request->post('pagina',0)) 
            ->bindValue(':fechaPicking',0)
            ->queryOne();
        }else{
            $sap= new Sap();
            $usuario=Yii::$app->request->post('usuario');        
            $sql = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
            $usuario = Yii::$app->db->createCommand($sql)->QueryOne();
            $codEmpleadoVenta = $usuario["codEmpleadoVenta"];

            $resultado = $sap->DocumentosImportadosContador($codEmpleadoVenta);
            
            $usuariosincronizamovil= new Usuariosincronizamovil();
            $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Documentosmovilsap');
            
            $resultado=['contador'=>$resultado[0]->CANTIDAD];
        }

       
    
        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Documentosmovilsap');
        
        return $this->correcto($resultado, 'OK'); 
    }

    /*public function actionDoccliente(){
        Yii::error("Entra actionDoccliente");
        $sap= new Sap();
        $codigo=Yii::$app->request->post('codigo');
        $resultado = $sap->DocumentosImportadosCliente($codigo);
    
        return $this->correcto($resultado, 'OK');
    }*/
    

    public function actionDoccliente(){
        Yii::error("Entra actionDoccliente - CONSULTA DIRECTA HANA");
        $documentos= new Documentos();
        $CardCode=Yii::$app->request->post('codigo');
        $resultado = $documentos->obtenerDeudaCLiente($CardCode);
    
        return $this->correcto($resultado, 'OK');
    }
    
}
