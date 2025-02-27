
<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\Autocomplete;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\CabeceradocumentosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reportes';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
//print_r($resumen[0]);
//var_dump(json_encode($detalleDOF));
//echo '<br>';
//print_r($detalleDOP);
//echo '<br>';
//print_r($detalleDFA);
//echo '<br>';
//print_r($detalleDOE);
//echo '<br>';
 ?>
 <?php  if ($tipoReporte == 1) {  ?>
    <h4 class="text-center">Reporte de Ventas </h4>
    <h5 class="text-center"><?php echo $fini." - ".$ffin?> </h5>
    <small>Usuario: <?php echo $usuario; ?></small>
 
 <br>
 <table width="98%" border="0" align="center" >
 <tr>
 <td></td>
 <td><?php 
 if ($resumen[0]['DocType']) {
     echo nombreTipoDato($resumen[0]['DocType']);
 }
 ?></td>
 <td><?php 
 if ($resumen[1]['DocType']) {
    echo nombreTipoDato($resumen[1]['DocType']);
 }
 ?></td>
 <td><?php 
 if ($resumen[2]['DocType']) {
    echo nombreTipoDato($resumen[2]['DocType']);
 }
 ?></td>
 <td><?php 
 if ($resumen[3]['DocType']) {
    echo nombreTipoDato($resumen[3]['DocType']);
 }
 ?></td>
 </tr>
 <tr>
 <td>TOTAL</td>
 <td><?php echo $resumen[0]['cantidad'] ? $resumen[0]['cantidad'] : '';?></td>
 <td><?php echo $resumen[1]['cantidad'] ? $resumen[1]['cantidad'] : '';?></td>
 <td><?php echo $resumen[2]['cantidad'] ? $resumen[2]['cantidad'] : '';?></td>
 <td><?php echo $resumen[3]['cantidad'] ? $resumen[3]['cantidad'] : '';?></td>
 </tr>
 <tr>
 <td>MONTO</td>
 <td><?php echo $resumen[0]['total'] ? $resumen[0]['total'] : '';?></td>
 <td><?php echo $resumen[1]['total'] ? $resumen[1]['total'] : '';?></td>
 <td><?php echo $resumen[2]['total'] ? $resumen[2]['total'] : '';?></td>
 <td><?php echo $resumen[3]['total'] ? $resumen[3]['total'] : '';?></td>
 </tr>
 </table>
<br>
<?php if (count($detalleDOF)>0) {?>
<h5>Ofertas</h5>
<table width="98%" border="0" align="center" style="font-size:10px;">
    <tr>
    <td>ID Documento</td>
    <td>Fecha</td>
    <td>Código Cliente</td>
    <td>Nombre</td>
    <td>Total</td>
    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($detalleDOF); $i++) { 
    for ($i=0; $i < count($detalleDOF); $i++) { 
        echo "<tr>";
        echo "<td>".$detalleDOF[$i]['idDocPedido']."</td>";
        echo "<td>".$detalleDOF[$i]['DocDate']."</td>";
        echo "<td>".$detalleDOF[$i]['CardCode']."</td>";
        echo "<td>".$detalleDOF[$i]['CardName']."</td>";
        echo "<td>".$detalleDOF[$i]['DocTotalPay']."</td>";
        echo "</tr>";
    }
}
    ?>
    <?php if (count($detalleDOF)>0) {?>
</table>
<?php } ?>

<?php if (count($detalleDOP)>0) {?>
<h5>Pedidos</h5>
<table width="98%" border="0" align="center" style="font-size:10px;" >
<tr>
<td>ID Documento</td>
<td>Fecha</td>
<td>Código Cliente</td>
<td>Nombre</td>
<td>Total</td>
</tr>
<?php } ?>
<?php
if (count($detalleDOP)>0) {
    for ($i=0; $i < count($detalleDOP); $i++) { 
        echo "<tr>";
        echo "<td>".$detalleDOP[$i]['idDocPedido']."</td>";
        echo "<td>".$detalleDOP[$i]['DocDate']."</td>";
        echo "<td>".$detalleDOP[$i]['CardCode']."</td>";
        echo "<td>".$detalleDOP[$i]['CardName']."</td>";
        echo "<td>".$detalleDOP[$i]['DocTotalPay']."</td>";
        echo "</tr>";
    }
}
?>
<?php if (count($detalleDOP)>0) {?>
</table>
<?php } ?>

<?php if (count($detalleDFA)>0) {?>
<h5>Facturas</h5>
<table width="98%" border="0" align="center" style="font-size:10px;" >
<tr>
<td>ID Documento</td>
<td>Fecha</td>
<td>Código Cliente</td>
<td>Nombre</td>
<td>Total</td>
</tr>
<?php } ?>
<?php
if (count($detalleDFA)>0) {
    for ($i=0; $i < count($detalleDFA); $i++) { 
        echo "<tr>";
        echo "<td>".$detalleDFA[$i]['idDocPedido']."</td>";
        echo "<td>".$detalleDFA[$i]['DocDate']."</td>";
        echo "<td>".$detalleDFA[$i]['CardCode']."</td>";
        echo "<td>".$detalleDFA[$i]['CardName']."</td>";
        echo "<td>".$detalleDFA[$i]['DocTotalPay']."</td>";
        echo "</tr>";
    }
}
    ?>
<?php if (count($detalleDFA)>0) {?>
</table>
<?php } ?>

<?php if (count($detalleDOE)>0) {?>
<h5>Entregas</h5>
<table width="98%" border="0" align="center" style="font-size:10px;" >
<tr>
<td>ID Documento</td>
<td>Fecha</td>
<td>Código Cliente</td>
<td>Nombre</td>
<td>Total</td>
</tr>
<?php } ?>
<?php
if (count($detalleDOE)>0) {
    for ($i=0; $i < count($detalleDOE); $i++) { 
        echo "<tr>";
        echo "<td>".$detalleDOE[$i]['idDocPedido']."</td>";
        echo "<td>".$detalleDOE[$i]['DocDate']."</td>";
        echo "<td>".$detalleDOE[$i]['CardCode']."</td>";
        echo "<td>".$detalleDOE[$i]['CardName']."</td>";
        echo "<td>".$detalleDOE[$i]['DocTotalPay']."</td>";
        echo "</tr>";
    }
}
    ?>
<?php if (count($detalleDOE)>0) {?>
</table width="98%" border="0" align="center" >
<?php } ?>

<?php } ?>

<?php if ($tipoReporte == 2) { ?>
<h4 class="text-center">Reporte de movimientos de caja </h4>
<h5 class="text-center"><?php echo $fini." - ".$ffin?> </h5>
    <table width="98%" border="0" align="center">
        <thead>
            <tr>
                <th>Forma de Pago</th>
                <th>Total</th>      
            </tr>        
        </thead>
        <tbody>
            <?php 
            foreach ($caja as $key) { ?>
                <tr>
                    
                    <td><?= $key["formaPago"] ?></td>
                    <td><?= $key["montoTotal"] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<hr/>
    <table style="font-size:10px;">
        <tbody>
            <?php 
            foreach ($caja2 as $key) { ?>
                <tr>
                    <?php if ($key["h"] != '') { ?>
                        <td>PG-<?= $key["h"] ?></td>
                    <?php } else { ?>
                        <td><?= $key["a"] ?></td>
                    <?php } ?>
                    <td><?= $key["b"] ?></td>
                    <td><?= $key["c"] ?></td>
                    <td><?= $key["d"] ?></td>
                    <td><?= $key["e"] ?></td>
                    <td><?= $key["f"] ?></td>
                    <td><?= $key["g"] ?></td>
                    <td><?= $key["i"] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<p></p>
<hr/>
    <table style="font-size:10px;">
        <tbody>
            <?php 
            foreach ($caja3 as $key) { ?>
                <tr>
                    <?php if ($key["h"] != '') { ?>
                        <td>PG-<?= $key["h"] ?></td>
                    <?php } else { ?>
                        <td><?= $key["a"] ?></td>
                    <?php } ?>
                    <td><?= $key["b"] ?></td>
                    <td><?= $key["c"] ?></td>
                    <td><?= $key["d"] ?></td>
                    <td><?= $key["e"] ?></td>
                    <td><?= $key["f"] ?></td>
                    <td><?= $key["g"] ?></td>
                    <td><?= $key["i"] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <p></p>
    <hr/>
    <table style="font-size:10px;">
        <tbody>
            <?php 
            foreach ($caja4 as $key) { ?>
                <tr>
                    <?php if ($key["h"] != '') { ?>
                        <td>PG-<?= $key["h"] ?></td>
                    <?php } else { ?>
                        <td><?= $key["a"] ?></td>
                    <?php } ?>
                    <td><?= $key["b"] ?></td>
                    <td><?= $key["c"] ?></td>
                    <td><?= $key["d"] ?></td>
                    <td><?= $key["e"] ?></td>
                    <td><?= $key["f"] ?></td>
                    <td><?= $key["g"] ?></td>
                    <td><?= $key["i"] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>


<?php } ?>

 <?php
 function nombreTipoDato($code){
    switch ($code) {
        case 'DOF':
            return 'OFERTAS';
        case 'DOP':
            return 'PEDIDOS';
        case 'DFA':
            return 'FACTURAS';
        case 'DOE':
            return 'ENTREGAS';
        default:
            return 'DOCUMENTO';
    }
 }
 ?>


<!-- REPORTE ITEM VENTA -->

<?php  if ($tipoReporte == 4) {  ?>
    <h4 class="text-center">Reporte Ítem Ventas </h4>
    <h5 class="text-center"><?php echo $fini." - ".$ffin?> </h5>
    <small>Usuario: <?php echo $usuario; ?></small>
 
 <br>
 
<br>
<?php if (count($reporte)>0) {?>
<h5>Item Venta</h5>
<table width="98%" border="0" align="center" style="font-size:10px;">
    <tr>
    <td>ID Documento</td>
    <td>ID SAP</td>
    <td>Código Ítem</td>
    <td>Nombre</td>
    <td>Cantidad</td>
    <td>Precio</td>
    <td>Total Línae</td>
    <td>Almacén</td>
    <td>C. Costo</td>
    <td>Fecha Doc.</td>
    <td>Fecha Venc.</td>
    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($reporte); $i++) { 
    for ($i=0; $i < count($reporte); $i++) { 
        echo "<tr>";
        echo "<td>".$reporte[$i]['idDocPedido']."</td>";
        echo "<td>".$reporte[$i]['DocNum']."</td>";
        echo "<td>".$reporte[$i]['ItemCode']."</td>";
        echo "<td>".$reporte[$i]['Dscription']."</td>";
        echo "<td>".$reporte[$i]['Quantity']."</td>";
        echo "<td>".$reporte[$i]['Price']."</td>";
        echo "<td>".$reporte[$i]['LineTotal']."</td>";
        echo "<td>".$reporte[$i]['WhsCode']."</td>";
        echo "<td>".$reporte[$i]['producto_std1']."</td>";
        echo "<td>".Yii::$app->formatter->asDate($reporte[$i]['DocDueDate'],'dd/MM/yyyy')."</td>";
        echo "<td>".Yii::$app->formatter->asDate($reporte[$i]['DocDate'],'dd/MM/yyyy')."</td>";
        echo "</tr>";
    }
}
    ?>
    
</table>
<?php } ?>
<!-- fin de reporte item venta -->


<!-- REPORTE ARQUEO CAJA / DETALLE -->

<?php  if ($tipoReporte == 5) {  ?>
    <h4 class="text-center">Reporte Arqueo Caja </h4>
    <h5 class="text-center"><?php echo $fini." - ".$ffin?> </h5>
    <small>Usuario: <?php echo $usuario; ?></small>
<br>
<br>
<?php if (count($arqueo)>0) {?>
<h5>Resumen</h5>
<table width="98%" border="0" align="center" style="font-size:10px;">
    <tr>
    <td>Transaccion</td>
    <td># Documento</td>
    <td>Cliente</td>
    <td>T.Cambio</td>
    <td>BS</td>
    <td>$US</td>
    <td>Fecha</td>
    <td>Cambio</td>
    <td>Usuario</td>
    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($arqueo); $i++) { 
    for ($i=0; $i < count($arqueo); $i++) { 
        echo "<tr>";
        echo "<td>".$arqueo[$i]['Transaccion']."</td>";
        echo "<td>".$arqueo[$i]['documentoId']."</td>";
        echo "<td>".$arqueo[$i]['clienteId']."</td>";
        echo "<td>".$arqueo[$i]['formaPago']."</td>";
        echo "<td>".Yii::$app->formatter->asDecimal($arqueo[$i]['tipoCambioDolar'],2,[],[])."</td>";
        echo "<td>".Yii::$app->formatter->asDecimal($arqueo[$i]['bs'],2,[],[])."</td>";
        echo "<td>".Yii::$app->formatter->asDecimal($arqueo[$i]['Sus'],2,[],[])."</td>";
        echo "<td>".Yii::$app->formatter->asDate($arqueo[$i]['fecha'],'dd/MM/yyyy')."</td>";
        echo "<td>".Yii::$app->formatter->asDecimal($arqueo[$i]['cambio'],2,[],[])."</td>";
        echo "<td>".$arqueo[$i]['usuario']."</td>";
        echo "</tr>";
    }
}
?>
</table>

<?php if (count($detalle)>0) {?>
<h5>Detalle</h5>
<table width="98%" border="0" align="center" style="font-size:10px;">
    <tr>
    <td>Usuario</td>
    <td>F. Pago</td>
    <td>Detalle</td>
    <td>T. Cambio</td>
    <td>BS</td>
    <td>$US</td>
    <td>Equivalente</td>
    <td>Fecha</td>
    <td>Cambio</td>
    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($detalle); $i++) { 
    for ($i=0; $i < count($detalle); $i++) { 
        echo "<tr>";
        echo "<td>".$detalle[$i]['usuario']."</td>";
        echo "<td>".$detalle[$i]['formaPago']."</td>";
        echo "<td>".$detalle[$i]['detalle']."</td>";
        echo "<td>".$detalle[$i]['tipoCambioDolar']."</td>";
        echo "<td>".$detalle[$i]['bs']."</td>";
        echo "<td>".$detalle[$i]['Sus']."</td>";
        echo "<td>".$detalle[$i]['equivalente']."</td>";
        echo "<td>".$detalle[$i]['fecha']."</td>";
        echo "<td>".$detalle[$i]['cambio']."</td>";
        echo "</tr>";
    }
}
?>
    
</table>
<?php } ?>
<!-- fin de reporte arqueo -->


<!-- inicio de resumen ventas -->
<?php  if ($tipoReporte == 6) {  ?>
    <h4 class="text-center">Resumen de Ventas </h4>
    <small>Fecha: <?php echo $fini/* ." - ".$ffin */?> </small>
    <br>
    <small>Usuario: <?php echo $usuario; ?></small>
<br>
<br>
<?php if (count($resumen)>0) {?>
<table width="98%" border="0" align="center" style="font-size:10px;">
    <tr>
    <td><b>TIPO</b></td>
    <td><b>CANTIDAD</b></td>
    <td><b>MONTO</b></td>

    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($resumen); $i++) { 
    for ($i=0; $i < count($resumen); $i++) { 
        echo "<tr>";
        echo "<td>".$resumen[$i]['TIPO']."</td>";
        echo "<td>".$resumen[$i]['CANTIDAD']."</td>";
        echo "<td>".$resumen[$i]['MONTO']."</td>";
        echo "</tr>";
    }
}
?>
</table>

<?php } ?>
<!-- fin de resumen ventas -->

<!-- inicio de cierre diario -->
<?php  if ($tipoReporte == 7) {  ?>
    <h4 class="text-center">Cierre Diario </h4>
    <small>Fecha: <?php echo $fini/* ." - ".$ffin */?> </small>
    <br>
    <small>Usuario: <?php echo $usuario; ?></small>
<br>
<br>
<?php if (count($cabecera)>0) {?>
<table width="98%" border="0" align="center" style="font-size:10px;">
    <tr>
    <td><b>CIERRE</b></td>
    <td><b>MONTO</b></td>
    <td><b>MONTOBS</b></td>

    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($cabecera); $i++) { 
    for ($i=0; $i < count($cabecera); $i++) { 
        echo "<tr>";
        echo "<td>".$cabecera[$i]['CIERRE']."</td>";
        echo "<td>".$cabecera[$i]['MONTO']."</td>";
        echo "<td>".$cabecera[$i]['MONTOBS']."</td>";
        echo "</tr>";
    }
}
?>
</table>
<br>
<?php if (count($detalle)>0) {?>
<small>DETALLE</small>
<br>
<table width="98%" border="0" align="center" style="font-size:10px;">
    <tr>
    <td><b>NRO</b></td>
    <td><b>FECHA</b></td>
    <td><b>BANCO</b></td>
    <td><b>MONTO</b></td>

    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($detalle); $i++) { 
    for ($i=0; $i < count($detalle); $i++) { 
        echo "<tr>";
        echo "<td>".$detalle[$i]['NRO']."</td>";
        echo "<td>".$detalle[$i]['FECHA']."</td>";
        echo "<td>".$detalle[$i]['BANCO']."</td>";
        echo "<td>".$detalle[$i]['MONTO']."</td>";
        echo "</tr>";
    }
}
?>
</table>

<?php } ?>
<!-- fin de cierre diario -->

<!-- inicio de resumen diario inventario -->

<?php  if ($tipoReporte == 8) {  ?>
    <h4 class="text-center">Resumen Inventario </h4>
    <small>Fecha: <?php echo $fini/* ." - ".$ffin */?> </small>
    <br>
    <small>Usuario: <?php echo $usuario; ?></small>
<br>
<br>
<table width="98%" border="0" align="center" style="font-size:10px;">
<?php if (count($resumen)>0) {?>
    <tr>
    <td><b>PRODUCTO</b></td>
    <td><b>COMPROMETIDO</b></td>
    <td><b>STOCK</b></td>
    <td><b>DISPONIBLE</b></td>
    </tr>
<?php } ?>
<?php
for ($i=0; $i < count($resumen); $i++) { 
    for ($i=0; $i < count($resumen); $i++) { 
        echo "<tr>";
        echo "<td>".$resumen[$i]['PRODUCTO']."</td>";
        echo "<td>".$resumen[$i]['COMPROMETIDO']."</td>";
        echo "<td>".$resumen[$i]['STOCK']."</td>";
        echo "<td>".$resumen[$i]['DISPONIBLE']."</td>";
        echo "</tr>";
    }
}
?>
</table>

<?php } ?>

<!-- fin de resumen diario inventario -->
