<?php

namespace backend\controllers;

use Yii;
use backend\models\Clientesnoenviados;
use backend\models\ClientesnoenviadosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\PagosSearch;
use backend\models\LogEnvioNoEnvioSearch;
use backend\models\Clientessucursales;
use backend\models\Contactos;
/**
 * ClientesController implements the CRUD actions for Clientes model.
 */
class ClientesnoenviadosController extends Controller
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
     * Lists all Clientes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientesnoenviadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Clientes model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $client = $this->findModel($id);
		$searchModelp = new PagosSearch();
        $dataProviderp = $searchModelp->search(Yii::$app->request->queryParams);
		$dataProviderp->query
						->andFilterWhere(['clienteId' => $client->CardCode])
						->andFilterWhere(['otpp' => 3])
						->orderBy(['id' => SORT_DESC]);
        $modelSucursal= Clientessucursales::find()->where("CardCode = '".$client->CardCode."'")->all();
        $modelContactos= Contactos::find()->where("cardCode = '".$client->CardCode."'")->all();

        $searchModelle = new LogEnvioNoEnvioSearch();
        $dataProviderle = $searchModelle->search(Yii::$app->request->queryParams);
		$dataProviderle->query->andFilterWhere(['documento' => $client->CardCode])->orderBy('fecha DESC')->limit(1);


        return $this->render('view', [
            'model' => $client,
			'dataProviderp' => $dataProviderp,
			'searchModelp' => $searchModelp,
            'modelSucursal'=> $modelSucursal,
            'modelContactos'=>$modelContactos,
            'searchModelle' => $searchModelle,
            'dataProviderle' => $dataProviderle,
        ]);
    }

    /**
     * Creates a new Clientes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Clientesnoenviados();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Clientes model.
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
     * Deletes an existing Clientes model.
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
     * Finds the Clientes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Clientes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Clientesnoenviados::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
