<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="user-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'status',
            'created_at',
            'updated_at',
            'verification_token',
            'access_token:ntext',
            'idPersona',
            'estadoUsuario',
            'fechaUMUsuario',
            'plataformaUsuario',
            'plataformaPlataforma',
            'plataformaEmei',
            'reset',
        ],
    ]) ?>

</div>
