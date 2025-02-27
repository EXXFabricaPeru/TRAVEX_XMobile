<?php

namespace backend\controllers;

use Yii;
use backend\models\Poligonocliente;
use backend\models\PoligonoclienteSearch;
use backend\models\Poligonocabeceraterritorio;
use backend\models\PoligonocabeceraterritorioSearch;
use backend\models\Usuariomovilterritorio;
use backend\models\Poligonocabecera;
use backend\models\Vipoligonocabecera;
use backend\models\Poligonodetalle;
use backend\models\ViCoordenadasdirecciones;
use backend\models\Vicoordenadasdireccionescliente;
use backend\models\Viusuariopersona;
use backend\models\Configuracion;
use backend\models\Iconomaps;
use backend\models\Territorios;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use backend\models\v2\Clientes;
/**
 * PoligonocabeceraterritorioController implements the CRUD actions for Poligonocabeceraterritorio model.
 */
  
class PoligonocabeceraterritorioController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report','usuarioterritorio','poligonocabecera','poligonodetalle','listacliente','guardardetalle','listaclienteguardados','updatedetalle','listaiconos','verifcaasignacion'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Poligonocabeceraterritorio models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PoligonocabeceraterritorioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $modeluser = Viusuariopersona::find()->asArray()->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modeluser' => $modeluser,
        ]);
    }

    /**
     * Displays a single Poligonocabeceraterritorio model.
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
     * Creates a new Poligonocabeceraterritorio model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
       /* $model = new Poligonocabeceraterritorio();
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
        $datos = Yii::$app->request->post();
        $valorDetalle=explode('@@',$datos["detallePoligono"]);
        Yii::error("DETALLE POLIGONO");
        Yii::error(count($valorDetalle));
        Yii::error($valorDetalle);
        $model = new Poligonocabeceraterritorio();
       // $idDia=['Lunes'=>'1','Martes'=>'2','Miercoles'=>'3','Jueves'=>'4','Viernes'=>'5','Sabado'=>'6','Domingo'=>'7'];
        if ($model->load(Yii::$app->request->post()) || (count($datos))) {

            $model->id = 0;
            $model->fechaRegistro = $datos["fechaRegistro"];
            $model->fechaSistema = $datos["fechaSistema"];
            $model->dia = $datos["dia"];
            $model->idDia = $datos["idDia"];
            $model->idVendedor = $datos["idVendedor"];
            $model->vendedor = $datos["vendedor"];
            $model->tipoVendedor = '-';
            $model->idTerritorio =$datos["idTerritorio"];
            $model->territorio = $datos["territorio"];
            $model->idPoligono =$datos["idPoligono"];
            $model->poligono =$datos["poligono"];
            $model->estado ='A';
            $model->tipo ='-';
            $model->idUserRegister =$datos["idUserRegister"];
            $model->userRegister =$datos["userRegister"];
            $model->nombreRuta =$datos["nombreRuta"];

            if ($model->save()) {       
                      
                foreach($valorDetalle as $value){            
                    $poligonocliente = new Poligonocliente();  
                    $valor=explode('&&',$value); 
                    if($valor[0]!=""){
                        $poligonocliente->id =0;
                        $poligonocliente->idCabecera =$model->id;
                        $poligonocliente->cardcode = $valor[0];
                        $poligonocliente->cardname = $valor[1];
                        $poligonocliente->latitud = $valor[4];
                        $poligonocliente->longitud = $valor[5];
                        $poligonocliente->territoryid = $valor[6];
                        $poligonocliente->territoryname = $valor[7];
                        $poligonocliente->poligonoid = $valor[8];
                        $poligonocliente->poligononombre = '';
                        $poligonocliente->nombreDireccion =$valor[2];
                        $poligonocliente->calle = $valor[2];
                        $poligonocliente->posicion = $valor[3];
                        $poligonocliente->dia = $valor[9];
                        $poligonocliente->vendedor = $valor[10];
                        $poligonocliente->save(false);   

                    }
                         
                }            
                return $model->id;
            } 
            else {
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
     * Updates an existing Poligonocabeceraterritorio model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
   
    public function actionUpdate($id=0) {
        $datos = Yii::$app->request->post();

        $valorDetalle=explode('@@',$datos["detallePoligono"]);
        Yii::error("DETALLE POLIGONO");
        Yii::error(count($valorDetalle));
        Yii::error($valorDetalle);

        if (count($datos)) $id = $datos["id"];

         $model = $this->findModel($id);
         
        if ($model->load(Yii::$app->request->post()) || (count($datos))) {

            $model->id = $datos["id"];
            $model->fechaRegistro = $datos["fechaRegistro"];
            $model->fechaSistema = $datos["fechaSistema"];
            $model->dia = $datos["dia"];
            $model->idDia = $datos["idDia"];
            $model->idVendedor = $datos["idVendedor"];
            $model->vendedor = $datos["vendedor"];
            $model->tipoVendedor = '-';
            $model->idTerritorio =$datos["idTerritorio"];
            $model->territorio = $datos["territorio"];
            $model->idPoligono =$datos["idPoligono"];
            $model->poligono =$datos["poligono"];
            $model->estado ='A';
            $model->tipo ='-';
            $model->idUserRegister =$datos["idUserRegister"];
            $model->userRegister =$datos["userRegister"];
            $model->nombreRuta =$datos["nombreRuta"];
            Yii::error("SE FILTRA SEGUN ID DE CABECERA TERRITORIO: ".$datos["id"]);
            Yii::error($model);
            if ($model->update()) { 
                $borrar = Poligonocliente::find()->where("idCabecera = ".$model->id)->all();
                 // print(json_encode($borrar));
                foreach($borrar as $valorP) $valorP->delete(); 

                foreach($valorDetalle as $value){            
                    $poligonocliente = new Poligonocliente();  
                    $valor=explode('&&',$value); 
                    if($valor[0]!=""){
                        $poligonocliente->id =0;
                        $poligonocliente->idCabecera =$model->id;
                        $poligonocliente->cardcode = $valor[0];
                        $poligonocliente->cardname = $valor[1];
                        $poligonocliente->latitud = $valor[4];
                        $poligonocliente->longitud = $valor[5];
                        $poligonocliente->territoryid = $valor[6];
                        $poligonocliente->territoryname = $valor[7];
                        $poligonocliente->poligonoid = $valor[8];
                        $poligonocliente->poligononombre = '';
                        $poligonocliente->nombreDireccion =$valor[2];
                        $poligonocliente->calle = $valor[2];
                        $poligonocliente->posicion = $valor[3];
                        $poligonocliente->dia = $valor[9];
                        $poligonocliente->vendedor = $valor[10];
                        $poligonocliente->save(false);   

                    }
                         
                }              
                return $model->id;
            } 
            else {
                $data = $model->getErrors();
                return json_encode($data);
            } 
            \Yii::$app->end();
        }        
        return $this->renderPartial('update', [
                    'model' => $model,
        ]);
         /*
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $model->id;
            } else {
                $data = $model->getErrors();
                return json_encode($data);
            }
            \Yii::$app->end();
        }*/
        //return $this->renderPartial('update', ['model' => $model]);
    }
   
    
    /**
     * Deletes an existing Poligonocabeceraterritorio model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        $poligonocliente = Poligonocliente::find()->where("idCabecera = ".$id)->all();
        foreach($poligonocliente as $valorP) $valorP->delete(); 

        return $this->redirect(['index']);
    }

    public function actionEliminar($id) {
        
        $poligonocliente = Poligonocliente::find()->where("idCabecera = ".$id)->all();
        foreach($poligonocliente as $valorP) $valorP->delete();
        return $this->findModel($id)->delete();
    }
    
    /**
     * Finds the Poligonocabeceraterritorio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Poligonocabeceraterritorio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Poligonocabeceraterritorio::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // se guarda el detalle poligono

    public function actionGuardardetalle(){
              
        Yii::error("Entrada a datos");
        $datos = Yii::$app->request->post();
        Yii::error($datos);
        $clientes = $datos["detallePoligono"];

        
        Yii::error("Entrada a cliente");
        Yii::error($clientes);

        foreach($clientes as $value){            
            $poligonocliente = new Poligonocliente();
            $poligonocliente->id = 1;
            $poligonocliente->cardcode = $value["cardCode"];
            $poligonocliente->cardname = $value["cardName"];
            $poligonocliente->latitud = $value["latitud"];
            $poligonocliente->longitud = $value["longitud"];
            $poligonocliente->territoryid = $value["idTerritorio"];
            $poligonocliente->territoryname = '';
            $poligonocliente->poligonoid = $value["idPoligono"];
            $poligonocliente->poligononombre = '';
            $poligonocliente->nombreDireccion = $value["calle"];
            $poligonocliente->calle = $value["calle"];
            $poligonocliente->posicion = $value["posicion"];
            $poligonocliente->dia = $value["dia"];
            $poligonocliente->vendedor = $value["vendedor"];
            $poligonocliente->save();         
        }
        return json_encode('Registros guardados');

    }

    public function actionListaiconos() {
        Yii::error("Entrada a texto lista iconos");
        //$usuariomovilterritorio = Usuariomovilterritorio::find();
        $iconomaps = Iconomaps::find()->asArray()->all();
        Yii::error($iconomaps);
        return json_encode($iconomaps);
       
    }

    public function actionVerifcaasignacion(){
        Yii::error("Entrada a texto Verifica Asignacion");
        $datos = Yii::$app->request->post();
        $datosVerificados = Poligonocabeceraterritorio::find()->where("dia='".$datos['dia']."' and idVendedor=".$datos['idVendedor'])->asArray()->all();
        Yii::error($datosVerificados);
        return json_encode($datosVerificados);
    }

    public function actionUsuarioterritorio() {
        Yii::error("Entrada a texto TERRITORIO");
        $datos = Yii::$app->request->post();
        Yii::error($datos['idVendedor']);
        //$usuariomovilterritorio = Usuariomovilterritorio::find();
        $usuariomovilterritorio = Usuariomovilterritorio::find()->where('idUser = '.$datos['idVendedor'])->asArray()->one();
        Yii::error($usuariomovilterritorio);
        return json_encode($usuariomovilterritorio);
       
    }
    public function actionListaclienteguardados() {
        Yii::error("Entrada a texto CLIENTES GUARDADOS");
        $datos = Yii::$app->request->post();
        Yii::error($datos['idCabecera']);
        //$usuariomovilterritorio = Usuariomovilterritorio::find();
        $DataPoligonocliente = Poligonocliente::find()->where('idCabecera = '.$datos['idCabecera'])->orderby('posicion asc')->asArray()->all();
        Yii::error($DataPoligonocliente);
        return json_encode($DataPoligonocliente);
       
    }
    

    public function actionPoligonocabecera() {
        Yii::error("Entrada a texto POLIGONO");
        $datos = Yii::$app->request->post();

        $idTerritorio=explode('@',$datos['idTerritorio']);
        $condicion="";
        for($i=0;$i<count($idTerritorio)-1;$i++){
            $condicion=$condicion." territoryid=".$idTerritorio[$i]." OR ";
        }
        $condicion=substr($condicion, 0, -3);
        Yii::error($condicion);
        //$usuariomovilterritorio = Usuariomovilterritorio::find();
        $poligonocabecera = Vipoligonocabecera::find()->where($condicion)->asArray()->all();
        Yii::error($poligonocabecera);
        return json_encode($poligonocabecera);
       
    }


    public function actionPoligonodetalle() {
        Yii::error("Entrada a texto POLIGONO DETALLE");
        $datos = Yii::$app->request->post();
        $idPoligono=explode('@',$datos['idPoligono']);
        $condicion="";
        for($i=0;$i<count($idPoligono)-1;$i++){
           $condicion=$condicion." idcabecera=".$idPoligono[$i]." OR ";
        }
        $condicion=substr($condicion, 0, -3);
        Yii::error($condicion);
        //$usuariomovilterritorio = Usuariomovilterritorio::find();
        //$poligonodetalle = Poligonodetalle::find()->where('idcabecera = '.$datos['idPoligono'])->asArray()->all();
        $poligonodetalle = Poligonodetalle::find()->where($condicion)->asArray()->all();
        Yii::error($poligonodetalle);
        return json_encode($poligonodetalle);
       
    }

    public function actionListacliente() {
        Yii::error("Entrada a texto lista clientes");
        $datos = Yii::$app->request->post();
        $idTerritorio=explode('@',$datos['territoryid']);
        $condicion="";
        for($i=0;$i<count($idTerritorio)-1;$i++){
            $DataTerritorio = Territorios::find()->where("TerritoryID=".$idTerritorio[$i])->asArray()->one();
            if($DataTerritorio['Parent']==-2){
                //$condicion=$condicion."(territorio=".$idTerritorio[$i]." and cliente_std1='2')  OR ";
            }else{
                $condicion=$condicion.' C."Territory"='.$idTerritorio[$i].'   OR ';
            }
            
        }
        $condicion=substr($condicion, 0, -3);
        Yii::error("CONDICION DIRECCION CLIENTE:");
        Yii::error($condicion);
        /*$DataConfiguracion = Configuracion::find()->where("id=1")->asArray()->one();

        switch ($DataConfiguracion['valor']) {
            case 0:
                $Dataclientes_ = Vicoordenadasdireccionescliente::find()->where($condicion)->asArray()->all();
                break;
            case 1:
                
                $Dataclientes_ = Vicoordenadasdireccionescliente::find()->where($condicion)->asArray()->all();
  
                break;
            case 2:
                $Dataclientes_ = Vicoordenadasdireccionessucursales::find()->where($condicion)->asArray()->all();
                break;
            case 3:
                $Dataclientes_ = Vicoordenadasdireccionessucursales::find()->where($condicion)->asArray()->all();
                break;         
        }*/
        $Dataclientes = []; 
        Yii::error('LISTA DE CLIENTES FILTRADOS SEGUN ID TERRITORIO');
        $clientes = new Clientes();
        $result= $clientes->obtenerClientesPorTerritorio($condicion);
        Yii::error($result);
        //Yii::error($Dataclientes_);
		$Dataclientes=$result ;
        /*foreach ($Dataclientes_ as $key => $value) {
            switch ($datos['dia']) {
                case 'lunes':
                    if($value["Properties1"]=='Y' || $value["Properties1"]=='tYES'){
                        array_push($Dataclientes, $value);
                    }
                    break;
                case 'martes':
                    if($value["Properties2"]=='Y' || $value["Properties2"]=='tYES'){
                        Yii::error("martes");
                        Yii::error($value["Properties2"]);
                        //array_push($Dataclientes, $value["CardCode"],$value["CardName"],$value["latitud"],$value["longitud"],$value["territorio"],$value["tipo"],$value["direccion"] ,$value["calle"]);
                        array_push($Dataclientes, $value);
                    }
                    break;
                case 'miercoles':
                    if($value["Properties3"]=='Y' || $value["Properties3"]=='tYES'){
                        //array_push($Dataclientes, $value["CardCode"],$value["CardName"],$value["latitud"],$value["longitud"],$value["territorio"],$value["tipo"],$value["direccion"] ,$value["calle"]);
                        array_push($Dataclientes, $value);
                    }
                    break;
                case 'jueves':
                    if($value["Properties4"]=='Y' || $value["Properties4"]=='tYES'){
                        //array_push($Dataclientes, $value["CardCode"],$value["CardName"],$value["latitud"],$value["longitud"],$value["territorio"],$value["tipo"],$value["direccion"] ,$value["calle"]);
                        array_push($Dataclientes, $value);
                    }
                    break;
                case 'viernes':
                    if($value["Properties5"]=='Y' || $value["Properties5"]=='tYES'){
                        //array_push($Dataclientes, $value["CardCode"],$value["CardName"],$value["latitud"],$value["longitud"],$value["territorio"],$value["tipo"],$value["direccion"] ,$value["calle"]);
                        array_push($Dataclientes, $value);
                    }
                    break;
                case 'sabado':
                    if($value["Properties6"]=='Y' || $value["Properties6"]=='tYES'){
                        //array_push($Dataclientes, $value["CardCode"],$value["CardName"],$value["latitud"],$value["longitud"],$value["territorio"],$value["tipo"],$value["direccion"] ,$value["calle"]);
                        array_push($Dataclientes, $value);
                    }
                    break;
                case 'domingo':
                    if($value["Properties7"]=='Y' || $value["Properties7"]=='tYES'){
                        //array_push($Dataclientes, $value["CardCode"],$value["CardName"],$value["latitud"],$value["longitud"],$value["territorio"],$value["tipo"],$value["direccion"] ,$value["calle"]);
                        array_push($Dataclientes, $value);
                    }
                    break;
            }
        }*/
        Yii::error('LISTA CLIENTES DISPONIBLE:');
        Yii::error($Dataclientes);

        // se obtiene los poligonos
        $idPoligono=explode('@',$datos['idPoligono']);
        $condicion="";
        for($i=0;$i<count($idPoligono)-1;$i++){
           $condicion=$condicion." idcabecera=".$idPoligono[$i]." OR ";
        }
        $condicion=substr($condicion, 0, -3);
        Yii::error($condicion);
        //$usuariomovilterritorio = Usuariomovilterritorio::find();
        //$poligonodetalle = Poligonodetalle::find()->where('idcabecera = '.$datos['idPoligono'])->asArray()->all();
        //$poligonodetalle = Poligonodetalle::find()->where($condicion)->asArray()->all();

        ///se verifica si las coordenadas de los clientes estan dentro de los poligonos
        $pointLocation = new pointLocation();
        
        $poligono = [];
        $clientesSucursales = [];
        $clientesSucursalesDescartados = [];
        $swPoligono=1;
        $ind=0;
        // codigo para controlar si el cliente esta dentro del mapa
        /*
        foreach($Dataclientes as $valorCliente){
            
           
            ///se compara recore el array poligono
            for($i=0;$i<count($poligonodetalle);$i++) {
                Yii::error('RECORRE LAS CORDENADAS DE LOS POLIGONOS: ');
                Yii::error($poligonodetalle);
                if($poligonodetalle[$i]['idcabecera']==$poligonodetalle[$i+$swPoligono]['idcabecera']){
                    array_push($poligono, $poligonodetalle[$i]["latitud"].' '.$poligonodetalle[$i]["longitud"] );
                }else{
                    $punto = $valorCliente["latitud"].' '.$valorCliente["longitud"];
                   
                    array_push($poligono, $poligonodetalle[$i]["latitud"].' '.$poligonodetalle[$i]["longitud"] );
                  
                    Yii::error('POLIGONO : '.$i);
                    Yii::error($punto);
                    Yii::error($poligono);

                    $respuesta =  $pointLocation->pointInPolygon($punto, $poligono);
                    Yii::error("respuesta");
                    Yii::error($respuesta);
                    if ($respuesta == 'inside') {
                        
                        array_push($clientesSucursales, $valorCliente);
                        $clientesSucursales[$ind]['idPoligono']=$poligonodetalle[$i]['id'];
                        $clientesSucursales[$ind]['descartado']='NO';
                        $ind++;
                    }
                    else{
                        /*array_push($clientesSucursales, $valorCliente);
                        $clientesSucursales[$ind]['idPoligono']=$poligonodetalle[$i]['id'];
                        $clientesSucursales[$ind]['descartado']='SI';
                        */
                   // }
                  //  $poligono = [];
                   // break;
               // }
               /* if($i==count($poligonodetalle)-2){
                    $respuesta =  $pointLocation->pointInPolygon($punto, $poligono);
                    if ($respuesta == 'inside') {
                        array_push($clientesSucursales, $valorCliente);
                    }
                    $swPoligono=0;
                }*/
          //  }
           /* Yii::error('clientesSucursales TABLE : ');
            Yii::error($clientesSucursales);
            Yii::error('clientesSucursales Descartados : ');
            Yii::error($clientesSucursalesDescartados);
            /// fin re recorer el array poligono
           
            //array_push($clientes, $c);
        }*/
        //return (json_encode ($clientesSucursales));
        return (json_encode ($Dataclientes));
   // ));
       
    }

    public function actionListaclientesap() {
        Yii::error("Entrada a texto lista clientes SAP");
        $datos = Yii::$app->request->post();
        $idTerritorio=explode('@',$datos['territoryid']);
        $condicion="";
        for($i=0;$i<count($idTerritorio)-1;$i++){
            $DataTerritorio = Territorios::find()->where("TerritoryID=".$idTerritorio[$i])->asArray()->one();
            /*if($DataTerritorio['Parent']==-2){
                //$condicion=$condicion."(territorio=".$idTerritorio[$i]." and cliente_std1='2')  OR ";
            }else{
                $condicion=$condicion."(territorio=".$idTerritorio[$i]." and cliente_std1!='2')  OR ";
            }
            */
             $condicion=$condicion." territorio=".$idTerritorio[$i]."  OR ";
            
        }
        $condicion=substr($condicion, 0, -3);
        Yii::error("CONDICION DIRECCION CLIENTE SAP:");
        Yii::error($condicion);
        //SE FILTRA DE ACUERDO A LA CONFIGURACION EL PROYECTO
        $DataConfiguracion = Configuracion::find()->where("id=1")->asArray()->one();
        $Dataclientes = []; 
        $clientes =new Clientes();
        //obtenion campos especificos de SAP//
       // $campos="\"CardCode\",\"U_XM_ICEEspecifico\",\"U_FE_AlicuotaporLitro\" ";
        //$condicion="where \"ItemCode\"='{$lineaP->ItemCode}'";
        //$Dataclientes = $clientes->obtenerCamposEspecificos($campos,$condicion);
        //Yii::error('LISTA DE CLIENTES FILTRADOS SEGUN ID TERRITORIO');
        //Yii::error($Dataclientes);

        // se obtiene los poligonos
    
        return (json_encode ($Dataclientes));    
    }

      
}
 ///verifica si las coorenas estan dentro del poligono seleccionado

 class pointLocation {
    var $pointOnVertex = true; // Checar si el punto se encuentra exactamente en uno de los v�rtices?

    function pointLocation() {
    }

        function pointInPolygon($point, $polygon, $pointOnVertex = true) {
        $this->pointOnVertex = $pointOnVertex;

        // Transformar la cadena de coordenadas en matrices con valores "x" e "y"
        $point = $this->pointStringToCoordinates($point);
        $vertices = array(); 
        foreach ($polygon as $vertex) {            
            $vertices[] = $this->pointStringToCoordinates($vertex); 
        }

        // Checar si el punto se encuentra exactamente en un v�rtice
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }

        // Checar si el punto est� adentro del poligono o en el borde
        $intersections = 0; 
        $vertices_count = count($vertices);

        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1]; 
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Checar si el punto est� en un segmento horizontal
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) { 
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x']; 
                if ($xinters == $point['x']) { // Checar si el punto est� en un segmento (otro que horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++; 
                }
            } 
        } 
        // Si el n�mero de intersecciones es impar, el punto est� dentro del poligono. 
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }

    function pointOnVertex($point, $vertices) {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }

    }

    function pointStringToCoordinates($pointString) {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }

   
}