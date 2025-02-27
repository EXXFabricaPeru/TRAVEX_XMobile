<?php

namespace backend\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Poligonocabeceraterritorio;
use backend\models\Virutasventaregion;

/**
 * PoligonocabeceraterritorioSearch represents the model behind the search form of `backend\models\Poligonocabeceraterritorio`.
 */
class PoligonocabeceraterritorioSearch extends Virutasventaregion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idVendedor', 'idUserRegister','idDia'], 'integer'],
            [['fechaSistema', 'fechaRegistro', 'dia', 'vendedor', 'tipoVendedor', 'territorio', 'poligono', 'estado', 'tipo', 'userRegister','idTerritorio', 'idPoligono','nombreRuta','U_Regional'], 'safe'],
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
        $query = Virutasventaregion::find()->orderBy(['idDia' => SORT_ASC]);
        
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
            'fechaSistema' => $this->fechaSistema,
            'fechaRegistro' => $this->fechaRegistro,
            'idVendedor' => $this->idVendedor,
            'idTerritorio' => $this->idTerritorio,
            'idPoligono' => $this->idPoligono,
            'idUserRegister' => $this->idUserRegister,
            'poligono' => $this->poligono,
            'U_Regional' => $this->U_Regional,
            
        ]);

        $query->andFilterWhere(['like', 'dia', $this->dia])
            ->andFilterWhere(['like', 'idDia', $this->idDia])
            ->andFilterWhere(['like', 'vendedor', $this->vendedor])
            ->andFilterWhere(['like', 'tipoVendedor', $this->tipoVendedor])
            ->andFilterWhere(['like', 'territorio', $this->territorio])
            ->andFilterWhere(['like', 'poligono', $this->poligono])
            ->andFilterWhere(['like', 'estado', $this->estado])
            ->andFilterWhere(['like', 'tipo', $this->tipo])
            ->andFilterWhere(['like', 'userRegister', $this->userRegister])
            ->andFilterWhere(['like', 'nombreRuta', $this->nombreRuta])
            ->andFilterWhere(['like', 'U_Regional', $this->U_Regional]);

        return $dataProvider;
    }
}
