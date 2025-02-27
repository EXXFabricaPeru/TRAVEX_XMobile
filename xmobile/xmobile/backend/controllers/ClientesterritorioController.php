<?php

namespace backend\controllers;

use Yii;
use backend\models\Clientesterritorio;
use backend\models\ClientesterritorioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use backend\models\Servislayer;
/**
 * ClientesterritorioController implements the CRUD actions for Clientesterritorio model.
 */
  
class ClientesterritorioController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','reasignarterritorio'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Clientesterritorio models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientesterritorioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Clientesterritorio model.
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
     * Creates a new Clientesterritorio model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Clientesterritorio();
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
     * Updates an existing Clientesterritorio model.
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
     * Deletes an existing Clientesterritorio model.
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
     * Finds the Clientesterritorio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Clientesterritorio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Clientesterritorio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionReasignarterritorio(){
        $datos = Yii::$app->request->post();
        $datosError=[];
        Yii::error("DETALLE CLIENTES REASIGNAR TERRITORIOS");
        Yii::error(count($datos));
        Yii::error($datos);

        foreach ($datos["CardCode"] as $key => $value) {
       
           // Yii::error($value);

            $serviceLayer = new Servislayer();
            $datosCliente = [
                "Territory" => $datos["TerritoryId"]
            ];

            Yii::error("ENTRÓ AL UPDATE movil :: " . json_encode($datosCliente));
            $serviceLayer->actiondir = "BusinessPartners('".$value."')";
            Yii::error($serviceLayer->actiondir);
            $clienteSap = $serviceLayer->executePatchPut('PATCH', $datosCliente);

            Yii::error("RESPUESTA SAP :: " . json_encode($clienteSap));

            if ($clienteSap) {
                Yii::error("Actualizacion exitosa" . json_encode($clienteSap));
                $serviceLayer->actiondir = "BusinessPartners('".$cardCode."')";
                $responseSap = $serviceLayer->executex();

                $model = new Clientesterritorio();
                $model->CardCode = $value;
                $model->CardName = "";
                $model->TerritoryId = $datos["TerritoryId"];
                $model->TerritoryName = $datos["TerritoryName"];
                $model->fechaRegistro=date('Y-m-d H:i:s');
                $model->estado=1;  

                if ($model->save(false)) {       
                    Yii::error("se guardo correctamente: ".$value);
                } 
                else {
                    $datError = $model->getErrors();   
                    array_push($datosError,$datError.' - '.$value);
                } 
                  
            }else{
                if (isset($clienteSap->message)) {
                    Yii::error("ID-MID:{$response->id};DATA-" . json_encode($clienteSap->message->value));
                } else {
                    Yii::error("ID-MID1:{$response->id};DATA-" . json_encode($clienteSap));
                }

                $model = new Clientesterritorio();
                $model->CardCode = $value;
                $model->CardName = "";
                $model->TerritoryId = $datos["TerritoryId"];
                $model->TerritoryName = $datos["TerritoryName"];
                $model->fechaRegistro=date('Y-m-d H:i:s');
                $model->estado=0;  
                if ($model->save(false)) {       
                    Yii::error("se guardo correctamente: ".$value);
                } 
                else {
                    $datError = $model->getErrors();   
                    array_push($datosError,$datError.' - '.$value);
                } 
                
            }
            date_default_timezone_set('America/La_Paz');
            
            
        }
            
           
        Yii::error("CardCodes Error registro");
        Yii::error($datosError);
        return json_encode("se guardo correctamente");
    }
}
