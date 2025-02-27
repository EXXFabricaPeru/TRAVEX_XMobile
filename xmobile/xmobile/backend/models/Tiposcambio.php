<?php

namespace backend\models;

use api\traits\Respuestas;
use backend\helpers\Common;
use backend\helpers\ConexionApi;
use Yii;

/**
 * This is the model class for table "tiposcambio".
 *
 * @property int $id
 * @property int $ExchangeRateFrom
 * @property int $ExchangeRateTo
 * @property string $ExchangeRateDate
 * @property string $ExchangeRate
 * @property string $User
 * @property string $Status
 * @property string $DateUpdate
 *
 * @property Monedas $exchangeRateFrom
 * @property Monedas $exchangeRateTo
 */
class Tiposcambio extends \yii\db\ActiveRecord
{
    use Respuestas;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tiposcambio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'ExchangeRateFrom', 'ExchangeRateTo'], 'integer'],
            [['ExchangeRateDate', 'DateUpdate'], 'safe'],
            [['ExchangeRate', 'User', 'Status'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['ExchangeRateFrom'], 'exist', 'skipOnError' => true, 'targetClass' => Monedas::className(), 'targetAttribute' => ['ExchangeRateFrom' => 'id']],
            [['ExchangeRateTo'], 'exist', 'skipOnError' => true, 'targetClass' => Monedas::className(), 'targetAttribute' => ['ExchangeRateTo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ExchangeRateFrom' => 'Exchange Rate From',
            'ExchangeRateTo' => 'Exchange Rate To',
            'ExchangeRateDate' => 'Exchange Rate Date',
            'ExchangeRate' => 'Exchange Rate',
            'User' => 'User',
            'Status' => 'Status',
            'DateUpdate' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExchangeRateFrom()
    {
        return $this->hasOne(Monedas::className(), ['id' => 'ExchangeRateFrom']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExchangeRateTo()
    {
        return $this->hasOne(Monedas::className(), ['id' => 'ExchangeRateTo']);
    }

    public function findOneByExchengeFromAndExchangeTo($exchangeFrom, $exchangeTo){
        $oTipoCambio = null;
        if (Yii::$app->session->get('offline')) {
            $oTipoCambio = Tiposcambio::find()
                ->where(['like', 'ExchangeRateFrom', "{$exchangeFrom}", false])
                ->where(['like', 'ExchangeRateTo', "{$exchangeTo}", false])
                ->one();
        } else {
            $data = $this->response(ConexionApi::apiPost(['exchangeFrom' => $exchangeFrom, 'exchangeTo' => $exchangeTo], "/tipocambio/findonebyexchangefromandexchangeto"));
            if($data){
                $oTipoCambio = new Tiposcambio();
                $oTipoCambio->setAttributes(Common::std2array($data));
            }
        }
        return $oTipoCambio;
    }
}
