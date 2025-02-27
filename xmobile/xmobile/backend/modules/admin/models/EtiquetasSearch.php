<?php

namespace backend\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\admin\models\Etiquetas;

/**
 * EtiquetasSearch represents the model behind the search form of `backend\modules\admin\models\Etiquetas`.
 */
class EtiquetasSearch extends Etiquetas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'estado', 'idlocalidad'], 'integer'],
            [['clave', 'valor', 'fecha'], 'safe'],
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
        $query = Etiquetas::find();

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
            'idlocalidad' => $this->idlocalidad,
        ]);

        $query->andFilterWhere(['like', 'clave', $this->clave])
            ->andFilterWhere(['like', 'valor', $this->valor]);

        return $dataProvider;
    }
}
