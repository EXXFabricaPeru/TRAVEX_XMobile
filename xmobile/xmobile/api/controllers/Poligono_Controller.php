<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Poligonocabecera;
use backend\models\Poligonocabeceraterritorio;
use backend\models\Poligonodetalle;
use backend\models\Poligonocliente;
use backend\models\Usuarioconfiguracion;
use backend\models\Vendedores;
use backend\models\Rutacabecera;
use backend\models\Rutadetalle;

class PoligonoController extends ActiveController {
    
    use Respuestas;
    
    public $modelClass = 'backend\models\Usuario';
    
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

    public function actionCreate() {
        

        $vendedor = Yii::$app->request->post('idvendedor');
        ///SE OBTIENE LOS DATOS DEL USUARIO   
        $usuarioConfig = Usuarioconfiguracion::find()->where('idUser = '.$vendedor)->asArray()->one();
        
		if($usuarioConfig['TipoUsuario']==0){//por verdad SI ES VENDEDOR Vendedor
            $cabeceraTerritorio = Poligonocabeceraterritorio::find()->where('idVendedor = '.$vendedor)->asArray()->all();
            //if(isset($cabeceraTerritorio['id'])){
            foreach ($cabeceraTerritorio as $key => $value) {
                # code...
                $poligonoCliente = Poligonocliente::find()->where('idCabecera = '.$value['id'])->orderby('posicion asc')->asArray()->all();
            
                $resultado[$key]['id'] = $value['id'];
                $resultado[$key]['fechaRegistro']= $value['fechaRegistro'];
                $resultado[$key]['fechaSistema']= $value['fechaSistema'];
                $resultado[$key]['dia'] = $value['dia'];
                $resultado[$key]['idVendedor']= $value['idVendedor'];
                $resultado[$key]['vendedor']= $value['vendedor'];
                $resultado[$key]['tipoVendedor']= 0;
                $resultado[$key]['poligono']= $poligonoCliente;
                    
                
            }  
            Yii::error("detalle poligono Rodri");
            Yii::error($resultado);   
            if(count($resultado)>0){
                return $this->correcto($resultado);
            }
            else{
                return $this->error('No hay rutas agendadas',201);
            }        
           
        }
        else{//por falso SI ES DESPACHADOR
		
			$rutacabecera = Rutacabecera::find()->where('idvendedor = '.$vendedor.' and status=1')->asArray()->all();
            //if(isset($cabeceraTerritorio['id'])){
            foreach ($rutacabecera as $key => $value) {
                # code...
                $rutadetalle_=array();
                $rutadetalle = Rutadetalle::find()->where('idcabecera = '.$value['id'])->orderby('posicion asc')->asArray()->all();
				foreach ($rutadetalle as $keyD => $valueD) {
					$rutadetalle_[$keyD]['id'] = $valueD['id'];
					$rutadetalle_[$keyD]['cardcode'] = $valueD['idcliente'];
					$rutadetalle_[$keyD]['cardname'] = $valueD['cardname'];
					$rutadetalle_[$keyD]['latitud'] = $valueD['latitud'];
					$rutadetalle_[$keyD]['longitud'] = $valueD['longitud'];
					$rutadetalle_[$keyD]['territoryid'] = '';
					$rutadetalle_[$keyD]['territoryname'] = '';
					$rutadetalle_[$keyD]['posicion'] = $valueD['posicion'];
					$rutadetalle_[$keyD]['nombreDireccion'] = '';
					$rutadetalle_[$keyD]['calle'] = '-';
					$rutadetalle_[$keyD]['idCabecera'] = $valueD['idcabecera'];
				}
            
                $resultado[$key]['id'] = $value['id'];
                $resultado[$key]['fechaRegistro']= $value['fecha'];
                $resultado[$key]['fechaSistema']= $value['dateUpdate'];
                $resultado[$key]['dia'] = '0';
                $resultado[$key]['idVendedor']= $value['idvendedor'];
                $resultado[$key]['vendedor']= '-';
                $resultado[$key]['tipoVendedor']= 1;
                $resultado[$key]['poligono']= $rutadetalle_;
                    
                
            }  
            Yii::error("detalle poligono Rodri");
            Yii::error($resultado);   
            if(count($resultado)>0){
                return $this->correcto($resultado);
            }
            else{
                return $this->error('No hay rutas agendadas',201);
            }
			
        }
       
    }
}