<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Productosprecios;

/**
 * ProductospreciosSearch represents the model behind the search form of `backend\models\Productosprecios`.
 */
class ProductospreciosSearch extends Productosprecios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'IdListaPrecios', 'IdUnidadMedida', 'User', 'Status'], 'integer'],
            [['ItemCode', 'Currency', 'DateUpdate'], 'safe'],
            [['Price'], 'number'],
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
        $query = Productosprecios::find();

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
            'IdListaPrecios' => $this->IdListaPrecios,
            'IdUnidadMedida' => $this->IdUnidadMedida,
            'Price' => $this->Price,
            'User' => $this->User,
            'Status' => $this->Status,
            'DateUpdate' => $this->DateUpdate,
        ]);

        $query->andFilterWhere(['like', 'ItemCode', $this->ItemCode])
            ->andFilterWhere(['like', 'Currency', $this->Currency]);

        return $dataProvider;
    }
}
