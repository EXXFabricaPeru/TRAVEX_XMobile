<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use backend\models\v2\Clientes;

class ClientesearchmovilController extends ActiveController {

    public $modelClass = 'backend\models\ViClientes';
    protected function verbs() {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }
	
	public function actionSearch() {
        
       /* $query = $this->modelClass::find()
							->where(['like', 'CardCode', '%' . $_GET['name'] . '%', false])
							->orwhere(['like', 'razonsocial', '%' . $_GET['name'] . '%', false])
							->orwhere(['like', 'FederalTaxId', '%' . $_GET['name'] . '%', false])
							->orwhere(['like', 'CardName', '%' . $_GET['name'] . '%', false]);
        return new ActiveDataProvider([
            'query' => $query
        ]);*/
        $usuario= $_GET['usuario']; 
        $texto= $_GET['name']; 
        $modelo=New Clientes;
        $socios= $modelo->obtenerTodosClientes($usuario,$texto);

       // $sql = 'SELECT * FROM vi_clientes WHERE (CardCode like "%'. $_GET['name'].'%") or (razonsocial like "%'. $_GET['name'].'%") OR (FederalTaxId like "%'. $_GET['name'].'%") OR (CardName like "%'. $_GET['name'].'%") ORDER BY CardCode';
        //$socios = Yii::$app->db->createCommand($sql)->queryAll();
        /*
        foreach ($socios as $key => $value) {
             $sql = 'SELECT * FROM clientessucursales WHERE CardCode="'.$value['CardCode'].'"';
             $sucursales = Yii::$app->db->createCommand($sql)->queryAll();
             $socios[$key]["sucursales"]=$sucursales;
        }*/
        foreach ($socios as $key => $value) {
            //$sql = 'SELECT * FROM clientessucursales WHERE CardCode="'.$value['CardCode'].'"';
            $sucursales =$modelo->obtenerSucursalesTodos($usuario,$texto);
            $socios[$key]["sucursales"]=$sucursales;
       }

        Yii::error("CLIENTES EN LINEA");
        //return  $socios;
    
        if (count($socios) > 0) {
            return $socios;
            }
            return array("error"=>201);       
        

    }
}
