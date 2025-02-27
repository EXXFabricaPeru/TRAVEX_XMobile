<?php

namespace backend\models\v2;

use Yii;

/**
 * This is the model class for table "anulacion".
 *
 * @property int $id
 * @property string|null $tipodocumento
 * @property string|null $iddocumento
 * @property string|null $fechahora
 * @property int|null $usuario
 * @property int|null $equipo
 */
class Layautconfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'config_layaut';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
       
            [['margin_down','margin_up','margin_left','margin_rigth','papel_width','layaut_style'], 'integer'],
            [[ 'sub_title','title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'margin_down' => 'Margen hacia abajo',
            'margin_up' => 'Margen hacia arriba',
            'margin_left' => 'Margen a la izquierda',
            'margin_rigth' => 'Margen a la derecha ',
			'papel_width' => 'Ancho del papel',
            'description' => 'Descripcion',
            'sub_title' => 'Sub titulo',
			'title' => 'titulo'
        ];
    }
}
