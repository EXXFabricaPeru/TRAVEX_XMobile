<?php

namespace backend\models;
use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Usuariosincronizamovil;

/**
 * UsuariosincronizamovilSearch represents the model behind the search form of `backend\models\Usuariosincronizamovil`.
 */
class UsuariosincronizamovilSearch extends Usuariosincronizamovil
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idUsuario', 'idSucursal'], 'integer'],
            [['fecha','fechahora', 'equipo', 'servicio'], 'safe'],
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
        //$query=Yii::$app->db->createCommand("select * from usuariosincronizamovil")->find();
        $query = Usuariosincronizamovil::find();

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
            'fecha' => $this->fecha,
            'fechahora' => $this->fechahora,
            'idUsuario' => $this->idUsuario,
            'idSucursal' => $this->idSucursal,
        ]);

        $query->andFilterWhere(['like', 'equipo', $this->equipo])
            ->andFilterWhere(['like', 'servicio', $this->servicio]);

        return $dataProvider;
    }
}
