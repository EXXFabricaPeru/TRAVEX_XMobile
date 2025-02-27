<?php

namespace backend\controllers;

use Yii;
use backend\models\Configuracion;
use backend\models\ConfiguracionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
/**
 * ConfiguracionController implements the CRUD actions for Configuracion model.
 */
  
class ConfiguracionController extends Controller
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
     * Lists all Configuracion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ConfiguracionSearch();
        $searchModel->visible = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Configuracion model.
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
     * Creates a new Configuracion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Configuracion();
        if ($model->load(Yii::$app->request->post())) {
            $model->especificacion = 'itemcode va en valor 2, valor en valor';
            if ($model->parametro == 'combo'){
                $model->especificacion = 'lista de precios para combos';
            }
            if ($model->save()) {
                return $model->id;
            } else {                
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        else{
            $model->especificacion = 'itemcode va en valor 2, valor en valor';
        }
        return $this->renderPartial('create', [
                    'model' => $model,
                    'valor' => true,
                    'valor2' => true
        ]);
    }
    
    /**
     * Updates an existing Configuracion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $parametro = $model["parametro"];
        //if (split[1] == 'divisadeb' || split[1] == 'divisacred' || split[1] == 'divisadif' || split[1] == 's_defecto_cliente'){
        $valor = false;
        $valor2 = false;
        if ($parametro == 'divisadeb' || $parametro == 'divisacred' || $parametro == 'divisadif') $valor2 = true;
        else if($parametro == 's_defecto_cliente') $valor = true;
        
        if ($model->load(Yii::$app->request->post())) {
            $model->visible = 1;
            if ($model->save()) {
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        return $this->renderPartial('update', ['model' => $model, 'valor' => $valor, 'valor2' => $valor2]);
    }
    
    
    
    /**
     * Deletes an existing Configuracion model.
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
     * Finds the Configuracion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Configuracion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Configuracion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
