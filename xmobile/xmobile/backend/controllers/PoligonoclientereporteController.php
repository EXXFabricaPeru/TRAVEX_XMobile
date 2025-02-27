<?php

namespace backend\controllers;

use Yii;
use backend\models\Poligonocliente;
use backend\models\PoligonoclienteSearch;
use backend\models\Poligonocabecera;
use backend\models\Poligonodetalle;
use backend\models\Clientes;
use backend\models\Territorios;
use backend\models\VSalespersonrute;
use backend\models\Vendedores;
use backend\models\Empresa;
use backend\models\Usuarioconfiguracion;
use backend\models\Visitas;
use backend\models\Cabeceradocumentos;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
/**
 * PoligonoclientereporteController implements the CRUD actions for Poligonocliente model.
 */
  
class PoligonoclientereporteController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report', 'recuperarpoligonos', 'recuperarpoligonosdetalle','coordenadasiniciales','visitasporvendedor','documentosporvendedor'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Poligonocliente models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PoligonoclienteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Poligonocliente model.
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
     * Creates a new Poligonocliente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Poligonocliente();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
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
     * Updates an existing Poligonocliente model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
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
     * Deletes an existing Poligonocliente model.
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
        return $this->findModel($id)->delete();
    }
    
    /**
     * Finds the Poligonocliente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Poligonocliente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Poligonocliente::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionRecuperarpoligonos(){
        $datos = Yii::$app->request->post();
        $idterritorio = $datos["Territorio"];
        $poligonos = Poligonocabecera::find()->where("territoryid = ".$idterritorio)->asArray()->all();
        return json_encode($poligonos);
    }

    public function actionRecuperarpoligonosdetalle(){
        $datos = Yii::$app->request->post();
        $resultado = [];
        foreach($datos["Poligonos"] as $poligono){
            $detalle = Poligonodetalle::find()->where("idcabecera = ".$poligono)->asArray()->all();
            $dias = Poligonocliente::find()->where('poligonoid = '.$poligono)->groupBy('dia')->asArray()->all();
            $poligonoNombre = '';
            if (count($dias)) $poligonoNombre = $dias[0]["poligononombre"];
            $Dias = [];
            foreach($dias as $dia){
                $d = [
                    "dia" => $dia["dia"],
                    "nombre" => $this->textDia($dia["dia"]),
                    "vendedores" => []
                ];
                $vendedores = Poligonocliente::find()->where('poligonoid = '.$poligono.' AND dia = '.$dia["dia"])->groupBy('vendedor')->asArray()->all();
                foreach($vendedores as $vendedor){
                    $cliente = Poligonocliente::find()->where('poligonoid = '.$poligono.' AND dia = '.$dia["dia"]." AND vendedor = ".$vendedor["vendedor"])->asArray()->all();
					$nombreVendedor = Vendedores::find()->where('SalesEmployeeCode = '.$vendedor["vendedor"])->asArray()->one();
					if ($nombreVendedor == null) $nombreVendedor = '';
					else $nombreVendedor = $nombreVendedor["SalesEmployeeName"];
                    $v = [
                        "vendedor" => $vendedor["vendedor"],
                        "nombre" => $nombreVendedor,
                        "clientes" => $cliente
                    ];
                    array_push($d["vendedores"], $v);
                }                
                array_push($Dias, $d);
            }
            array_push($resultado, ["id" => $poligono, "nombre" => $poligonoNombre, "detalle" => $detalle, "dias" => $Dias]);
        }
        return json_encode($resultado);
    }

    private function textDia($dia){
        $resultado = '';
        switch($dia){
            case '1': $resultado = 'LUNES'; break;
            case '2': $resultado = 'MARTES'; break;
            case '3': $resultado = 'MIERCOLES'; break;
            case '4': $resultado = 'JUEVES'; break;
            case '5': $resultado = 'VIERNES'; break;
            case '6': $resultado = 'SABADO'; break;
            case '7': $resultado = 'DOMINGO'; break;
        }
        return $resultado;
    }

    public function actionCoordenadasiniciales(){
        $empresa = Empresa::find()->asArray()->one();
        $resultado = ['lat' => $empresa['lat'], 'long' => $empresa['long'] ];
        return json_encode($resultado);
    }
  //DNE
    public function actionVisitasporvendedor(){
        $datos = Yii::$app->request->post();
        $vendedor = $datos["Vendedor"];
        $fini = $datos["FechaInicial"];
        $ffin = $datos["FechaFinal"];
        //$usuario = Usuarioconfiguracion::find()->where('codEmpleadoVenta = '.$vendedor)->asArray()->one();    
       //$idusuario = $usuario["idUser"];
       $usuarios = Usuarioconfiguracion::find()->where('codEmpleadoVenta = '.$vendedor)->asArray()->all();  
       if (empty($usuarios)) {
           return json_encode("N"); 
       }
       foreach($usuarios as $usuario){
      // $idusuario = 1;
        if ($fini) {
            if ($ffin) {
                // tiene ambas fechas
                
                        $puntos = Visitas::find()->select(['lat','lng','CardCode','CardName'])->where('usuario='.$usuario["idUser"])
                        ->andFilterWhere(['between','fecha', $fini, $ffin])
                        ->asArray()->all();
                        if (empty($puntos)) {  
                        }
                        else{return json_encode($puntos); }
                     
            } else {
                // solo tiene inicial
                $puntos = Visitas::find()->where(['usuario' => $usuario["idUser"]])
                        ->andFilterWhere(['>=', 'fecha', $fini])
                        ->asArray()->all();
                        if (empty($puntos)) {  
                        }
                        else{return json_encode($puntos); }
            }
        } else if ($ffin) {
            // solo tiene final
            $puntos = Visitas::find()->where(['usuario' => $usuario["idUser"]])
                    ->andFilterWhere(['<=', 'fecha', $ffin])
                    ->asArray()->all();
                    if (empty($puntos)) {  
                    }
                    else{return json_encode($puntos); }
        } else {
            // no tiene fechas
            $puntos = Visitas::find()->where(['usuario' => $usuario["idUser"]])
                    ->asArray()->all();
                    if (empty($puntos)) {  
                    }
                    else{return json_encode($puntos); }
        }
    }
    }
    
  //DNE
    public function actionDocumentosporvendedor(){
        $datos = Yii::$app->request->post();
        $vendedor = $datos["Vendedor"];
        $fini = $datos["FechaInicial"];
        $ffin = $datos["FechaFinal"];
        $tipo = $datos["tipoDoc"];  
     
        $usuarios = Usuarioconfiguracion::find()->where('codEmpleadoVenta = '.$vendedor)->asArray()->all();  

        if (empty($usuarios)) {
            return json_encode("N"); 
        }

        foreach($usuarios as $usuario){
            if ($fini) {
            if ($ffin) {
                // tiene ambas fechas
                if ($tipo == ''){
                 //DNE
                 $puntos =  Cabeceradocumentos::find()->select(['idUser','U_LATITUD','U_LONGITUD','CardCode','CardName','DocType','DocDate'])->where('idUser='.$usuario["idUser"])
                 ->andFilterWhere(['between', 'DocDate', $fini, $ffin])
                 ->asArray()->all();

                 if (empty($puntos)) {  
                }
                else{return json_encode($puntos); }     
                }
                else{
                    $puntos = Cabeceradocumentos::find()->where(['idUser' => $usuario["idUser"]])
                        ->andFilterWhere(['between', 'DocDate', $fini, $ffin])
                        ->andFilterWhere(['=', 'DocType', $tipo])
                        ->asArray()->all();
                        if (empty($puntos)) {  
                        }
                        else{return json_encode($puntos); }
                }
            } else {
                // solo tiene inicial
                if ($tipo == ''){
                    $puntos = Cabeceradocumentos::find()->where(['idUser' => $usuario["idUser"]])
                        ->andFilterWhere(['>=', 'DocDate', $fini])
                        ->asArray()->all();
                        if (empty($puntos)) {  
                        }
                        else{return json_encode($puntos); }
                }
                else{
                    $puntos = Cabeceradocumentos::find()->where(['idUser' => $usuario["idUser"]])
                        ->andFilterWhere(['>=', 'DocDate', $fini])
                        ->andFilterWhere(['=', 'DocType', $tipo])
                        ->asArray()->all();
                        if (empty($puntos)) {  
                        }
                        else{return json_encode($puntos); }
                }
            }
        } else if ($fechaFinal) {


            // solo tiene final
            if ($tipo == ''){
                $puntos = Cabeceradocumentos::find()->where(['idUser' => $usuario["idUser"]])
                    ->andFilterWhere(['<=', 'DocDate', $ffin])
                    ->asArray()->all();
                    if (empty($puntos)) {  
                    }
                    else{return json_encode($puntos); }
            }
            else{
                $puntos = Cabeceradocumentos::find()->where(['idUser' => $usuario["idUser"]])
                    ->andFilterWhere(['<=', 'DocDate', $ffin])
                    ->andFilterWhere(['=', 'DocType', $tipo])
                    ->asArray()->all();
                    if (empty($puntos)) {  
                    }
                    else{return json_encode($puntos); }               
            }
        } else {
            // no tiene fechas
            if ($tipo == ''){
                $puntos = Cabeceradocumentos::find()->where(['idUser' => $ffin])
                    ->asArray()->all();
                    if (empty($puntos)) {  
                    }
                    else{return json_encode($puntos); }
            }
            else{
                $puntos = Cabeceradocumentos::find()->where(['idUser' => $ffin])
                    ->andFilterWhere(['=', 'DocType', $tipo])
                    ->asArray()->all();
                    if (empty($puntos)) {  
                    }
                    else{return json_encode($puntos); }                
            }
        }
   }


    }
}
