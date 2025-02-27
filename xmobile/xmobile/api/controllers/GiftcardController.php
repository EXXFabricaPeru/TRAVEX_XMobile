<?php

namespace api\controllers;

use Yii;
use Carbon\Carbon;
use api\traits\Respuestas;
use yii\rest\ActiveController;
use backend\models\DB;
use backend\models\Gifcards;
use yii\filters\auth\QueryParamAuth;

class GiftcardController extends ActiveController
{
    use Respuestas;

    public $modelClass = 'backend\models\User';
    
    /*
    public function init() {
        parent::init();
        Yii::$app->user->enableSession = false;
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
        $giftCards = Gifcards::find()
            ->asArray()
            ->all();
        if (count($giftCards) > 0){
            return $this->correcto($giftCards);
        }
        return $this->error('Sin datos', 201);
    }

    public function actionCreate() {}
    
    public function actionVerificarcodigo() {
        $data = Yii::$app->request->post();
        $giftcardCode = $data['code'];
        $response = Gifcards::find()
            ->where("Code = '{$giftcardCode}' AND Status = 1")
            ->all();
        if (count($response)) {
            return $this->correcto($response);
        }
        return $this->error('No valido', 201);
    }

    public function actionUsegiftcard(){
        $giftCards = Gifcards::find()
            ->asArray()
            ->all();
        if (count($giftCards) > 0){
            return $this->correcto($giftCards);
        }
        return $this->error('Sin datos', 201);
    }

    public function actionValidargiftcard(){
        $data = Yii::$app->request->post();
        $serial = $data['serial'];
        //pa_validargiftcard
        $resultado = Yii::$app->db->createCommand("CALL pa_validargiftcard(:giftcard)")->bindParam(':giftcard', $serial)->queryAll();
        if (count($resultado) > 0){
            return $this->correcto($resultado);
        }
        else return $this->error('Sin datos', 201);
    }

}
