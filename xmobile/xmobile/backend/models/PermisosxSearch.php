<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Permisosx;

/**
 * PermisosxSearch represents the model behind the search form of `backend\models\Permisosx`.
 */
class PermisosxSearch extends Permisosx
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'rolexId', 'accionesId'], 'integer'],
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
        $query = Permisosx::find();

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
            'rolexId' => $this->rolexId,
            'accionesId' => $this->accionesId,
        ]);

        return $dataProvider;
    }
}
