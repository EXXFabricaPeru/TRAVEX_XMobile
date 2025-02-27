<?php

namespace backend\controllers;

use Yii;
use backend\models\Equipoxcuentascontables;
use backend\models\EquipoxcuentascontablesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * EquipoxcuentascontablesController implements the CRUD actions for Equipoxcuentascontables model.
 */
class EquipoxcuentascontablesController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Equipoxcuentascontables models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EquipoxcuentascontablesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Equipoxcuentascontables model.
     * @param integer $id
     * @param integer $equipoxId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionReport($id, $equipoxId) {
        $content = $this->renderPartial('view', [
            'model' => $this->findModel($id, $equipoxId),
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
     * Creates a new Equipoxcuentascontables model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Equipoxcuentascontables();
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
     * Updates an existing Equipoxcuentascontables model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $equipoxId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        Yii::error('entro actualizar');
        $model = $this->findModel($id);
        //Yii::error('estructura: '.json_encode($model));
        //Yii::error('equipox: '.$id);
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
     * Deletes an existing Equipoxcuentascontables model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $equipoxId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $equipoxId) {
        $this->findModel($id, $equipoxId)->delete();

        return $this->redirect(['index']);
    }

    public function actionEliminar($id) {
        return $this->findModel($id)->delete();
    }

    /**
     * Finds the Equipoxcuentascontables model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $equipoxId
     * @return Equipoxcuentascontables the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Equipoxcuentascontables::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
