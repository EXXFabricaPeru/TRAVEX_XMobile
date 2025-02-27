<?php

namespace backend\modules\admin\controllers;

use Yii;
use backend\modules\admin\models\Localidad;
use backend\modules\admin\models\LocalidadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LocalidadController implements the CRUD actions for Localidad model.
 */
class LocalidadController extends Controller
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
     * Lists all Localidad models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocalidadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Localidad model.
     * @param integer $id
     * @param string $fecha
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $fecha)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $fecha),
        ]);
    }

    /**
     * Creates a new Localidad model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Localidad();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'fecha' => $model->fecha]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Localidad model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $fecha
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $fecha)
    {
        $model = $this->findModel($id, $fecha);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'fecha' => $model->fecha]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Localidad model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $fecha
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $fecha)
    {
        $this->findModel($id, $fecha)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Localidad model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $fecha
     * @return Localidad the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $fecha)
    {
        if (($model = Localidad::findOne(['id' => $id, 'fecha' => $fecha])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
