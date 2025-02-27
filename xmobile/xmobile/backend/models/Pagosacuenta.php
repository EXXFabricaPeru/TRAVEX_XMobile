<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pagosacuenta".
 *
 * @property int $id
 * @property int DocEntry
 * @property string DocNum
 * @property string DocType
 * @property date DocDate
 * @property date DocDueDate
 * @property string CardCode
 * @property string CardName
 * @property string CashAcct
 * @property double CashSum
 * @property double CashSumFC
 * @property double CrediSum
 * @property double CrediSumFC
 * @property string CheckAcct
 * @property double CheckSum
 * @property double CheckSumFC
 * @property string TrsftAcct
 * @property double TrsftSum
 * @property double TrsftSumFC
 * @property date TrsftDate
 * @property string DocCurr
 * @property date DocRate
 * @property double DocTotal
 * @property double DocTotalFC
 * @property string Ref1
 * @property string JrnlMemo
 * @property int TransId
 * @property int usuario
 * @property int status
 * @property datetime DateUpdate
*/
class Pagosacuenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pagosacuenta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}
