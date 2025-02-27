<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class LotesproductosController extends ActiveController
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

    protected function verbs() {
        return [
            ///'index' => ['POST', 'HEAD'],
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
        $result = Yii::$app->db->createCommand("SELECT * FROM lotesproductos")->queryAll();
        if (count($result) > 0) {
            return $this->correcto($result);
        }
        return $this->correcto([],'Sin datos',201);
    }
	
	
	public function actionCreate(){
        
        Yii::error("LOTES PRODUCTOS: ");
        Yii::error(Yii::$app->request->post());
         $resultado = Yii::$app->db->createCommand("CALL pa_obtenerLotesProductos(:sucursal,:contador,:salto)")
        ->bindValue(':sucursal', Yii::$app->request->post('sucursal'))
        ->bindValue(':contador',0)
        ->bindValue(':salto',Yii::$app->request->post('pagina',0))
        ->queryAll();

	  	if (count($resultado)){
		  return $this->correcto($resultado);
		}
		return $this->error('Sin datos');
	}
    
    public function actionContador(){
        $resultado = Yii::$app->db->createCommand("CALL pa_obtenerLotesProductos(:sucursal,:contador,:salto)")
          ->bindValue(':sucursal', Yii::$app->request->post('sucursal'))
          ->bindValue(':contador',1)
          ->bindValue(':salto',0)
          ->queryOne();

        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Lotesproductos');
        return $this->correcto($resultado, 'OK'); 

    }

    public function actionFiltrar() {
        $almacen = Yii::$app->request->post('almacen');
        $lotes = Yii::$app->db->createCommand("SELECT * FROM lotesproductos WHERE WhsCode = :almacen AND Quantity > 0;")
            ->bindValue(':almacen', $almacen)
            ->queryAll();
        if (count($lotes) > 0) {
            return $this->correcto($lotes);
        }
        return $this->correcto([],'Sin datos',201);
    }

    public static function actionFindbyalmacen($producto) {
        $lotes = Yii::$app->db->createCommand("SELECT * FROM lotesproductos WHERE ItemCode = :producto AND Quantity > 0;")
            ->bindValue(':producto', $producto)
            ->queryAll();
        if (count($lotes) > 0) {
            return $lotes;
        }
        return [];
    }

    public function actionLotestraspaso() {
        $itemcode = Yii::$app->request->post('itemcode');
        $whscode = Yii::$app->request->post('whscode');
        $lotes = Yii::$app->db->createCommand("SELECT '0' AS CantidadLote, '' AS checkedvalue, lotesproductos.* FROM lotesproductos WHERE WhsCode = :WHS AND ItemCode = :ITM AND Quantity > 0 AND (Status is null OR Status <> 0)")
            ->bindValue(':WHS', $whscode)
            ->bindValue(':ITM', $itemcode)
            ->queryAll();
        if (count($lotes) > 0) {
            return $this->correcto($lotes);
        }
        return $this->correcto([],'Sin datos',201);
    }
}
