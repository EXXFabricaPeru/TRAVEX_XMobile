<?php

namespace api\controllers;

use yii;
use yii\base\ModelEvent;
use backend\models\TipoCambio;
use yii\rest\ActiveController;
use backend\modules\admin\models\Localidad;

class LocalidadController extends ActiveController {


    public $modelClass = 'backend\modules\admin\models\Localidad';
    /**
     * @var TipoCambio $modelTipoCambio
     */
    public $modelTipoCambio;

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
        $this->modelTipoCambio = new TipoCambio();
        $actions = parent::actions();
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $localidades = Localidad::find()->asArray()->all();
        $monedas = [
            "monedaLocal" => "SOL",//$this->modelTipoCambio->monedaLocal(),
            "monedaSistema" => "USD"//$this->modelTipoCambio->monedaSistema(),
        ];
        for($i=0;$i<count($localidades);$i++) {
            $localidades[$i]["monedas"] = $monedas;
        }
        return $localidades;
    }
	
	public function actionView($id) {
		$arr = [];
        $sql = "SELECT * FROM `etiquetas` WHERE idlocalidad = ".$id;
		$data = Yii::$app->db->createCommand($sql)->queryAll();
		foreach($data as $key => $val){
			$arr += [$val['clave'] => $val['valor']];
        }
        return $arr;
    }
	
}
