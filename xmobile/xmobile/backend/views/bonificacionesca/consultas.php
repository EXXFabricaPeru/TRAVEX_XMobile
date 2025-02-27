<?php

//use Yii;
//use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Bonificacionde1;
use backend\models\Bonificacionde2;
use backend\models\Bonificacionesca;
use yii\db\ActiveRecord;


header('Content-Type: application/json');
if(isset($_POST['CONDICION'])){
    switch ($_POST['CONDICION']) {
        case 'DETALLEBONIFICACION':
           $Data = ArrayHelper::map(backend\models\Bonificacionde1::find()
           ->where(['U_ID_bonificacion' => $_POST['ID']])
           ->all(), 'Code', 'Name');
           if(count($Data)){
               echo (json_encode ($Data));
           }
           else{
             echo "0";
           }
            break;
        case 'GUARDARDETALLEBONO':
            $datos=explode('@',$_POST['DATA']);

            Bonificacionde1::deleteAll(['U_ID_bonificacion' =>$_POST['ID']]);

            for($i=0;$i<count($datos)-1;$i++){
                $detalle=explode('//',$datos[$i]);
                $bonificacionde1 =  new Bonificacionde1();
                $bonificacionde1->load(Yii::$app->request->post()); 
                $bonificacionde1->Code=$detalle[0];
                $bonificacionde1->Name= utf8_decode( $detalle[1]);
                $bonificacionde1->U_ID_bonificacion=$_POST['ID'];
                $bonificacionde1->U_regla=$_POST['REGLATIPO'];
                $bonificacionde1->save();
            }
            echo "TRUE";
            break;
        case 'DETALLECOMPRA':
            /*$Data = ArrayHelper::map(backend\models\Bonificacionde2::find()
           ->where(['U_ID_bonificacion' => $_POST['ID']])
           ->all(), 'Code', 'Name');
          */
           $Data=Bonificacionde2::find()->where('U_ID_bonificacion='.$_POST['ID'])->asArray()->all();

           if(count($Data)){
              echo (json_encode ($Data));
            }
            else{
            echo "0";
            }
            break;
        case 'GUARDARDETALLECOMPRA':
            $datos=explode('@',$_POST['DATA']);

            Bonificacionde2::deleteAll(['U_ID_bonificacion' =>$_POST['ID']]);

            for($i=0;$i<count($datos)-1;$i++){
                $detalle=explode('//',$datos[$i]);

                if(isset($detalle[3])){
                   $cantidad=$detalle[2];
                   $estado=$detalle[3];
                }
                else{
                   $cantidad=0;
                   $estado=1;
                }
               
                $bonificacionde2 =  new Bonificacionde2();
                $bonificacionde2->load(Yii::$app->request->post()); 
                $bonificacionde2->Code=$detalle[0];
                $bonificacionde2->Name=$detalle[1];
                $bonificacionde2->Cantidad=$cantidad;
                $bonificacionde2->Estado=$estado;
                $bonificacionde2->U_ID_bonificacion=$_POST['ID'];
                $bonificacionde2->U_regla=$_POST['REGLATIPO'];
                $bonificacionde2->save();
            }
            echo "TRUE";
            break;
		case 'DUPLICARREGISTRO':
			
			$bonificacionesca = new Bonificacionesca();
			$bonificacionescaResult =  Bonificacionesca::findOne($_POST['ID']);
			
      $codigo=explode('.', $bonificacionescaResult->Code);
      if(isset($codigo[1])){
         $complemento=$codigo[1]+1;
      }
      else{
        $complemento=1;
      }
			$bonificacionesca->Code=$codigo[0].'.'.$complemento;
			$bonificacionesca->Name=$bonificacionescaResult->Name;
			$bonificacionesca->U_cliente=$bonificacionescaResult->U_cliente;
			$bonificacionesca->U_fecha=$bonificacionescaResult->U_fecha;
			$bonificacionesca->U_fecha_inicio=$bonificacionescaResult->U_fecha_inicio;
			$bonificacionesca->U_fecha_fin=$bonificacionescaResult->U_fecha_fin;
			$bonificacionesca->U_estado=$bonificacionescaResult->U_estado;
			$bonificacionesca->U_entrega=$bonificacionescaResult->U_entrega;
			$bonificacionesca->U_limitemaxregalo=$bonificacionescaResult->U_limitemaxregalo;
			$bonificacionesca->U_cantidadbonificacion=$bonificacionescaResult->U_cantidadbonificacion;
			$bonificacionesca->U_observacion=$bonificacionescaResult->U_observacion;
			$bonificacionesca->U_reglatipo=$bonificacionescaResult->U_reglatipo;
			$bonificacionesca->U_tipo=$bonificacionescaResult->U_tipo;
			$bonificacionesca->U_reglabonificacion=$bonificacionescaResult->U_reglabonificacion;
			$bonificacionesca->U_reglaunidad=$bonificacionescaResult->U_reglaunidad;
			$bonificacionesca->U_reglacantidad=$bonificacionescaResult->U_reglacantidad;
			$bonificacionesca->U_bonificaciontipo=$bonificacionescaResult->U_bonificaciontipo;
			$bonificacionesca->U_bonificacionunidad=$bonificacionescaResult->U_bonificacionunidad;
			$bonificacionesca->U_bonificacioncantidad=$bonificacionescaResult->U_bonificacioncantidad;
			$bonificacionesca->idTerritorio=$bonificacionescaResult->idTerritorio;
			$bonificacionesca->territorio=$bonificacionescaResult->territorio;
			$bonificacionesca->idUsuario=$bonificacionescaResult->idUsuario;
			$bonificacionesca->usuario=$bonificacionescaResult->usuario;
      $bonificacionesca->idBonificacionTipo=$bonificacionescaResult->idBonificacionTipo;
      $bonificacionesca->tipoReglaCompra=$bonificacionescaResult->tipoReglaCompra;
      $bonificacionesca->detalleEspecifico=$bonificacionescaResult->detalleEspecifico;
      $bonificacionesca->montoTotal=$bonificacionescaResult->montoTotal;
      $bonificacionesca->cantidadMaximaCompra=$bonificacionescaResult->cantidadMaximaCompra;
      $bonificacionesca->idReglaBonificacion=$bonificacionescaResult->idReglaBonificacion;
      $bonificacionesca->canalCode=$bonificacionescaResult->canalCode;
      $bonificacionesca->porcentajeDescuento=$bonificacionescaResult->porcentajeDescuento;
			$bonificacionesca->save();
			$idCabecera=$bonificacionesca->id;
			if(isset($idCabecera)){
				
				///adicionando bonificacion////
				$bonificacionde1Result = Bonificacionde1::find()->where('U_ID_bonificacion='.$bonificacionescaResult->id)->all();

				foreach($bonificacionde1Result as $value){
					
					$bonificacionde1 =  new Bonificacionde1();
					$bonificacionde1->Code=$value['Code'];
					$bonificacionde1->Name=$value['Name'];
					$bonificacionde1->U_ID_bonificacion=''.$idCabecera;
					$bonificacionde1->U_regla=$value['U_regla'];
					$bonificacionde1->save();
				}
				///adicionar compra////
				$bonificacionde2Result = Bonificacionde2::find()->where('U_ID_bonificacion='.$bonificacionescaResult->id)->all();

				foreach($bonificacionde2Result as $value){
					
					$bonificacionde2 =  new Bonificacionde2();
					$bonificacionde2->Code=$value['Code'];
					$bonificacionde2->Name=$value['Name'];
					$bonificacionde2->U_ID_bonificacion=''.$idCabecera;
					$bonificacionde2->U_regla=$value['U_regla'];
          $bonificacionde2->Cantidad=$value['Cantidad'];
          $bonificacionde2->Estado=$value['Estado'];
					$bonificacionde2->save();
				}
			}
            echo "TRUE";
            break;
		case 'CODIGOBONIFICACION':
           $Data = ArrayHelper::map(backend\models\Bonificacionesca::find()
           ->all(), 'id', 'Code');
           if(count($Data)){
               echo (json_encode ($Data));
           }
           else{
             echo "0";
           }
           break;
		
	
        default:
           echo "error! no existe condicion";
    }
}
else{
    echo "error! comuniquese con su administrador de sistemas";
}



?>

