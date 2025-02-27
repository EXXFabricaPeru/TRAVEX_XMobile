<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="container-fluid">
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'idPersona',
            'nombrePersona',
            'apellidoPPersona',
            'apellidoMPersona',
        //'estadoPersona',
        //'fechaUMPersona',
        //'documentoIdentidadPersona',
        ],
    ])
    ?>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Usuario movil</a></li>
            <li><a href="#tabs-2">Usuario desktop</a></li>
        </ul>
        <div id="tabs-1">
            <div class="row">
                <div class="col-xs-6 thumbnail">
                    <?php foreach ($usermovil as $key => $val) {
                        echo $val->username;
                        echo '<br/>';
                        echo $val->plataformaPlataforma;
                    }
                    ?>
                    <input type="text" class="form-control" placeholder="Usuario" aria-describedby="basic-addon1">
                    <br/>
                    <input type="text" class="form-control" placeholder="Password"  placeholder="Recipient's username" aria-describedby="basic-addon2">
                    <br/>
                    <button class="btn btn-default btn-xs dropdown-toggle" style="width:100%" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Guardar usuario movil
                    </button>
                </div>
                <div class="col-xs-6">

                </div>
            </div>

        </div>
        <div id="tabs-2">
            <div class="row">
                <div class="col-xs-6 thumbnail">
                    <input type="text" class="form-control" placeholder="Usuario" aria-describedby="basic-addon1">
                    <br/>
                    <input type="text" class="form-control" placeholder="Password"  placeholder="Recipient's username" aria-describedby="basic-addon2">
                    <br/>
                    <button class="btn btn-warning btn-xs dropdown-toggle" style="width:100%" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Guardar Usuario desktop
                    </button>
                </div>
                <div class="col-xs-6 thumbnail">

                </div>
            </div>
        </div>
    </div>








</div>


