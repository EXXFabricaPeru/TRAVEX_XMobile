<?php

namespace backend\models\v2;

use Exception;
use stdClass;
use Yii;
use Carbon\Carbon;
use yii\base\Model;
use backend\models\hana;
use backend\models\v2\Sapenviodoc ;
use backend\models\v2\Common;
use backend\models\Evidenciacabecera;
use backend\models\Evidenciadetalle;
use backend\models\Servislayer;

//\app\models\Clientes
class Evidencias extends Model {
    private $hana;
    public function __construct() {
        $this->hana=New hana;
    }
    
    public function createEvidencias($evidencia){

        try {
            $cabecera = new Evidenciacabecera();
            $cabecera->DocEntry = $evidencia["cabecera"]["DocEntry"];
            $cabecera->idDocPedido = $evidencia["cabecera"]["idDocPedido"];
            $cabecera->fechasend = $evidencia["cabecera"]["fechasend"];
            $cabecera->idUser = $evidencia["cabecera"]["idUser"];
            $cabecera->U_LATITUD = $evidencia["cabecera"]["U_LATITUD"];
            $cabecera->U_LONGITUD = $evidencia["cabecera"]["U_LONGITUD"];
            $cabecera->CardCode = $evidencia["cabecera"]["CardCode"];
            $cabecera->save(false);



            if($evidencia["detalle"]["firma"] && $evidencia["detalle"]["firma"]!=''){

                $fileName="addPhoto.svg";
                preg_match("/data:image\/(.*?);/",$evidencia["detalle"]["firma"],$image_extension); // extract the image extension
                $image = preg_replace('/data:image\/(.*?);base64,/','',$evidencia["detalle"]["firma"]); // remove the type part
                $image = str_replace(' ', '+', $image);
                $fileName = $evidencia["cabecera"]["idDocPedido"]."-".uniqid().".jpg"; 
                //Yii::error("decode img ->".$image); 
                //Yii::error("decode img -> 2".json_encode($evidencia["detalle"]["firma"])); 
                file_put_contents ("../../api/web/imgs/evidencias/firmas/".$fileName, base64_decode($image));

                $detalle = new Evidenciadetalle();
                $detalle->idDocPedido = $evidencia["cabecera"]["idDocPedido"];
                $detalle->tipo_evidencia = 'F';
                $detalle->ruta = '../../api/web/imgs/evidencias/firmas/';
                $detalle->nombre = $fileName;
                $detalle->idCabecera=$cabecera->id;
                $detalle->save(false);
            }

            foreach ($evidencia["detalle"]["evidencia"] as $evidencias) {

                $fileName="addPhoto.svg";
                preg_match("/data:image\/(.*?);/",$evidencias["imagen"],$image_extension); // extract the image extension
                $image = preg_replace('/data:image\/(.*?);base64,/','',$evidencias["imagen"]); // remove the type part
                $image = str_replace(' ', '+', $image);
                $fileName = $evidencia["cabecera"]["idDocPedido"]."-".uniqid().".jpg"; 
                //Yii::error("decode img ->".$image); 
                //Yii::error("decode img -> 2".json_encode($evidencias["imagen"])); 
                file_put_contents ("../../api/web/imgs/evidencias/evidencia/".$fileName, base64_decode($image));

                $detalle = new Evidenciadetalle();
                $detalle->idDocPedido = $evidencia["cabecera"]["idDocPedido"];
                $detalle->tipo_evidencia = 'E';
                $detalle->ruta = '../../api/web/imgs/evidencias/evidencia/';
                $detalle->nombre = $fileName;
                $detalle->idCabecera=$cabecera->id;
                $detalle->save(false);
            }
            // actualiza en sap evidencia registrada
            if($cabecera->DocEntry!=""){
                //$datos=["U_xMOB_GCardSerie"=>"Firma/Evidencia"];
                $datos=["U_Entregado"=>"SI"];
                $serviceLayer = new Servislayer();
                $serviceLayer->actiondir = "DeliveryNotes(".$cabecera->DocEntry.")";
                $respuesta = $serviceLayer->executePatchPut('PATCH', $datos);
                Yii::error("Respuesta Evidencia:".$cabecera->DocEntry." -> ".json_encode($respuesta));
            }
            $respuestaP = [
                "estado" => 3,
                "registro"=>true,
                "mensaje"=>"Evidencias Registradas"
            ];

        } catch (Exception $e) {
            Yii::error($e);
            Yii::error("error al registrar en la base local en algun lado".json_encode($e));
            
            $respuestaP = [
                "estado" => 0,
                "registro"=>false,
                "mensaje"=>"A ocurrido un error al registrar la evidencia"
            ];
        }
         return $respuestaP;
       
    }
}
?>