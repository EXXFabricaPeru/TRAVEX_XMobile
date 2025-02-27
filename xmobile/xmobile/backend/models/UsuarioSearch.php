<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Usuario;

/**
 * UsuarioSearch represents the model behind the search form of `backend\models\Usuario`.
 */
class UsuarioSearch extends Usuario
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idUsuario', 'idPersona', 'estadoUsuario'], 'integer'],
            [['nombreUsuario', 'claveUsuario', 'fechaUMUsuario', 'plataformaUsuario', 'plataformaPlataforma', 'plataformaEmei'], 'safe'],
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
        $query = Usuario::find();

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
            'idUsuario' => $this->idUsuario,
            'idPersona' => $this->idPersona,
            'estadoUsuario' => $this->estadoUsuario,
            'fechaUMUsuario' => $this->fechaUMUsuario,
        ]);

        $query->andFilterWhere(['like', 'nombreUsuario', $this->nombreUsuario])
            ->andFilterWhere(['like', 'claveUsuario', $this->claveUsuario])
            ->andFilterWhere(['like', 'plataformaUsuario', $this->plataformaUsuario])
            ->andFilterWhere(['like', 'plataformaPlataforma', $this->plataformaPlataforma])
            ->andFilterWhere(['like', 'plataformaEmei', $this->plataformaEmei]);

        return $dataProvider;
    }
}
