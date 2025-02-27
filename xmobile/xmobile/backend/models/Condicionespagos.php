<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "condicionespagos".
 *
 * @property string $GroupNumber
 * @property string $PaymentTermsGroupName
 * @property string $StartFrom
 * @property int $NumberOfAdditionalMonths
 * @property int $NumberOfAdditionalDays
 * @property string $CreditLimit
 * @property string $GeneralDiscount
 * @property string $InterestOnArrears
 * @property int $PriceListNo
 * @property string $LoadLimit
 * @property string $OpenReceipt
 * @property string $DiscountCode
 * @property string $DunningCode
 * @property string $BaselineDate
 * @property string $NumberOfInstallments
 * @property int $NumberOfToleranceDays
 * @property string $U_UsaLc
 * @property int $User
 * @property string $DateUpdated
 * @property int $Status
 */
class Condicionespagos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'condicionespagos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['GroupNumber'], 'required'],
            [['NumberOfAdditionalMonths', 'NumberOfAdditionalDays', 'PriceListNo', 'NumberOfToleranceDays', 'User', 'Status'], 'integer'],
            [['CreditLimit', 'GeneralDiscount', 'InterestOnArrears', 'LoadLimit'], 'number'],
            [['DateUpdated'], 'safe'],
            [['GroupNumber', 'PaymentTermsGroupName', 'StartFrom'], 'string', 'max' => 255],
            [['OpenReceipt'], 'string', 'max' => 10],
            [['DiscountCode', 'DunningCode', 'U_UsaLc'], 'string', 'max' => 5],
            [['BaselineDate', 'NumberOfInstallments'], 'string', 'max' => 20],
            [['GroupNumber'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'GroupNumber' => 'Group Number',
            'PaymentTermsGroupName' => 'Payment Terms Group Name',
            'StartFrom' => 'Start From',
            'NumberOfAdditionalMonths' => 'Number Of Additional Months',
            'NumberOfAdditionalDays' => 'Number Of Additional Days',
            'CreditLimit' => 'Credit Limit',
            'GeneralDiscount' => 'General Discount',
            'InterestOnArrears' => 'Interest On Arrears',
            'PriceListNo' => 'Price List No',
            'LoadLimit' => 'Load Limit',
            'OpenReceipt' => 'Open Receipt',
            'DiscountCode' => 'Discount Code',
            'DunningCode' => 'Dunning Code',
            'BaselineDate' => 'Baseline Date',
            'NumberOfInstallments' => 'Number Of Installments',
            'NumberOfToleranceDays' => 'Number Of Tolerance Days',
            'U_UsaLc' => 'U Usa Lc',
            'User' => 'User',
            'DateUpdated' => 'Date Updated',
            'Status' => 'Status',
        ];
    }
}
