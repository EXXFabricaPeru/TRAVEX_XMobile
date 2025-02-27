<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Configuracion;

/**
 * ConfiguracionSearch represents the model behind the search form of `backend\models\Configuracion`.
 */
class ConfiguracionSearch extends Configuracion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'valor', 'estado'], 'integer'],
            [['parametro', 'especificacion', 'valor2', 'valor3', 'valor4'], 'safe'],
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
        $query = Configuracion::find();

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
            'valor' => $this->valor,
            'estado' => $this->estado,
            'visible' => $this->visible
        ]);

        $query->andFilterWhere(['like', 'parametro', $this->parametro])
            ->andFilterWhere(['like', 'especificacion', $this->especificacion])
            ->andFilterWhere(['like', 'valor2', $this->valor2])
            ->andFilterWhere(['like', 'valor3', $this->valor3])
            ->andFilterWhere(['like', 'valor4', $this->valor4]);

        return $dataProvider;
    }
}
