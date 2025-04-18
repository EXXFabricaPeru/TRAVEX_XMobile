<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Viuservendedor;

/**
 * UserSearch represents the model behind the search form of `backend\models\User`.
 */
class ViuservendedorSearch extends Viuservendedor
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'idPersona', 'estadoUsuario', 'reset'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'verification_token', 'access_token', 'fechaUMUsuario', 'plataformaUsuario', 'plataformaPlataforma', 'plataformaEmei','U_Regional'], 'safe'],
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
        $query = Viuservendedor::find();//->orderby('username asc');

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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'idPersona' => $this->idPersona,
            'estadoUsuario' => $this->estadoUsuario,
            'fechaUMUsuario' => $this->fechaUMUsuario,
            'reset' => $this->reset,
            'U_Regional' => $this->U_Regional,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'verification_token', $this->verification_token])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'plataformaUsuario', $this->plataformaUsuario])
            ->andFilterWhere(['like', 'plataformaPlataforma', $this->plataformaPlataforma])
            ->andFilterWhere(['like', 'plataformaEmei', $this->plataformaEmei])
            ->andFilterWhere(['like', 'U_Regional', $this->U_Regional]);

        return $dataProvider;
    }
}
