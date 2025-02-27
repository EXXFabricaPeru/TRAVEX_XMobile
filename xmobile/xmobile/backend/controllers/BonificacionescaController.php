<?php

namespace backend\controllers;

use Yii;
use backend\models\Bonificacionesca;
use backend\models\Bonificaciontipo;
use backend\models\BonificacionescaSearch;
use backend\models\Bonificacionde1;
use backend\models\Bonificacionde2;
use backend\models\Bonificacionterritorio;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * BonificacionescaController implements the CRUD actions for Bonificacionesca model.
 */
  
class BonificacionescaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','consultas','getdetallebonificacion','detalleespecifico','tiporeglacompra','obtenercantdetalle','armahojacalculo','cellColor','itembonificacioncompra'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Bonificacionesca models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BonificacionescaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       
      //$this->Armahojacalculo();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bonificacionesca model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    
    public function actionReport($id) {
        $content = $this->renderPartial('view', [
            'model' => $this->findModel($id),
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
    
    
    
    /**
     * Creates a new Bonificacionesca model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Bonificacionesca();
       
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
              //se guarda territorios de bonificaciones
                $territorioValor= explode('@',$model->territorio);
                for($i=0; $i<count($territorioValor)-1;$i++){
                    $territorio=explode('=>',$territorioValor[$i]);
                    $bonificacionterritorio = new Bonificacionterritorio();
                    $bonificacionterritorio->idCabecera=$model->id;
                    $bonificacionterritorio->idTerritorio=$territorio[0];
                    $bonificacionterritorio->territorio=$territorio[1];
                    $bonificacionterritorio->save();
                    Yii::error("SE INSERTA DETALLE");
                }
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        return $this->renderPartial('create', [
                    'model' => $model,
        ]);
    }
    
    /**
     * Updates an existing Bonificacionesca model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $borrar = Bonificacionterritorio::find()->where("idCabecera = ".$model->id)->all();
                foreach($borrar as $valor) $valor->delete(); 

                 //se guarda los territorios de las bonificaciones
                $territorioValor= explode('@',$model->territorio);
                for($i=0; $i<count($territorioValor)-1;$i++){
                    $territorio=explode('=>',$territorioValor[$i]);
                    $bonificacionterritorio = new Bonificacionterritorio();
                    $bonificacionterritorio->idCabecera=$model->id;
                    $bonificacionterritorio->idTerritorio=$territorio[0];
                    $bonificacionterritorio->territorio=$territorio[1];
                    $bonificacionterritorio->save();
                    Yii::error("SE INSERTA TERRITORIO DE BONIFICACION");
                }
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        return $this->renderPartial('update', ['model' => $model]);
    }
    
    
    /**
     * Deletes an existing Bonificacionesca model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionEliminar($id) {
      $borrar = Bonificacionterritorio::find()->where("idCabecera = ".$id)->all();
      foreach($borrar as $valor) $valor->delete(); 
      
      return $this->findModel($id)->delete();
    }
    
    /**
     * Finds the Bonificacionesca model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bonificacionesca the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bonificacionesca::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionConsultas(){
       return $this->renderPartial('consultas', ['model' => $model]);
      // return $this->renderPartial(['consultas']);
      // return $this->render();
      // return $this->renderPartial();
    }

    public function actionGetdetallebonificacion(){
        Yii::error("Entrada a texto detalle bonificacion");
        $datos = Yii::$app->request->post();
        Yii::error($datos);
        $bonificacionde1 = Bonificacionde1::find()->where('U_ID_bonificacion = '.$datos['id'])->asArray()->all();
        $bonificacionde2 = Bonificacionde2::find()->where('U_ID_bonificacion = '.$datos['id'])->asArray()->all();
        Yii::error($bonificacionde1);
        $bono=count($bonificacionde1);
        $compra=count($bonificacionde2);
        $valor=[$bono,$compra];
        return json_encode($valor);
    }


    public function actionDetalleespecifico() {
        Yii::error("Entrada a texto Detalle Especifico");
        $datos = Yii::$app->request->post();
        $bonificaciontipo = Bonificaciontipo::find()->where('idTipoRegla='.$datos['tipoRegla'].' AND estado=1')->asArray()->all();
        Yii::error($bonificaciontipo);
        return json_encode($bonificaciontipo);
       
    }

    public function actionTiporeglacompra() {
        Yii::error("Entrada a texto Tipo Regla Compra");
        $datos = Yii::$app->request->post();
        $bonificaciontipo = Bonificaciontipo::find()->where("detalle='".$datos['detalle']."' and idTipoRegla=".$datos['tipoRegla'])->limit(1)->asArray()->all();
        Yii::error($bonificaciontipo);
        return json_encode($bonificaciontipo);
       
    }

    public function actionObtenercantdetalle(){
      $datos = Yii::$app->request->post();
      Yii::error($datos);
      $dataCantidad = Yii::$app->db->createCommand('SELECT SUM(bonificacion_de2.Cantidad) as cantidad from bonificacion_de2
      where bonificacion_de2.U_ID_bonificacion='.$datos['idCabecera'])->queryOne();
      Yii::error($dataCantidad);
      return json_encode($dataCantidad);
    }
    //verifica si un item esta en una bonificaion
    public function actionItembonificacioncompra(){
      $datos = Yii::$app->request->post();
      Yii::error($datos);
      $data = Yii::$app->db->createCommand("SELECT * from vi_comprabonificacion
      where item='".$datos['item']."'")->queryOne();
      Yii::error($data);
      return json_encode($data);
    }
//////////////////////////////////////EXPORTA EXCEL///////////////////////////////////////////////
    public function actionArmahojacalculo(){
      if(isset($_GET['fecha']))$fecha=$_GET['fecha'];
      else $fecha=date("Y-m-d");

      
      $fechaFiltro=$fecha;
      $fecha=explode('-', $fecha);
      $fecha=$fecha[2].'/'.$fecha[1].'/'.$fecha[0];

      if(isset($_GET['estado'])){
         if($_GET['estado']=='1')$estadoLiteral='VIGENTES';
         else if($_GET['estado']=='2')$estadoLiteral='VENCIDOS';
         else $estadoLiteral='TODOS';

      }
      else {
        $estadoLiteral='-';
      }
      // CONSULTAS A LA BASE DE DATOS
      $departamento=$_SESSION['DEPARTAMENTO'];
      $usuario=$_SESSION['USUARIO'];

      if($departamento!='Todos'){
        if(isset($_GET['estado'])){
          if($_GET['estado']=='1'){
              $query = Bonificacionesca::find()->Where("territorio='".$departamento."' and U_fecha_fin >='".$fechaFiltro."'")->orderby('U_bonificaciontipo asc')->asArray()->all();
          }
          else if($_GET['estado']=='2'){
              $query = Bonificacionesca::find()->Where("territorio='".$departamento."' and U_fecha_fin < '".$fechaFiltro."'")->orderby('U_bonificaciontipo asc')->asArray()->all();
          }
          else{
              $query = Bonificacionesca::find()->Where("territorio='".$departamento."'")->orderby('U_bonificaciontipo asc')->asArray()->all();
          }
        }
        else{
            $query = Bonificacionesca::find()->Where("territorio='".$departamento."' and U_fecha_fin >= curdate()")->orderby('U_bonificaciontipo asc')->asArray()->all(); 
        }
           
      }
      else{
        if(isset($_GET['estado'])){
          if($_GET['estado']=='1'){
              $query = Bonificacionesca::find()->Where(" U_fecha_fin >='".$fechaFiltro."'")->orderby('U_bonificaciontipo asc')->asArray()->all();
          }
          else if($_GET['estado']=='2'){
              $query = Bonificacionesca::find()->Where(" U_fecha_fin < '".$fechaFiltro."'")->orderby('U_bonificaciontipo asc')->asArray()->all();
          }
          else{
              $query = Bonificacionesca::find()->orderby('U_bonificaciontipo asc')->asArray()->all();//->with('bonificacion_de2');
              //$query = Bonificacionesca::find()->with('bonificacion_de2','U_ID_bonificacion');
             // $query = Bonificacionesca::find()->leftJoin('bonificacion_de2', 'bonificacion_de2.U_ID_bonificacion = bonificacion_ca.id');
          } 
        }
        else{
          $query = Bonificacionesca::find()->Where(" U_fecha_fin >= curdate()")->orderby('U_bonificaciontipo asc')->asArray()->all();   
        }
        
      }
      $dataBonificacionCabecera=$query;
      //print_r($dataBonificacionCabecera);
     // die();

      $GLOBALS['PIE_USUARIO']=$usuario;
      $GLOBALS['NOMBRE_ARCHIVO']='Bonificaciones';
      $GLOBALS['TITULO_REPORTE']='ESTADO: '.$estadoLiteral.' - FECHA: '.$fecha;
      require_once('../../vendor/PHPExcel/class.docEXCEL.inc.php');
      //$objPHPExcel = new \PHPExceL();

     //llamando al objeto
     // $dataBonificacion = Yii::$app->db->createCommand('SELECT * from Bonificacionesca')->queryAll();
      ///  ENCABEZADO

      
      $objPHPExcel->getActiveSheet()->getCell('A'.$GLOBALS['FILA_INICIAL'])->setValue("Nro");
      $objPHPExcel->getActiveSheet()->getStyle('A'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);  

      $objPHPExcel->getActiveSheet()->getCell('B'.$GLOBALS['FILA_INICIAL'])->setValue("TIPO REGALO");
      $objPHPExcel->getActiveSheet()->getStyle('B'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(strlen("TIPO REGALO")*2);
        
      $objPHPExcel->getActiveSheet()->getCell('C'.$GLOBALS['FILA_INICIAL'])->setValue("DETALLE ESPECIFICO");
      $objPHPExcel->getActiveSheet()->getStyle('C'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(strlen("DETALLE ESPECIFICO")*2);
      
      $objPHPExcel->getActiveSheet()->getCell('D'.$GLOBALS['FILA_INICIAL'])->setValue("CODIGO");
      $objPHPExcel->getActiveSheet()->getStyle('D'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(strlen("CODIGO")*2);
      
      $objPHPExcel->getActiveSheet()->getCell('E'.$GLOBALS['FILA_INICIAL'])->setValue(utf8_decode(utf8_encode("NOMBRE CAMPAÑA")));
      $objPHPExcel->getActiveSheet()->getStyle('E'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(strlen("NOMBRE CAMPAÑA")*2.5);
      
      
      $objPHPExcel->getActiveSheet()->getCell('F'.$GLOBALS['FILA_INICIAL'])->setValue("REGION");
      $objPHPExcel->getActiveSheet()->getStyle('F'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(strlen("REGION")*2);
      
      
      $objPHPExcel->getActiveSheet()->getCell('G'.$GLOBALS['FILA_INICIAL'])->setValue("GRUPO CLIENTE");
      $objPHPExcel->getActiveSheet()->getStyle('G'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(strlen("GRUPO PRODUCTO")*2);


      $objPHPExcel->getActiveSheet()->getCell('H'.$GLOBALS['FILA_INICIAL'])->setValue("FECHA INICIO");
      $objPHPExcel->getActiveSheet()->getStyle('H'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(strlen("FECHA INICIO")*2);


      $objPHPExcel->getActiveSheet()->getCell('I'.$GLOBALS['FILA_INICIAL'])->setValue("FECHA FIN");
      $objPHPExcel->getActiveSheet()->getStyle('I'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(strlen("FECHA FIN")*2);

      $objPHPExcel->getActiveSheet()->getCell('J'.$GLOBALS['FILA_INICIAL'])->setValue("SITUACION");
      $objPHPExcel->getActiveSheet()->getStyle('J'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(strlen("SITUACION"));

      $objPHPExcel->getActiveSheet()->getCell('K'.$GLOBALS['FILA_INICIAL'])->setValue("PRODUCTO COMPRA");
      $objPHPExcel->getActiveSheet()->getStyle('K'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(strlen("PRODUCTO COMPRA"));

      $objPHPExcel->getActiveSheet()->getCell('L'.$GLOBALS['FILA_INICIAL'])->setValue("CANTIDAD COMPRA");
      $objPHPExcel->getActiveSheet()->getStyle('L'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(strlen("CANTIDAD COMPRA"));

      $objPHPExcel->getActiveSheet()->getCell('M'.$GLOBALS['FILA_INICIAL'])->setValue("CANTIDAD MAXIMA COMPRA");
      $objPHPExcel->getActiveSheet()->getStyle('M'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(strlen("CANTIDAD MAXIMA COMPRA"));

      $objPHPExcel->getActiveSheet()->getCell('N'.$GLOBALS['FILA_INICIAL'])->setValue("UNIDAD MEDIDA");
      $objPHPExcel->getActiveSheet()->getStyle('N'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(strlen("UNIDAD MEDIDA"));

      $objPHPExcel->getActiveSheet()->getCell('O'.$GLOBALS['FILA_INICIAL'])->setValue("MONTO TOTAL");
      $objPHPExcel->getActiveSheet()->getStyle('O'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(strlen("MONTO TOTAL"));

      $objPHPExcel->getActiveSheet()->getCell('P'.$GLOBALS['FILA_INICIAL'])->setValue("CANTIDAD REGALO");
      $objPHPExcel->getActiveSheet()->getStyle('P'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(strlen("PORCENTAJE REGALO"));

      $objPHPExcel->getActiveSheet()->getCell('Q'.$GLOBALS['FILA_INICIAL'])->setValue("PORCENTAJE DESCUENTO");
      $objPHPExcel->getActiveSheet()->getStyle('Q'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(strlen("PORCENTAJE DESCUENTO"));

      $objPHPExcel->getActiveSheet()->getCell('R'.$GLOBALS['FILA_INICIAL'])->setValue("PORCENTAJE REGALO EXTRA");
      $objPHPExcel->getActiveSheet()->getStyle('R'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(strlen("PORCENTAJE REGALO EXTRA"));

      $objPHPExcel->getActiveSheet()->getCell('S'.$GLOBALS['FILA_INICIAL'])->setValue("LIMITE MAXIMO ITERACION");
      $objPHPExcel->getActiveSheet()->getStyle('S'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(strlen("LIMITE MAXIMO ITERACION"));

      $objPHPExcel->getActiveSheet()->getCell('T'.$GLOBALS['FILA_INICIAL'])->setValue("OBSERVACION");
      $objPHPExcel->getActiveSheet()->getStyle('T'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
      $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(strlen("OBSERVACION"));
      
      $GLOBALS['FILA_INICIAL']++;
      foreach ($dataBonificacionCabecera as $key => $value) {
        # code...
        if($value['idBonificacionTipo']=='1'){
          $cantidadBono=$value['U_bonificacioncantidad'];
          if($value['idReglaBonificacion']=='13') $cantidaDecuento=$value['porcentajeDescuento'];
          else $cantidaDecuento=0;
        }
        else{
            $cantidadBono=0;
            $cantidaDecuento=$value['U_bonificacioncantidad'];     
        }

        if($value['idBonificacionTipo']=='3' || $value['idBonificacionTipo']=='8'){
          $unidadMedida="";
        }
        else{
          $unidadMedida=$value['U_bonificacionunidad'];
        }
        //obteniendo region
        $regiones=explode('@', $value['territorio']);
        $regionesT="";
        for ($i=0; $i < count($regiones)-1; $i++) {
          $region=explode('=>', $regiones[$i]);
          $regionesT.= $region[1].', ';
          # code...
        }
        $regionesT = substr($regionesT,0, -2);

        $objPHPExcel->getActiveSheet()->getCell('A'.$GLOBALS['FILA_INICIAL'])->setValue($key+1);
        $objPHPExcel->getActiveSheet()->getCell('B'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_bonificaciontipo']);   
        $objPHPExcel->getActiveSheet()->getCell('C'.$GLOBALS['FILA_INICIAL'])->setValue($value['detalleEspecifico']);
        $objPHPExcel->getActiveSheet()->getCell('D'.$GLOBALS['FILA_INICIAL'])->setValue($value['Code']);
        $objPHPExcel->getActiveSheet()->getCell('E'.$GLOBALS['FILA_INICIAL'])->setValue(utf8_decode(utf8_encode($value['Name'])));
        $objPHPExcel->getActiveSheet()->getCell('F'.$GLOBALS['FILA_INICIAL'])->setValue($regionesT);
        $objPHPExcel->getActiveSheet()->getCell('G'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_cliente']);
        $objPHPExcel->getActiveSheet()->getCell('H'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_fecha_inicio']);
        $objPHPExcel->getActiveSheet()->getCell('I'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_fecha_fin']);
        $objPHPExcel->getActiveSheet()->getCell('J'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_estado']);
        $objPHPExcel->getActiveSheet()->getCell('K'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_reglatipo']);
        $objPHPExcel->getActiveSheet()->getCell('L'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_reglacantidad']);
        $objPHPExcel->getActiveSheet()->getCell('M'.$GLOBALS['FILA_INICIAL'])->setValue($value['cantidadMaximaCompra']);
        $objPHPExcel->getActiveSheet()->getCell('N'.$GLOBALS['FILA_INICIAL'])->setValue($unidadMedida);
        $objPHPExcel->getActiveSheet()->getCell('O'.$GLOBALS['FILA_INICIAL'])->setValue($value['montoTotal']);
        $objPHPExcel->getActiveSheet()->getCell('P'.$GLOBALS['FILA_INICIAL'])->setValue($cantidadBono);
        $objPHPExcel->getActiveSheet()->getCell('Q'.$GLOBALS['FILA_INICIAL'])->setValue($cantidaDecuento);
        $objPHPExcel->getActiveSheet()->getCell('R'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_cantidadbonificacion']);
        $objPHPExcel->getActiveSheet()->getCell('S'.$GLOBALS['FILA_INICIAL'])->setValue($value['U_limitemaxregalo']);
        $objPHPExcel->getActiveSheet()->getCell('T'.$GLOBALS['FILA_INICIAL'])->setValue(utf8_decode(utf8_encode($value['U_observacion'])));

        $objPHPExcel->getActiveSheet()->getStyle('A'.$GLOBALS['FILA_INICIAL'].':T'.$GLOBALS['FILA_INICIAL'])->getFill()->applyFromArray($styleFondoCell);

        //$dataCompra = Bonificacionde2::find()->Where(" U_ID_bonificacion=".$value['id'])->asArray()->all();
        $dataCompra = Yii::$app->db->createCommand(" select * from v_bonificacion_detalle_compra where id_bonificacion_cabezera=".$value['id'])->queryAll();
        if(count($dataCompra)>0){
          $GLOBALS['FILA_INICIAL']++;
          $objPHPExcel->getActiveSheet()->getCell('B'.$GLOBALS['FILA_INICIAL'])->setValue("NRO");
          $objPHPExcel->getActiveSheet()->getStyle('B'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
          //$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(strlen("NRO")*2);
            
          $objPHPExcel->getActiveSheet()->getCell('C'.$GLOBALS['FILA_INICIAL'])->setValue("CODIGO ARTICULO");
          $objPHPExcel->getActiveSheet()->getStyle('C'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
          $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(strlen("CODIGO ARTICULO")*2);
          
          $objPHPExcel->getActiveSheet()->getCell('D'.$GLOBALS['FILA_INICIAL'])->setValue("ARTICULO COMPRA");
          $objPHPExcel->getActiveSheet()->getStyle('D'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
          $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(strlen("ARTICULO COMPRA")*2);
          if($value['idReglaBonificacion']==12 || $value['idReglaBonificacion']==11 ){

            $objPHPExcel->getActiveSheet()->getCell('E'.$GLOBALS['FILA_INICIAL'])->setValue(utf8_decode(utf8_encode("CANTIDAD")));
            $objPHPExcel->getActiveSheet()->getStyle('E'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
            //$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(strlen("CANTIDAD")*2.5);
            if($value['idReglaBonificacion']==12){
                $objPHPExcel->getActiveSheet()->getCell('F'.$GLOBALS['FILA_INICIAL'])->setValue(utf8_decode(utf8_encode("ESTADO ART. COM.")));
                $objPHPExcel->getActiveSheet()->getStyle('F'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
            }
            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(strlen("ESTADO ART. COM.")*2.5);
          }
          foreach ($dataCompra as $keyCompra => $valueCompra) {
            # code...
            if($valueCompra['estado']=='1') $estadoCompra='Habilitado';
            else $estadoCompra='Deshabilitado';
            

            $GLOBALS['FILA_INICIAL']++;
            $objPHPExcel->getActiveSheet()->getCell('B'.$GLOBALS['FILA_INICIAL'])->setValue($keyCompra+1);              
            $objPHPExcel->getActiveSheet()->getCell('C'.$GLOBALS['FILA_INICIAL'])->setValue($valueCompra['code_compra']); 
            $objPHPExcel->getActiveSheet()->getCell('D'.$GLOBALS['FILA_INICIAL'])->setValue($valueCompra['producto_nombre_compra']);
            if($value['idReglaBonificacion']==12 || $value['idReglaBonificacion']==11){
              $objPHPExcel->getActiveSheet()->getCell('E'.$GLOBALS['FILA_INICIAL'])->setValue($valueCompra['producto_cantidad']);
              if($value['idReglaBonificacion']==12)
                $objPHPExcel->getActiveSheet()->getCell('F'.$GLOBALS['FILA_INICIAL'])->setValue($estadoCompra);
            }

          }
        }
        ////articulo bonificable
        $dataBono = Yii::$app->db->createCommand(" select * from v_bonificacion_detalle_regalo where id_bonificacion_cabezera=".$value['id'])->queryAll();
        if(count($dataBono)>0){
          $GLOBALS['FILA_INICIAL']++;
          $objPHPExcel->getActiveSheet()->getCell('B'.$GLOBALS['FILA_INICIAL'])->setValue("NRO");
          $objPHPExcel->getActiveSheet()->getStyle('B'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
          //$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(strlen("NRO")*2);
            
          $objPHPExcel->getActiveSheet()->getCell('C'.$GLOBALS['FILA_INICIAL'])->setValue("CODIGO ARTICULO");
          $objPHPExcel->getActiveSheet()->getStyle('C'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
          $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(strlen("CODIGO ARTICULO")*2);
          
          $objPHPExcel->getActiveSheet()->getCell('D'.$GLOBALS['FILA_INICIAL'])->setValue("ARTICULO BONIFICABLE");
          $objPHPExcel->getActiveSheet()->getStyle('D'.$GLOBALS['FILA_INICIAL'])->applyFromArray($EstiloCabeceraCampos);
          $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(strlen("ARTICULO COMPRA")*2);
          
          foreach ($dataBono as $keyRegalo => $valueRegalo) {
            # code...
            $GLOBALS['FILA_INICIAL']++;
            $objPHPExcel->getActiveSheet()->getCell('B'.$GLOBALS['FILA_INICIAL'])->setValue($keyRegalo+1);              
            $objPHPExcel->getActiveSheet()->getCell('C'.$GLOBALS['FILA_INICIAL'])->setValue($valueRegalo['code_regalo']); 
            $objPHPExcel->getActiveSheet()->getCell('D'.$GLOBALS['FILA_INICIAL'])->setValue($valueRegalo['producto_nombre_regalo']);
          }
        }

        $GLOBALS['FILA_INICIAL']++;

      }


      //header('Cache-Control: max-age=0');

      $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

      $objWriter->save('php://output');

      exit;
      //return  json_encode($objWriter);
     // echo "";*/

    }

  

}
