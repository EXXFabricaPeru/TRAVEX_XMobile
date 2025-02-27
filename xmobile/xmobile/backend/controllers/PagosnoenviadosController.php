<?php

namespace backend\controllers;

use Yii;
use backend\models\Pagos;
use backend\models\PagosnoenviadosSearch;
use backend\models\Xmfcabezerapagos;
use backend\models\XmfcabezerapagosSearch;
use backend\models\Xmffacturaspagos;
use backend\models\XmffacturaspagosSearch;
use backend\models\Xmfmediospagos;
use backend\models\XmfmediospagosSearch;
use backend\models\LogEnvioSearch;
use backend\models\LogEnvioNoEnvioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PagosController implements the CRUD actions for Pagos model.
 */
class PagosnoenviadosController extends Controller
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
     * Lists all Pagos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XmfcabezerapagosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['estado' => 2]);
      
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Pagos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        //FACTURA PAGO//
        $searchModelFP = new XmffacturaspagosSearch();
        $dataProviderFP = $searchModelFP->search(Yii::$app->request->queryParams);
		$dataProviderFP->query->andFilterWhere(['idCabecera' => $id]);
        //FORMA DE PAGO
        $searchModelMP = new XmfmediospagosSearch();
        $dataProviderMP = $searchModelMP->search(Yii::$app->request->queryParams);
		$dataProviderMP->query->andFilterWhere(['idCabecera' => $id]);
        //LOG ENVIO//
        $data = $this->findModel($id);
        $searchModellog = new LogEnvioSearch();
        $dataProviderlog = $searchModellog->search(Yii::$app->request->queryParams);
		$dataProviderlog->query->andFilterWhere(['documento' => $data->nro_recibo])->orderBy('fecha DESC')->limit(1);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModelFP'=>$searchModelFP,
            'dataProviderFP'=>$dataProviderFP,
            'searchModelMP'=>$searchModelMP,
            'dataProviderMP'=>$dataProviderMP,
            'searchModellog' => $searchModellog,
            'dataProviderlog' => $dataProviderlog,
        ]);
    }

    /**
     * Creates a new Pagos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pagos();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Pagos model.
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
     * Deletes an existing Pagos model.
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
     * Finds the Pagos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pagos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xmfcabezerapagos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
