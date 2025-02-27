<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "series".
 *
 * @property int $id
 * @property string $Document
 * @property string $DocumentSubType
 * @property int $InitialNumber
 * @property int $LastNumber
 * @property int $NextNumber
 * @property string $Prefix
 * @property string $Suffix
 * @property string $Remarks
 * @property string $GroupCode
 * @property string $Locked
 * @property string $PeriodIndicator
 * @property string $Name
 * @property int $Series
 * @property string $IsDigitalSeries
 * @property int $DigitNumber
 * @property string $SeriesType
 * @property string $IsManual
 * @property string $BPLID
 * @property string $ATDocumentType
 * @property string $IsElectronicCommEnabled
 * @property string $CostAccountOnly
 */
class Series extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'series';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['InitialNumber', 'LastNumber', 'NextNumber', 'Series', 'DigitNumber'], 'integer'],
            [['Document', 'DocumentSubType', 'IsManual'], 'string', 'max' => 10],
            [['Prefix', 'Suffix'], 'string', 'max' => 20],
            [['Remarks', 'GroupCode', 'PeriodIndicator', 'Name', 'SeriesType', 'BPLID', 'ATDocumentType'], 'string', 'max' => 255],
            [['Locked', 'IsDigitalSeries', 'IsElectronicCommEnabled', 'CostAccountOnly'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Document' => 'Document',
            'DocumentSubType' => 'Document Sub Type',
            'InitialNumber' => 'Initial Number',
            'LastNumber' => 'Last Number',
            'NextNumber' => 'Next Number',
            'Prefix' => 'Prefix',
            'Suffix' => 'Suffix',
            'Remarks' => 'Remarks',
            'GroupCode' => 'Group Code',
            'Locked' => 'Locked',
            'PeriodIndicator' => 'Period Indicator',
            'Name' => 'Name',
            'Series' => 'Series',
            'IsDigitalSeries' => 'Is Digital Series',
            'DigitNumber' => 'Digit Number',
            'SeriesType' => 'Series Type',
            'IsManual' => 'Is Manual',
            'BPLID' => 'Bplid',
            'ATDocumentType' => 'At Document Type',
            'IsElectronicCommEnabled' => 'Is Electronic Comm Enabled',
            'CostAccountOnly' => 'Cost Account Only',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
}
