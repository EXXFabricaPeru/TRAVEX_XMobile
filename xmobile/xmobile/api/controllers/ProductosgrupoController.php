<?php

namespace api\controllers;

use yii\rest\ActiveController;

class ProductosgrupoController extends ActiveController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
