<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Poligonocliente;
$modelDetalle=Poligonocliente::find()->where('idCabecera='.$model->id)->orderby('posicion asc')->asArray()->all();
$arrayRutas=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA'];
$fechaRuta=explode('-',$model->fechaRegistro);
$fechaRuta=$fechaRuta[2].'/'.$fechaRuta[1].'/'.$fechaRuta[0];
?>

    <div class="poligonocabeceraterritorio-view">
      
       <table class="" width="100%"  border="" >
            <tr>
                <td style="font-weight: bold;" align="center"><h3><?=$model->nombreRuta?></h3></td>
            </tr>
        
        </table>
        <table class=""   border="" >
           <!-- <tr>    
                <td width="30%" style="font-weight: bold;" > FECHA DE LA RUTA: </td>
                <td width="70%" style="font-weight: bold;" > <?=$fechaRuta?></td>          
            </tr>-->

            <tr>    
                <td width="30%" style="font-weight: bold;" > DIA: </td>
                <td width="70%" style="font-weight: bold;" > <?=ucfirst($model->dia)?></td>          
            </tr>

            <tr>    
                <td width="30%" style="font-weight: bold;" > NOMBRE VENDEDOR: </td>
                <td width="70%" style="font-weight: bold;" > <?=strtoupper($model->vendedor)?></td>          
            </tr>
      
        </table>
        <hr>
        <table class="" width="100%"  border="" >
            <tr>
                <td style="font-weight: bold;"><h5>HOJA DE RUTA </h5></td>
            </tr>
        </table>

        <table class="hdClientes"  width="100%" border="1" >
            <tr style="background-color: #C3C2C3;">
                <td width="10%" style="font-weight: bold;"  align="center" >ORDEN DE VISITA</td>
                <td width="10%" style="font-weight: bold;" > CODIGO</td>
                <td width="30%" style="font-weight: bold;" >NOMBRE CLIENTE</td>
                <td width="20%" style="font-weight: bold;" >DIRECCION</td>
                <td width="20%" style="font-weight: bold;" >TERRITORIO</td>
                        
            </tr>
            <?php
            foreach ($modelDetalle as $key => $value) {
                # code...

            ?>
                <tr>
                    <!-- <td><?=$key+1?></td>-->
                    <td align="center"><?=$value['posicion']?></td>
                    <td><?=$value['cardcode']?></td>
                    <td><?=ucfirst($value['cardname'])?></td>
                    <td><?=ucfirst($value['nombreDireccion'])?></td>
                    <td><?=$value['territoryname']?></td>
                    
                    
                </tr>
                
            <?php
            }
            ?>
        </table>

    </div>
