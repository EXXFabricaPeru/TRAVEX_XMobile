<?php

namespace backend\controllers;

use Yii;
use backend\models\Geolocalizacion;
use backend\models\GeolocalizacionSearch;
use backend\models\Cabeceradocumentos;
use backend\models\Poligonodetalle;
use backend\models\Rutadetalle;
use backend\models\Equipox;
use backend\models\Clientes;
use backend\models\Configuracion;
use backend\models\Visitas;
use backend\models\Pagos;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;

/**
 * GeolocalizacionController implements the CRUD actions for Geolocalizacion model.
 */
class GeolocalizacionController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'report', 'cargarpuntoshoy', 'mapapordocumento', 'mapacomparaciones', 'mapaporcliente', 'cargarpuntossoloclientes', 'actualizarcliente','mapavisitas'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Geolocalizacion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GeolocalizacionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['id' => '0']);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Geolocalizacion model.     
     */
    public function actionView()
    {
    }

    /*public function actionReport($id) {
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
    }*/

    /**
     * Creates a new Geolocalizacion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
    }

    /**
     * Updates an existing Geolocalizacion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     */
    public function actionUpdate()
    {
    }

    /**
     * Deletes an existing Geolocalizacion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     */
    public function actionDelete()
    {
    }

    /**
     * Finds the Geolocalizacion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $idusuario
     * @return Geolocalizacion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($idusuario)
    {
        if (($model = Geolocalizacion::find()->where(['=', 'usuario', $id])->asArray()->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCargarpuntoshoy()
    {
        $datos = Yii::$app->request->post();
        $idequipox = $datos["equipox"];
        $fechaInicial = $datos["inicio"];
        $fechaFinal = $datos["fin"];
        $horaInicial = $datos["hinicio"];
        $horaFinal = $datos["hfin"];
        // valido los campos horas
        if(empty($horaInicial)){
            $horaInicial='00:00:00';
        }
        if(empty($horaFinal)){
            $horaFinal='23:59:59';
        }

        //obtenemos los ids de los equipos
        $uuids_array=array();
        if(!empty($idequipox)){
            $uuids = Equipox::find()
            ->where(['in', 'id', $idequipox])
            ->asArray()
            ->all();
            foreach ($uuids as $key => $uuid) {
                $uuids_array[]=$uuid['uuid'];
            }
        }
        $resultado = [];
        if ($fechaInicial) {
            if ($fechaFinal) {
                // tiene ambas fechas
                $puntos = Geolocalizacion::find()
                    ->select([
                        'geolocalizacion.idequipox', 
                        'geolocalizacion.latitud', 
                        'geolocalizacion.longitud', 
                        'geolocalizacion.fecha', 
                        'geolocalizacion.hora', 
                        'equipox.equipo',
                        'user.username'
                    ] )
                    ->leftjoin('equipox', 'equipox.uuid = geolocalizacion.idequipox')
                    ->leftjoin('user', 'user.id = geolocalizacion.usuario');
                if(!empty($uuids_array)){
                    $puntos->where(['in', 'geolocalizacion.idequipox', $uuids_array]);
                }
                $puntos->andFilterWhere(['between', 'geolocalizacion.fecha', $fechaInicial, $fechaFinal])
                ->andFilterWhere(['between', 'geolocalizacion.hora', $horaInicial, $horaFinal])
               // ->groupby('longitud')
                ->asArray();
                return json_encode($puntos->all());
            } else {
                // solo tiene inicial
                $puntos = Geolocalizacion::find()
                    ->select([
                        'geolocalizacion.idequipox', 
                        'geolocalizacion.latitud', 
                        'geolocalizacion.longitud', 
                        'geolocalizacion.fecha', 
                        'geolocalizacion.hora', 
                        'equipox.equipo',
                        'user.username'
                    ] )
                    ->leftjoin('equipox', 'equipox.uuid = geolocalizacion.idequipox')
                    ->leftjoin('user', 'user.id = geolocalizacion.usuario');
                if(!empty($uuids_array)){
                    $puntos->where(['in', 'geolocalizacion.idequipox', $uuids_array]);
                }
                $puntos->andFilterWhere(['>=', 'geolocalizacion.fecha', $fechaInicial])
                ->andFilterWhere(['between', 'geolocalizacion.hora', $horaInicial, $horaFinal])
                 //->groupby('longitud')
                ->asArray();
                return json_encode($puntos->all());
            }
        } else if ($fechaFinal) {
            // solo tiene final
            $puntos = Geolocalizacion::find()
                ->select([
                    'geolocalizacion.idequipox', 
                    'geolocalizacion.latitud', 
                    'geolocalizacion.longitud', 
                    'geolocalizacion.fecha', 
                    'geolocalizacion.hora', 
                    'equipox.equipo',
                    'user.username'
                ] )
                ->leftjoin('equipox', 'equipox.uuid = geolocalizacion.idequipox')
                ->leftjoin('user', 'user.id = geolocalizacion.usuario');
            if(!empty($uuids_array)){
                $puntos->where(['in', 'geolocalizacion.idequipox', $uuids_array]);
            }
            $puntos->andFilterWhere(['<=', 'geolocalizacion.fecha', $fechaFinal])
                ->andFilterWhere(['between', 'geolocalizacion.hora', $horaInicial, $horaFinal])
                 //->groupby('longitud')
                ->asArray();

            return json_encode($puntos->all());
        } else {
            // no tiene fechas
            $puntos = Geolocalizacion::find()
                ->select([
                    'geolocalizacion.idequipox', 
                    'geolocalizacion.latitud', 
                    'geolocalizacion.longitud', 
                    'geolocalizacion.fecha', 
                    'geolocalizacion.hora', 
                    'equipox.equipo',
                    'user.username'
                ] )
                ->leftjoin('equipox', 'equipox.uuid = geolocalizacion.idequipox')
                ->leftjoin('user', 'user.id = geolocalizacion.usuario');
            if(!empty($uuids_array)){
                $puntos->where(['in', 'geolocalizacion.idequipox', $uuids_array]);
            }
            $puntos->andFilterWhere(['between', 'geolocalizacion.hora', $horaInicial, $horaFinal])
            // ->groupby('longitud')
                ->asArray();
            return json_encode($puntos->all());
        }
    }

    public function actionMapapordocumento()
    {
        $datos = Yii::$app->request->post();
        $idusuario = $datos["usuario"];
        $documento = $datos["documento"];
        $fechaInicial = $datos["inicio"];
        $fechaFinal = $datos["fin"];
        $resultadoFinal = [];
        if ($fechaInicial) {
            if ($fechaFinal) {
                // tiene ambas fechas
                if ($documento != '') {
                    $puntos = Cabeceradocumentos::find()
                    ->select([
                        'cabeceradocumentos.idDocPedido', 
                        'cabeceradocumentos.CardCode',
                        'cabeceradocumentos.CardName',
                        'cabeceradocumentos.DocType',
                        'cabeceradocumentos.U_LATITUD',
                        'cabeceradocumentos.U_LONGITUD',
                        'cabeceradocumentos.fecharegistro',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                    ->where([
                        'cabeceradocumentos.idUser' => $idusuario,
                        'cabeceradocumentos.DocType' => $documento
                    ])
                        ->andFilterWhere(['between', 'cabeceradocumentos.fecharegistro', $fechaInicial.' 00:00:00', $fechaFinal.' 23:59:59'])
                        ->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                        ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                       ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                        ->orderBy('cabeceradocumentos.fecharegistro DESC')
                        ->asArray()->all();
                       
                    $puntosPagos =  Pagos::find()
                    ->select([
                        'pagos.recibo', 
                        'pagos.clienteId',
                        'clientes.CardName',
                        'pagos.U_LATITUD',
                        'pagos.U_LONGITUD',
                        'pagos.fecha',
                        'pagos.hora',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id = pagos.usuario')
                    ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                    ->where([
                        'pagos.usuario' => $idusuario
                    ])
                        ->andFilterWhere(['>=', 'pagos.fecha', $fechaInicial])
                        ->andFilterWhere(['<=', 'pagos.fecha', $fechaFinal])
                        ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                        ->asArray()->all();
                    
                    foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                    foreach($puntosPagos as $pago){
                        if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                            array_push($resultadoFinal, [
                                'idDocPedido' => $pago['recibo'], 
                                'CardCode' => $pago['clienteId'],
                                'CardName' => $pago['CardName'],
                                'DocType' => 'Pago',
                                'U_LATITUD' => $pago['U_LATITUD'],
                                'U_LONGITUD' => $pago['U_LONGITUD'],
                                'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                'username' => $pago['username']
                            ]);
                        }
                    }
                    return json_encode($resultadoFinal);  
                } else {
                    $puntos = Cabeceradocumentos::find()
                    ->select([
                        'cabeceradocumentos.idDocPedido',
                        'cabeceradocumentos.CardCode',
                        'cabeceradocumentos.CardName',
                        'cabeceradocumentos.DocType',
                        'cabeceradocumentos.U_LATITUD',
                        'cabeceradocumentos.U_LONGITUD',
                        'cabeceradocumentos.fecharegistro',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                    ->where(['cabeceradocumentos.idUser' => $idusuario])
                        ->andFilterWhere(['between', 'cabeceradocumentos.fecharegistro', $fechaInicial.' 00:00:00', $fechaFinal.' 23:59:59'])
                        ->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                        ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                        ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                        ->orderBy('cabeceradocumentos.fecharegistro DESC')
                        ->asArray()->all();

                        $puntosPagos =  Pagos::find()
                        ->select([
                            'pagos.recibo', 
                            'pagos.clienteId',
                            'clientes.CardName',
                            'pagos.U_LATITUD',
                            'pagos.U_LONGITUD',
                            'pagos.fecha',
                            'pagos.hora',
                            'user.username'
                        ])
                        ->leftjoin('user', 'user.id = pagos.usuario')
                        ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                        ->where([
                            'pagos.usuario' => $idusuario
                        ]) 
                            ->andFilterWhere(['>=', 'pagos.fecha', $fechaInicial])
                            ->andFilterWhere(['<=', 'pagos.fecha', $fechaFinal])
                            ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                            ->asArray()->all();
                        
                        foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                        foreach($puntosPagos as $pago){
                            if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                                array_push($resultadoFinal, [
                                    'idDocPedido' => $pago['recibo'], 
                                    'CardCode' => $pago['clienteId'],
                                    'CardName' => $pago['CardName'],
                                    'DocType' => 'Pago',
                                    'U_LATITUD' => $pago['U_LATITUD'],
                                    'U_LONGITUD' => $pago['U_LONGITUD'],
                                    'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                    'username' => $pago['username']
                                ]);
                            }
                        }
                        return json_encode($resultadoFinal);  
                }
            } else {
                // solo tiene inicial
                if ($documento != '') {
                    $puntos = Cabeceradocumentos::find()
                    ->select([
                        'cabeceradocumentos.idDocPedido',
                        'cabeceradocumentos.CardCode',
                        'cabeceradocumentos.CardName',
                        'cabeceradocumentos.DocType',
                        'cabeceradocumentos.U_LATITUD',
                        'cabeceradocumentos.U_LONGITUD',
                        'cabeceradocumentos.fecharegistro',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                    ->where([
                        'cabeceradocumentos.idUser' => $idusuario,
                        'cabeceradocumentos.DocType' => $documento
                    ])->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                        ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                        ->andFilterWhere(['>=', 'cabeceradocumentos.DocDate', $fechaInicial])
                       ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                        ->orderBy('cabeceradocumentos.fecharegistro DESC')
                        ->asArray()->all();
                        
                        $puntosPagos =  Pagos::find()
                        ->select([
                            'pagos.recibo', 
                            'pagos.clienteId',
                            'clientes.CardName',
                            'pagos.U_LATITUD',
                            'pagos.U_LONGITUD',
                            'pagos.fecha',
                            'pagos.hora',
                            'user.username'
                        ])
                        ->leftjoin('user', 'user.id = pagos.usuario')
                        ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                        ->where([
                            'pagos.usuario' => $idusuario
                        ])
                            ->andFilterWhere(['>=', 'pagos.fecha', $fechaInicial])
                            ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                            ->asArray()->all();
                        
                        foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                        foreach($puntosPagos as $pago){
                            if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                                array_push($resultadoFinal, [
                                    'idDocPedido' => $pago['recibo'], 
                                    'CardCode' => $pago['clienteId'],
                                    'CardName' => $pago['CardName'],
                                    'DocType' => 'Pago',
                                    'U_LATITUD' => $pago['U_LATITUD'],
                                    'U_LONGITUD' => $pago['U_LONGITUD'],
                                    'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                    'username' => $pago['username']
                                ]);
                            }
                        }
                        return json_encode($resultadoFinal);  
                } else {
                    $puntos = Cabeceradocumentos::find()
                    ->select([
                        'cabeceradocumentos.idDocPedido',
                        'cabeceradocumentos.CardCode',
                        'cabeceradocumentos.CardName',
                        'cabeceradocumentos.DocType',
                        'cabeceradocumentos.U_LATITUD',
                        'cabeceradocumentos.U_LONGITUD',
                        'cabeceradocumentos.fecharegistro',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                    ->where(['cabeceradocumentos.idUser' => $idusuario])
                        ->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                        ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                        ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                        ->andFilterWhere(['>=', 'cabeceradocumentos.DocDate', $fechaInicial])
                        ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                        ->orderBy('cabeceradocumentos.fecharegistro DESC')
                        ->asArray()->all();
                        
                        $puntosPagos =  Pagos::find()
                        ->select([
                            'pagos.recibo', 
                            'pagos.clienteId',
                            'clientes.CardName',
                            'pagos.U_LATITUD',
                            'pagos.U_LONGITUD',
                            'pagos.fecha',
                            'pagos.hora',
                            'user.username'
                        ])
                        ->leftjoin('user', 'user.id = pagos.usuario')
                        ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                        ->where([
                            'pagos.usuario' => $idusuario
                        ])
                            ->andFilterWhere(['>=', 'pagos.fecha', $fechaInicial])
                            ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                            ->asArray()->all();
                        
                        foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                        foreach($puntosPagos as $pago){
                            if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                                array_push($resultadoFinal, [
                                    'idDocPedido' => $pago['recibo'], 
                                    'CardCode' => $pago['clienteId'],
                                    'CardName' => $pago['CardName'],
                                    'DocType' => 'Pago',
                                    'U_LATITUD' => $pago['U_LATITUD'],
                                    'U_LONGITUD' => $pago['U_LONGITUD'],
                                    'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                    'username' => $pago['username']
                                ]);
                            }
                        }
                        return json_encode($resultadoFinal);  
                }
            }
        } else if ($fechaFinal) {
            // solo tiene final
            if ($documento != '') {
                $puntos = Cabeceradocumentos::find()
                ->select([
                    'cabeceradocumentos.idDocPedido',
                    'cabeceradocumentos.CardCode',
                    'cabeceradocumentos.CardName',
                    'cabeceradocumentos.DocType',
                    'cabeceradocumentos.U_LATITUD',
                    'cabeceradocumentos.U_LONGITUD',
                    'cabeceradocumentos.fecharegistro',
                    'user.username'
                ])
                ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                ->where([
                    'cabeceradocumentos.idUser' => $idusuario,
                    'cabeceradocumentos.DocType' => $documento
                ])->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                    ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                    ->andFilterWhere(['<=', 'cabeceradocumentos.DocDate', $fechaFinal])
                    ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                    ->orderBy('cabeceradocumentos.fecharegistro DESC')
                    ->asArray()->all();
                        
                    $puntosPagos =  Pagos::find()
                    ->select([
                        'pagos.recibo', 
                        'pagos.clienteId',
                        'clientes.CardName',
                        'pagos.U_LATITUD',
                        'pagos.U_LONGITUD',
                        'pagos.fecha',
                        'pagos.hora',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id = pagos.usuario')
                    ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                    ->where([
                        'pagos.usuario' => $idusuario
                    ])
                        ->andFilterWhere(['<=', 'pagos.fecha', $fechaFinal])
                        ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                        ->asArray()->all();
                    
                    foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                    foreach($puntosPagos as $pago){
                        if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                            array_push($resultadoFinal, [
                                'idDocPedido' => $pago['recibo'], 
                                'CardCode' => $pago['clienteId'],
                                'CardName' => $pago['CardName'],
                                'DocType' => 'Pago',
                                'U_LATITUD' => $pago['U_LATITUD'],
                                'U_LONGITUD' => $pago['U_LONGITUD'],
                                'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                'username' => $pago['username']
                            ]);
                        }
                    }
                    return json_encode($resultadoFinal);  
            } else {
                $puntos = Cabeceradocumentos::find()
                ->select([
                    'cabeceradocumentos.idDocPedido',
                    'cabeceradocumentos.CardCode',
                    'cabeceradocumentos.CardName',
                    'cabeceradocumentos.DocType',
                    'cabeceradocumentos.U_LATITUD',
                    'cabeceradocumentos.U_LONGITUD',
                    'cabeceradocumentos.fecharegistro',
                    'user.username'
                ])
                ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                ->where(['cabeceradocumentos.idUser' => $idusuario])
                    ->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                    ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                    ->andFilterWhere(['<=', 'cabeceradocumentos.DocDate', $fechaFinal])
                    ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                    ->orderBy('cabeceradocumentos.fecharegistro DESC')
                    ->asArray()->all();
                        
                    $puntosPagos =  Pagos::find()
                    ->select([
                        'pagos.recibo', 
                        'pagos.clienteId',
                        'clientes.CardName',
                        'pagos.U_LATITUD',
                        'pagos.U_LONGITUD',
                        'pagos.fecha',
                        'pagos.hora',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id = pagos.usuario')
                    ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                    ->where([
                        'pagos.usuario' => $idusuario
                    ])
                        ->andFilterWhere(['<=', 'pagos.fecha', $fechaFinal])
                        ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                        ->asArray()->all();
                    
                    foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                    foreach($puntosPagos as $pago){
                        if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                            array_push($resultadoFinal, [
                                'idDocPedido' => $pago['recibo'], 
                                'CardCode' => $pago['clienteId'],
                                'CardName' => $pago['CardName'],
                                'DocType' => 'Pago',
                                'U_LATITUD' => $pago['U_LATITUD'],
                                'U_LONGITUD' => $pago['U_LONGITUD'],
                                'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                'username' => $pago['username']
                            ]);
                        }
                    }
                    return json_encode($resultadoFinal);  
            }
        } else {
            // no tiene fechas
            if ($documento != '') {
                $puntos = Cabeceradocumentos::find()
                ->select([
                    'cabeceradocumentos.idDocPedido',
                    'cabeceradocumentos.CardCode',
                    'cabeceradocumentos.CardName',
                    'cabeceradocumentos.DocType',
                    'cabeceradocumentos.U_LATITUD',
                    'cabeceradocumentos.U_LONGITUD',
                    'cabeceradocumentos.fecharegistro',
                    'user.username'
                ])
                ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                ->where([
                    'cabeceradocumentos.idUser' => $idusuario,
                    'cabeceradocumentos.DocType' => $documento
                ])->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                    ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                    ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                    ->orderBy('cabeceradocumentos.fecharegistro DESC')
                    ->asArray()->all();
                        
                    $puntosPagos =  Pagos::find()
                    ->select([
                        'pagos.recibo', 
                        'pagos.clienteId',
                        'clientes.CardName',
                        'pagos.U_LATITUD',
                        'pagos.U_LONGITUD',
                        'pagos.fecha',
                        'pagos.hora',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id = pagos.usuario')
                    ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                    ->where([
                        'pagos.usuario' => $idusuario
                    ])
                        ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                        ->asArray()->all();
                    
                    foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                    foreach($puntosPagos as $pago){
                        if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                            array_push($resultadoFinal, [
                                'idDocPedido' => $pago['recibo'], 
                                'CardCode' => $pago['clienteId'],
                                'CardName' => $pago['CardName'],
                                'DocType' => 'Pago',
                                'U_LATITUD' => $pago['U_LATITUD'],
                                'U_LONGITUD' => $pago['U_LONGITUD'],
                                'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                'username' => $pago['username']
                            ]);
                        }
                    }
                    return json_encode($resultadoFinal);  
            } else {
                $puntos = Cabeceradocumentos::find()
                ->select([
                    'cabeceradocumentos.idDocPedido',
                    'cabeceradocumentos.CardCode',
                    'cabeceradocumentos.CardName',
                    'cabeceradocumentos.DocType',
                    'cabeceradocumentos.U_LATITUD',
                    'cabeceradocumentos.U_LONGITUD',
                    'cabeceradocumentos.fecharegistro',
                    'user.username'
                ])
                ->leftjoin('user', 'user.id=cabeceradocumentos.idUser')
                ->where(['cabeceradocumentos.idUser' => $idusuario])
                    ->andFilterWhere(['<>', 'cabeceradocumentos.U_LATITUD', '0'])
                    ->andFilterWhere(['<>', 'cabeceradocumentos.U_LONGITUD', '0'])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LATITUD', null])
                    ->andFilterWhere(['is not', 'cabeceradocumentos.U_LONGITUD', null])
                    ->andFilterWhere(['cabeceradocumentos.estado'=>3])
                    ->orderBy('cabeceradocumentos.fecharegistro DESC')
                    ->asArray()->all();
                        
                    $puntosPagos =  Pagos::find()
                    ->select([
                        'pagos.recibo', 
                        'pagos.clienteId',
                        'clientes.CardName',
                        'pagos.U_LATITUD',
                        'pagos.U_LONGITUD',
                        'pagos.fecha',
                        'pagos.hora',
                        'user.username'
                    ])
                    ->leftjoin('user', 'user.id = pagos.usuario')
                    ->leftjoin('clientes','clientes.CardCode = pagos.clienteId')
                    ->where([
                        'pagos.usuario' => $idusuario
                    ])
                        ->orderBy('pagos.fecha DESC, pagos.hora DESC')
                        ->asArray()->all();
                    
                    foreach($puntos as $doc) array_push($resultadoFinal, $doc);
                    foreach($puntosPagos as $pago){
                        if ($pago["U_LATITUD"] != null && $pago["U_LATITUD"] != '0' && $pago["U_LATITUD"] != ''){
                            array_push($resultadoFinal, [
                                'idDocPedido' => $pago['recibo'], 
                                'CardCode' => $pago['clienteId'],
                                'CardName' => $pago['CardName'],
                                'DocType' => 'Pago',
                                'U_LATITUD' => $pago['U_LATITUD'],
                                'U_LONGITUD' => $pago['U_LONGITUD'],
                                'fecharegistro' =>$pago['fecha'].' '.$pago['hora'],
                                'username' => $pago['username']
                            ]);
                        }
                    }
                    return json_encode($resultadoFinal);  
            }
        }
    }

    public function actionMapacomparaciones()
    {
        $datos = Yii::$app->request->post();
        $poligono = $datos["poligono"];
        $ruta = $datos["ruta"];
        $resultado = [
            'poligonos' => [],
            'rutas' => []
        ];
        if ($poligono != '') {
            $p = Poligonodetalle::find()->where(['idcabecera' => $poligono])->asArray()->all();
            $resultado["poligonos"] = $p;
        }
        if ($ruta != '') {
            $r = Rutadetalle::find()->where(['idcabecera' => $ruta])->asArray()->all();
            $resultado["rutas"] = $r;
        }
        return json_encode($resultado);
    }

    public function actionMapaporcliente()
    {
        $datos = Yii::$app->request->post();
        $idcliente = $datos["cliente"];
        $documento = $datos["documento"];
        $grupo = $datos["grupo"];
        // filtro de territorio por definirse, de momento no se toma en cuenta
        // $territorio = $datos["territorio"];
        $fechaInicial = $datos["inicio"];
        $fechaFinal = $datos["fin"];
        $filtro = '';
        if ($idcliente != '') $filtro = 'l.id = ' . $idcliente . ' AND ';
        if ($grupo != '') $filtro = $filtro . 'l.GroupCode = ' . $grupo . ' AND ';
        // if ($territorio != '') $filtro = $filtro . 'l.Territory = ' . $territorio . ' AND ';

        $sql = 'SELECT 
                    c.*,
                    -- IF(c.Address="0", "", c.Address) AS Direccion,
                    IF(ISNULL((Select AddresName from clientessucursales where clientessucursales.CardCode=c.CardCode and clientessucursales.RowNum=c.sucursalxId)),(Select AddresName from clientessucursales where clientessucursales.CardCode=c.CardCode and clientessucursales.RowNum=0),(Select AddresName from clientessucursales where clientessucursales.CardCode=c.CardCode and clientessucursales.RowNum=c.sucursalxId))as Direccion,
                    (SELECT Name FROM clientesgrupo WHERE Code=l.GroupCode) AS GroupCode,
                    u.username
                    /*(SELECT Description FROM territorios WHERE TerritoryID=l.Territory) AS Territory  */
                FROM 
                    clientes l, 
                    cabeceradocumentos c 
                INNER JOIN user AS u ON(u.id=c.idUser)
                WHERE ' . $filtro . 'l.CardCode = c.CardCode';
        if ($documento != '') $sql = $sql . " AND c.DocType = '" . $documento . "'";

        $latitud = Configuracion::find()->where("valor2 = 'U_XM_Latitud' AND estado = 1")->asArray()->one();
        $longitud = Configuracion::find()->where("valor2 = 'U_XM_Longitud' AND estado = 1")->asArray()->one();

        $resultado = [
            'latitud' => 'latitud',
            'longitud' => 'longitud',
            'resultado' => []
        ];

        if (count($latitud) && count($longitud)) {
            $sql = $sql . ' AND ' . $latitud["parametro"] . ' <> 0 AND ' . $longitud["parametro"] . ' <> 0 AND ' . $latitud["parametro"] . ' is not null AND ' . $longitud["parametro"] . ' is not null';
            $resultado["latitud"] = $latitud["parametro"];
            $resultado["longitud"] = $longitud["parametro"];
        }

        if ($fechaInicial) {
            if ($fechaFinal) {
                $sql .= " AND c.fecharegistro BETWEEN '".$fechaInicial." 00:00:00' AND '".$fechaFinal." 23:59:59'";
            } else {
                $sql .= " AND c.fecharegistro >= '".$fechaInicial." 00:00:00'";
            }
        } else if ($fechaFinal) {
            $sql .= " AND c.fecharegistro <= '".$fechaFinal." 23:59:59'";;
        }
        $sql.= " AND estado=3  ORDER BY c.fecharegistro DESC";
        Yii::error("Consulta por clientes");
        Yii::error($sql);
        $resultado["resultado"] = Yii::$app->db->createCommand($sql)->queryAll();
        return json_encode($resultado);
    }

    public function actionCargarpuntossoloclientes()
    {
        $datos = Yii::$app->request->post();
        $idcliente = $datos["cliente"];
        $grupo = $datos["grupo"];
        $territorio = $datos["territorio"];
        $fechaInicial = $datos["inicio"];
        $fechaFinal = $datos["fin"];
        $filtro = '';
        if ($idcliente != '') $filtro = 'c.id = ' . $idcliente . ' AND ';
        if ($grupo != '') $filtro = $filtro . 'c.GroupCode = ' . $grupo . ' AND ';
        if ($territorio != '') $filtro = $filtro . 'c.Territory = ' . $territorio . ' AND ';
        $sql = 'SELECT 
                    c.*,
                    (SELECT Name FROM clientesgrupo WHERE Code=c.GroupCode) AS GroupCode,
                    cs.u_lat AS lat,
                    cs.u_lon AS lon
                FROM 
                    clientes c 
                INNER JOIN clientessucursales AS cs ON(cs.CardCode=c.CardCode AND cs.AdresType="S")
                WHERE ' . $filtro . '1 = 1';

        $latitud = Configuracion::find()->where("valor2 = 'U_XM_Latitud' AND estado = 1")->asArray()->one();
        $longitud = Configuracion::find()->where("valor2 = 'U_XM_Longitud' AND estado = 1")->asArray()->one();

        $resultado = [
            'latitud' => 'latitud',
            'longitud' => 'longitud',
            'resultado' => []
        ];

        if (count($latitud) && count($longitud)) {
            $sql = $sql . ' AND ' . $latitud["parametro"] . ' <> 0 AND ' . $longitud["parametro"] . ' <> 0 AND ' . $latitud["parametro"] . ' is not null AND ' . $longitud["parametro"] . ' is not null';
            $resultado["latitud"] = $latitud["parametro"];
            $resultado["longitud"] = $longitud["parametro"];
        }

        if ($fechaInicial) {
            if ($fechaFinal) {
                $sql .= " AND c.DateUpdate BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'";
            } else {
                $sql .= " AND c.DateUpdate >= '".$fechaInicial."'";
            }
        } else if ($fechaFinal) {
            $sql .= " AND c.DateUpdate <= '".$fechaFinal."'";;
        }
        Yii::error("Consulta clientes mapa");
        Yii::error($sql);
        $resultado["resultado"] = Yii::$app->db->createCommand($sql)->queryAll();
        return json_encode($resultado);
    }

    public function actionActualizarcliente()
    {
        $datos = Yii::$app->request->post();
        $cardcode = $datos["CardCode"];
        $territorio = $datos["Territory"];
        $resultado = [];
        $sql = "UPDATE clientes set Territory=:territorio  WHERE CardCode=:cardcode";
        Yii::$app->db->createCommand($sql)->bindValue(':territorio', $territorio)->bindValue(':cardcode', $cardcode)->execute();
        json_encode($resultado);
    }

    public function actionMapavisitas()
    {
        $datos = Yii::$app->request->post();
        $idusuario = $datos["usuario"];
        $fechaInicial = $datos["inicio"];
        $fechaFinal = $datos["fin"];
        if ($fechaInicial) {
            if ($fechaFinal) {
                // tiene ambas fechas
                $puntos = Visitas::find()->select(['CardCode', 'CardName', 'fecha', 'hora', 'horafin', 'lat', 'lng','foto','usuario','estadoEnviado','motivoRazon','descripcion'])
                        ->distinct()        
                        ->where(['usuario' => $idusuario])
                        ->andFilterWhere(['between', 'fecha', $fechaInicial, $fechaFinal])
                        ->asArray()->all();
                return json_encode($puntos);
            } else {
                // solo tiene inicial
                $puntos = Visitas::find()->select(['CardCode', 'CardName', 'fecha', 'hora', 'horafin', 'lat', 'lng','foto','usuario','estadoEnviado','motivoRazon','descripcion'])
                ->distinct()     
                ->where(['usuario' => $idusuario])
                        ->andFilterWhere(['>=', 'fecha', $fechaInicial])
                        ->asArray()->all();
                return json_encode($puntos);
            }
        } else if ($fechaFinal) {
            // solo tiene final
            $puntos = Visitas::find()->select(['CardCode', 'CardName', 'fecha', 'hora', 'horafin', 'lat', 'lng','foto','usuario','estadoEnviado','motivoRazon','descripcion'])
            ->distinct()     
            ->where(['usuario' => $idusuario])
                    ->andFilterWhere(['<=', 'fecha', $fechaFinal])
                    ->asArray()->all();
            return json_encode($puntos);
        } else {
            // no tiene fechas
            $puntos = Visitas::find()->select(['CardCode', 'CardName', 'fecha', 'hora', 'horafin', 'lat', 'lng','foto','usuario','estadoEnviado','motivoRazon','descripcion'])
            ->distinct()     
            ->where(['usuario' => $idusuario])
                    ->asArray()->all();
            return json_encode($puntos);
        }
    }
}
