<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vi_clientes".
 *
 * @property int $id
 * @property string|null $CardCode Id BussinesPartner
 * @property string|null $CardName
 * @property string|null $CreditLimit
 * @property string|null $MaxCommitment Deuda Actual *
 * @property string|null $DiscountPercent
 * @property int|null $PriceListNum
 * @property string|null $SalesPersonCode
 * @property string|null $Currency
 * @property string|null $County Ciudad
 * @property string|null $Country Pais
 * @property string|null $CurrentAccountBalance Saldo actual
 * @property string|null $NoDiscounts
 * @property string|null $PriceMode
 * @property string|null $FederalTaxId
 * @property string|null $PhoneNumber
 * @property string|null $ContactPerson
 * @property string|null $PayTermsGrpCode
 * @property string|null $Latitude
 * @property string|null $Longitude
 * @property int|null $GroupCode
 * @property int|null $User
 * @property int|null $Status
 * @property string|null $DateUpdate
 * @property string|null $razonsocial
 * @property string|null $celular
 * @property string|null $personacontactocelular
 * @property string|null $correoelectronico
 * @property string|null $Address
 * @property string|null $lunes
 * @property string|null $martes
 * @property string|null $miercoles
 * @property string|null $jueves
 * @property string|null $viernes
 * @property string|null $sabado
 * @property string|null $domingo
 * @property string|null $comentario
 * @property string|null $tipoEmpresa
 * @property string|null $GroupName
 * @property string|null $img
 * @property string|null $CardType
 * @property int|null $U_XM_DosificacionSocio
 * @property int|null $Territory
 * @property string|null $Description
 * @property string|null $cliente_std1
 * @property string|null $cliente_std2
 * @property string|null $cliente_std3
 * @property string|null $cliente_std4
 * @property string|null $cliente_std5
 * @property string|null $cliente_std6
 * @property string|null $cliente_std7
 * @property string|null $cliente_std8
 * @property string|null $cliente_std9
 * @property string|null $cliente_std10
 * @property string|null $cndpago
 * @property string|null $cndpagoname
 */
class ViClientes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vi_clientes';
    }
    public static function primaryKey() {
        return ['id'];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'PriceListNum', 'GroupCode', 'User', 'Status', 'U_XM_DosificacionSocio', 'Territory'], 'integer'],
            [['DateUpdate'], 'safe'],
            [['CardCode', 'CardName', 'CreditLimit', 'MaxCommitment', 'DiscountPercent', 'SalesPersonCode', 'Currency', 'County', 'Country', 'CurrentAccountBalance', 'NoDiscounts', 'PriceMode', 'FederalTaxId', 'PhoneNumber', 'ContactPerson', 'PayTermsGrpCode', 'Latitude', 'Longitude', 'razonsocial', 'correoelectronico', 'Address', 'comentario', 'tipoEmpresa', 'GroupName', 'img', 'Description', 'cliente_std1', 'cliente_std2', 'cliente_std3', 'cliente_std4', 'cliente_std5', 'cliente_std6', 'cliente_std7', 'cliente_std8', 'cliente_std9', 'cliente_std10', 'cndpago', 'cndpagoname'], 'string', 'max' => 255],
            [['celular', 'personacontactocelular', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'], 'string', 'max' => 10],
            [['CardType'], 'string', 'max' => 240],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'CardCode' => 'Card Code',
            'CardName' => 'Card Name',
            'CreditLimit' => 'Credit Limit',
            'MaxCommitment' => 'Max Commitment',
            'DiscountPercent' => 'Discount Percent',
            'PriceListNum' => 'Price List Num',
            'SalesPersonCode' => 'Sales Person Code',
            'Currency' => 'Currency',
            'County' => 'County',
            'Country' => 'Country',
            'CurrentAccountBalance' => 'Current Account Balance',
            'NoDiscounts' => 'No Discounts',
            'PriceMode' => 'Price Mode',
            'FederalTaxId' => 'Federal Tax ID',
            'PhoneNumber' => 'Phone Number',
            'ContactPerson' => 'Contact Person',
            'PayTermsGrpCode' => 'Pay Terms Grp Code',
            'Latitude' => 'Latitude',
            'Longitude' => 'Longitude',
            'GroupCode' => 'Group Code',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
            'razonsocial' => 'Razonsocial',
            'celular' => 'Celular',
            'personacontactocelular' => 'Personacontactocelular',
            'correoelectronico' => 'Correoelectronico',
            'Address' => 'Address',
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miercoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sabado',
            'domingo' => 'Domingo',
            'comentario' => 'Comentario',
            'tipoEmpresa' => 'Tipo Empresa',
            'GroupName' => 'Group Name',
            'img' => 'Img',
            'CardType' => 'Card Type',
            'U_XM_DosificacionSocio' => 'U Xm Dosificacion Socio',
            'Territory' => 'Territory',
            'Description' => 'Description',
            'cliente_std1' => 'Cliente Std1',
            'cliente_std2' => 'Cliente Std2',
            'cliente_std3' => 'Cliente Std3',
            'cliente_std4' => 'Cliente Std4',
            'cliente_std5' => 'Cliente Std5',
            'cliente_std6' => 'Cliente Std6',
            'cliente_std7' => 'Cliente Std7',
            'cliente_std8' => 'Cliente Std8',
            'cliente_std9' => 'Cliente Std9',
            'cliente_std10' => 'Cliente Std10',
            'cndpago' => 'Cndpago',
            'cndpagoname' => 'Cndpagoname',
        ];
    }
}
