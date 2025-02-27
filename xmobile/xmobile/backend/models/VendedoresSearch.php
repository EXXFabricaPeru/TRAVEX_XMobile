<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Vendedores;

/**
 * VendedoresSearch represents the model behind the search form of `backend\models\Vendedores`.
 */
class VendedoresSearch extends Vendedores
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'EmployeeId'], 'integer'],
            [['SalesEmployeeCode', 'SalesEmployeeName', 'User', 'Status', 'DateUpdate'], 'safe'],
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
        $query = Vendedores::find();

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
            'EmployeeId' => $this->EmployeeId,
            'DateUpdate' => $this->DateUpdate,
        ]);

        $query->andFilterWhere(['like', 'SalesEmployeeCode', $this->SalesEmployeeCode])
            ->andFilterWhere(['like', 'SalesEmployeeName', $this->SalesEmployeeName])
            ->andFilterWhere(['like', 'User', $this->User])
            ->andFilterWhere(['like', 'Status', $this->Status]);

        return $dataProvider;
    }
}
