<?php

namespace api\controllers;

use backend\models\Persona;
use yii;
use yii\rest\ActiveController;

class ImageController extends ActiveController {

    public $modelClass = 'backend\models\Usuariopersona';

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
        //unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        $this->enableCsrfValidation = false;
        if (isset($_FILES['file']['name'])) {
            $upload_folder = Yii::getAlias('@webroot') . '/upload/normal/';
            $nombre_archivo = $_FILES['file']['name'];
            $tipo_archivo = $_FILES['file']['type'];
            $tamano_archivo = $_FILES['file']['size'];
            $tmp_archivo = $_FILES['file']['tmp_name'];
            $archivador = $upload_folder . '/' . $nombre_archivo;
            if (move_uploaded_file($tmp_archivo, $archivador)) {
                $upload = new \backend\models\Upload();
                $upload->xs($nombre_archivo);
                $upload->md($nombre_archivo);
                $upload->crop($nombre_archivo);
                return json_encode(['resp' => $nombre_archivo]);
            } else {
                echo json_encode(['resp' => true]);
            }
        } else {
            echo json_encode(['resp' => false]);
        }


        /* $porciones = explode("/", $_FILES["file"]["type"]);
          $uploadPath = Yii::getAlias('@webroot') . '/upload/normal/';
          $nombreNuevo = uniqid(time(), true) . '.' . $porciones[1];
          move_uploaded_file($_FILES["file"]["tmp_name"], $uploadPath . $nombreNuevo);
          $upload = new \backend\models\Upload();
          $upload->xs($nombreNuevo);
          $upload->md($nombreNuevo);
          return $nombreNuevo; */
    }

}
