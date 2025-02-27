<?php

namespace backend\controllers;

use Yii;
use backend\models\FexSucursales;
use backend\models\FexSucursalesSearch;
use backend\models\Configuracion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * FexSucursalesController implements the CRUD actions for FexSucursales model.
 */
class FexSucursalesController extends Controller {

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
     * Lists all FexSucursales models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new FexSucursalesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FexSucursales model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
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
     * Creates a new FexSucursales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new FexSucursales();
        $visibleGrupoClientes = false;
        $visibleGrupoProductos = false;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        else{
            $configuracion = Configuracion::find()->where("parametro = 'localizacion'")->asArray()->one();
            if (count($configuracion)){                
                if ($configuracion["estado"] == '1' && $configuracion["valor"] == '2'){
                    $visibleGrupoClientes = true;
                    $visibleGrupoProductos = true;
                }
            }
        }
        return $this->renderPartial('create', [
                    'model' => $model,
                    'grupoCliente' => $visibleGrupoClientes,
                    'grupoProducto' => $visibleGrupoProductos
        ]);
    }

    /**
     * Updates an existing FexSucursales model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $visibleGrupoClientes = false;
        $visibleGrupoProductos = false;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        else{
            $configuracion = Configuracion::find()->where("parametro = 'localizacion'")->asArray()->one();
            if (count($configuracion)){                
                if ($configuracion["estado"] == '1' && $configuracion["valor"] == '2'){
                    $visibleGrupoClientes = true;
                    $visibleGrupoProductos = true;
                }
            }
        }
        return $this->renderPartial('update', [
                                                'model' => $model,
                                                'grupoCliente' => $visibleGrupoClientes,
                                                'grupoProducto' => $visibleGrupoProductos
                                            ]);
    }

    /**
     * Deletes an existing FexSucursales model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionEliminar($id) {
        return $this->findModel($id)->delete();
    }

    /**
     * Finds the FexSucursales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FexSucursales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = FexSucursales::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
