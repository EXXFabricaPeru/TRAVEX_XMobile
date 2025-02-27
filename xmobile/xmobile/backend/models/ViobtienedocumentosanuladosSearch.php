<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Viobtienedocumentosanulados;

/**
 * ViobtienedocumentosanuladosSearch represents the model behind the search form of `backend\models\Viobtienedocumentosanulados`.
 */
class ViobtienedocumentosanuladosSearch extends Viobtienedocumentosanulados
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechaRegistro', 'docDate', 'docEntry', 'docType', 'docNum', 'motivoAnulacion','motivoAnulacionComentario', 'origen', 'usuario'], 'safe'],
            [['estado', 'idUser'], 'integer'],
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
        $query = Viobtienedocumentosanulados::find();

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
            'fechaRegistro' => $this->fechaRegistro,
            'docDate' => $this->docDate,
            'estado' => $this->estado,
            'idUser' => $this->idUser,
        ]);

        $query->andFilterWhere(['like', 'docEntry', $this->docEntry])
            ->andFilterWhere(['like', 'docType', $this->docType])
            ->andFilterWhere(['like', 'docNum', $this->docNum])
            ->andFilterWhere(['like', 'motivoAnulacion', $this->motivoAnulacion])
            ->andFilterWhere(['like', 'motivoAnulacionComentario', $this->motivoAnulacionComentario])
            ->andFilterWhere(['like', 'origen', $this->origen])
            ->andFilterWhere(['like', 'usuario', $this->usuario]);

        return $dataProvider;
    }
}
