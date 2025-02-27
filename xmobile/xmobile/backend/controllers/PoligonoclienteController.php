<?php

namespace backend\controllers;

use Yii;
use backend\models\Poligonocliente;
use backend\models\PoligonoclienteSearch;
use backend\models\Poligonocabecera;
use backend\models\Poligonodetalle; 
use backend\models\Clientes;
use backend\models\Territorios;
use backend\models\VSalespersonrute;
use backend\models\Vendedores;
use backend\models\ViCoordenadasdirecciones;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
/**
 * PoligonoclienteController implements the CRUD actions for Poligonocliente model.
 */
  
class PoligonoclienteController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'view', 'eliminar', 'report', 'recuperarclientes', 'guardar', 'eliminarvendedor', 'rutadelvendedor'],
                        'roles' => ['@']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Poligonocliente models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PoligonoclienteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Poligonocliente model.
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
     * Creates a new Poligonocliente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate() {
        $model = new Poligonocliente();
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
     * Updates an existing Poligonocliente model.
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
     * Deletes an existing Poligonocliente model.
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

    // Danae1 ***F.eliminar vendedor
    public function actionEliminarvendedor() {
      //  print "********";
        $datos = Yii::$app->request->post();
        $poligono = $datos["Poligono"];
        $territorio = $datos["Territorio"];
        $clientes = $datos["Clientes"];
        $dias = $datos["Dia"];
        $idvendedor = $datos["Idtabla"];
       // print(json_encode($datos));
         $borrar = Poligonocliente::find()->where("poligonoid = ".$poligono['Codigo']." AND dia = ".$dias." AND vendedor =".$idvendedor)->all();
        // print(json_encode($borrar));
        foreach($borrar as $b) $b->delete(); 
       // die;
     }


        /**
     * Finds the Poligonocliente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Poligonocliente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Poligonocliente::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }




    
    public function actionRecuperarclientes($id){
        //$datos = Yii::$app->request->post();
        $cabecera = Poligonocabecera::find()->where('id = '.$id)->asArray()->one();
        $detalle = Poligonodetalle::find()->where('idcabecera = '.$id)->asArray()->all();
        $territorio = Territorios::find()->where('TerritoryID = '.$cabecera['territoryid'])->asArray()->one();        
        $ddlDias = [];
        array_push($ddlDias, ['Codigo' => '1', 'Dia' => 'Lunes']);
        array_push($ddlDias, ['Codigo' => '2', 'Dia' => 'Martes']);
        array_push($ddlDias, ['Codigo' => '3', 'Dia' => 'Miercoles']);
        array_push($ddlDias, ['Codigo' => '4', 'Dia' => 'Jueves']);
        array_push($ddlDias, ['Codigo' => '5', 'Dia' => 'Viernes']);
        array_push($ddlDias, ['Codigo' => '6', 'Dia' => 'Sabado']);
        array_push($ddlDias, ['Codigo' => '7', 'Dia' => 'Domingo']);
        $DiasGuardados = [
            'LU' => '<strike>LUNES</strike>',
            'MA' => '<strike>MARTES</strike>',
            'MI' => '<strike>MIERCOLES</strike>',
            'JU' => '<strike>JUEVES</strike>',
            'VI' => '<strike>VIERNES</strike>',
            'SA' => '<strike>SABADO</strike>',
            'DO' => '<strike>DOMINGO</strike>'
        ];
        
        $vendedoresVista = VSalespersonrute::find()->where('Description = '.$cabecera["territoryid"])->groupBy('SalesPersonCode')->asArray()->all();
        $vendedores = [];
        foreach ($vendedoresVista as $vendedor){
            $nombre = Vendedores::find()->where('SalesEmployeeCode = '.$vendedor['SalesPersonCode'])->asArray()->one();
            array_push($vendedores,[
                'SalesPersonCode' => $vendedor['SalesPersonCode'],
                'Nombre' => $nombre['SalesEmployeeName'],
                'LU' => 'NO',
                'MA' => 'NO',
                'MI' => 'NO',
                'JU' => 'NO',
                'VI' => 'NO',
                'SA' => 'NO',
                'DO' => 'NO'
            ]);
        }
        
        //$clientesT = Clientes::find()->where('Territory = '.$cabecera['territoryid'].' AND cliente_std4 IS NOT null AND cliente_std4 <> 0')->asArray()->all();
        $clientesT = ViCoordenadasdirecciones::find()->where("territorio = ".$cabecera['territoryid']." AND tipo = 'S'")->asArray()->all();
        $clientes = [];
        
        $pointLocation = new pointLocation();
        $p = 0;
        $poligono = [];
        foreach($detalle as $d) array_push($poligono, $d["latitud"].' '.$d["longitud"] );
        foreach($clientesT as $c){
            $punto = $c["latitud"].' '.$c["longitud"];
            $r =  $pointLocation->pointInPolygon($punto, $poligono);
            if ($r == 'inside') array_push($clientes, $c);
            //array_push($clientes, $c);
        }

        $clientesGuardados = Poligonocliente::find()->where('poligonoid = '.$id)->asArray()->all();
        $ddlClientes = [];
        $crear = true;
        $vendedoresFinal = [];
        if (count($clientesGuardados)){
            $agrupar = Poligonocliente::find()->where('poligonoid = '.$id)->groupBy('dia')->asArray()->all();
            foreach($agrupar as $grupo){
                switch ($grupo["dia"]){
                    case 1: $DiasGuardados['LU'] = 'LUNES';  break;
                    case 2: $DiasGuardados['MA'] = 'MARTES';  break;
                    case 3: $DiasGuardados['MI'] = 'MIERCOLES';  break;
                    case 4: $DiasGuardados['JU'] = 'JUEVES';  break;
                    case 5: $DiasGuardados['VI'] = 'VIERNES';  break;
                    case 6: $DiasGuardados['SA'] = 'SABADO';  break;
                    case 7: $DiasGuardados['DO'] = 'DOMINGO';  break;                    
                }
            }
            foreach($clientesGuardados as $cliente) array_push($ddlClientes, ['CardCode' => $cliente['cardcode'], 'CardName' => $cliente['cardname'] ]);
            $ve = 0;            
            foreach($vendedores as $v){
                $cg = Poligonocliente::find()->where('poligonoid = '.$id.' AND vendedor = '.$v['SalesPersonCode'])->groupBy('dia')->asArray()->all();
                if (count($cg)){
                    foreach($cg as $c){
                        switch($c['dia']){
                            case 1: $v["LU"] = 'SI'; break;
                            case 2: $v["MA"] = 'SI'; break;
                            case 3: 
                                $v["MI"] = 'SI'; 
                                break;
                            case 4: $v["JU"] = 'SI'; break;
                            case 5: $v["VI"] = 'SI'; break;
                            case 6: $v["SA"] = 'SI'; break;
                            case 7: $v["DO"] = 'SI'; break;
                        }
                    }
                }
                array_push($vendedoresFinal, $v);
            }
        }
        else{
            foreach($clientes as $cliente) array_push($ddlClientes, ['CardCode' => $cliente['CardCode'], 'CardName' => $cliente['CardName'] ]);
            $vendedoresFinal = $vendedores;
        }
        $model = new Poligonocliente();
        $model->territoryid = $cabecera["territoryid"];
        $model->territoryname = $territorio["Description"];
        $model->poligonoid = $id;
        $model->poligononombre = $cabecera["nombre"];
/*
        print "******************CLIENTES***********************************";
        print_r($clientes);
        print "*************************CGUARDSADOT****************************";
        print_r($clientesGuardados);
        print "***********************CTTTTTTTTTTTTTTTTTTTTTTT*******************";
        print_r($clientesT);

        
        print "****************************************cccccccccccccccccccccccccccccc*************";
        print_r($c);
        die;*/
         return $this->renderPartial('poligonocliente', [
            'model' => $model,
            'ddlDias' => $ddlDias,
            'DiasGuardados' => $DiasGuardados,
            'cabecera' => $cabecera,
            'clientes' => $clientes,
            'ddlClientes' => $ddlClientes,
            'crear' => $crear,
            'detalle' => $detalle,
            'clientesGuardados' => $clientesGuardados,
            'vendedores' => $vendedoresFinal
        ]);
    }
    public function actionGuardar(){
            
        Yii::error("Entrada a texto");
        $datos = Yii::$app->request->post();
        $poligono = $datos["Poligono"];
        $territorio = $datos["Territorio"];
        $clientes = $datos["Clientes"];
        $dias = $datos["Dia"];
        $vendedores = $datos["Vendedores"];    
        if(count($clientes) == 1){
             return json_encode('N');
        }
       foreach($vendedores as $vendedor){
        $borrar = Poligonocliente::find()->where("poligonoid = ".$poligono['Codigo']." AND dia = ".$dias." AND vendedor =".$vendedor["Codigo"])->all();
            //DNE ****
           if (count($borrar) == 0){
                $pos = 1;
                foreach($clientes as $cliente){            
                $c = new Poligonocliente();
                $c->id = 0;
                $c->cardcode = $cliente["CardCode"];
                $c->cardname = $cliente["CardName"];
                $c->latitud = $cliente["latitud"];
                $c->longitud = $cliente["longitud"];
                $c->territoryid = $territorio["Codigo"];
                $c->territoryname = $territorio["Nombre"];
                $c->poligonoid = $poligono["Codigo"];
                $c->poligononombre = $poligono["Nombre"];
                $c->nombreDireccion = $cliente["direccion"];
                $c->calle = $cliente["calle"];
                $c->posicion = $pos;
                $c->dia = $dias;
                $c->vendedor = $vendedor["Codigo"];
                $pos = $pos + 1;
                $c->save(false);
                }
            }
        }
            return json_encode('Registros guardados');
    }
//DNE
    public function actionRutadelvendedor(){
        $datos = Yii::$app->request->post();
        $poligono = $datos["Poligono"];
        $territorio = $datos["Territorio"];
        $clientes = $datos["Clientes"];
        $dias = $datos["Dia"];
        $idvendedor = $datos["Idtabla"];
        $vendedores = $datos["Vendedores"];
       //print($idvendedor);
       //print($dias);
       //print($poligono["Codigo"]);
       //die;
        $clientedelvendedor = Poligonocliente::find()->where("poligonoid = ".$poligono['Codigo']." AND dia = ".$dias." AND vendedor =".$idvendedor)->asArray()->all();;
        return json_encode($clientedelvendedor);
       }
}





class pointLocation {
    var $pointOnVertex = true; // Checar si el punto se encuentra exactamente en uno de los vértices?

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

        // Checar si el punto se encuentra exactamente en un vértice
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }

        // Checar si el punto está adentro del poligono o en el borde
        $intersections = 0; 
        $vertices_count = count($vertices);

        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1]; 
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Checar si el punto está en un segmento horizontal
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) { 
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x']; 
                if ($xinters == $point['x']) { // Checar si el punto está en un segmento (otro que horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++; 
                }
            } 
        } 
        // Si el número de intersecciones es impar, el punto está dentro del poligono. 
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