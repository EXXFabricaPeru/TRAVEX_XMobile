<?php
namespace backend\models;

use Yii;
/**
 * This is the model class for table "clientes".
 *
 * @property int $id
 * @property string $proceso
 * @property string $envio
 * @property string $documento
  * @property string $cabecera
 * @property string $respuesta
 *  @property string $ultimo
 *  @property string $fecha
 * 
 */
class LogIngreso extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'log_ingreso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
				[['proceso', 'envio'], 'required'],
                
                [['documento','cabecera','respuesta'], 'string'],
                //[['ultimo','fecha'], 'safe'],
                
                
                // [['DateUpdate'], 'safe'],
                // [['CardCode', 'CardName', 'CardType', 'Address', 'CreditLimit', 'MaxCommitment', 'DiscountPercent', 'SalesPersonCode', 'Currency', 'County', 'Country', 'CurrentAccountBalance', 'NoDiscounts', 'PriceMode', 'FederalTaxId', 'PhoneNumber', 'ContactPerson', 'PayTermsGrpCode', 'Latitude', 'Longitude', 'img', 'Industry'], 'string', 'max' => 255],
                // [['Currency'], 'exist', 'skipOnError' => true, 'targetClass' => Monedas::className(), 'targetAttribute' => ['Currency' => 'Code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'proceso' => 'tipo de proceso',
            'envio' => 'data de ingreso',
            'documento' => 'data del documento',
            'cabecera' => 'Cabecera documento',
            'respuesta' => 'Respuesta de la solicitud',
            'ultimo' => 'ultima fecha de modificacion',
            'fecha' => 'Fecha de registro',

            
        ];
    }


}
