<?php

namespace api\controllers;

use backend\models\Tiposcambio;
use Yii;
use Carbon\Carbon;
use api\traits\Respuestas;
use backend\models\TipoCambio;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;

class TipocambiomovilController extends ActiveController
{
    use Respuestas;
    public $modelClass = 'backend\models\User';
    
    /**
     * @var TipoCambio $modelTipoCambio
     */
    public $modelTipoCambio;
   /* public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
  

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'tokenParam' => 'access-token',
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }  }*/

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
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
		Yii::info('PETICION TIPO CAMBIO ', '________');
        $tiposCambios = Yii::$app->db->createCommand("SELECT * FROM vi_cambioparalelo")->queryAll();
        if (count($tiposCambios) > 0) {
            return $this->correcto($tiposCambios,'OK');
        }
        return $this->correcto([],'Tipo de cambio no actualizado',201);
    }

    public function actionCreate()
    {
        $cambio = Yii::$app->request->post('cambio',0);
        if ($cambio == 0) {
            $tiposCambios = Yii::$app->db->createCommand("CALL pa_tiposCambios(:cambio,CURDATE())")
                            ->bindValue(':cambio',Yii::$app->request->post('cambio',0))
                            ->queryAll();
        } else {
            $tiposCambios = Yii::$app->db->createCommand("CALL pa_tiposCambios(:cambio,:fecha)")
                            ->bindValue(':cambio',Yii::$app->request->post('cambio',0))
                            ->bindValue(':fecha',date("Y-m-d"))
                            ->queryOne();
        }
        if (!is_null($tiposCambios)) {
            if (is_object($tiposCambios)) {
                return $this->correcto([$tiposCambios]);
            }else if(count($tiposCambios) > 0){
                 return $this->correcto($tiposCambios);
            }
        }
        return $this->error('Sin datos',201);
    }

    public function actionRatechange(){
      $rateChange = Yii::$app->db->createCommand('CALL pa_tiposCambios(1,:fecha)')
                  ->bindValue(':fecha',Carbon::today()->format('Y-m-d'))
                  ->queryAll();
      if (count($rateChange)){
        return $this->correcto($rateChange);
      }
      return $this->error('Sin Datos',201);
    }


    public function actionFindonebyexchangefrom($exchangeFrom){
        $oTipoCambio = Tiposcambio::find()
            ->where(['like', 'ExchangeRateFrom', $exchangeFrom])
            ->one();
        if (is_object($oTipoCambio)) {
            return $this->correcto($oTipoCambio);
        } else {
            return $this->error();
        }
    }
    public function actionFindonebyexchangefromandexchangeto(){

        $exchangeFrom = Yii::$app->request->post('exchangeFrom');
        $exchangeTo = Yii::$app->request->post('exchangeTo');

        $oTipoCambio = Tiposcambio::find()
            ->where(['like', 'ExchangeRateFrom', $exchangeFrom])
            ->where(['like', 'ExchangeRateTo', $exchangeTo])
            ->one();
        if (is_object($oTipoCambio)) {
            return $this->correcto($oTipoCambio);
        } else {
            return $this->error();
        }
    }
    public function actionFindonebyexchangeto($exchangeTo){
        $oTipoCambio = Tiposcambio::find()
            ->where(['like', 'ExchangeRateTo', $exchangeTo])
            ->one();
        if (is_object($oTipoCambio)) {
            return $this->correcto($oTipoCambio);
        } else {
            return $this->error();
        }
    }

}
