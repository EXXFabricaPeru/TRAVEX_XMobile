<div class="container">
    <div class="row">
        <div class="col" style="border-right: 1px solid #cccccc; width:20% !important">
            <div class="row">
                <div class="col x4"><b><?= $data->DocType ?></b></div>
                <div class="col x4"><b><?= $data->idDocPedido ?></b></div>
                <div class="col x4"><b><?= $data->estado ?></b></div>
            </div>
            <div class="row">
                <div class="col x3"><b>Cliente</b></div>
                <div class="col x6"><?= $data->CardCode ?> (<?= $data->CardName ?>)</div>
            </div>

            <div class="row">
                <div class="col x3"><b>Condicion de pago</b></div>
                <div class="col x6">
                    <div class="ui-widget">
                        <select id="combobox">
                            <option value="0"> Selecionar </option>
                            <?php foreach ($condicion as $key) { ?>
                                <option value="<?= ($key->NumberOfAdditionalMonths * 30) + $key->NumberOfAdditionalDays ?>"><?= $key->PaymentTermsGroupName ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col x3"><b>Razon social</b></div>
                <div class="col x6"><?= $data->U_4RAZON_SOCIAL ?></div>
            </div>
            <div class="row">
                <div class="col x3"><b>Fecha de creacion</b></div>
                <div class="col x6"><?= $data->CreateDate ?></div>
            </div>
            <div class="row">
                <div class="col x3">
                    <table>
                        <tr>
                            <td><b>Plazo de entrega.</b></td>
                            <td>
                                <div class="loadPickerLOAD" id="circularG"><div id="circularG_1" class="circularG"></div><div id="circularG_2" class="circularG"></div><div id="circularG_3" class="circularG"></div><div id="circularG_4" class="circularG"></div><div id="circularG_5" class="circularG"></div><div id="circularG_6" class="circularG"></div><div id="circularG_7" class="circularG"></div><div id="circularG_8" class="circularG"></div></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col x6"> 
                    <span id="labelDocDueDate"><?= $data->DocDueDate ?></span> 
                </div>
            </div>
            <div class="row">
                <div class="col x3"><b>Descuento en porsentaje</b></div>
                <div class="col x6"><?= $data->TotalDiscPrcnt ?></div>
            </div>
            <div class="row">
                <div class="col x3"><b>Descuento en monetario</b></div>
                <div class="col x6"><?= $data->TotalDiscMonetary ?></div>
            </div>
            <div class="row">
                <div class="col x3"><b>Total</b></div>
                <div class="col x6"><?= $data->DocTotalPay ?></div>
            </div>
        </div>
        <div class="col m6">
            <table class="minimalistBlack">
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Descuento</th>
                        <th>L. Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($detalles as $value) {
                        $total += $value->LineTotal;
                        ?>
                        <tr class="itemLime<?= $value->id ?>">
                            <td> <input class="valorQuantity" value="<?= $value->Quantity ?>" data-id='<?= $value->id ?>' style="width:40px;"> </td>
                            <td><?= $value->ItemCode ?> | <?= $value->Dscription ?> </td>
                            <td><span id="linePrecio<?= $value->id ?>" name='<?= $value->Price ?>'><?= $value->Price ?></td>
                            <td><?= $value->DiscTotalMonetary ?></td>
                            <td><span id="lineTotal<?= $value->id ?>" class="lineTotal" ><?= $value->LineTotal ?></span></td>
                            <td><button class="bagDad eliminarLine" value="<?= $value->id ?>" >x</button></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b class="totalTodo"><?= $total ?></b></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    .bagDad{
        background-color: #e8eaf6;
        width: auto;
        padding: 5px;
        padding-left: 8px;
        padding-right: 8px;
        border-radius: 5px;
    }
    table.minimalistBlack {
        border: 3px solid #ccc;
        width: 100%;
        text-align: left;
        border-collapse: collapse;
    }
    table.minimalistBlack td, table.minimalistBlack th {
        border: 1px solid #ccc;
        padding: 5px 4px;
    }
    table.minimalistBlack tbody td {
        font-size: 13px;
    }
    table.minimalistBlack thead {
        background: #CFCFCF;
        background: -moz-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        background: -webkit-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        background: linear-gradient(to bottom, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
        border-bottom: 3px solid #000000;
    }
    table.minimalistBlack thead th {
        font-size: 15px;
        font-weight: bold;
        color: #000000;
        text-align: left;
    }
    table.minimalistBlack tfoot {
        font-size: 14px;
        font-weight: bold;
        color: #ccc;
        border-top: 3px solid #ccc;
    }
</style>

