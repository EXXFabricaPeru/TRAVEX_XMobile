<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Anulaciondocmovil;

/**
 * AnulaciondocmovilSearch represents the model behind the search form of `backend\models\Anulaciondocmovil`.
 */
class AnulaciondocmovilSearch extends Anulaciondocmovil
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'estado', 'idUser'], 'integer'],
            [['usuario','fechaRegistro', 'docDate', 'docType', 'docEntry', 'motivoAnulacion','docNum'], 'safe'],
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
        $query = Anulaciondocmovil::find();

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
            'fechaRegistro' => $this->fechaRegistro,
            'usuario' => $this->usuario,
            'docDate' => $this->docDate,
            'estado' => $this->estado,
            'idUser' => $this->idUser,
            'docNum' => $this->docNum,
        ]);

        $query->andFilterWhere(['like', 'docType', $this->docType])
            ->andFilterWhere(['like', 'docEntry', $this->docEntry])
            ->andFilterWhere(['like', 'motivoAnulacion', $this->motivoAnulacion]);

        return $dataProvider;
    }
}
