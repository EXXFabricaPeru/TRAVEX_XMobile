<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "listapreciosuser".
 *
 * @property int $user_id
 * @property int $idlistaprecios
 *
 * @property User $user
 */
class Listapreciosuser extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'listapreciosuser';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'idlistaprecios'], 'required'],
            [['user_id', 'idlistaprecios'], 'integer'],
            [['user_id', 'idlistaprecios'], 'unique', 'targetAttribute' => ['user_id', 'idlistaprecios']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'user_id' => 'User ID',
            'idlistaprecios' => 'Idlistaprecios',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}
