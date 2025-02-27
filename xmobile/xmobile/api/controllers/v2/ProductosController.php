<?php

namespace api\controllers\v2;


use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;
use backend\models\v2\Productos;

class ProductosController extends ActiveController {
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
        Yii::error("Datos de ingreso: ". json_encode(Yii::$app->request->post()));
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $modelo=new Productos;
        $modelo= $modelo->obtenerProductos($equipo,$usuario,$salto);
        
        //se verifica si el usuario esta tiqueado como zona franca
        $resultado=Yii::$app->db->createCommand("Select zonaFranca from usuarioconfiguracion where idUser=".$usuario)->queryOne();
        if($resultado['zonaFranca']==1){
            foreach ($modelo as $key => $value) {
                $modelo[$key]['producto_std2']="N";
                $modelo[$key]['producto_std3']=0;
                $modelo[$key]['producto_std4']=0;
            }
        }
        // fin verificacion de usuario zona franca
        
        if (count($modelo)){
          return $this->correcto($modelo);
        }
        return $this->error('Sin datos',201);
    }  
    public function actionContador(){
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');        
        $modelo=new Productos;
        $modelo= $modelo->obtenerProductosContador($equipo,$usuario);
        return $this->correcto($modelo, 'OK'); 
    }  
    public function actionBuscador(){
        $equipo=Yii::$app->request->post('equipo');
        $usuario=Yii::$app->request->post('usuario');
        $almacen=Yii::$app->request->post('almacen'); 
        $texto=Yii::$app->request->post('texto');       
        $modelo=new Productos;
        $modelo= $modelo->obtenerTodosProductos($equipo,$usuario,$almacen,$texto);
        return $this->correcto($modelo, 'OK'); 
    }
}