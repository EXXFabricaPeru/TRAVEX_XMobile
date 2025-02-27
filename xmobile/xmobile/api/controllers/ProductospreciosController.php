<?php

namespace api\controllers;

use Yii;
use yii\rest\ActiveController;
use api\traits\Respuestas;
use backend\models\Productosprecios;
use backend\models\Copiaproductosprecios;
use backend\models\Usuariosincronizamovil;

class ProductospreciosController extends ActiveController
{
    use Respuestas;
    
    public $modelClass = 'backend\models\Productosprecios';

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

    public function actionCreate()
    {
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $productosPrecios= Yii::$app->db->createCommand("select * from vi_obtenerproductoslistaprecios where IdListaPrecios in(select listapreciosuser.idlistaprecios from listapreciosuser where listapreciosuser.user_id=".$usuario." )  order by ItemCode limit 1000 OFFSET {$salto}")->queryAll();

        /*$productosPrecios = Yii::$app->db->createCommand("CALL pa_obtenerProductosListaPrecios(:usuario,:texto,:lstprecio)")
                        ->bindValue(':usuario',Yii::$app->request->post('usuario'))
                        ->bindValue(':texto',Yii::$app->request->post('texto',0))
                        ->bindValue(':lstprecio',Yii::$app->request->post('lstprecio',0))
                        ->queryAll();*/
        if (count($productosPrecios) > 0) {
            return $this->correcto($productosPrecios);
        }
        return $this->error('Sin datos',201);
    }

    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        $resultado=Yii::$app->db->createCommand("Select count(*) as contador from  vi_obtenerproductoslistaprecios where IdListaPrecios in(select listapreciosuser.idlistaprecios from listapreciosuser where listapreciosuser.user_id=".$usuario." )")->queryOne();
        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Productosprecios');
        return $this->correcto($resultado, 'OK'); 
    }

    public static function actionFindonebyitemcode($itemCode, $priceList){
        $sql ="SELECT p.ItemCode, p.Price,p.Currency, l.PriceListName,u.Name ".
        "FROM productosprecios p, listaprecios l, unidadesmedida u where p.ItemCode='".
         $itemCode."' AND p.Price>0 AND p.IdListaPrecios=".$priceList." AND p.IdListaPrecios=l.id AND p.IdUnidadMedida=u.id";
        $oProducto = Yii::$app->db->createCommand($sql)->queryAll();
        //$oProducto = Productosprecios::find()->where(['ItemCode' => trim($itemCode,'?')])->all();
        if (count($oProducto) > 0) {
            return $oProducto;//$this->correcto($oProducto);
          }
          return $oProducto;//$this->error('Sin datos',201);
    }

    public function actionGuardarproductosprecios(){
        $datos = Yii::$app->request->post();
        $producto = $datos["producto"];
        $p = new copiaproductosprecios();
        $p->id = null;        
        $p->ItemCode = $producto["ItemCode"];
        $p->IdListaPrecios = $producto["PriceList"];
        $p->IdUnidadMedida = $producto["UomEntry"];
        $p->Price = $producto["Price"];
        $p->Currency = $producto["Currency"];
        $p->User = 1;
        $p->Status = 1;
        $p->DateUpdate = date("Y-m-d");
        //$p->save();
        return $this->correcto($p);
    }

}
