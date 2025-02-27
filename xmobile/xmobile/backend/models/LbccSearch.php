<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Lbcc;
use Yii;

/**
 * LbccSearch represents the model behind the search form of `backend\models\Lbcc`.
 */
class LbccSearch extends Lbcc {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'DocEntry', 'UserSign', 'U_ObjType', 'U_PrimerNumero', 'U_NumeroSiguiente', 'U_UltimoNumero', 'U_Series', 'U_TipoDosificacion', 'U_GrupoCliente', 'U_Actividad', 'User', 'Status', 'equipoId', 'papelId'], 'integer'],
            [['Code', 'Name', 'Canceled', 'Object', 'LogInst', 'Transfered', 'CreateDate', 'CreateTime', 'UpdateDate', 'UpdateTime', 'DataSource', 'U_NumeroAutorizacion', 'U_Estado', 'U_SeriesName', 'U_FechaLimiteEmision', 'U_LlaveDosificacion', 'U_Leyenda', 'U_Leyenda2', 'U_Sucursal', 'U_EmpleadoVentas', 'DateUpdate', 'papelId','descripcion'], 'safe'],
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
        $query = Lbcc::find();

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
            'DocEntry' => $this->DocEntry,
            'UserSign' => $this->UserSign,
            'CreateDate' => $this->CreateDate,
            'CreateTime' => $this->CreateTime,
            'UpdateDate' => $this->UpdateDate,
            'UpdateTime' => $this->UpdateTime,
            'U_ObjType' => $this->U_ObjType,
            'U_PrimerNumero' => $this->U_PrimerNumero,
            'U_NumeroSiguiente' => $this->U_NumeroSiguiente,
            'U_UltimoNumero' => $this->U_UltimoNumero,
            'U_Series' => $this->U_Series,
            'U_FechaLimiteEmision' => $this->U_FechaLimiteEmision,
            'U_TipoDosificacion' => $this->U_TipoDosificacion,
            'U_GrupoCliente' => $this->U_GrupoCliente,
            'U_Actividad' => $this->U_Actividad,
            'User' => $this->User,
            'Status' => $this->Status,
            'DateUpdate' => $this->DateUpdate,
            'equipoId' => $this->equipoId,
            'papelId' => $this->papelId,
        ]);

        $query->andFilterWhere(['like', 'Code', $this->Code])
                ->andFilterWhere(['like', 'Name', $this->Name])
                ->andFilterWhere(['like', 'Canceled', $this->Canceled])
                ->andFilterWhere(['like', 'Object', $this->Object])
                ->andFilterWhere(['like', 'LogInst', $this->LogInst])
                ->andFilterWhere(['like', 'Transfered', $this->Transfered])
                ->andFilterWhere(['like', 'DataSource', $this->DataSource])
                ->andFilterWhere(['like', 'U_NumeroAutorizacion', $this->U_NumeroAutorizacion])
                ->andFilterWhere(['like', 'U_Estado', $this->U_Estado])
                ->andFilterWhere(['like', 'U_SeriesName', $this->U_SeriesName])
                ->andFilterWhere(['like', 'U_LlaveDosificacion', $this->U_LlaveDosificacion])
                ->andFilterWhere(['like', 'U_Leyenda', $this->U_Leyenda])
                ->andFilterWhere(['like', 'U_Leyenda2', $this->U_Leyenda2])
                ->andFilterWhere(['like', 'U_Sucursal', $this->U_Sucursal])
                ->andFilterWhere(['like', 'papelId', $this->papelId])
                ->andFilterWhere(['like', 'U_EmpleadoVentas', $this->U_EmpleadoVentas]);

        return $dataProvider;
    }

}
