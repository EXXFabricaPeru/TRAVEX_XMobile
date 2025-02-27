<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "configuracionesgenerales".
 *
 * @property int $id
 * @property string $precio Configuración de Precio.
 * @property string $bonificacion Configuración de bonificación.
 * @property string $grupoproductos Configuración de grupo de productos.
 * @property string $grupoclientes Configuración de grupo de clientes.
 * @property string $docificacion Configuración de dosificación.
 */
class Configuracionesgenerales extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'configuracionesgenerales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['precio', 'bonificacion', 'grupoproductos', 'grupoclientes', 'docificacion', 'empresa', 'code'], 'required'],
            [['precio', 'bonificacion', 'grupoproductos', 'grupoclientes', 'docificacion', 'empresa', 'code'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'precio' => Yii::t('app', 'Configuración de Precio  '),
            'bonificacion' => Yii::t('app', 'Configuración de bonificación '),
            'grupoproductos' => Yii::t('app', 'Configuración de grupo de productos. '),
            'grupoclientes' => Yii::t('app', 'Configuración de grupo de clientes.'),
            'docificacion' => Yii::t('app', 'Configuración de dosificación '),
            'empresa' => Yii::t('app', 'Nombre de la empresa '),
            'code' => Yii::t('app', 'Codigo'),
        ];
    }

    public function actionappx() {
        $sql = 'CREATE TABLE configuracionesgenerales(
                    id SERIAL PRIMARY KEY,
                    precio ENUM("SI","NO") NOT NULL COMMENT "Configuración de Precio.",
                    bonificacion ENUM("SI","NO") NOT NULL COMMENT "Configuración de bonificación.",
                    grupoproductos ENUM("SI","NO") NOT NULL COMMENT "Configuración de grupo de productos.",
                    grupoclientes ENUM("SI","NO") NOT NULL COMMENT "Configuración de grupo de clientes.",
                    docificacion ENUM("SI","NO") NOT NULL COMMENT "Configuración de dosificación.",
                    empresa VARCHAR(150) NOT NULL COMMENT "Nombre de la entidad.",
                    code VARCHAR(150) NOT NULL COMMENT "Codigo."
              )';
        return Yii::$app->db->createCommand($sql)->execute();
    }

}
