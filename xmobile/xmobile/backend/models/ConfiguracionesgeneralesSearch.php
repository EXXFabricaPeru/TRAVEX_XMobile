<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Configuracionesgenerales;

/**
 * ConfiguracionesgeneralesSearch represents the model behind the search form of `backend\models\Configuracionesgenerales`.
 */
class ConfiguracionesgeneralesSearch extends Configuracionesgenerales
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['precio', 'bonificacion', 'grupoproductos', 'grupoclientes', 'docificacion'], 'safe'],
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
        $query = Configuracionesgenerales::find();

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
        ]);

        $query->andFilterWhere(['like', 'precio', $this->precio])
            ->andFilterWhere(['like', 'bonificacion', $this->bonificacion])
            ->andFilterWhere(['like', 'grupoproductos', $this->grupoproductos])
            ->andFilterWhere(['like', 'grupoclientes', $this->grupoclientes])
            ->andFilterWhere(['like', 'docificacion', $this->docificacion]);

        return $dataProvider;
    }
}
