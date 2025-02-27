<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Equipox;

/**
 * EquipoxSearch represents the model behind the search form of `backend\models\Equipox`.
 */
class EquipoxSearch extends Equipox
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sucursalxId'], 'integer'],
            [['equipo', 'uuid', 'keyid', 'plataforma', 'estado', 'registrado', 'version'], 'safe'],
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
        $query = Equipox::find();

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
            'registrado' => $this->registrado,
            'sucursalxId' => $this->sucursalxId,
        ]);

        $query->andFilterWhere(['like', 'equipo', $this->equipo])
            ->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'keyid', $this->keyid])
            ->andFilterWhere(['like', 'plataforma', $this->plataforma])
            ->andFilterWhere(['like', 'estado', $this->estado])
            ->andFilterWhere(['like', 'version', $this->version]);

        return $dataProvider;
    }
}
