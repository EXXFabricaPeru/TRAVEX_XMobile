<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<button title="Editar registro" class="btn-success btn-grid-action-edit btn-group-sm" value="<?= $model->id; ?>" >
    <i class="fas fa-edit text-info"></i> Actualizar
</button>
<?=
DetailView::widget([
    'model' => $model,
    'attributes' => [
        // 'id',
        'code',
        'empresa',
        'precio',
        'bonificacion',
        'grupoproductos',
        'grupoclientes',
        'docificacion',
    ],
])
?>