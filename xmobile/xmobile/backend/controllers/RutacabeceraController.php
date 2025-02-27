<?php

namespace backend\controllers;

use Yii;
use backend\models\Rutacabecera;
use backend\models\RutacabeceraSearch;
use backend\models\Rutadetalle;
use backend\models\Clientes;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use backend\models\v2\Documentos;
/**
 * RutacabeceraController implements the CRUD actions for Rutacabecera model.
 */
  
class RutacabeceraController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','consultas','verificaregistrousuario'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Rutacabecera models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RutacabeceraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rutacabecera model.
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
     * Creates a new Rutacabecera model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Rutacabecera();
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
     * Updates an existing Rutacabecera model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    /*public function actionUpdate($id) {
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
    }*/
    public function actionUpdate($id = 0) {
        $datos = Yii::$app->request->post();
        if (count($datos)) $id = $datos["id"];
        //$model = $this->findModel($id);
        $model = RutaCabecera::find()->where(['id' => $id])->one();
        $detalle = Rutadetalle::find()->where(['=', 'idcabecera', $id])->orderby(['posicion'=>'asc' ])->all();
         Yii::error($detalle);
        //if ($model->load(Yii::$app->request->post()) || count($datos)) {
        if (count($datos)) {
            $fecha = date("Y-m-d");
            $usuario = Yii::$app->user->identity->getId();
            //$model = RutaCabecera::find(['id' => $id])->one();
            $model->idvendedor = $datos["idvendedor"];
            $model->nombre = $datos["nombre"];
            $model->fecha = $datos["fecha"];
            $model->idclienteinicial = $datos["idclienteinicial"];
            $model->latitud = $datos["latitud"];
            $model->longitud = $datos["longitud"];
            $model->tipousuario = $datos["tipousuario"];
            if ($model->save()) {
                $borrar =  Rutadetalle::find()->where(["idcabecera" => $id])->all();
                foreach($borrar as $b){
                    $b->delete();
                }

                foreach($datos["detalle"] as $d){
                    $detalle = new Rutadetalle();
                    $detalle->id = 0;
                    $detalle->idcabecera = $id;
                    $detalle->idcliente = $d["cliente"];
                    $detalle->posicion = $d["pos"];
                    $detalle->longitud = $d["lon"];
                    $detalle->latitud = $d["lat"];
                    $detalle->usuario = $usuario;
                    $detalle->status = 1;
                    $detalle->dateUpdate = $fecha;
                    $detalle->tipodoc = $d["tipodoc"];
                    $detalle->iddoc = $d["iddoc"];
                    $detalle->save();
                }
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
            
        }
        return $this->renderPartial('update', [
                                                'model' => $model,
                                                'detalle' => $detalle
                                            ]);

    }
    
    
    
    /**
     * Deletes an existing Rutacabecera model.
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
    
    
    public function actionConsultas_(){
        return $this->renderPartial('consultas', ['model' => $model]);
       // return $this->renderPartial(['consultas']);
       // return $this->render();
       // return $this->renderPartial();
     }
     
    /**
     * Finds the Rutacabecera model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rutacabecera the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rutacabecera::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function actionVerificaregistrousuario(){
        Yii::error("Entrada a texto Verifica Registro usuario");
        $datos = Yii::$app->request->post();
        $datosVerificados = RutaCabecera::find()->where("fecha='".$datos['fechaDespacho']."' and status='1' and idvendedor=".$datos['idUsuario'])->orderby('id desc')->limit(1)->asArray()->all();
        Yii::error($datosVerificados);
        return json_encode($datosVerificados);
    }

    public function actionConsultas(){
        if(isset($_POST['CONDICION'])){
            switch ($_POST['CONDICION']) {
                case 'DOCUMENTOSIMPORTADOS':
                    $DataServicios = Yii::$app->db->createCommand("CALL pa_documentosimportadosrutas('".$_POST['usuario']."','0','0','".$_POST['fechaPicking']."')")->queryAll();
                    echo (json_encode ($DataServicios));
                    break;
                default:
                   echo "error! no existe condicion";
            }
        }
        else{
            echo "error! comuniquese con su administrador de sistemas";
        }
        //prueba obtener vista hana picking list
        $documentos =new Documentos();

        $resultado = $documentos->obtenerListaPicking($_POST['usuario']);
        Yii::error("Resultado picking: ");
        Yii::error($resultado);
    }
    
}
