<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use backend\assets\AppAsset;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode(Yii::$app->name) ?></title>
        <?php $this->head() ?>
    </head>

    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => 'Xmobile - Middleware',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-default navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                [
                    'label' => 'Configuraciones',
                    'items' => [
                        ['label' => 'Tipo papel', 'url' => ['/tipopapel']],
                        ['label' => 'Acciones', 'url' => ['/acciones']]
                    ],
                ],
                [
                    'label' => 'Herramientas',
                    'items' => [
                        ['label' => 'Sucursales', 'url' => ['/sucursalx']],
                        ['label' => 'Almacenes', 'url' => ['/almacenes']],
                        ['label' => 'Equipos | Moviles', 'url' => ['/equipox']],
                    ],
                ],
                [
                    'label' => 'Usuarios',
                    'items' => [
                        ['label' => 'Usuarios (Persona) ', 'url' => ['/usuariopersona']],
                        ['label' => 'Usuarios y accesos', 'url' => ['/user']],
                        ['label' => 'Roles', 'url' => ['/rolex']],
                    ],
                ],
                [
                    'label' => 'Documentos',
                    'items' => [
                        ['label' => 'Documentos', 'url' => ['/cabeceradocumentos']],
                        ['label' => 'Cuerpo del documento', 'url' => ['/detalledocumentos']],
                        ['label' => 'Pagos y cobros', 'url' => ['/pagos']],
                    ],
                ],
            ];
            if (!Yii::$app->user->isGuest) {
                $menuItems[] = '<li>'
                        . Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                                'Salir (' . Yii::$app->user->identity->username . ')',
                                ['class' => 'btn btn-link logout']
                        )
                        . Html::endForm()
                        . '</li>'
                        . '<li>'
                        . '<a href="#" onclick="abrirCambio();">Tipo de cambio</a>'
                        . '</li>'
                        . '<li>'
                        . '<a href="#" onclick="abrirSincronizar();">Sincronizar (Middleware)</a>'
                        /* . Html::beginForm(['/site/sincronizacion'], 'post')
                          . Html::submitButton(
                          'Sincronizar (Middleware)',
                          ['class' => 'btn btn-link logout','id'=>'btn-sinc']
                          )
                          . Html::endForm() */
                        . '</li>';
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items' => $menuItems,
                ]);
            }
            NavBar::end();
            ?>
            <div class="container">
                <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
                <?= $content ?>
            </div>
        </div>
        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; <?= Html::encode('Xmobile - Middleware') ?> <?= date('Y') ?></p>
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>