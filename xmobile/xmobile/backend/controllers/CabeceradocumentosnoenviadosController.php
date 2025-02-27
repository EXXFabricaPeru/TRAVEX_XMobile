<?php

namespace backend\controllers;

use Yii;
use backend\models\Cabeceradocumentos;
use backend\models\CabeceradocumentosnoenviadosSearch;
use backend\models\DetalledocumentosSearch;
use backend\models\LogEnvioNoEnvioSearch;
use backend\models\PagosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CabeceradocumentosController implements the CRUD actions for Cabeceradocumentos model.
 */
class CabeceradocumentosnoenviadosController extends Controller
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
        ];
    }

    /**
     * Lists all Cabeceradocumentos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CabeceradocumentosnoenviadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['estado' => 2]);

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
		
		$searchModelle = new LogEnvioNoEnvioSearch();
        $dataProviderle = $searchModelle->search(Yii::$app->request->queryParams);
		$dataProviderle->query->andFilterWhere(['documento' => $data->idDocPedido])->orderBy('fecha DESC')->limit(1);
		
		
        return $this->render('view', [
            'model' => $this->findModel($id),
			'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'searchModelp' => $searchModelp,
            'dataProviderp' => $dataProviderp,
			'searchModelle' => $searchModelle,
            'dataProviderle' => $dataProviderle,
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
}
