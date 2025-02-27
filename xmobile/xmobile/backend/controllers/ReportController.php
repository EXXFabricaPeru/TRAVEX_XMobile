<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\User;
use backend\models\Usuariopersona;
use kartik\mpdf\Pdf;

class ReportController extends Controller{
	
  public function actionVenta($usuario,$inicio,$fin){
    date_default_timezone_set('America/La_Paz');      
    if (!isset($inicio)) {
        $inicio=date('y-m-d');
    }
    if (!isset($fin)) {
        $fin=date('y-m-d');
    }
    $tipo=1;
        // Totales
        $resumen = Yii::$app->db->createCommand("CALL pa_reporte_venta(:usuario, :inicio, :fin)")
            ->bindValue(':usuario',$usuario)
            ->bindValue(':inicio',$inicio)
            ->bindValue(':fin',$fin)
        ->queryAll();
        // DOF
        $detalleDOF = Yii::$app->db->createCommand("CALL pa_lista_documentos_por_fecha(:usuario, :tipo, :inicio, :fin)")
            ->bindValue(':usuario',$usuario)
            ->bindValue(':tipo','DOF')
            ->bindValue(':inicio',$inicio)
            ->bindValue(':fin',$fin)
        ->queryAll();
        // DOP
        $detalleDOP = Yii::$app->db->createCommand("CALL pa_lista_documentos_por_fecha(:usuario, :tipo, :inicio, :fin)")
            ->bindValue(':usuario',$usuario)
            ->bindValue(':tipo','DOP')
            ->bindValue(':inicio',$inicio)
            ->bindValue(':fin',$fin)
        ->queryAll();
        // DFA
        $detalleDFA = Yii::$app->db->createCommand("CALL pa_lista_documentos_por_fecha(:usuario, :tipo, :inicio, :fin)")
            ->bindValue(':usuario',$usuario)
            ->bindValue(':tipo','DFA')
            ->bindValue(':inicio',$inicio)
            ->bindValue(':fin',$fin)
        ->queryAll();
        // DOE
        $detalleDOE = Yii::$app->db->createCommand("CALL pa_lista_documentos_por_fecha(:usuario, :tipo, :inicio, :fin)")
            ->bindValue(':usuario',$usuario)
            ->bindValue(':tipo','DOE')
            ->bindValue(':inicio',$inicio)
            ->bindValue(':fin',$fin)
        ->queryAll();
        $usuarioBD = User::find()->where(['id' => $usuario])->one();
        $usuarioPersona = Usuariopersona::find()->where(['idPersona' => $usuarioBD->idPersona])->one();
	    $content = $this->renderPartial('index',[
            "usuario" =>  $usuarioPersona->nombrePersona . " " . $usuarioPersona->apellidoPPersona . " " . $usuarioPersona->apellidoMPersona,
            "tipoReporte" => $tipo,
            "resumen" => $resumen,
            "detalleDOF" => $detalleDOF,
            "detalleDOP" => $detalleDOP,
            "detalleDFA" => $detalleDFA,
            "detalleDOE" => $detalleDOE,
            "fini" => $inicio,
            "ffin" => $fin
            ]);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Exxis-Bolivia'],
			'destination'  => Pdf::DEST_DOWNLOAD,
            'methods' => [
                'SetHeader' => ['Exxis-Bolivia'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
		$pdf->filename = 'filename.pdf';
        return $pdf->render();
  }
  
  public function actionCaja($usuario){  
    date_default_timezone_set('America/La_Paz');      
    $inicio=date('y-m-d');
    $fin=date('y-m-d');
    $caja = Yii::$app->db->createCommand('CALL pa_reporteCaja(:usuario,:equipo,:sucursal,:detalle,:fini,:ffin)')
            ->bindValues([
                ':usuario' => $usuario,
                ':equipo'=>0,
                ':sucursal'=>0,
                ':detalle'=>0,
                ':fini' => $inicio,
                ':ffin' => $fin
            ])->queryAll(); 

    $caja2 = Yii::$app->db->createCommand('CALL pa_reporteCaja2(:usuario,:fini,:ffin)')
          ->bindValues([
                ':usuario' => $usuario,
                ':fini' => $inicio,
                ':ffin' => $fin
            ])->queryAll(); 
    $caja3 = Yii::$app->db->createCommand('CALL pa_reporteCaja3(:usuario,:fini,:ffin)')
          ->bindValues([
                ':usuario' => $usuario,
                ':fini' => $inicio,
                ':ffin' => $fin
            ])->queryAll();     
    $caja4 = Yii::$app->db->createCommand('CALL pa_reporteCaja4(:usuario,:fini,:ffin)')
            ->bindValues([
                  ':usuario' => $usuario,
                  ':fini' => $inicio,
                  ':ffin' => $fin
              ])->queryAll();  
    
    $content = $this->renderPartial('index',[
                "tipoReporte" => 2,
                "caja" => $caja,
                "caja2" => $caja2,
                "caja3" => $caja3,
                "caja4" => $caja4,
                "fini" => $inicio,
                "ffin" => $fin
            ]);

    $pdf = new Pdf([
        'mode' => Pdf::MODE_CORE,
        'format' => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,
        'content' => $content,
        'cssInline' => '.kv-heading-1{font-size:18px}',
        'options' => ['title' => 'Exxis-Bolivia'],
        'methods' => [
            'SetHeader' => ['Exxis-Bolivia'],
            'SetFooter' => ['{PAGENO}'],
        ]
    ]);
	$pdf->filename = 'filename.pdf';
    return $pdf->render();
}


  public function actionCaja2($usuario, $inicio, $fin){        

        $caja2 = Yii::$app->db->createCommand('CALL pa_reporteCaja2(:usuario,:fini,:ffin)')
              ->bindValues([
                    ':usuario' => $usuario,
                    ':fini' => $inicio,
                    ':ffin' => $fin
                ])->queryAll();      
		
    	$content = $this->renderPartial('index',[
                    "tipoReporte" => 2,
                    "caja" => $caja2
                ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Exxis-Bolivia'],
            'methods' => [
                'SetHeader' => ['Exxis-Bolivia'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
  }

    public function actionCaja3($usuario, $inicio, $fin){        

    $caja3 = Yii::$app->db->createCommand('CALL pa_reporteCaja3(:usuario,:fini,:ffin)')
            ->bindValues([
                ':usuario' => $usuario,
                ':fini' => $inicio,
                ':ffin' => $fin
            ])->queryAll();      

    $content = $this->renderPartial('index',[
                "tipoReporte" => 3,
                "caja" => $caja3
            ]);

    $pdf = new Pdf([
        'mode' => Pdf::MODE_CORE,
        'format' => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,
        'content' => $content,
        'cssInline' => '.kv-heading-1{font-size:18px}',
        'options' => ['title' => 'Exxis-Bolivia'],
        'methods' => [
            'SetHeader' => ['Exxis-Bolivia'],
            'SetFooter' => ['{PAGENO}'],
        ]
    ]);
    return $pdf->render();
    }

    public function actionCaja4($usuario, $inicio, $fin){        

        $caja4 = Yii::$app->db->createCommand('CALL pa_reporteCaja4(:usuario,:fini,:ffin)')
                ->bindValues([
                    ':usuario' => $usuario,
                    ':fini' => $inicio,
                    ':ffin' => $fin
                ])->queryAll();      
        
        $content = $this->renderPartial('index',[
                    "tipoReporte" => 4,
                    "caja" => $caja4
                ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Exxis-Bolivia'],
            'methods' => [
                'SetHeader' => ['Exxis-Bolivia'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }
  
  /*
  http://10.147.17.61:8082/01Elite/backend/web/index.php?r=report/itemventas&usuario=74&inicio=2020-06-01&fin=2020-06-17&tipo=1
  */
    public function actionItemventas($usuario, $tipo='', $itemcode='', $centro='', $vendedor, $inicio, $fin){
        $sql = "SELECT * FROM `vi_reporteitemventa`";
        if ($inicio && $fin) {
          $sql .= " WHERE idUser = ".$usuario." AND SlpCode = '".$vendedor."' AND DocDate BETWEEN '" . $inicio . "' AND '" . $fin . "'";
        } 
        if ($itemcode) {
          $sql .= " AND ItemCode LIKE '" . $id . "'";
        }
        if ($tipo) {
          $sql .= " AND DocType LIKE '" . $tipo . "'";
        }
        if ($centro) {
          $sql .= " AND producto_std1 LIKE '" . $centro . "'";
        }
        $sql .= ";";
        $respuesta = Yii::$app->db->createCommand($sql)
            ->queryAll();
        $usuarioBD = User::find()->where(['id' => $usuario])->one();
        $usuarioPersona = Usuariopersona::find()->where(['idPersona' => $usuarioBD->idPersona])->one();
        $content = $this->renderPartial('index',[
          "usuario" =>  $usuarioPersona->nombrePersona . " " . $usuarioPersona->apellidoPPersona . " " . $usuarioPersona->apellidoMPersona,
          "tipoReporte" => 4,
          "reporte" => $respuesta,
          "fini" => Yii::$app->formatter->asDate($inicio, 'dd/MM/yyyy'),
          "ffin" =>  Yii::$app->formatter->asDate($fin, 'dd/MM/yyyy')
          ]);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Exxis-Bolivia'],
			'destination'  => Pdf::DEST_DOWNLOAD,
            'methods' => [
                'SetHeader' => ['Exxis-Bolivia'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
		$pdf->filename = 'filename.pdf';
        return $pdf->render();
    }

    /*
    http://10.147.17.61:8082/01Elite/backend/web/index.php?r=report/arqueocaja&usuario=74&inicio=2020-01-27&fin=2020-08-06
    */
    public function actionArqueocaja($usuario, $inicio, $fin) {
        $sql = "SELECT * FROM vi_arqueocaja WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
        $arqueo = Yii::$app->db->createCommand($sql)
            ->bindValue(':usuario' , $usuario)
            ->bindValue(':inicial' , $inicio)
            ->bindValue(':final' , $fin)
            ->queryAll();

        
        $sql2 = "SELECT * FROM vi_arqueodetalle WHERE usuario = :usuario AND (fecha >= :inicial AND fecha <= :final)";
        $detalle = Yii::$app->db->createCommand($sql2)
            ->bindValue(':usuario' , $usuario)
            ->bindValue(':inicial' , $inicio)
            ->bindValue(':final' , $fin)
            ->queryAll();

        $usuarioBD = User::find()->where(['id' => $usuario])->one();
        $usuarioPersona = Usuariopersona::find()->where(['idPersona' => $usuarioBD->idPersona])->one();
        $content = $this->renderPartial('index',[
          "usuario" =>  $usuarioPersona->nombrePersona . " " . $usuarioPersona->apellidoPPersona . " " . $usuarioPersona->apellidoMPersona,
          "tipoReporte" => 5,
          "arqueo" => $arqueo,
          "detalle" => $detalle,
          "fini" => Yii::$app->formatter->asDate($inicio,'dd/MM/yyyy'),
          "ffin" => Yii::$app->formatter->asDate($fin,'dd/MM/yyyy')
          ]);

        $pdf = new Pdf([
        'mode' => Pdf::MODE_CORE,
        'format' => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,
        'content' => $content,
        'cssInline' => '.kv-heading-1{font-size:18px}',
        'options' => ['title' => 'Exxis-Bolivia'],
        'destination'  => Pdf::DEST_DOWNLOAD,
        'methods' => [
            'SetHeader' => ['Exxis-Bolivia'],
            'SetFooter' => ['{PAGENO}'],
        ]
        ]);
		$pdf->filename = 'filename.pdf';
        return $pdf->render();
    }

    /*
    localhost/MIDBOLIVIA/backend/web/index.php?r=report/resumenventas&usuario=74&inicio=2020-01-27&fin=2020-08-06
    */
    public function actionResumenventas($usuario, $inicio, $fin) {
        $sql = "CALL pa_resumenVenta(:usuario, :inicio, :fin)";
        $resumen = Yii::$app->db->createCommand($sql)
            ->bindValue(':usuario' , $usuario)
            ->bindValue(':inicio' , $inicio)
            ->bindValue(':fin' , $fin)
        ->queryAll();

        $usuarioBD = User::find()->where(['id' => $usuario])->one();
        $usuarioPersona = Usuariopersona::find()->where(['idPersona' => $usuarioBD->idPersona])->one();

        $content = $this->renderPartial('index',[
            "usuario" =>  $usuarioPersona->nombrePersona . " " . $usuarioPersona->apellidoPPersona . " " . $usuarioPersona->apellidoMPersona,
            "tipoReporte" => 6,
            "resumen" => $resumen,
            "fini" => Yii::$app->formatter->asDate($inicio,'dd/MM/yyyy'),
            "ffin" => Yii::$app->formatter->asDate($fin,'dd/MM/yyyy')
            ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Exxis-Bolivia'],
            'destination'  => Pdf::DEST_DOWNLOAD,
            'methods' => [
                'SetHeader' => ['Exxis-Bolivia'],
                'SetFooter' => ['{PAGENO}'],
            ]
            ]);
            $pdf->filename = 'filename.pdf';
            return $pdf->render();
    }

    /*
    localhost/MIDBOLIVIA/backend/web/index.php?r=report/cierrediario&usuario=74&inicio=2020-01-27&fin=2020-08-06
    */
    public function actionCierrediario($usuario, $inicio, $fin) {
        $usuarioBD = User::find()->where(['id' => $usuario])->one();
        $usuarioPersona = Usuariopersona::find()->where(['idPersona' => $usuarioBD->idPersona])->one();

        $sql = "CALL pa_cierreDiario(:usuario, :inicio, :fin)";
        $cabecera = Yii::$app->db->createCommand($sql)
            ->bindValue(':usuario' , $usuario)
            ->bindValue(':inicio' , $inicio)
            ->bindValue(':fin' , $fin)
        ->queryAll();
        $sql2 = "CALL pa_cierreDiarioDetalle(:usuario, :inicio, :fin)";
        $detalle = Yii::$app->db->createCommand($sql2)
            ->bindValue(':usuario' , $usuario)
            ->bindValue(':inicio' , $inicio)
            ->bindValue(':fin' , $fin)
        ->queryAll();

        $content = $this->renderPartial('index',[
            "usuario" =>  $usuarioPersona->nombrePersona . " " . $usuarioPersona->apellidoPPersona . " " . $usuarioPersona->apellidoMPersona,
            "tipoReporte" => 7,
            "cabecera" => $cabecera,
            "detalle" => $detalle,
            "fini" => Yii::$app->formatter->asDate($inicio,'dd/MM/yyyy'),
            "ffin" => Yii::$app->formatter->asDate($fin,'dd/MM/yyyy')
            ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Exxis-Bolivia'],
            'destination'  => Pdf::DEST_DOWNLOAD,
            'methods' => [
                'SetHeader' => ['Exxis-Bolivia'],
                'SetFooter' => ['{PAGENO}'],
            ]
            ]);
            $pdf->filename = 'filename.pdf';
        return $pdf->render();
    }

    /*
    localhost/MIDBOLIVIA/backend/web/index.php?r=report/resumeninventario&usuario=74&unidad=64&inicio=2020-01-27&fin=2020-08-06
    */
    public function actionResumeninventario($usuario, $inicio, $fin, $unidad) {
        $usuarioBD = User::find()->where(['id' => $usuario])->one();
        $usuarioPersona = Usuariopersona::find()->where(['idPersona' => $usuarioBD->idPersona])->one();

        $sql = "CALL pa_resumenInventario(:unidad)";
        $resumen = Yii::$app->db->createCommand($sql)
            ->bindValue(':unidad' , (int)$unidad)
        ->queryAll();

        if(count($resumen)<=0){
            $resumen = [];
        }

        $content = $this->renderPartial('index',[
            "usuario" =>  $usuarioPersona->nombrePersona . " " . $usuarioPersona->apellidoPPersona . " " . $usuarioPersona->apellidoMPersona,
            "tipoReporte" => 8,
            "resumen" => $resumen,
            "fini" => Yii::$app->formatter->asDate($inicio,'dd/MM/yyyy'),
            "ffin" => Yii::$app->formatter->asDate($fin,'dd/MM/yyyy')
            ]);


        // return print_r($content);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Exxis-Bolivia'],
            'destination'  => Pdf::DEST_DOWNLOAD,
            'methods' => [
                'SetHeader' => ['Exxis-Bolivia'],
                'SetFooter' => ['{PAGENO}'],
            ]
            ]);

        $pdf->filename = 'filename.pdf';
        return $pdf->render();
    }

}
