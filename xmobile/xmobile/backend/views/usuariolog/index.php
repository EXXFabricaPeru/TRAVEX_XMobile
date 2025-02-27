<?php
/*MAHH-EXXIS-BOLIVIA*/
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use backend\models\Viusuariopersona;
ini_set('max_execution_time', 9000000);
ini_set('memory_limit',"20000000M");
$modeluser=Viusuariopersona::find()->asArray()->all();
//print_r($modeluser);
$this->title = 'Usuariologs';
?>


<style>
    #ModalSincronizacionSize{
      width: 60% !important;
    }
	.minusculas{
	text-transform:lowercase;
	}	
	.mayusculas{
		text-transform:uppercase;
	}

</style>
<div id="PATH" name="<?php echo Yii::$app->urlManager->createAbsoluteUrl(['']);?>"></div>
<?php
    $fechaInicio=date("Y-m-d");
    $fechaFin=date("Y-m-d");
    $idUser="0";

    if(isset($_GET['fechaInicio']) && isset($_GET['fechaFin'])){
        $fechaInicio=$_GET['fechaInicio'];
        $fechaFin=$_GET['fechaFin']; 
        $idUser=$_GET['id'];
        
    }
    function getSelected($valor){
        $estado="";
        if(isset($_GET['estado'])){
            if($_GET['estado']==$valor){
                $estado="selected";
            }
        }
        return $estado;
    }
    
    if(isset($_GET['estado'])){
        if($_GET['estado']==1) $lblSincronizo='';
        else $lblSincronizo='disabled';

        $estado=$_GET['estado'];
       
    }
    else{
        $estado=0;
        $lblSincronizo='disabled';

    }
    
    if(isset($_GET['sincronizo'])){
        if($_GET['sincronizo']==1)$lblSincronizo='checked';
        $sincronizo=$_GET['sincronizo'];
    }
    else{
        
        $sincronizo=0;
    }
   
?>
<div class="usuariolog-index">

<div class="row">

    <div class="col-lg-2">
    </div>
    <?php  $dataUser = $modeluser;//\yii\helpers\ArrayHelper::map(backend\models\Viusuariopersona::find()->all(), 'id', 'nombreCompletoUsuario'); ?>  
    <div class="col-lg-2"><span style="color:#09104D"> USUARIO </span>
    <select name="USUARIO" id="USUARIO" class="form-control" value="<?=$idUser?>">
            <option value="0">TODOS</option>
        <?php
            foreach ($dataUser as $key => $value) {
                    echo"<option  value='".$value['id']."'> ".$value['nombreCompletoUsuario']." </option>";                                      
            }
        ?>
    </select>    
    </div>
    <div class="col-lg-2"><span style="color:#09104D"> ESTADO </span>
        <select name="ESTADO" id="ESTADO" class="form-control" value="" onchange="cambiaEstado(this.value)">
                <option value="0" <?=getSelected(0)?>>NO INGRESO</option>
                <option value="1" <?=getSelected(1)?>>SI INGRESO</option>
        </select> 
         <input type="checkbox" id="SINCRONIZO" <?=$lblSincronizo?>  value="1"> <label for="SINCRONIZO">Sincronización completa</label>
    </div>
  
    <div class="col-lg-2"> <span style="color:#09104D"> FECHA INICIO </span>
    <input id="FECHAINICIO" name="FECHAINICIO" type="date"  class="form-control" data-validation="date" data-validation-format="yyyy-mm-dd" value="<?=$fechaInicio?>" />
    </div>
    <div class="col-lg-2"><span style="color:#09104D"> FECHA FIN </span> 
    <input id="FECHAFIN" name="FECHAFIN" type="date"  class="form-control" data-validation="date" data-validation-format="yyyy-mm-dd" value="<?=$fechaFin?>" />
    </div>
    <div class="col-lg-1"><br>
        <button class="btn btn-success" id="btn-filtrar" onclick="cargarPagina()"> Filtrar </button>
    </div>
    <div class="col-lg-1">
    </div>
    <input id="USUARIO_" name="USUARIO_" type="hidden"  value="<?=$idUser?>">
</div>


<br>


    <?php
   
    ?>
    
    <div class="panel panel-default">
        <div class="panel-heading">
        <div class="row">
            <div class="col-lg-9">Lista de Usuario - XMobile</div>
            <div class="col-lg-1">Buscador:</div>
            <div class="col-lg-2">
              <input id="searchTerm" type="text" class="form-control" onkeyup="doSearch()" />
            </div>
        </div>
             
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body" >
            <table width="100%" class="table table-striped table-bordered table-hover" id="datos">
                <thead >
                    <tr>
                        <th width='5%'>Nro</th>
                        <th width='10%'>Usuario</th>
                        <th width='10%'>Equipo</th>
                        <th width='10%'>Fecha</th>  
                        <th width='10%'>Ingreso al<br> Xmobile</th>
                        <th width='10%'>Cantidad Ingresos</th>  
                        <th width='10%'>Primera<br>Sincronización</th> 
                        <th width='10%'>Ultima<br>Sincronización</th> 
                      
                    </tr>
                </thead>
                <tbody>
                    <?php 
 
                    //for($i=0; $i<2;$i++){
                     $fechaInicio=strtotime($fechaInicio);
                     $fechaFin=strtotime($fechaFin);
                    for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
                        
                        $fechaPrincipal= date("Y-m-d", $i);
                        
                        //settype($fecha, 'string');                      
                        $Data = Yii::$app->db->createCommand("CALL pa_obtenerUsuariosMovilLog('".$fechaPrincipal."', '".$fechaPrincipal."', '".$idUser."','".Yii::$app->session->get('DEPARTAMENTO')."',".$estado.")")->queryAll();

                        foreach ($Data as $key => $value){
                        
                            $hora="";
                            $horaFinalIngreso="";
                            $horaInicioIngreso="";
                            $fecha="";
                            $ingreso="NO";
                          
                            $fechaHora[0]="";
                            ////////////////////////////////////////////////
                            $sincronizo1="";
                            $fechaInicio1="";
                            $fechaFin1="";
                            $horaInicio1="";
                            $horaFin1="";
                            //////////////////////////////////////////////
                            $sincronizo2="";
                            $fechaInicio2="";
                            $fechaFin2="";
                            $horaInicio2="";
                            $horaFin2="";

                            if($value['cantidadIngreso']>0){
                                $fechaHora=explode(' ',$value['fechaUltima']);
                                $fechaHoraInicio=explode(' ',$value['fechaPrimera']);
                                $fecha=explode('-',$fechaHora[0]);
                                $horaFinalIngreso=$fechaHora[1];//la obtiene la hora final del ingreso al movil
                                $horaInicioIngreso=$fechaHoraInicio[1];

                                $fecha=$fecha[2]."/".$fecha[1]."/".$fecha[0];
                                $ingreso="SI";
                                // usuario que si ingresaron
                                $sqlHoraFin=" SELECT usuariosincronizamovil.fechahora 
                                FROM usuariosincronizamovil WHERE usuariosincronizamovil.idUsuario=".$value['id']."
                                AND usuariosincronizamovil.fecha='".$fechaHora[0]."'
                                ORDER BY usuariosincronizamovil.fechahora asc LIMIT 1";

                                $DataHoraFin = Yii::$app->db->createCommand($sqlHoraFin)->queryOne();
                               
                                $sincronizo1="SI, Sincronizo Todos los servicios";
                                $fechaInicio1="";
                                $fechaFin1="";
                                $horaInicio1="";
                                $horaFin1="";

                                if($DataHoraFin['fechahora']!=""){
                                    $fechaFin1_ = new DateTime($DataHoraFin['fechahora']); 
                                    $fechaFin1_->modify('+5 minute'); 
                                    $fechaFin1=$fechaFin1_->format('Y-m-d H:i:s');
        
                                    $fechaInicio1=$DataHoraFin['fechahora'];

                                    $horaFin1=explode(' ',$fechaFin1);
                                    $horaFin1="Hr: ".$horaFin1[1];

                                    $horaInicio1=explode(' ',$DataHoraFin['fechahora']);
                                    $horaInicio1="Hr: ".$horaInicio1[1];
                                }
                                else{         
                                   
                                }
                                $DataServicios_ = Yii::$app->db->createCommand("CALL pa_obtenerServiciosMovilSincroHora('".$fechaInicio1."','".$fechaFin1."', '".$value['id']."')")->queryAll();
                                foreach ($DataServicios_ AS $valueSer) {
                                    if($valueSer['servicio']==''){
                                        $sincronizo1="NO, Sincronizo Todos los servicios";
                                    }
                                    if($ingreso=="NO"){
                                        $sincronizo1="";
                                    }
                                }
                                //////segunda sincronizacion
                                $sqlHoraFin2=" SELECT usuariosincronizamovil.fechahora 
                                FROM usuariosincronizamovil WHERE usuariosincronizamovil.idUsuario=".$value['id']."
                                AND usuariosincronizamovil.fecha='".$fechaHora[0]."'
                                ORDER BY usuariosincronizamovil.fechahora desc LIMIT 1";

                                $DataHoraFin2 = Yii::$app->db->createCommand($sqlHoraFin2)->queryOne();
                                $sincronizo2="SI, Sincronizo Todos los servicios";
                                $fechaInicio2="";
                                $fechaFin2="";
                                $horaInicio2="";
                                $horaFin2="";

                                if($DataHoraFin2['fechahora']!=""){
                                    $fechaInicio2_ = new DateTime($DataHoraFin['fechahora']); 
                                    $fechaInicio2_->modify('-5 minute'); 
                                    $fechaInicio2=$fechaInicio2_->format('Y-m-d H:i:s');
        
                                    $fechaFin2=$DataHoraFin2['fechahora'];

                                    $horaInicio2=explode(' ',$fechaInicio2);
                                    $horaFin2=explode(' ',$DataHoraFin2['fechahora']);
                                    $horaInicio2="Hr: ".$horaInicio2[1];
                                    $horaFin2="Hr: ".$horaFin2[1];
                                }
                                else{         
                                   
                                }
                                $DataServicios__ = Yii::$app->db->createCommand("CALL pa_obtenerServiciosMovilSincroHora('".$fechaInicio2."','".$fechaFin2."', '".$value['id']."')")->queryAll();
                                foreach ($DataServicios__ AS $valueSer) {
                                    if($valueSer['servicio']==''){
                                        $sincronizo2="NO, Sincronizo Todos  los servicios";
                                    }
                                    if($ingreso=="NO"){
                                        $sincronizo2="";
                                    }
                                }

                            }
                            else{
                                $fechaHora=explode(' ',$fechaPrincipal);
                                $fecha=explode('-',$fechaHora[0]);
                               // $hora=$fechaHora[1];
                                $fecha=$fecha[2]."/".$fecha[1]."/".$fecha[0];

                            }
                            

                            if($sincronizo==1){
                                if($sincronizo1=="SI, Sincronizo Todos los servicios" && $sincronizo2=="SI, Sincronizo Todos los servicios" ){

                               
                        ?>
                                <tr>
                                    <td><?=$key+1 ?></td>
                                    <td><?=$value['username']?></td>
                                    <td><?=$value['equipo']?></td>
                                    <td><?=$fecha?></td>
                                    <td align="Center" >
        								<?php if($ingreso=='SI'){
        									echo "- Primer Ingreso Hr: ".$horaInicioIngreso."<br> - Ultimo Ingreso Hr: ".$horaFinalIngreso;
        								}else{
        									echo $ingreso;
        								}?>
        							</td>
                                    <td align="Center" ><?=$value['cantidadIngreso']?></td>
                                    <td><?php
                                    if($ingreso=="SI"){
                                        if($horaFin1!=""){
                                        echo $horaInicio1.' - '.$sincronizo1;
                                        $horaSincronizacion=" - Primera sincronización ".$fecha." ".$horaInicio1;
                                        ?>

                                        <a href="" class="btn-link" data-toggle="modal" id="btn-detallesincro" data-target="#ModalSincronizacion" onclick="CargarServicios('<?=$fechaInicio1?>','<?=$fechaFin1?>','<?=$value['id']?>','<?=$value['username']?>','<?=$horaSincronizacion?>')"><br>Ver Detalle sincronización.</a>
                                        <?php }
                                        } ?>
                                    </td>
                                    <td><?php
                                    if($ingreso=="SI"){
                                        if($horaFin2!=""){
                                        echo $horaFin2.' - '.$sincronizo2;
                                        $horaSincronizacion=" - Ultima sincronización ".$fecha." ".$horaFin2;
                                        ?>
                                        
                                        <a href="" class="btn-link" data-toggle="modal" id="btn-detallesincro" data-target="#ModalSincronizacion" onclick="CargarServicios('<?=$fechaInicio2?>','<?=$fechaFin2?>','<?=$value['id']?>','<?=$value['username']?>','<?=$horaSincronizacion?>')"><br>Ver Detalle sincronización.</a>
                                        <?php }
                                        } ?> 
                                    </td>
                                    
                                </tr>
                           <?php
                                }
                            }
                            else{
                            ?>
                                <tr>
                                    <td><?=$key+1 ?></td>
                                    <td><?=$value['username']?></td>
                                    <td><?=$value['equipo']?></td>
                                    <td><?=$fecha?></td>
                                    <td align="Center" >
                                        <?php if($ingreso=='SI'){
                                            echo "- Primer Ingreso Hr: ".$horaInicioIngreso."<br> - Ultimo Ingreso Hr: ".$horaFinalIngreso;
                                        }else{
                                            echo $ingreso;
                                        }?>
                                    </td>
                                    <td align="Center" ><?=$value['cantidadIngreso']?></td>
                                    <td><?php
                                    if($ingreso=="SI"){
                                        if($horaFin1!=""){
                                        echo $horaInicio1.' - '.$sincronizo1;
                                        $horaSincronizacion=" - Primera sincronización ".$fecha." ".$horaInicio1;
                                        ?>

                                        <a href="" class="btn-link" data-toggle="modal" id="btn-detallesincro" data-target="#ModalSincronizacion" onclick="CargarServicios('<?=$fechaInicio1?>','<?=$fechaFin1?>','<?=$value['id']?>','<?=$value['username']?>','<?=$horaSincronizacion?>')"><br>Ver Detalle sincronización.</a>
                                        <?php }
                                        } ?>
                                    </td>
                                    <td><?php
                                    if($ingreso=="SI"){
                                        if($horaFin2!=""){
                                        echo $horaFin2.' - '.$sincronizo2;
                                        $horaSincronizacion=" - Ultima sincronización ".$fecha." ".$horaFin2;
                                        ?>
                                        
                                        <a href="" class="btn-link" data-toggle="modal" id="btn-detallesincro" data-target="#ModalSincronizacion" onclick="CargarServicios('<?=$fechaInicio2?>','<?=$fechaFin2?>','<?=$value['id']?>','<?=$value['username']?>','<?=$horaSincronizacion?>')"><br>Ver Detalle sincronización.</a>
                                        <?php }
                                        } ?> 
                                    </td>
                                    
                                </tr>



                        <?php
                            }
                        } 
                    }
                    ?>
                    
                </tbody>
            </table>
            <!-- /.table-responsive -->
            <div class="well">
                <p id="p-resultado"> </p>  
            </div>
            
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
   
</div>
<!-- Modal -->
<div class="modal fade" id="ModalSincronizacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" id="ModalSincronizacionSize">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" align="Center"> Servicios de Sincronización - Xmobile </h4>
      </div>
      <div class="modal-body">
      <span style="color:#0A1253" > <h4 id="NOMBRE-FECHA"></h4></span><hr>
            <div class="row" id="DIV-SERVICIOS">
                
            </div>     
      
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<?=  $this->registerJsFile(Yii::getAlias('@web') . '/scripts/Usuariolog.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?> 
<script>
    
</script>
