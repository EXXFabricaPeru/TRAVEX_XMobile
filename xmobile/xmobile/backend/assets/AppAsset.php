<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/page-styles.css',
        'js/jquery-ui.css',
        'toast/jquery.toast.css',
        'css/animate.css',
        'fontawesome/css/all.css',
        'datatables/css/dataTables.bootstrap4.min.css',
        'datatables/css/fixedHeader.bootstrap.min.css',
        'datatables/css/responsive.bootstrap4.min.css',
        //'geolocalizacion/leaflet.css',
    ];
    public $js = [
        'js/jquery-ui.js',
        'toast/jquery.toast.js',
        'scripts/index.js',
        //'datatables/js/jquery-3.5.1.js',
        'datatables/js/jquery.dataTables.min.js',
        'datatables/js/dataTables.bootstrap4.min.js',
        'datatables/js/dataTables.fixedHeader.min.js',
        'datatables/js/dataTables.responsive.min.js',
        'datatables/js/responsive.bootstrap4.min.js',
        //'geolocalizacion/leaflet.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
