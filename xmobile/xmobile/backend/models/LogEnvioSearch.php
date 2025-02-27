<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\LogEnvio;

/**
 * LogEnvioSearch represents the model behind the search form of `backend\models\LogEnvio`.
 */
class LogEnvioSearch extends LogEnvio
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idlog'], 'integer'],
            [['proceso', 'envio', 'respuesta', 'fecha', 'ultimo', 'endpoint','documento'], 'safe'],
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
        $query = LogEnvio::find();

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
            'idlog' => $this->idlog,
            'fecha' => $this->fecha,
            'ultimo' => $this->ultimo,
        ]);

        $query->andFilterWhere(['like', 'proceso', $this->proceso])
            ->andFilterWhere(['like', 'envio', $this->envio])
            ->andFilterWhere(['like', 'respuesta', $this->respuesta])
            ->andFilterWhere(['like', 'endpoint', $this->endpoint])
            ->andFilterWhere(['like', 'documento', $this->documento]);

        return $dataProvider;
    }
}
