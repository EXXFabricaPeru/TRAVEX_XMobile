<?php

namespace backend\controllers;

use Yii;
use backend\models\Rutacabecera;
use backend\models\RutacabeceraSearch;
use backend\models\Rutadetalle;
use backend\models\Clientes;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * RutaController implements the CRUD actions for Ruta model.
 */
class RutaController extends Controller {

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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report', 'traerdatoscliente'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all ruta models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new RutacabeceraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

     /**
     * Displays a single ruta model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

     /**
     * Creates a new ruta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $datos = Yii::$app->request->post();
        $model = new Rutacabecera();
        if ($model->load(Yii::$app->request->post()) || (count($datos))) {
            $fecha = date("Y-m-d h:i:s");
            $usuario = Yii::$app->user->identity->getId();
            if($datos["idActualizar"]!=0){
                // se actualiza el estado del registro de la ruta
                Yii::error("Anactiva primera ruta: ". $datos["idActualizar"]);
                $modelUpdate = RutaCabecera::find()->where(['id' => $datos["idActualizar"]])->one();
                
               // Yii::error(json_encode($modelUpdate));
                $modelUpdate->status=0;
                Yii::error("MODELO RUTA: ".$modelUpdate->vendedor);
                
                if($modelUpdate->update()){
                    Yii::error("SE ACTUALIZO CORRECTAMENTE: ");
                }else{
                    Yii::error("ERROR AL ACTUALIZAR: ");
                    $data2 = $modelUpdate->getErrors();
                    Yii::error(json_encode($data2));
                }
            }
            
            
            $model->id = 0;
            $model->idvendedor = $datos["idvendedor"];
            $model->vendedor = $datos["vendedor"];
            $model->nombre = $datos["nombre"];
            $model->fecha = $datos["fecha"];
            $model->idclienteinicial = $datos["idclienteinicial"];
            $model->latitud = $datos["latitud"];
            $model->longitud = $datos["longitud"];
            $model->usuario = $usuario;
            $model->status = 1;
            $model->dateUpdate = $fecha;
            $model->tipousuario =$datos["tipousuario"];
            $model->fechapicking = $datos["fechapicking"];
            if ($model->save()) {                
                foreach($datos["detalle"] as $d){
                    $detalle = new Rutadetalle();
                    $detalle->id = 0;
                    $detalle->idcabecera = $model->id;
                    $detalle->idcliente = $d["cliente"];
                    $detalle->cardname = $d["cardname"];
                    $detalle->posicion = $d["pos"];
                    $detalle->longitud = $d["lon"];
                    $detalle->latitud = $d["lat"];
                    $detalle->usuario = $usuario;
                    $detalle->status = 1;
                    $detalle->dateUpdate = $d["fechaupdate"];
                    $detalle->tipodoc = $d["tipodoc"];
                    $detalle->iddoc = $d["iddoc"];
                    $detalle->nropicking = $d["nropicking"];
                    $detalle->save();
                   
                }                
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
     * Updates an existing ruta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id = 0) {
        $datos = Yii::$app->request->post();
        if (count($datos)) $id = $datos["id"];
        //$model = $this->findModel($id);
        $model = RutaCabecera::find()->where(['id' => $id])->one();
        $detalle = Rutadetalle::find()->where(['=', 'idcabecera', $id])->all();
        //if ($model->load(Yii::$app->request->post()) || count($datos)) {
        if (count($datos)) {
            $fecha = date("Y-m-d");
            $usuario = Yii::$app->user->identity->getId();
            if($datos["idActualizar"]!=0){
                // se actualiza el estado del registro de la ruta
                Yii::error("Anactiva primera ruta: ". $datos["idActualizar"]);
                $modelUpdate = RutaCabecera::find()->where(['id' => $datos["idActualizar"]])->one();
                
               // Yii::error(json_encode($modelUpdate));
                $modelUpdate->status=0;
                Yii::error("MODELO RUTA: ".$modelUpdate->vendedor);
                
                if($modelUpdate->update()){
                    Yii::error("SE ACTUALIZO CORRECTAMENTE: ");
                }else{
                    Yii::error("ERROR AL ACTUALIZAR: ");
                    $data2 = $modelUpdate->getErrors();
                    Yii::error(json_encode($data2));
                }
            }
            
            //$model = RutaCabecera::find(['id' => $id])->one();
            $model->idvendedor = $datos["idvendedor"];
            $model->vendedor = $datos["vendedor"];
            $model->nombre = $datos["nombre"];
            $model->fecha = $datos["fecha"];
            $model->idclienteinicial = $datos["idclienteinicial"];
            $model->latitud = $datos["latitud"];
            $model->status = 1;
            $model->longitud = $datos["longitud"];
            $model->tipousuario = $datos["tipousuario"];
            $model->fechapicking = $datos["fechapicking"];
            if ($model->save()) {
                $borrar =  Rutadetalle::find()->where(["idcabecera" => $id])->all();
                foreach($borrar as $b){
                    $b->delete();
                }
                 Yii::error("detalle77");
                    Yii::error($datos["detalle"]);

                foreach($datos["detalle"] as $d){
                    $detalle = new Rutadetalle();
                    $detalle->id = 0;
                    $detalle->idcabecera = $id;
                    $detalle->idcliente = $d["cliente"];
                    $detalle->cardname = $d["cardname"];
                    $detalle->posicion = $d["pos"];
                    $detalle->longitud = $d["lon"];
                    $detalle->latitud = $d["lat"];
                    $detalle->usuario = $usuario;
                    $detalle->status = 1;
                    $detalle->dateUpdate = $d["fechaupdate"];
                    $detalle->tipodoc = $d["tipodoc"];
                    $detalle->iddoc = $d["iddoc"];
                    $detalle->nropicking = $d["nropicking"];
                   
                    $detalle->save();
                }
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }
        return $this->renderPartial('update', [
                                                'model' => $model,
                                                'detalle' => $detalle
                                            ]);

    }

    /**
     * Deletes an existing ruta model.
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
     * Finds the ruta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rutacabecera the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Rutacabecera::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionTraerdatoscliente(){
        $datos = Yii::$app->request->post();
        $idcliente = $datos["id"];
        $cliente = Clientes::find()->where(['=', 'id', $idcliente])->asArray()->one();
        return json_encode($cliente);
    }
}