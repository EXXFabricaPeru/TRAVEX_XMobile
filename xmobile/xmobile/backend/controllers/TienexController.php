<?php

namespace backend\controllers;

use Yii;
use backend\models\Tienex;
use backend\models\Permisosx;
use backend\models\Acciones;
use backend\models\TienexSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * TienexController implements the CRUD actions for Tienex model.
 */
class TienexController extends Controller {

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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report', 'nuevaaccionadicional', 'borraraccionadicional'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Tienex models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TienexSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tienex model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $roles = \backend\models\Rolex::find()->all();
        $accionesUsuario = $this->obtenerAcciones($id);
        $accionesAdicionales = $this->obtenerAccionesAdicionales($id);
        
        $listController = Acciones::find()->all();
        return $this->renderAjax('asignaciones', [
                    'roles' => $roles,
                    'acciones' => $listController,
                    'accionesActivadas' => $accionesUsuario,
                    'accionesAdicionales' => $accionesAdicionales,
                    'id' => $id
        ]);
    }

    public function actionBorraraccionadicional() {
        $data = Yii::$app->request->post();
        $tieneBorrar = Tienex::find()->where(["userId" => $data['idUsuario'], "accionesId" => $data['idAccion']])->one();
        $idDeleted = $tieneBorrar->id;
        $tieneBorrar->delete();
        return json_encode($idDeleted);
    }

    public function actionNuevaaccionadicional() {
        $data = Yii::$app->request->post();
        $tiene = new Tienex();
        $tiene->rolexId = 0;
        $tiene->userId = $data['idUsuario'];
        $tiene->accionesId = $data['idAccion'];
        if ($tiene->save()) {
            return $tiene->id;
        } else {
            $data = $tiene->getErrors();
            return json_encode($data);
        }
        return $tiene->accionesId;
    }

    private function obtenerAccionesAdicionales($id) {
        $accionesAdicionales = Tienex::find()->where(["userId" => $id, "rolexId" => 0])->all();
        $acciones = [];
        foreach ($accionesAdicionales as $accion) {
            array_push($acciones, $accion->accionesId);
        }
        return $acciones;
    }

    private function obtenerAcciones($id){
        $rolesUsuario = Tienex::find()->where(["userId" => $id])->all();
        $rolUsuarioParaPermiso = [];
        foreach ($rolesUsuario as $rolUser) {
            array_push($rolUsuarioParaPermiso, $rolUser->rolexId);
        }
        $permisosList = [];
        for($i=0;$i<count($rolUsuarioParaPermiso);$i++) {
            $permisos = Permisosx::find()->where(["rolexId" => $rolUsuarioParaPermiso[$i]])->all();
            foreach ($permisos as $permiso) {
                array_push($permisosList, $permiso->accionesId);
            }
        }
        return $permisosList;
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
     * Creates a new Tienex model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $data = Yii::$app->request->post();
        $resp = Tienex::find()->where(["rolexId" => $data["id"], "userId" => $data["iduser"]])->all();
        if (count($resp) == 0) {
            $model = new Tienex();
            $model->rolexId = $data["id"];
            $model->userId = $data["iduser"];
            if ($model->save()) {
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
        } else {
            return $this->findModel($resp[0]->id)->delete();
        }
        /* $model = new Tienex();
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
          ]); */
    }

    /**
     * Updates an existing Tienex model.
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
     * Deletes an existing Tienex model.
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
     * Finds the Tienex model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tienex the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tienex::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
