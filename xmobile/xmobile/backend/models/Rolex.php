<?php

namespace backend\models;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "rolex".
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property string $tipo
 * @property int|null $user
 *
 * @property Permisosx[] $permisosxes
 */
class Rolex extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'rolex';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['nombre', 'descripcion', 'tipo'], 'required'],
            [['descripcion', 'tipo'], 'string'],
            [['user'], 'integer'],
            [['nombre'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nombre' => 'Rol',
            'descripcion' => 'Descripcion',
            'tipo' => 'Rol para:',
            'user' => 'User',
        ];
    }

    /**
     * Gets query for [[Permisosxes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermisosxes() {
        return $this->hasMany(Permisosx::className(), ['rolexId' => 'id']);
    }

    public function getAllControllerActions() {
        $controllers = FileHelper::findFiles(Yii::getAlias('@backend/controllers'), ['recursive' => true]);
        $controllersList = [];
        foreach ($controllers as $controller) {
            $contents = file_get_contents($controller);
            $controllerId = Inflector::camel2id(substr(basename($controller), 0, -14));
            preg_match_all('/public function action(\w+?)\(/', $contents, $result);
            $actions = [];
            foreach ($result[1] as $action) {
                if ($action != 's')
                    $actions[] = Inflector::camel2id($action);
            }
            $controllersList[$controllerId] = $actions;
        }
        return $controllersList;
    }

}
