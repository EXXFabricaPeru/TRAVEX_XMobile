<div class="row" id="pasoUno">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="page-header">
            <h1>Xmobile <small>Sistema de instalacion.  </small></h1>
        </div>
        <p class="text-info" id="textMsm">Datos de la base de datos local.</p>
        <form class="form-horizontal" id="form-data-1">
            <input type="hidden" name="_csrf-frontend" value="<?= Yii::$app->request->getCsrfToken() ?>" />
            <div class="form-group">
                <label for="usuarioini" class="col-sm-2 control-label">Usuario</label>
                <div class="col-sm-8">
                    <input type="text" name="usuario" class="form-control" id="usuarioini">
                </div>
            </div>
            <div class="form-group">
                <label for="contraseniaini" class="col-sm-2 control-label">Contraseña</label>
                <div class="col-sm-8">
                    <input type="password" name="password" class="form-control" id="contraseniaini" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label for="basedataini" class="col-sm-2 control-label">Base de datos</label>
                <div class="col-sm-8">
                    <input type="text" name="dbdata" class="form-control" id="basedataini" placeholder="">
                </div>
            </div>
        </form>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-2">
                <button id="actionPasoUno" class="btn btn-default">Siguente >></button>
            </div>
            <div class="col-md-5">
                <div class="spinner" style="display:none">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row" style="display:none"  id="pasoDos">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="page-header">
            <h1>Xmobile <small>Sistema de instalacion.  </small></h1>
        </div>
        <p class="text-info">Datos de  SAP.</p>
        <form class="form-horizontal" id="actionPasoDos">
            <div class="form-group">
                <label for="usuarioini" class="col-sm-2 control-label">IP HANA</label>
                <div class="col-sm-8">
                    <input type="text" name="hanaip" class="form-control" value="https://192.168.50.71:50000/b1s/v1/" id="usuarioini">
                </div>
            </div>
            <div class="form-group">
                <label for="usuarioini" class="col-sm-2 control-label">Usuario</label>
                <div class="col-sm-8">
                    <input type="text" name="hanauser" class="form-control" value="manager" id="usuarioini">
                </div>
            </div>
            <div class="form-group">
                <label for="contraseniaini" class="col-sm-2 control-label">Contraseña</label>
                <div class="col-sm-8">
                    <input type="password" name="password" class="form-control" value="1234" id="contraseniaini" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label for="basedataini" class="col-sm-2 control-label">Base de datos</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="hanadatabase" value="SBO_INKAFERRO_PROD" id="basedataini" placeholder="">
                </div>
            </div>
        </form>
        <div class="form-group">

            <div class="col-sm-offset-2 col-sm-2">
                <button id="btnactionPasoDos" class="btn btn-default">Siguiente</button>
            </div>
            <div class="col-md-5">
                <div class="spinner" style="display:none">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row" style="display:none"  id="pasoTres">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="page-header">
            <h1>Xmobile <small>Sistema de instalacion.  </small></h1>
        </div>
        <p class="text-info">Monedas.</p>
        <form class="form-horizontal" id="actionPasoTres">
            <div class="form-group">
                <label for="usuarioini" class="col-sm-2 control-label">Moneda Sistema:</label>
                <div class="col-sm-8">
                    <select name="moneda-sistema" id="moneda-sistema" class="form-control"></select>
                </div>
            </div>
            <div class="form-group">
                <label for="usuarioini" class="col-sm-2 control-label">Moneda Local:</label>
                <div class="col-sm-8">
                <select name="moneda-local" id="moneda-local" class="form-control"></select>
                </div>
            </div>
            <div class="form-group">
                <label for="contraseniaini" class="col-sm-2 control-label">Moneda Otro:</label>
                <div class="col-sm-8">
                <select name="moneda-otro" id="moneda-otro" class="form-control"></select>
                </div>
            </div>
        </form>
        <div class="form-group">

            <div class="col-sm-offset-2 col-sm-2">
                <button id="btnactionPasoTres" class="btn btn-default">Finalizar</button>
            </div>
            <div class="col-md-5">
                <div class="spinner" style="display:none">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>
</div>

<span id="datapath"  name="<?= Yii::$app->urlManager->createUrl(''); ?>"></span>

<style>
    .spinner {
        width: 50px;
        height: 40px;
        text-align: center;
        font-size: 10px;
    }
    .spinner > div {
        background-color: #333;
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }
    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }

</style>
<?= $this->registerJsFile(Yii::getAlias('@web') . '/js/Main.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

