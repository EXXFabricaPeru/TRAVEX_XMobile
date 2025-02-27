<?php

namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use backend\models\Servislayer;
use yii\filters\auth\QueryParamAuth;
use api\traits\Respuestas;
use backend\models\Almacenes;
use Carbon\Carbon;

class ReporteController extends ActiveController
{

  use Respuestas;

  public $modelClass = 'backend\models\Usuario';

  /*public function init()
  {
    parent::init();
    \Yii::$app->user->enableSession = false;
  }

  public function behaviors()
  {
    $behaviors = parent::behaviors();
    $behaviors['authenticator'] = [
      'tokenParam' => 'access-token',
      'class' => QueryParamAuth::className(),
    ];
    return $behaviors;
  }*/

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

  public function actionIndex()
  {
    $reporte = Yii::$app->db->createCommand('CALL pa_reporte_pedido_cliente()')
      ->queryAll();
    if (count($reporte) > 0) {
      return $this->correcto($reporte);
    }
    return $this->error('Sin datos', 201);
  }

  public function actionCliente()
  {
    $reporte = Yii::$app->db->createCommand("CALL pa_ubicacioncliente(:texto,:territorio,:industria,:vendedor,:lunes,:martes,:miercoles,:jueves,:viernes,:sabado,:domingo)")
      ->bindValues([
        ':texto'    => Yii::$app->request->post('texto', ''),
        ':territorio'    => Yii::$app->request->post('territorio', 0),
        ':industria' => Yii::$app->request->post('industria', 0),
        ':vendedor' => Yii::$app->request->post('vendedor', 0),
        ':lunes'   => Yii::$app->request->post('lunes', 0),
        ':martes'       => Yii::$app->request->post('martes', 0),
        ':miercoles'       => Yii::$app->request->post('miercoles', 0),
        ':jueves'       => Yii::$app->request->post('jueves', 0),
        ':viernes'       => Yii::$app->request->post('viernes', 0),
        ':sabado'       => Yii::$app->request->post('sabado', 0),
        ':domingo'       => Yii::$app->request->post('domingo', 0),
      ])
      ->queryAll();
    if (count($reporte) > 0) {
      return $this->correcto($reporte);
    }
    return $this->error('Sin datos', 201);
  }

  public function actionTodos()
  { // servicio para todos los documentos abiertos o cerrados
    $tipo = Yii::$app->request->post('tipo');
    if ($tipo) {
      $sql = "SELECT * FROM `vi_documentosimportados_todos` WHERE DocType LIKE '" . $tipo . "'";
      $inicio = Yii::$app->request->post('inicio');
      $fin = Yii::$app->request->post('fin');
      $cliente = Yii::$app->request->post('cliente');
      if ($inicio && $fin) {
        $sql .= " AND DocDate BETWEEN '" . $inicio . "' AND '" . $fin . "'";
      } /* else {
        $sql .= " AND DocDate LIKE '" . Carbon::today()->format('Y-m-d') . "'";
      } */
      if ($cliente) {
        $sql .= " AND CardName LIKE '%" . $cliente . "%'";
      }
      $sql .= ";";
      $respuesta = Yii::$app->db->createCommand($sql)
        ->queryAll();
      if (count($respuesta) > 0) {
        return $this->correcto($respuesta);
      }
      return $this->error('Sin datos', 201);
    } else {
      return $this->error('No se recibió tipo de dato', 201);
    }
  }

  public function actionLocales()
  {
    $tipo = Yii::$app->request->post('tipo');
    if ($tipo) {
      $sql = "SELECT * FROM `vi_documentosimportados` WHERE DocType LIKE '" . $tipo . "'";
      $inicio = Yii::$app->request->post('inicio');
      $fin = Yii::$app->request->post('fin');
      $cliente = Yii::$app->request->post('cliente');
      if ($inicio && $fin) {
        $sql .= " AND DocDate BETWEEN '" . $inicio . "' AND '" . $fin . "'";
      } /* else {
        $sql .= " AND DocDate LIKE '" . Carbon::today()->format('Y-m-d') . "'";
      } */
      if ($cliente) {
        $sql .= " AND CardCode LIKE '%" . $cliente . "%'";
      }
      $sql .= ";";
      $respuesta = Yii::$app->db->createCommand($sql)
        ->queryAll();
      if (count($respuesta) > 0) {
        return $this->correcto($respuesta);
      }
      return $this->error('Sin datos', 201);
    } else {
      return $this->error('No se recibió tipo de dato', 201);
    }
  }

  public function actionDetallelocales() {
    $Doctype = Yii::$app->request->post('DocType');
    $DocNum = Yii::$app->request->post('DocNum');
    $DocEntry = Yii::$app->request->post('DocEntry');
    if ($Doctype && $DocNum && $DocEntry) {
      $sql = "SELECT * FROM `vi_documentosimportadosdetalle` WHERE Doctype LIKE '" . $Doctype . "' AND DocNum = " . $DocNum . " AND DocEntry = " . $DocEntry;
      $detalle = Yii::$app->db->createCommand($sql)
        ->queryAll();
      if (count($detalle) > 0) {
        return $this->correcto($detalle);
      }
      return $this->error('Sin datos', 201);
    } else {
      return $this->error('No complete parameters', 201);
    }
  }

  public function actionReportecaja(){
    $tipo = Yii::$app->request->post('tipo');
    $id = Yii::$app->request->post('id');
    $fini = Yii::$app->request->post('fini');
    $ffin = Yii::$app->request->post('ffin');
    $xusuario = 0;
    $xequipo = 0;
    $xsucursal = 0;
    switch($tipo){
      case '1':
        $xusuario = $id;
        break;
      case '2':
        $xequipo = $id;
        break;
      default:
        $xsucursal = $id;
        break;
    }
    $resumen = Yii::$app->db->createCommand('CALL pa_reporteCaja(:usuario,:equipo,:sucursal,0,:fini,:ffin)')
          ->bindValues([
              ':usuario'    => $xusuario,
              ':equipo'    => $xequipo,
              ':sucursal' => $xsucursal,
              ':fini' => $fini,
              ':ffin' => $ffin
          ])
          ->queryAll();

    $detalle = Yii::$app->db->createCommand('CALL pa_reporteCaja(:usuario,:equipo,:sucursal,1,:fini,:ffin)')
          ->bindValues([
              ':usuario'    => $xusuario,
              ':equipo'    => $xequipo,
              ':sucursal' => $xsucursal,
              ':fini' => $fini,
              ':ffin' => $ffin
          ])
          ->queryAll();
    $resultado = [
      'Resumen' => [],
      'Detalle' => []
    ];
    if (count($resumen) > 0) {
      $resultado["Resumen"] = $resumen;
    }
    if (count($detalle) > 0) {
      $resultado["Detalle"] = $detalle;
    }
    if (count($resumen) > 0 || count($detalle) > 0 ){
      return $this->correcto($resultado);
    }
    else return $this->error('Sin datos', 201);
  }
  public function actionReportecaja2(){
    
    $id = Yii::$app->request->post('usuario');
    $fini = Yii::$app->request->post('f1');
    $ffin = Yii::$app->request->post('f2');
    
    $resumen = Yii::$app->db->createCommand('CALL pa_reporteCaja2(:xusuario,:fini,:ffin)')
          ->bindValues([
              ':xusuario'    => $id,              
              ':fini' => $fini,
              ':ffin' => $ffin
          ])
          ->queryAll();
   
    if (count($resumen) > 0 ){
      return $this->correcto($resumen);
    }
    else return $this->error('Sin datos', 201);
  }
  public function actionReportecaja3(){
    
    $id = Yii::$app->request->post('usuario');
    $fini = Yii::$app->request->post('f1');
    $ffin = Yii::$app->request->post('f2');
    
    $resumen = Yii::$app->db->createCommand('CALL pa_reporteCaja3(:usuario,:fini,:ffin)')
          ->bindValues([
              ':usuario'    => $id,              
              ':fini' => $fini,
              ':ffin' => $ffin
          ])
          ->queryAll();
   
    if (count($resumen) > 0 ){
      return $this->correcto($resumen);
    }
    else return $this->error('Sin datos', 201);
  }
  public function actionReportecaja4(){
    
    $id = Yii::$app->request->post('usuario');
    $fini = Yii::$app->request->post('f1');
    $ffin = Yii::$app->request->post('f2');
    
    $resumen = Yii::$app->db->createCommand('CALL pa_reporteCaja4(:usuario,:fini,:ffin)')
          ->bindValues([
              ':usuario'    => $id,              
              ':fini' => $fini,
              ':ffin' => $ffin
          ])
          ->queryAll();
   
    if (count($resumen) > 0 ){
      return $this->correcto($resumen);
    }
    else return $this->error('Sin datos', 201);
  }

  public function actionCajero() {
    $reporte = Yii::$app->db->createCommand('CALL pa_reporteCaja("74", "2", "2", "1", "2020-06-15", "2020-06-16")')
      ->queryAll();
    if (count($reporte) > 0) {
      return $this->correcto($reporte);
    }
    return $this->error('Sin datos',201);
  }

  public function actionHtml() {
    $pagina = "<h1>Hello World</h1><p>Prueba HTML por servicio</p>";
    return $pagina;
  }

}
