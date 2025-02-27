<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

?>
<div class="lbcc-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'Code',
            'Name',
            'DocEntry',
            'Canceled',
            'Object',
            'LogInst',
            'UserSign',
            'Transfered',
            'CreateDate',
            'CreateTime',
            'UpdateDate',
            'UpdateTime',
            'DataSource',
            'U_NumeroAutorizacion',
            'U_ObjType',
            'U_Estado',
            'U_PrimerNumero',
            'U_NumeroSiguiente',
            'U_UltimoNumero',
            'U_Series',
            'U_SeriesName',
            'U_FechaLimiteEmision',
            'U_LlaveDosificacion',
            'U_Leyenda',
            'U_Leyenda2',
            'U_TipoDosificacion',
            'U_Sucursal',
            'U_EmpleadoVentas',
            'U_GrupoCliente',
            'U_Actividad',
            'User',
            'Status',
            'DateUpdate',
            'equipoId',
        ],
    ]) ?>

</div>
