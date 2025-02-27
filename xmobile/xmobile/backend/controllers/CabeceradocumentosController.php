<?php

namespace backend\controllers;

use Yii;
use backend\models\Cabeceradocumentos;
use backend\models\CabeceradocumentosSearch;
use backend\models\DetalledocumentosSearch;
use backend\models\PagosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Sap;

/**
 * CabeceradocumentosController implements the CRUD actions for Cabeceradocumentos model.
 */
class CabeceradocumentosController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'autorizaranulacion'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }


    /**
     * Lists all Cabeceradocumentos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CabeceradocumentosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['estado' => 3]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cabeceradocumentos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
		$searchModel = new DetalledocumentosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['idCabecera' => $id]);
		
		$data = $this->findModel($id);
		
		$searchModelp = new PagosSearch();
        $dataProviderp = $searchModelp->search(Yii::$app->request->queryParams);
		$dataProviderp->query->andFilterWhere(['documentoId' => $data->idDocPedido]);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
			'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'searchModelp' => $searchModelp,
            'dataProviderp' => $dataProviderp,
        ]);
    }

    /**
     * Creates a new Cabeceradocumentos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Cabeceradocumentos();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Cabeceradocumentos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Cabeceradocumentos model.
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

    /**
     * Finds the Cabeceradocumentos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cabeceradocumentos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cabeceradocumentos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionAutorizaranulacion(){
        Yii::error("Entrada a texto Autoriza");
        $datos = Yii::$app->request->post();
        $model = new Cabeceradocumentos();
        $model = $this->findModel($datos['id']);
        //Yii::error($model);
        //$model->anulaAutorizado=1;
        Yii::error("PRUEBA: ". $model->idDocPedido);
        $sap= new Sap();
        Yii::error("RESPUESTA VERIFICA PEDIDO: ");
        if($model->DocType=='DOP'){
            Yii::error($model->DocType);
            $resultado = $sap->VerificaPedido($model->DocEntry);
        }
        elseif($model->DocType=='DFA'){
            Yii::error($model->DocType);
            $resultado = $sap->VerificaFactura($model->DocEntry);
        }
        elseif($model->DocType=='DOF'){
            $resultado = $sap->VerificaOferta($model->DocEntry); 
        } 

        //$resultado=json_encode($resultado,true);
        Yii::error(json_encode($resultado));
        foreach ($resultado as $key => $value) {
            if($value->DocStatus=='O' || $value->InvntSttus=='O' ){
                Yii::error("Abierto: DocStatus-> ".$value->DocStatus." InvntSttus-> ".$value->InvntSttus);
                return $this->actualizaEstadoDoc($datos['id']);
            }
            else{
                Yii::error("Cerrado: DocStatus-> ".$value->DocStatus);
                return ("CERRADO");
            }   
        } 
        if(is_null($resultado)){
            Yii::error("SIN REGISTROS");
            $this->actualizaEstadoDoc($datos['id']);
            return ("SIN REGISTROS");
        }
        
         
    }
    function actualizaEstadoDoc($id){
         /*******se actualiza el estado del documento********/
         $respuesta= Yii::$app->db->createCommand("Update cabeceradocumentos set anulaAutorizado=1 where id=".$id)->execute();
         Yii::error("Respuesta actualizacion: ");
         Yii::error($respuesta);
         if ($respuesta==1) {
             Yii::error("Actualizacion correcta: ");
             return ("CORRETO");
             
         } else {
         // $data = $model->getErrors();
             Yii::error("Error al Actualizacion: ");
             return ("ERROR");
         }
    }

}
