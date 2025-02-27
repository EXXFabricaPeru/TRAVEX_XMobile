<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Clientes;

/**
 * ClientesSearch represents the model behind the search form of `backend\models\Clientes`.
 */
class ClientesSearch extends Clientes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'PriceListNum', 'GroupCode', 'User', 'Status', 'U_XM_DosificacionSocio', 'Territory', 'StatusSend'], 'integer'],
            [['CardCode', 'CardName', 'CardType', 'Address', 'CreditLimit', 'MaxCommitment', 'DiscountPercent', 'SalesPersonCode', 'Currency', 'County', 'Country', 'CurrentAccountBalance', 'NoDiscounts', 'PriceMode', 'FederalTaxId', 'PhoneNumber', 'ContactPerson', 'PayTermsGrpCode', 'Latitude', 'Longitude', 'DateUpdate', 'GroupName', 'DiscountRelations', 'Mobilecod', 'CardForeignName', 'Phone2', 'Cellular', 'EmailAddress', 'MailAdress', 'Properties1', 'Properties2', 'Properties3', 'Properties4', 'Properties5', 'Properties6', 'Properties7', 'FreeText', 'img', 'Industry'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Clientes::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'PriceListNum' => $this->PriceListNum,
            'GroupCode' => $this->GroupCode,
            'User' => $this->User,
            'Status' => $this->Status,
            'DateUpdate' => $this->DateUpdate,
            'U_XM_DosificacionSocio' => $this->U_XM_DosificacionSocio,
            'Territory' => $this->Territory,
            'StatusSend' => $this->StatusSend,
        ]);

        $query->andFilterWhere(['like', 'CardCode', $this->CardCode])
            ->andFilterWhere(['like', 'CardName', $this->CardName])
            ->andFilterWhere(['like', 'CardType', $this->CardType])
            ->andFilterWhere(['like', 'Address', $this->Address])
            ->andFilterWhere(['like', 'CreditLimit', $this->CreditLimit])
            ->andFilterWhere(['like', 'MaxCommitment', $this->MaxCommitment])
            ->andFilterWhere(['like', 'DiscountPercent', $this->DiscountPercent])
            ->andFilterWhere(['like', 'SalesPersonCode', $this->SalesPersonCode])
            ->andFilterWhere(['like', 'Currency', $this->Currency])
            ->andFilterWhere(['like', 'County', $this->County])
            ->andFilterWhere(['like', 'Country', $this->Country])
            ->andFilterWhere(['like', 'CurrentAccountBalance', $this->CurrentAccountBalance])
            ->andFilterWhere(['like', 'NoDiscounts', $this->NoDiscounts])
            ->andFilterWhere(['like', 'PriceMode', $this->PriceMode])
            ->andFilterWhere(['like', 'FederalTaxId', $this->FederalTaxId])
            ->andFilterWhere(['like', 'PhoneNumber', $this->PhoneNumber])
            ->andFilterWhere(['like', 'ContactPerson', $this->ContactPerson])
            ->andFilterWhere(['like', 'PayTermsGrpCode', $this->PayTermsGrpCode])
            ->andFilterWhere(['like', 'Latitude', $this->Latitude])
            ->andFilterWhere(['like', 'Longitude', $this->Longitude])
            ->andFilterWhere(['like', 'GroupName', $this->GroupName])
            ->andFilterWhere(['like', 'DiscountRelations', $this->DiscountRelations])
            ->andFilterWhere(['like', 'Mobilecod', $this->Mobilecod])
            ->andFilterWhere(['like', 'CardForeignName', $this->CardForeignName])
            ->andFilterWhere(['like', 'Phone2', $this->Phone2])
            ->andFilterWhere(['like', 'Cellular', $this->Cellular])
            ->andFilterWhere(['like', 'EmailAddress', $this->EmailAddress])
            ->andFilterWhere(['like', 'MailAdress', $this->MailAdress])
            ->andFilterWhere(['like', 'Properties1', $this->Properties1])
            ->andFilterWhere(['like', 'Properties2', $this->Properties2])
            ->andFilterWhere(['like', 'Properties3', $this->Properties3])
            ->andFilterWhere(['like', 'Properties4', $this->Properties4])
            ->andFilterWhere(['like', 'Properties5', $this->Properties5])
            ->andFilterWhere(['like', 'Properties6', $this->Properties6])
            ->andFilterWhere(['like', 'Properties7', $this->Properties7])
            ->andFilterWhere(['like', 'FreeText', $this->FreeText])
            ->andFilterWhere(['like', 'img', $this->img])
            ->andFilterWhere(['like', 'Industry', $this->Industry]);

        return $dataProvider;
    }
}
