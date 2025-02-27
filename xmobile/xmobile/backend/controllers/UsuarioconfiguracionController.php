<?php

namespace backend\controllers;

use Yii;
use backend\models\Usuarioconfiguracion;
use backend\models\UsuarioconfiguracionSearch;
use backend\models\Condicionespagos;
use backend\models\Usuario_condicionespago;
use backend\models\Listapreciosuser;
use backend\models\Usuariocentrodecosto;
use backend\models\Configuracion;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * UsuarioconfiguracionController implements the CRUD actions for Usuarioconfiguracion model.
 */
class UsuarioconfiguracionController extends Controller {

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
     * Lists all Usuarioconfiguracion models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UsuarioconfiguracionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuarioconfiguracion model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $resp = Usuarioconfiguracion::find()->where(['idUser' => $id])->all();
        $cond = Condicionespagos::find()->all();
        $condicionesSeleccionadas = Usuario_condicionespago::find()->where(["idusuario" => $id])->all();
        $textoCondiciones = '';
        foreach ($condicionesSeleccionadas as $condicion) {
            if ($textoCondiciones == '')
                $textoCondiciones = $textoCondiciones . $condicion->idcondicion;
            else
                $textoCondiciones = $textoCondiciones . ',' . $condicion->idcondicion;
        }
        $verCC = Configuracion::find()->where(["parametro" => "usuario_centro_costo"])->one();
        if (count($resp) == 0) {
            $model = new Usuarioconfiguracion();
            return $this->renderPartial('create', [
                        'model' => $model,
                        'condiciones' => $cond,
                        'verCC' => $verCC["valor"]
            ]);
        } else {
            $Centros = Usuariocentrodecosto::find()->where(["iduser" => $id])->all();
            return $this->renderPartial('update', [
                        'model' => $resp[0],
                        'exitx' => $resp[0]->id,
                        'condiciones' => $cond,
                        'textoCondiciones' => $textoCondiciones,
                        'cc' => $Centros,
                        'verCC' => $verCC["valor"]
            ]);
        }
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
     * Creates a new Usuarioconfiguracion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($condiciones, $centro) {
        $model = new Usuarioconfiguracion();
        $condiciones = json_decode($condiciones);
        $cc = $centro;
        if ($model->load(Yii::$app->request->post())) {
            $datax = Yii::$app->request->post();
            $model->almacenes = '0';
            if ($model->save()) {
                if (is_array($condiciones))
                    foreach ($condiciones as $condicion) {
                        $c = new Usuario_condicionespago();
                        $c->id = 0;
                        $c->idusuario = $model->idUser;
                        $c->idcondicion = $condicion;
                        $c->save();
                    }                
                if ($cc != ''){
                    $nuevoCC = new Usuariocentrodecosto();
                    $nuevoCC->id = 0;
                    $nuevoCC->iduser = $model->idUser;
                    $nuevoCC->PrcCode = $cc;
                    $nuevoCC->save();
                }
                $this->inlistaprecios($datax, $model->idUser);
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        return $this->renderPartial('create', [
                    'model' => $model,
                    'cc' => []
        ]);
    }

    /**
     * Updates an existing Usuarioconfiguracion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $condiciones
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function inlistaprecios($data, $idusuario) {
        try {
            $sql = "DELETE FROM listapreciosuser WHERE user_id = " . $idusuario;
            Yii::$app->db->createCommand($sql)->execute();
        } catch (\Exception $e) {
            
        }
        $listaPre = json_decode($data['Usuarioconfiguracion']['multiListaPrecios']);
        foreach ($listaPre as $key) {
            $listPreU = new Listapreciosuser();
            $listPreU->user_id = $idusuario;
            $listPreU->idlistaprecios = $key;
            $listPreU->save();
        }
    }

    public function actionUpdate($id, $condiciones, $centro) {
        $model = $this->findModel($id);
        $condiciones = json_decode($condiciones);
        $cc = $centro;
        if ($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            if ($model->save()) {
                $idusuario = $model->idUser;
                $borrar = Usuario_condicionespago::find()->where(["idusuario" => $model->idUser])->all();
                foreach ($borrar as $b) {
                    $b->delete();
                }
                if (is_array($condiciones))
                    foreach ($condiciones as $condicion) {
                        $c = new Usuario_condicionespago();
                        $c->id = 0;
                        $c->idusuario = $idusuario;
                        $c->idcondicion = $condicion;
                        $c->save();
                    }
                if ($cc != ''){
                    $borrarCC =  Usuariocentrodecosto::find()->where(["iduser" => $model->idUser])->all();
                    foreach($borrarCC as $b) $b->delete();
                    $nuevoCC = new Usuariocentrodecosto();
                    $nuevoCC->id = 0;
                    $nuevoCC->iduser = $model->idUser;
                    $nuevoCC->PrcCode = $cc;
                    $nuevoCC->save();
                }
                $this->inlistaprecios($data, $idusuario);
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        $Centros = Usuariocentrodecosto::find()->where(["iduser" => $model->idUser])->all();
        return $this->renderPartial('update', ['model' => $model, 'cc' => $Centros]);
    }

    /**
     * Deletes an existing Usuarioconfiguracion model.
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
     * Finds the Usuarioconfiguracion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuarioconfiguracion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Usuarioconfiguracion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
