<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Contactos;

/**
 * ContactosSearch represents the model behind the search form of `backend\models\Contactos`.
 */
class ContactosSearch extends Contactos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'User', 'Status'], 'integer'],
            [['cardCode', 'nombre', 'direccion', 'telefono1', 'telefono2', 'celular', 'tipo', 'comentarios', 'DateUpdate', 'correo', 'titulo'], 'safe'],
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
        $query = Contactos::find();

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
            'User' => $this->User,
            'Status' => $this->Status,
            'DateUpdate' => $this->DateUpdate,
        ]);

        $query->andFilterWhere(['like', 'cardCode', $this->cardCode])
            ->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'telefono1', $this->telefono1])
            ->andFilterWhere(['like', 'telefono2', $this->telefono2])
            ->andFilterWhere(['like', 'celular', $this->celular])
            ->andFilterWhere(['like', 'tipo', $this->tipo])
            ->andFilterWhere(['like', 'comentarios', $this->comentarios])
            ->andFilterWhere(['like', 'correo', $this->correo])
            ->andFilterWhere(['like', 'titulo', $this->titulo]);

        return $dataProvider;
    }
}
