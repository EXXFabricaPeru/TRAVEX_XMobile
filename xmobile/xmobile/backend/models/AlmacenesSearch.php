<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Almacenes;

/**
 * AlmacenesSearch represents the model behind the search form of `backend\models\Almacenes`.
 */
class AlmacenesSearch extends Almacenes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['Street', 'WarehouseCode', 'State', 'Country', 'City', 'WarehouseName', 'User', 'Status', 'DateUpdate'], 'safe'],
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
        $query = Almacenes::find();

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
            'DateUpdate' => $this->DateUpdate,
        ]);

        $query->andFilterWhere(['like', 'Street', $this->Street])
            ->andFilterWhere(['like', 'WarehouseCode', $this->WarehouseCode])
            ->andFilterWhere(['like', 'State', $this->State])
            ->andFilterWhere(['like', 'Country', $this->Country])
            ->andFilterWhere(['like', 'City', $this->City])
            ->andFilterWhere(['like', 'WarehouseName', $this->WarehouseName])
            ->andFilterWhere(['like', 'User', $this->User])
            ->andFilterWhere(['like', 'Status', $this->Status]);

        return $dataProvider;
    }
}
