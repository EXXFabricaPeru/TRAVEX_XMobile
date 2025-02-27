<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Cabeceradocumentos;

class DocumentcancelController extends ActiveController {

    use Respuestas;

    public $modelClass = 'backend\models\Cabeceradocumentos';

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
	
	
		private function estadosDoc(){
		$sql = 'SELECT * FROM cabeceradocumentos WHERE idDocPedido = "DOF000162001160002";';
        $resp = Yii::$app->db->createCommand($sql)->queryAll();
		$doctype = 0;
		$DocEntry  = 0;
		if(count($resp)>0){
			$DocEntry = $resp[0]['DocEntry'];
			switch($resp[0]['DocType']){
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
		}else{
			$doctype = 0;
			$DocEntry  = 0;
		}
		return ['doctype'=>$doctype,'docentry'=>$DocEntry] ;
	}
	
	public function actionIndex(){
		$data = $this->estadosDoc();
		return $data['doctype'];
	}
	                     /*
                        if ($cabecera->clone!=0){
                            $origen="Select docentry,doctype from cabecera documentos where iddocpedido =".$cabecera->clone;
                            $doctype=$origen["doctype"];
                            $docentry=$origen["docentry"];
                            if($doctype=="DOF")
                            $doctype=23;
                            if($doctype=="DOP")
                            $doctype=17;
                            if($doctype=="DAF")
                            $doctype=13;
                        }else{
                            $doctype=0 ;
                            $docentry=0 ;
                        }
                        */
	
	
	
	
	
	
	
	


    public function actionCreate() {
        $data = Yii::$app->request->post();
        $KeyDoc = $data['KeyDoc'];
        $sql = 'UPDATE cabeceradocumentos SET estado = "6" WHERE idDocPedido = "' . $KeyDoc . '";';
        $resp = Yii::$app->db->createCommand($sql)->execute();
        if ($resp == 0)
            return 1;
        else
            return 0;
    }

}
