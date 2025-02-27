<?php

namespace backend\controllers;

use Yii;
use backend\models\Bonificacionde1;
use backend\models\Bonificacionde1Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
/**
 * Bonificacionde1Controller implements the CRUD actions for Bonificacionde1 model.
 */
  
class Bonificacionde1Controller extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Bonificacionde1 models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Bonificacionde1Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bonificacionde1 model.
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
     * Creates a new Bonificacionde1 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Bonificacionde1();
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
     * Updates an existing Bonificacionde1 model.
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
     * Deletes an existing Bonificacionde1 model.
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
     * Finds the Bonificacionde1 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bonificacionde1 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bonificacionde1::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    
    public function getDetalleBonificacion(){
       $model = new Bonificacionde1();
       return $model->DetalleBonificacion();
        //return array();
    }
  
    
    public function getDetalleProductos(){
        /*return $bonificacionde1= Bonificacionde1::find()
       ->where(['imagen' => 'pp'])
       ->all();
       //, 'Name', 'U_regla'
       */return array();
    }
}
