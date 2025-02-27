<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = '';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-index">
    <style>
        .loader {
            position: fixed;
            left: 48%px;
            top: 0px;
           
            z-index: 9999;
            background: url('103.gif') 50% 50% no-repeat rgb(249,249,249);
            opacity: .8;
        }
        .loaderText {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            opacity: .8;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .hide-loader{
            display:none;
        }
  
    </style>
    <div id="loader" class="loader" style="display:none">
    </div>
    <div class="jumbotron">
        <h1>Xmobile - Middleware</h1>
    </div>
</div>