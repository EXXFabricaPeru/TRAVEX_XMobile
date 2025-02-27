<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Cuentascontables;

/**
 * CuentascontablesSearch represents the model behind the search form of `backend\models\Cuentascontables`.
 */
class CuentascontablesSearch extends Cuentascontables
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'AccountLevel', 'Status'], 'integer'],
            [['Code', 'Name', 'FatherAccountKey', 'AcctCurrency', 'FormatCode', 'User', 'DateUpdate'], 'safe'],
            [['Balance'], 'number'],
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
        $query = Cuentascontables::find();

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
            'Balance' => $this->Balance,
            'AccountLevel' => $this->AccountLevel,
            'Status' => $this->Status,
            'DateUpdate' => $this->DateUpdate,
        ]);

        $query->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'FatherAccountKey', $this->FatherAccountKey])
            ->andFilterWhere(['like', 'AcctCurrency', $this->AcctCurrency])
            ->andFilterWhere(['like', 'FormatCode', $this->FormatCode])
            ->andFilterWhere(['like', 'User', $this->User]);

        return $dataProvider;
    }
}
