<?php

namespace backend\controllers;

use Yii;
use backend\models\Poligonocabecera;
use backend\models\PoligonocabeceraSearch;
use backend\models\Poligonodetalle;
use backend\models\Poligonocliente;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
/**
 * PoligonocabeceraController implements the CRUD actions for Poligonocabecera model.
 */
  
class PoligonocabeceraController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','listapoligonocliente','obtienepoligonos'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Poligonocabecera models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PoligonocabeceraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Poligonocabecera model.
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
     * Creates a new Poligonocabecera model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $datos = Yii::$app->request->post();
        $model = new Poligonocabecera();
        if ($model->load(Yii::$app->request->post()) || (count($datos))) {
            $fecha = date("Y-m-d");
            $usuario = Yii::$app->user->identity->getId();
            $model->id = 0;
            $model->nombre = $datos["nombre"];
            $model->territoryid = $datos["territorio"];
            $model->usuario = $usuario;
            $model->status = 1;
            $model->dateUpdate = $fecha;
            if ($model->save()) {                
                foreach($datos["detalle"] as $d){
                    $detalle = new Poligonodetalle();
                    $detalle->id = 0;
                    $detalle->idcabecera = $model->id;
                    $detalle->longitud = $d["lon"];
                    $detalle->latitud = $d["lat"];
                    $detalle->usuario = $usuario;
                    $detalle->status = 1;
                    $detalle->dateUpdate = $fecha;
                    $detalle->save();
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
                    'detalle' => '[]'
        ]);
    }
    
    /**
     * Updates an existing Poligonocabecera model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id = 0) {
        $datos = Yii::$app->request->post();
        if (count($datos)) $id = $datos["id"];
        $model = Poligonocabecera::find()->where(['id' => $id])->one();
        $detalle = Poligonodetalle::find()->where(['=', 'idcabecera', $id])->asArray()->all();
        if (count($datos)) {
            $fecha = date("Y-m-d");
            $usuario = Yii::$app->user->identity->getId();
            $model->nombre = $datos["nombre"];
            $model->territoryid = $datos["territorio"];
            if ($model->save()) {
                $borrar =  Poligonodetalle::find()->where(["idcabecera" => $id])->all();
                foreach($borrar as $b){
                    $b->delete();
                }

                foreach($datos["detalle"] as $d){
                    $detalle = new Poligonodetalle();
                    $detalle->id = 0;
                    $detalle->idcabecera = $id;
                    $detalle->longitud = $d["lon"];
                    $detalle->latitud = $d["lat"];
                    $detalle->usuario = $usuario;
                    $detalle->status = 1;
                    $detalle->dateUpdate = $fecha;
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
                                                'detalle' => json_encode($detalle)
                                            ]);

    }
    
    
    
    /**
     * Deletes an existing Poligonocabecera model.
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
            //die('eliminar cabecera?');
            $borrar =  Poligonodetalle::find()->where(["idcabecera" => $id])->all();
            foreach($borrar as $b){
                $b->delete();
            }
			$poligonocliente =  Poligonocliente::find()->where(["poligonoid" => $id])->all();
            foreach($poligonocliente as $valor){
                $valor->delete();
            }
			
        return $this->findModel($id)->delete();
    }
    
    /**
     * Finds the Poligonocabecera model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Poligonocabecera the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Poligonocabecera::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	public function actionListapoligonocliente(){
		Yii::error("Entrada a texto POLIGONO CLIENTE");
        $datos = Yii::$app->request->post();

        $poligonocliente = Poligonocliente::find()->where('poligonoid='.$datos['poligonoid'])->asArray()->all();
        Yii::error($poligonocliente);
        return json_encode($poligonocliente);
	}
	
	public function actionObtienepoligonos(){
		$datos = Yii::$app->request->post(); 
		Yii::error("POLIGONOS GUARDADOS");
		
		//$poligonoCliente=Poligonodetalle::find()->where()->asArray()->all();
		$sql = "select
			poligonodetalle.idcabecera,
			poligonocabecera.nombre,
			poligonocabecera.territoryid,
			poligonodetalle.latitud,
			poligonodetalle.longitud
			from poligonocabecera 
			inner join poligonodetalle on poligonocabecera.id=poligonodetalle.idcabecera
			where poligonocabecera.territoryid=".$datos['idTerritorio'];
		$poligoDetalle= Yii::$app->db->createCommand($sql)->queryAll();
		return json_encode($poligoDetalle);
	}
	
}
