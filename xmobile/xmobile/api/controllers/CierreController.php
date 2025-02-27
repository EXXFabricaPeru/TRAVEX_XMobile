<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Cierrecaja;

class CierreController extends ActiveController
{
    use Respuestas;
    public $modelClass = 'backend\models\Cierrecaja';

    /*     public function actionIndex()
        {
            return $this->render('index');
        } */

    protected function verbs()
    {
      return [
        'index' => ['GET', 'HEAD'],
        'view' => ['GET', 'HEAD'],
        'create' => ['POST'],
        'update' => ['PUT', 'PATCH'],
        'delete' => ['DELETE'],
      ];
    }

    public function actions()
    {
      $actions = parent::actions();
      unset($actions['index']);
      unset($actions['view']);
      unset($actions['create']);
      unset($actions['update']);
      unset($actions['delete']);
      return $actions;
    }

    public function actionUltimo(){
        $sql = 'SELECT * FROM cierrecaja WHERE idUser = '.Yii::$app->request->post('id').' ORDER BY fechaFin DESC LIMIT 1;';
        $response = Cierrecaja::findBySql($sql)->one();
        //var_dump($response->attributes);
        if ($response){
            if (count($response->attributes) == 1) {
                return $this->error('Datos no encontrados', 201);
            } else {
                return $this->correcto($response);
            }
        } else {
            return $this->error('Datos no encontrados', 201);
        }
    }

    public function actionFechaultimocierre(){
        $sql = 'SELECT fechaFin FROM cierrecaja WHERE idUser = '.Yii::$app->request->post('id').' ORDER BY fechaFin DESC LIMIT 1;';
        $response = Cierrecaja::findBySql($sql)->one();
        if ($response){
            return $this->correcto($response);
        } else {
            return $this->error('Datos no encontrados', 201);
        }
    }

    public function actionCreate(){
        $data = Yii::$app->request->post();
        $idUser = Yii::$app->request->post('idUser');
        $numeracion = Yii::$app->request->post('numeracion');
        $efectivo_bs = Yii::$app->request->post('efectivo_bs');
        $efectivo_usd = Yii::$app->request->post('efectivo_usd');
        $tarjeta_de_credito_bs = Yii::$app->request->post('tarjeta_de_credito_bs');
        $tarjeta_de_credito_usd = Yii::$app->request->post('tarjeta_de_credito_usd');
        $cheque_bs = Yii::$app->request->post('cheque_bs');
        $cheque_usd = Yii::$app->request->post('cheque_usd');
        $transferencia_bs = Yii::$app->request->post('transferencia_bs');
        $transferencia_usd = Yii::$app->request->post('transferencia_usd');
        $gift_card_bs = Yii::$app->request->post('gift_card_bs');
        $total_bs = Yii::$app->request->post('total_bs');
        $ofertas = Yii::$app->request->post('ofertas');
        $pedidos = Yii::$app->request->post('pedidos');
        $facturas = Yii::$app->request->post('facturas');
        $facturas_contado = Yii::$app->request->post('facturas_contado');
        $facturas_credito = Yii::$app->request->post('facturas_credito');
        $pagos_recibidos_bs = Yii::$app->request->post('pagos_recibidos_bs');
        $total_ventas = Yii::$app->request->post('total_ventas');
        $fechaIni = Yii::$app->request->post('fechaIni');
        $fechaFin = Yii::$app->request->post('fechaFin');
        $sql = "CALL `pa_insertCierre`(" .
        "'$idUser'," .
        "'$numeracion'," .
        "'$efectivo_bs'," .
        "'$efectivo_usd'," .
        "'$tarjeta_de_credito_bs'," .
        "'$tarjeta_de_credito_usd'," .
        "'$cheque_bs'," .
        "'$cheque_usd'," .
        "'$transferencia_bs'," .
        "'$transferencia_usd'," .
        "'$gift_card_bs'," .
        "'$total_bs'," .
        "'$ofertas'," .
        "'$pedidos'," .
        "'$facturas'," .
        "'$facturas_contado'," .
        "'$facturas_credito'," .
        "'$pagos_recibidos_bs'," .
        "'$total_ventas'," .
        "'$fechaIni'," .
        "'$fechaFin'" .
        ");";
        $response = Yii::$app->db->createCommand($sql)->queryOne();
        //deshabilitacion de usuario
        // UPDATE user SET estadoUsuario = 4 WHERE id = 3
        $updated = Yii::$app->db->createCommand('CALL pa_cerrarcajausuario(:usuario)')
            ->bindValue(':usuario', Yii::$app->request->post('idUser'))
            ->queryAll();
        if (count($response) == 1) {
            return $this->error('Registro no Correcto', 201);
        } else {
            return $this->correcto($response, 'Registro Correcto');
        }


    }


}
