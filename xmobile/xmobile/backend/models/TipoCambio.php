<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use backend\models\Servislayer;


class TipoCambio extends Model
{
    public function monedaLocal()
    {
        $model = new Servislayer();
        $model->actiondir = 'SBOBobService_GetLocalCurrency';
        $respuesta = $model->executePost('');
        $error = is_object($respuesta)?$respuesta->error:null;
        if (!is_null($error)) {
            return 0;
        }
        return $respuesta;
    }

    public function monedaSistema()
    {
        $model = new Servislayer();
        $model->actiondir = 'SBOBobService_GetSystemCurrency';
        $respuesta = $model->executePost('');
        $error = is_object($respuesta)?$respuesta->error:null;
        if (!is_null($error)) {
            return 0;
        }
        return $respuesta;
    }
}
