<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Numeracion;

/**
 * NumeracionSearch represents the model behind the search form of `backend\models\Numeracion`.
 */
class NumeracionSearch extends Numeracion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'numcli', 'numdof', 'numdoe', 'numdfa', 'numdop', 'numgp', 'numgpa', 'numccaja', 'iduser'], 'integer'],
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
        $query = Numeracion::find();

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
            'numcli' => $this->numcli,
            'numdof' => $this->numdof,
            'numdoe' => $this->numdoe,
            'numdfa' => $this->numdfa,
            'numdop' => $this->numdop,
            'numgp' => $this->numgp,
            'numgpa' => $this->numgpa,
            'numccaja' => $this->numccaja,
            'iduser' => $this->iduser,
        ]);

        return $dataProvider;
    }
}
