<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

//funcion para el menu y accesos
function getPermisosMenu($menu){
    $permisosMenu=Yii::$app->session->get('PERMISOSMENU');
    $permisosMenu=explode('@',$permisosMenu);
    foreach ($permisosMenu as $key => $value) {
        //echo "value: ".strtolower($value)." = menu: ".strtolower($menu)."<br>";
        if(trim(strtolower($value))==trim(strtolower($menu)))
            return true;   
    }
    return false;
}
//fin para el menu acceso
//funcion para el habilitar menu sincronizar
/*function getPermisosMenuSincro($menu){

    $permisoSincro=Yii::$app->session->get('PERMISOSMENUSINCRO');
    $permisoSincro=explode('@',$permisoSincro);
    foreach ($permisoSincro as $key => $value) {
        if(trim(strtolower($value))==trim(strtolower($menu))){
            Yii::error("value: ".trim(strtolower($value))." = menu: ".trim(strtolower($menu)));
            return '';
        }
               
    }
    return 'disabled';
}*/
//fin del menu sincronizar

// verifica si la tabla de configuracion esta habilitado el tipo de cambio paralelo
$valor = backend\models\Configuracion::find()->where("parametro='cambio_paralelo'")->one();
$DataMenuSincro = backend\models\Menusincronizar::find()->where("estado='A'")->orderby('nombre asc')->all();
//foreach ($DataMenuSincro as $key => $value) {
   // echo($value['nombre']."<br>");
//}
//print_r($DataMenuSincro);
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" sizes="57x57" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::getAlias('@web') ?>/extras/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?= Yii::getAlias('@web') ?>/extras/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::getAlias('@web') ?>/extras/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?= Yii::getAlias('@web') ?>/extras/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::getAlias('@web') ?>/extras/favicon-16x16.png">
        <link rel="manifest" href="<?= Yii::getAlias('@web') ?>/extras/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?= Yii::getAlias('@web') ?>/extras/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <?php $this->registerCsrfMetaTags() ?>
        <title>Middleware</title>
        <?php $this->head() ?>
        <style>
            #loadingAjax{
                height: 90px;
                width: 90px;
                margin-left: 46%;
                margin-right: auto;
                position: fixed;
                z-index: 10000;
                background-color: rgba(0,0,0,0.2);
                border-radius: 10px;
                display: none;
            }
            .lds-roller {
                display: inline-block;
                position: relative;
                width: 80px;
                height: 80px;
            }
            .lds-roller div {
                animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
                transform-origin: 40px 40px;
            }
            .lds-roller div:after {
                content: " ";
                display: block;
                position: absolute;
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: #fff;
                margin: -4px 0 0 -4px;
            }
            .lds-roller div:nth-child(1) {
                animation-delay: -0.036s;
            }
            .lds-roller div:nth-child(1):after {
                top: 63px;
                left: 63px;
            }
            .lds-roller div:nth-child(2) {
                animation-delay: -0.072s;
            }
            .lds-roller div:nth-child(2):after {
                top: 68px;
                left: 56px;
            }
            .lds-roller div:nth-child(3) {
                animation-delay: -0.108s;
            }
            .lds-roller div:nth-child(3):after {
                top: 71px;
                left: 48px;
            }
            .lds-roller div:nth-child(4) {
                animation-delay: -0.144s;
            }
            .lds-roller div:nth-child(4):after {
                top: 72px;
                left: 40px;
            }
            .lds-roller div:nth-child(5) {
                animation-delay: -0.18s;
            }
            .lds-roller div:nth-child(5):after {
                top: 71px;
                left: 32px;
            }
            .lds-roller div:nth-child(6) {
                animation-delay: -0.216s;
            }
            .lds-roller div:nth-child(6):after {
                top: 68px;
                left: 24px;
            }
            .lds-roller div:nth-child(7) {
                animation-delay: -0.252s;
            }
            .lds-roller div:nth-child(7):after {
                top: 63px;
                left: 17px;
            }
            .lds-roller div:nth-child(8) {
                animation-delay: -0.288s;
            }
            .lds-roller div:nth-child(8):after {
                top: 56px;
                left: 12px;
            }
            @keyframes lds-roller {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }


        </style>
    </head>

    <body>
        <?php $this->beginBody() ?>
        <div id="loadingAjax">
            <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
        </div>
        <div id="loader" class="loader" style="display:none">
        </div>
        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => 'MIDDLEWARE',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-default navbar-fixed-top',
                ],
            ]);
            $menuItems = [
			    (getPermisosMenu('Formularios Especiales'))?(
                [
                    'label' => 'Formularios Especiales',
                    'items' => [
                        ['label' => 'Bonificación', 'url' => ['/bonificacionesca']],
                       
                    ],
                ]
				):(""),
                (getPermisosMenu('Configuraciones'))?(
                [
                    'label' => 'Configuraciones',
                    'items' => [
                        ['label' => 'Tipo papel', 'url' => ['/tipopapel']],
                        ['label' => 'Acciones', 'url' => ['/acciones']],
                        ['label' => 'Empresa', 'url' => ['/empresa']],
                        ['label' => 'Numeracion', 'url' => ['/numeracion']],
                        ['label' => 'Grupo cliente dosificacion', 'url' => ['/grupoclientedocificacion']],
                        ['label' => 'Grupo productos dosificacion', 'url' => ['/grupoproductodocificacion']],
						['label' => 'Productos precios', 'url' => ['/productosprecios']],
						['label' => 'Configuracion', 'url' => ['/configuracion']],
						['label' => 'Motivo anulaciones', 'url' => ['/motivoanulacion']],
                        ['label' => 'Motivo no venta', 'url' => ['/motivonoventa']],
                    ],
                ]
				):(""),
                (getPermisosMenu('Herramientas'))?(
                [
                    'label' => 'Herramientas',
                    'items' => [
                        ['label' => 'Sucursales', 'url' => ['/sucursalx']],
                        ['label' => 'Equipos | Moviles', 'url' => ['/equipox']],
                        ['label' => 'Autorizaciones', 'url' => ['/autorizacion']],
                        ['label' => 'Usuarios Log Resumen', 'url' => ['/usuariolog']] ,
                        ['label' => 'Usuarios Log Detalle', 'url' => ['/usuariologdetalle']] ,
                        ['label' => 'Usuario Sincroniza Movil Log', 'url' => ['/usuariosincronizamovil']] ,
                        ['label' => 'Campos de Usuario', 'url' => ['/camposusuarios']] 
                    ],
                ]
				):(""),
                (getPermisosMenu('Usuarios'))?(
                [
                    'label' => 'Usuarios',
                    'items' => [
                        ['label' => 'Usuarios (Persona) ', 'url' => ['/usuariopersona']],
                        ['label' => 'Usuarios y accesos', 'url' => ['/user']],
                        ['label' => 'Roles', 'url' => ['/rolex']],
                    ],
                ]
				):(""),
                (getPermisosMenu('Docx'))?(
                [
                    'label' => 'Docx',
                    'items' => [
                        ['label' => 'Documentos', 'url' => ['/cabeceradocumentos']],
                        ['label' => 'Documentos no enviados', 'url' => ['/cabeceradocumentosnoenviados']],
                        // ['label' => 'Cuerpo del documento', 'url' => ['/detalledocumentos']],
                        ['label' => 'Pagos', 'url' => ['/pagos']],
                        ['label' => 'Pagos no enviados', 'url' => ['/pagosnoenviados']],
                        ['label' => 'Documentos anulados', 'url' => ['/anulaciondocmovil']],
                        ['label' => 'Clientes', 'url' => ['/clientes']],
                        ['label' => 'Clientes no enviados', 'url' => ['/clientesnoenviados']],
                        ['label' => 'Productos', 'url' => ['/productos']],
                        ['label' => 'Almacenes', 'url' => ['/almacenes']],
                        ['label' => 'Personal de contactos', 'url' => ['/contactos']],
                        ['label' => 'Consola de log', 'url' => ['/log-envio']],
                    ],
                ]
				):(""),
                (getPermisosMenu('Geolocalizacion'))?(
                [
                    'label' => 'Geolocalizacion',
                    'items' => [
                        ['label' => 'Reportes', 'url' => ['/geolocalizacion']],
                       // ['label' => 'Polígonos', 'url' => ['/poligonocabecera']],
						['label' => 'Rutas Ventas', 'url' => ['/poligonocabeceraterritorio']],
                        //['label' => 'ruta', 'url' => ['/ruta']],
						//['label' => 'Reporte poligonos', 'url' => ['/poligonoclientereporte']],
						['label' => 'Asignacion de territorios', 'url' => ['/usuariomovilterritorio']],
						//['label' => 'Rutas Despacho', 'url' => ['/rutacabecera']],
                        
                    ]
                ]
				):(""),
            ];
            if (!Yii::$app->user->isGuest) {
                if(getPermisosMenu('Cambio') && $valor['valor']==1){
                    $menuItems[] = '<li>'
                    . '<a href="#" onclick="abrirCambio();">Cambio</a>'
                    . '</li>';
                }
                if(getPermisosMenu('Sincronizar')){
                    $menuItems[] = '<li>'
                    . '<a href="#" onclick="abrirSincronizar();">Sincronizar</a>'
                    . '</li>';
                }
                $menuItems[] = '<li style="margin-top:12px !important">'
                        . Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                                '<b>Salir</b>',
                                ['class' => 'btn-link']
                        )
                        . Html::endForm()
                        . '</li>';

                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items' => $menuItems,
                ]);
            }
            NavBar::end();
            ?>
            <br/>
            <br/>
            <br/>
            <div class="container-fluid">
                <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
                <?= $content ?>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; <?= Html::encode('Xmobile - Middleware') ?> <?= date('Y') ?><?=" - User: ".Yii::$app->session->get('USUARIO') ?></p>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>   

<div class="modal fade" id="divCambio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width:760px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tipo de cambio</h4>
            </div>
            <div class="modal-body">
                <table width="100%">
                    <tr>
                        <td style="width:250px">Ingrese el tipo de cambio para hoy:</td>
                        <td>
                            <input type="text" name="tipoCambio" id="tipoCambio" class="form-control">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="grabarCambio();" data-dismiss="modal">Grabar</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="divSicronizar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width:1000px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sincronizar</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3" id="div-1">
                         <div class="row" >
                         </div>
                    </div>
                    <div class="col-md-3" id="div-2">
                    </div>
                    <div class="col-md-3" id="div-3">
                    </div>
                    <div class="col-md-3" id="div-4">
                    </div> 
             
                </div>
            </div>
                  
            <div class="modal-footer">
                <table width="100%">
                    <tr>
                        <td style="width:90px;text-align:left;">Marcar todos&nbsp;</td>
                        <td style="text-align:left; width:25px;">
                            <input type="checkbox" name="chkTodos" id="chkTodos" class="form-control" onclick="marcarTodos(this);" />
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary" onclick="enviarSincronizar();" data-dismiss="modal">Sincronizar</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    /*var $btn = document.getElementById('btn-sinc');
     $btn.addEventListener('click',function(event){
     //event.preventDefault();
     var $loader = document.getElementById('loader');
     $loader.style.display = 'block';
     });*/
    //listaMenuSincronizar();
    var DataMenuSincro=[];
    function listaMenuSincronizar(){
        $.ajax({
        
        //url: $("#PATH").attr("name")+'menumiddle/listamenusincronizar',
        url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['menumiddle/listamenusincronizar']); ?>',
        type: 'POST',
        data: "",
            success: (data, status, xhr) => {
                if (status == 'success'){
                    var result = JSON.parse(data);
                    DataMenuSincro=result;
                    console.log("Resultado Menu Sincronizar"); 
                    console.log(result);
                    var contenido="";
                    var contador=0;
                    var div=1;
                   // var cantidadFila=Math.round(result.length/4);
                    var cantidadFila=Math.ceil(result.length/4);
                    console.log("Cantidad de filas: "+cantidadFila);
                    for (var i = 0; i < result.length; i++) {
                        if(contador==cantidadFila){
                            $("#div-"+div).html(contenido); 
                            div++;
                            contador=0;
                            contenido='';
                        }
                        contenido+='<div class="col-md-9" ><label for="'+result[i].idChecks+'">'+result[i].nombre+' &nbsp;</label></div><div class="col-md-3" ><input type="checkbox" name="'+result[i].idChecks+'" id="'+result[i].idChecks+'" class="form-control" '+getPermisosMenuSincro(result[i].nombre)+'/> </div>';

                        contador++;
                    }
                    $("#div-"+div).html(contenido);        
                }
                else{
                    console.error("ERROR: ");    
                }
            },
                error: (jqXhr, textStatus, errorMessage) => {
                    console.error("ERROR: " + errorMessage);
            } 
       });
    }

    function abrirCambio() {
        //var loc = window.location.pathname;
        //loc = loc.split('/');
        $.ajax({
            type: 'GET',
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tipocambioparalelo/index']); ?>',
            //url: '/' + loc[1] + '/backend/web/index.php?r=tipocambioparalelo/index',
            success: function (response) {
                console.log(response);
                $('#divCambio').modal();
                $('#divCambio').show('show');
                var cambio = $("#tipoCambio");
                cambio.val(response[0].tipoCambio);
            }
        });
    }

    function grabarCambio() {
        //var loc = window.location.pathname;
        //loc = loc.split('/');
        var cambio = $("#tipoCambio");
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['tipocambioparalelo/create']); ?>',
            type: 'POST',
            //url: '/' + loc[1] + '/backend/web/index.php?r=tipocambioparalelo/create',
            data: {tipocambio: cambio.val()},
            success: function (response) {
            }
        });
    }

    function abrirSincronizar() {
        var loc = window.location.pathname;
        loc = loc.split('/');
        $('#divSicronizar').modal();
        $('#divSicronizar').show('show');
        listaMenuSincronizar();
    }

    function enviarSincronizar() {
        
        var $loader = document.getElementById('loader');
        //$loader.style.display = 'block';
        //var loc = window.location.pathname;
        //loc = loc.split('/');
        var arrayChecks={};
        
        for (var i = 0; i < DataMenuSincro.length; i++) {
            var chkvalor = $("#"+DataMenuSincro[i].idChecks);
            arrayChecks[DataMenuSincro[i].idSite]=(chkvalor[0].checked);
        }
        console.log(arrayChecks);
        $.ajax({
            url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['site/sincronizar']); ?>',
            type: 'POST',
            data: arrayChecks,
            success: function (response) {
                $loader.style.display = 'none';
                alert("Sincronización finalizada correctamente.");
                //$('#loader').addClass("hide-loader");
            },
            error: function (response) {
                alert(response.statusText+": Intente sincronizar nuevamente");
                $loader.style.display = 'none';
                //$('#loader').addClass("hide-loader");
            }
        });
    }
    
    function marcarTodos(control) {
        var valor = false;
        if (control.checked == true) {
            valor = true;
        }

        for (var i = 0; i < DataMenuSincro.length; i++) {
           console.log(DataMenuSincro[i].nombre);
            if(!$("#"+DataMenuSincro[i].idChecks).is(':disabled')) $("#"+DataMenuSincro[i].idChecks).prop('checked', valor);
        }  
    }

    function getPermisosMenuSincro(menu){

        var permisoSincro='<?php echo Yii::$app->session->get('PERMISOSMENUSINCRO') ?>';
        console.log("Permisos: "+permisoSincro);
        permisoSincro=permisoSincro.split('@');
        for (var i = 0; i < permisoSincro.length; i++) {
           if(permisoSincro[i].toLowerCase().replace(/\s+/g, '')==menu.toLowerCase().replace(/\s+/g, '')){
            console.log(permisoSincro[i].toLowerCase().replace(/\s+/g, '')+"=="+permisoSincro[i].toLowerCase().replace(/\s+/g, ''));
                return '';
            } 

        }
        
        return 'disabled';
    }
</script>
