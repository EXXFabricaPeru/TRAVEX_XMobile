<?php

namespace api\controllers;

use backend\models\Unidadesmedida;
use Carbon\Carbon;
use Yii;
use DateTime;
use yii\rest\ActiveController;
use backend\models\LineasPedidos;
use yii\filters\auth\QueryParamAuth;
use backend\models\Servislayer;
use backend\models\Cabeceradocumentos;
use backend\models\Detalledocumentos;
use api\traits\Respuestas;
use backend\models\Usuariosincronizamovil;

class DescuentoController extends ActiveController
{
    use Respuestas;
    
    public $modelClass = 'backend\models\User';

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

    public function actionIndex()
    {
      $descuentos = Yii::$app->db->createCommand('select * from descuentos')
                      ->queryAll();
          if (count($descuentos)){
            return $this->correcto($descuentos);
          }
          return $this->error('Sin Datos',201);
    }

    public function actionBuscar()
    {
      $descuentos = Yii::$app->db->createCommand("SELECT tipodescuento,
                    ItemCode,CardCode,PriceListNum,
                    Price,Currency,DiscountPercent,
                    paid,free,max,prioridad,
                    linea,ValidTo,ValidFrom
                    FROM descuentos
                    WHERE
                    ItemCode = :itemCode AND
                    ((CardCode = :CardCode) OR
                    (CardCode = :GroupCode) OR
                    (CardCode = '*') OR
                    (PriceListNum = :PriceListNum))
                    and
                    (((ValidFrom='0000-00-00')and (ValidTo='0000-00-00')) or (:fecha BETWEEN ValidFrom and ValidTo) ) 
                    ORDER BY prioridad ASC,linea ASC,
                    DiscountPercent ASC LIMIT 1;")
                    ->bindValue(':itemCode',Yii::$app->request->post('ItemCode'))
                    ->bindValue(':CardCode',Yii::$app->request->post('CardCode'))
                    ->bindValue(':GroupCode',Yii::$app->request->post('GroupCode'))
                    ->bindValue(':PriceListNum',Yii::$app->request->post('PriceListNum'))
                    ->bindValue(':fecha',Carbon::today()->format('Y-m-d'))
                    ->queryAll();
          if (count($descuentos)){
            return $this->correcto($descuentos);
          }
          return $this->error('Sin Datos',201);
    }
    public function actionCreate() {
        $usuario=Yii::$app->request->post('usuario');
        $salto=Yii::$app->request->post('pagina');
        $resultado = Yii::$app->db->createCommand("select * from vi_descuentosEspecialesSap order by cardcode limit 1000 OFFSET {$salto}")->queryAll();

        /*
        $resultado = Yii::$app->db->createCommand('CALL pa_obtenerDescuentosEspecialesSap(:cliente,:item)')
                ->bindValue(':cliente', '')
                ->bindValue(':item', '')
                ->queryAll();*/

        if (count($resultado)){
            return $this->correcto($resultado);
        }
        return $this->error('Sin datos',201);
    }
    public function actionContador(){
        $usuario=Yii::$app->request->post('usuario');
        $resultado=Yii::$app->db->createCommand("Select count(*) as contador from descuentosespecialessap")->queryOne();
		
        $usuariosincronizamovil= new Usuariosincronizamovil();
        $usuariosincronizamovil->actionUsuarioSincronizaMovil(Yii::$app->request->post(),'Descuento');
        return $this->correcto($resultado, 'OK'); 
    }
}
