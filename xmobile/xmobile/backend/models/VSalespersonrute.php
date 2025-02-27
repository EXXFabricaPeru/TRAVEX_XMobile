<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "v_salespersonrute".
 *
 * @property int|null $EmployeeID
 * @property string|null $LastName
 * @property string|null $FirstName
 * @property string|null $MiddleName
 * @property int|null $SalesPersonCode
 * @property int|null $RoleID
 * @property int|null $LineNum
 * @property int|null $TypeID
 * @property string|null $Name
 * @property string|null $Description
 */
class VSalespersonrute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_salespersonrute';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['EmployeeID', 'SalesPersonCode', 'RoleID', 'LineNum', 'TypeID'], 'integer'],
            [['LastName', 'FirstName', 'MiddleName', 'Name', 'Description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'EmployeeID' => 'Employee ID',
            'LastName' => 'Last Name',
            'FirstName' => 'First Name',
            'MiddleName' => 'Middle Name',
            'SalesPersonCode' => 'Sales Person Code',
            'RoleID' => 'Role ID',
            'LineNum' => 'Line Num',
            'TypeID' => 'Type ID',
            'Name' => 'Name',
            'Description' => 'Description',
        ];
    }
}
