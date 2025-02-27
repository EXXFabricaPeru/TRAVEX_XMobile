<?php

namespace backend\controllers;

use Yii;
use backend\models\Equipox;
use backend\models\Userequipox;
use backend\models\UserequipoxSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * UserequipoxController implements the CRUD actions for Userequipox model.
 */
class UserequipoxController extends Controller {

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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','validaruser'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Userequipox models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserequipoxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Userequipox model.
     * @param integer $id
     * @param integer $userId
     * @param integer $equipoxId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $userId, $equipoxId) {
        return $this->render('view', [
                    'model' => $this->findModel($id, $userId, $equipoxId),
        ]);
    }

    public function actionReport($id, $userId, $equipoxId) {
        $content = $this->renderPartial('view', [
            'model' => $this->findModel($id, $userId, $equipoxId),
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
     * Creates a new Userequipox model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        /*$model = new Userequipox();
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
        ]);*/
        $model = new Userequipox();
        $datos = Yii::$app->request->post();
        Yii::error($datos);
        if (count($datos)>0) {            
            $datos = Yii::$app->request->post();
            $model->userId = $datos['userId'];
            $model->equipoxId = $datos['equipoxId'];
            $model->tiempo = $datos['tiempo'];
            Yii::error($model);
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
     * Updates an existing Userequipox model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $userId
     * @param integer $equipoxId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
       /* $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        return $this->renderPartial('update', ['model' => $model]);*/
        $model = $this->findModel($id);
        $datos = Yii::$app->request->post();
        Yii::error($datos);
        if (count($datos)>0) {            
            $datos = Yii::$app->request->post();
            $model->userId = $datos['userId'];
            $model->equipoxId = $datos['equipoxId'];
            $model->tiempo = $datos['tiempo'];
            Yii::error($model);
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
     * Deletes an existing Userequipox model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param integer $userId
     * @param integer $equipoxId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $userId, $equipoxId) {
        $this->findModel($id, $userId, $equipoxId)->delete();

        return $this->redirect(['index']);
    }

    public function actionEliminar($id) {
        return $this->findModel($id)->delete();
    }

    /**
     * Finds the Userequipox model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $userId
     * @param integer $equipoxId
     * @return Userequipox the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Userequipox::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionValidaruser(){
        Yii::error("Ingresa Validad user");
        $datos = Yii::$app->request->post();
        Yii::error("datos: ");
        Yii::error($datos);//.' or equipoxId='.$datos['equipoxId']
        $equipoId = Userequipox::find()->where('userId='.$datos['userId'].' or equipoxId='.$datos['equipoxId'] )->asArray()->one();
        //$ultimo = Clientes::find()->where(['FederalTaxId' => $nit])->all();
        Yii::error($equipoId);
        if(count($equipoId)>0){
            $equipo = Equipox::find()->where(['id'=>$equipoId['equipoxId']])->asArray()->one();
            Yii::error($equipo);
            if(count($equipo)>0){                                
                return "El usuario seleccionado ya esta asignado al equipo: ".$equipo['equipo'];
            }

        }
        return "true";
    }

}
