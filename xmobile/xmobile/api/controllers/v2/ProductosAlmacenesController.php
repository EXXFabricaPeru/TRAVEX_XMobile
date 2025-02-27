<?php

namespace api\controllers\v2;


use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;
use backend\models\v2\Productos;

class ProductosalmacenesController extends ActiveController {
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
    public function actionIndex(){
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $modelo=new Productos;
        $modelo= $modelo->obtenerProductosAlmacenes($equipo,$usuario,$salto);
        if (count($modelo)){
          return $this->correcto($modelo);
        }
        return $this->error('Sin datos',201);
    }  
    public function actionContador(){
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');        
        $modelo=new Productos;
        $modelo= $modelo->obtenerProductosAlmacenesContador($equipo);
        return $this->correcto($modelo, 'OK'); 
    }  
    public function actionBuscador(){
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');
        $almacen=Yii::$app->request->post('almacen'); 
        $texto=Yii::$app->request->post('texto');       
        $modelo=new Productos;
        $modelo= $modelo->obtenerProductosAlmacenesTodos($equipo,$texto,$almacen);
        return $this->correcto($modelo, 'OK'); 
    }

    
      public function actionListaproductosalmacensap(){
        $lista = Yii::$app->request->post();
        $aux = 0;
        $respuesta = '';
        $modelo=new Productos;
        foreach($lista as $dato){
          $resultado = $modelo->Lista_productos_stock($dato["ItemCode"],$dato["almacen"],$dato["cantidad"]);
          if($resultado[0]['CANTIDAD'] == 0){
            $aux = 1;
            $respuesta = $respuesta.$dato["ItemCode"].' - '.$dato["desc"].' pxp pxp' ;
          }
        }
        if($aux == 0){
          $respuesta = '0';
        }else{
          $respuesta = 'No se encontro stock para los productos: pxp pxp'.$respuesta;
        }
        return $this->correcto($respuesta, 'OK');
      }
}