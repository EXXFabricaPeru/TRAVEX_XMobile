<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Motivoanulacion;
use Yii;

/**
 * MotivoanulacionSearch represents the model behind the search form of `backend\models\Motivoanulacion`.
 */
class MotivoanulacionSearch extends Motivoanulacion {
     
    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'User', 'Status'], 'integer'],
            [['Code', 'Name', 'U_TipoAnulacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Motivoanulacion::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'query' => $query->andFilterWhere(['equipoId' => Yii::$app->session->get('IDEQUIPO')]),
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
            'Code' => $this->Code,
            'Name' => $this->Name,
            'U_TipoAnulacion' => $this->U_TipoAnulacion
        ]);

        $query->andFilterWhere(['like', 'Code', $this->Code])
                ->andFilterWhere(['like', 'Name', $this->Name])
                ->andFilterWhere(['like', 'U_TipoAnulacion', $this->U_TipoAnulacion]);

        return $dataProvider;
    }
}