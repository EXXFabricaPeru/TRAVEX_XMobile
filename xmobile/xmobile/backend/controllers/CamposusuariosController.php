<?php

namespace backend\controllers;

use Yii;
use backend\models\Camposusuarios;
use backend\models\CamposUsuariosSearch;
use backend\models\Listacamposusuarios;
use backend\models\ListacamposusuariosSearch;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use yii\web\Response;
/**
 * CamposusuariosController implements the CRUD actions for Camposusuarios model.
 */
  
class CamposusuariosController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','listamidd','validanombre'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Camposusuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CamposUsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Camposusuarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $searchModel = new ListacamposusuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['IdcampoUsuario' => $id]);
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'id' => $id,
        ]);

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
     * Creates a new Camposusuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Camposusuarios();
        $model->Status = '1';
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $model->Id;
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
     * Updates an existing Camposusuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $model->Id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        return $this->renderPartial('update', ['model' => $model]);
    }
    
    public function actionListamidd(){

        $id = Yii::$app->request->post('id');
        $resultado = Yii::$app->db->createCommand("SELECT id,nombre FROM camposusuario_camposmidd c where c.idobjeto = '".$id."' and c.status  = 1 and c.id not in(select campmidd from camposusuarios where objeto = '".$id."' and campmidd is not null)" )->queryAll();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (count($resultado)){
          return $resultado;// $this->correcto($resultado);
        }else{
          $arrayError= [];
          $error = ['' => ''];
          array_push($arrayError,$error);
          return $arrayError;
        }
      }

      

      public function actionValidanombre(){

        $nombre = strtolower(Yii::$app->request->post('nombre'));
        $resultado = Yii::$app->db->createCommand("select count(*) as cantidad from camposusuarios where nombre = '".$nombre."'" )->queryAll();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (count($resultado)){
          return $resultado;// $this->correcto($resultado);
        }else{
          $arrayError= [];
          $error = ['cantidad' => '0'];
          array_push($arrayError,$error);
          return $arrayError;
        }
      }
    
    /**
     * Deletes an existing Camposusuarios model.
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
     * Finds the Camposusuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Camposusuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Camposusuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
