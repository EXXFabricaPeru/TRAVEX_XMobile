<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "userequipox".
 *
 * @property int $id
 * @property int $userId
 * @property int $equipoxId
 * @property string|null $tiempo
 *
 * @property User $user
 */
class Userequipox extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'userequipox';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            //[['userId', 'equipoxId'], 'required'],
            [['userId', 'equipoxId'], 'integer'],
            [['tiempo'], 'safe'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'userId' => 'Usuario',
            'equipoxId' => 'Equipo',
            'tiempo' => 'Ultima Modificación',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

}
