<?php

namespace backend\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\admin\models\Localidad;

/**
 * LocalidadSearch represents the model behind the search form of `backend\modules\admin\models\Localidad`.
 */
class LocalidadSearch extends Localidad
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'estado'], 'integer'],
            [['nombre', 'descripcion', 'fecha'], 'safe'],
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
        $query = Localidad::find();

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
            'estado' => $this->estado,
            'fecha' => $this->fecha,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        return $dataProvider;
    }
}
