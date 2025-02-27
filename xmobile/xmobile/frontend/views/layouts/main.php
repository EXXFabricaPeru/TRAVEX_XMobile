<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\web\Request;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use common\widgets\Alert;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="baseUrl" content="<?= Url::base(true); ?>">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link rel="apple-touch-icon" sizes="57x57" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= yii::$app->request->BaseUrl; ?>/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?= yii::$app->request->BaseUrl; ?>/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= yii::$app->request->BaseUrl; ?>/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?= yii::$app->request->BaseUrl; ?>/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= yii::$app->request->BaseUrl; ?>/favicon-16x16.png">
        <meta name="msapplication-TileImage" content="<?= yii::$app->request->BaseUrl; ?>/ms-icon-144x144.png">
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">
            <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                // ['label' => 'Home', 'url' => ['/site/index']],
                ['label' => 'Xmobile', 'url' => str_replace('/frontend/web', '/backend/web', (new Request)->getBaseUrl())],
                    // ['label' => 'Contact', 'url' => ['/site/contact']],
            ];
            if (Yii::$app->user->isGuest) {
                //$menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                //$menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
                $menuItems[] = '<li>'
                        . Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                                'Logout (' . Yii::$app->user->identity->username . ')',
                                ['class' => 'btn btn-link logout']
                        )
                        . Html::endForm()
                        . '</li>';
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
            ?>

            <div class="container">
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

                <p class="pull-right"><?= Yii::powered() ?></p>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
