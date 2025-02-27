<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Seriesproductos;
use api\traits\Respuestas;
use yii\data\ActiveDataProvider;

class DocumentosmovilsapController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\User';

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

    public function actionCreate(){ 
		set_time_limit(0);
        $data = Yii::$app->request->post();
        $usuario = $data['usuario'];
       // $tipo =  $data['tipo'];
       // $texto = $data['texto'];
       /* 
       $sql = "SELECT codEmpleadoVenta FROM usuarioconfiguracion WHERE idUser=".$usuario;
        $usuario = Yii::$app->db->createCommand($sql)->QueryOne();
        $codEmpleadoVenta = $usuario["codEmpleadoVenta"];
        $sql = "SELECT * FROM `vi_documentosimportadosmovil` WHERE SalesPersonCode=".$codEmpleadoVenta;
        return Yii::$app->db->createCommand($sql)->queryAll();
        */
        $DocumentosImportados = Yii::$app->db->createCommand("CALL pa_obtenerCabDocsImp(:usuario)")
        ->bindValue(':usuario',$usuario)        
        ->queryAll();
            if (count($DocumentosImportados) > 0) {
            return $this->correcto($DocumentosImportados);
            }
            return $this->error('Sin datos',201);



	}
	
	public function actionIndex(){ 
		$sql2 = "SELECT * FROM `vi_documentosimportadosdetalle2`";
		return Yii::$app->db->createCommand($sql2)->queryAll();
	}
	
}
