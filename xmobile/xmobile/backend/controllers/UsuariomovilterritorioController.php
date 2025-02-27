<?php

namespace backend\controllers;

use Yii;
use backend\models\Usuariomovilterritorio;
use backend\models\Usuariomovilterritoriodetalle;
use backend\models\UsuariomovilterritorioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
/**
 * UsuariomovilterritorioController implements the CRUD actions for Usuariomovilterritorio model.
 */
  
class UsuariomovilterritorioController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','consultas'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Usuariomovilterritorio models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuariomovilterritorioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuariomovilterritorio model.
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
     * Creates a new Usuariomovilterritorio model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Usuariomovilterritorio();
       

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                //se guarda el detalle
                $territorioValor= explode('@',$model->territorio);
                for($i=0; $i<count($territorioValor)-1;$i++){
                    $territorio=explode('=>',$territorioValor[$i]);
                   
                    $modelDetalle = new Usuariomovilterritoriodetalle();
                    $modelDetalle->idCabecera=$model->id;
                    $modelDetalle->idUserMovil=$model->idUser;
                    $modelDetalle->userMovil=$model->user;
                    $modelDetalle->idTerritorio=$territorio[0];
                    $modelDetalle->territorio=$territorio[1];
                    $modelDetalle->estado='A';
                    $modelDetalle->fechaUpdate=$model->fechaSistema;
                    $modelDetalle->save();
                    Yii::error("SE INSERTA DETALLE");
                    Yii::error($model->territorio);
                }
                //fin
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
     * Updates an existing Usuariomovilterritorio model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $borrar = Usuariomovilterritoriodetalle::find()->where("idCabecera = ".$model->id)->all();
                foreach($borrar as $valor) $valor->delete(); 

                 //se guarda el detalle
                $territorioValor= explode('@',$model->territorio);
                for($i=0; $i<count($territorioValor)-1;$i++){
                     $territorio=explode('=>',$territorioValor[$i]);
                    
                     $modelDetalle = new Usuariomovilterritoriodetalle();
                     $modelDetalle->idCabecera=$model->id;
                     $modelDetalle->idUserMovil=$model->idUser;
                     $modelDetalle->userMovil=$model->user;
                     $modelDetalle->idTerritorio=$territorio[0];
                     $modelDetalle->territorio=$territorio[1];
                     $modelDetalle->estado='A';
                     $modelDetalle->fechaUpdate=$model->fechaSistema;
                     $modelDetalle->save();
                     Yii::error("SE INSERTA DETALLE");
                     Yii::error($model->territorio);
                }
                 //fin

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
     * Deletes an existing Usuariomovilterritorio model.
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
     * Finds the Usuariomovilterritorio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuariomovilterritorio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuariomovilterritorio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	public function actionConsultas(){
	    $model = new Usuariomovilterritorio();
        return $this->renderPartial('consultas', ['model' => $model]);
    
    }

}
