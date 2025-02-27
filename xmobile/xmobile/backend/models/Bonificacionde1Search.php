<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Bonificacionde1;

/**
 * Bonificacionde1Search represents the model behind the search form of `backend\models\Bonificacionde1`.
 */
class Bonificacionde1Search extends Bonificacionde1
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['Code', 'Name', 'U_ID_bonificacion', 'U_regla'], 'safe'],
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
        $query = Bonificacionde1::find();

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

        $query->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'U_ID_bonificacion', $this->U_ID_bonificacion])
            ->andFilterWhere(['like', 'U_regla', $this->U_regla]);

        return $dataProvider;
    }

}
