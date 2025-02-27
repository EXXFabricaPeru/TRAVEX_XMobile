<?php

namespace api\controllers;

use api\traits\Respuestas;
use Yii;
use yii\rest\ActiveController;
use backend\models\Anulaciondocmovil;

class AnulaciondocmovilController extends ActiveController
{
  use Respuestas;
  
  public $modelClass = 'backend\models\Anulaciondocmovil';
  

  protected function verbs()
  {
    return [
      'index' => ['POST'],
      'view' => ['POST'],
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

  
  public function actionIndex(){	  
    
  
   
    /*$usuario = Yii::$app->request->post('usuariodataid');
    $header = Yii::$app->request->post('header');
    Yii::error($header);
    $docDate = $header[0]['DocDate'];
    $docType = $header[0]['DocType'];
    $docEntry =$header[0]['DocEntry'];
     Yii::error($usuario);
    
       Yii::error($docDate);
        Yii::error($docEntry);
*/
    /*$docDate = Yii::$app->request->post('DocDate');
    $docType = Yii::$app->request->post('DocType');
    $docEntry = Yii::$app->request->post('DocEntry');*/

    $datos= Yii::$app->request->post();
    Yii::error("DATA envion anulacion movil :: " . json_encode($datos));
    $swRegistro=false;

    foreach ($datos As $cabecera) { //FOR 1

      $usuario=$cabecera["usuariodataid"];
      Yii::error("Datos77");
      Yii::error($cabecera["header"]);

      if(isset($cabecera["header"])){//IF 01
        Yii::error("Detalle77");
        Yii::error($cabecera["header"]["DocDate"]);
        //foreach ($cabecera["header"] as $key=> $detalle) {//FOR 2
          
          $docDate = $cabecera["header"]["DocDate"];
          $docType = $cabecera["header"]["DocType"];
          $docNum = $cabecera["header"]["DocNum"];
          $docEntry = $cabecera["header"]["U_DOCENTRY"];
          $motivoAnulacionComentario = $cabecera["header"]["U_4MOTIVOCANCELADO"];
          $motivoAnulacion = $cabecera["header"]["U_4MOTIVOCANCELADOCABEZERA"];

          

          //Yii::error("Detalle77");
          /*Yii::error($docDate);
          Yii::error($docType);
          Yii::error($docEntry);
*/

         // Yii::error($key);
          if($docDate!=null && $docType!=null && $docEntry!=null && $usuario!=null){//IF 1.1
            $dataUser = Yii::$app->db->createCommand("SELECT username FROM user WHERE id = ".$usuario)->queryOne();
            $user='-';
            if($dataUser['username']!=null){
              $user=$dataUser['username'];
            }
            $anulaciondocmovil = new Anulaciondocmovil();
            $anulaciondocmovil->id = 0;
            $anulaciondocmovil->fechaRegistro = date('Y-m-d H:i:s');
            $anulaciondocmovil->usuario = $user;
            $anulaciondocmovil->docDate = $docDate;
            $anulaciondocmovil->docType = $docType;
            $anulaciondocmovil->docEntry = $docEntry;
            $anulaciondocmovil->motivoAnulacion = $motivoAnulacion;
            $anulaciondocmovil->estado = 6;
            $anulaciondocmovil->idUser = $usuario;
            $anulaciondocmovil->docNum = $docNum;
            $anulaciondocmovil->motivoAnulacionComentario = $motivoAnulacionComentario;
            
            $dataAnulacion = Yii::$app->db->createCommand("SELECT count(*) as cantidad FROM anulaciondocmovil WHERE docEntry = '".$docEntry."'")->queryOne();
            if($dataAnulacion['cantidad']==0){//IF 1.2
                if($anulaciondocmovil->save(false)){//IF 1.3
              
                  $swRegistro=true;
                }
                else{
                   $swRegistro=false;
                }//IF 1.3
            }
            else{
              $swRegistro=true;
            }//IF 1.2

            
          }
          else{
              return $this->correcto(false,"Error! valores nulos",100);
          }//IF 1.1
        //}//FOR 2
      }
      else{
         return $this->correcto(false,"Error! en la cabecera del registro",100);

      }//IF 01
    }//FOR 1
    if($swRegistro){
       return $this->correcto(true, "Anulacion registrada");
    }
    else{
       return $this->correcto(false,"Error al registrar anulacion",100);
        
    }
	
  }

}
