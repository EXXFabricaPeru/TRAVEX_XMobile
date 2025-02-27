<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Bonificacionesca;

/**
 * BonificacionescaSearch represents the model behind the search form of `app\models\Bonificacionesca`.
 */
class BonificacionescaSearch extends Bonificacionesca
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'U_cantidadbonificacion', 'U_reglacantidad', 'U_bonificacioncantidad','U_limitemaxregalo','idTerritorio','idUsuario'], 'integer'],
            [['Code', 'Name', 'U_tipo', 'U_cliente', 'U_fecha', 'U_fecha_inicio', 'U_fecha_fin', 'U_estado', 'U_observacion', 'U_reglatipo', 'U_reglaunidad', 'U_bonificaciontipo', 'U_bonificacionunidad','U_reglabonificacion', 'territorio','usuario'], 'safe'],
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
		//$departamento=Yii::$app->session->get('DEPARTAMENTO');
		$departamento=$_SESSION['DEPARTAMENTO'];
        $fechaFiltro= $_GET['fecha'];

		if($departamento!='Todos'){
            if(isset($_GET['estado'])){
                 if($_GET['estado']=='1'){
                    $query = Bonificacionesca::find()->Where("territorio='".$departamento."' and U_fecha_fin >='".$fechaFiltro."'")->orderby('id desc');
                }
                else if($_GET['estado']=='2'){
                    $query = Bonificacionesca::find()->Where("territorio='".$departamento."' and U_fecha_fin < '".$fechaFiltro."'")->orderby('id desc');
                }
                else{
                    $query = Bonificacionesca::find()->Where("territorio='".$departamento."'")->orderby('id desc');
                }
            }
            else{
                $query = Bonificacionesca::find()->Where("territorio='".$departamento."' and U_fecha_fin >= curdate()")->orderby('id desc'); 
            }
           
		}
		else{
            if(isset($_GET['estado'])){
                if($_GET['estado']=='1'){
                    $query = Bonificacionesca::find()->Where(" U_fecha_fin >='".$fechaFiltro."'")->orderby('id desc');
                }
                else if($_GET['estado']=='2'){
                    $query = Bonificacionesca::find()->Where(" U_fecha_fin < '".$fechaFiltro."'")->orderby('id desc');
                }
                else{
                    $query = Bonificacionesca::find()->orderby('id desc');
                } 
            }
            else{
                $query = Bonificacionesca::find()->Where(" U_fecha_fin >= curdate()")->orderby('id desc');   
            }
			
		}

       
       

      
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>['pageSize'=>10]
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
            'U_fecha' => $this->U_fecha,
            'U_fecha_inicio' => $this->U_fecha_inicio,
            'U_fecha_fin' => $this->U_fecha_fin,
            'U_cantidadbonificacion' => $this->U_cantidadbonificacion,
            'U_reglacantidad' => $this->U_reglacantidad,
            'U_bonificacioncantidad' => $this->U_bonificacioncantidad,
			'territorio' => $this->territorio,
			'usuario' => $this->usuario,
        ]);

        $query->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'U_tipo', $this->U_tipo])
            ->andFilterWhere(['like', 'U_cliente', $this->U_cliente])
            ->andFilterWhere(['like', 'U_estado', $this->U_estado])
            ->andFilterWhere(['like', 'U_limitemaxregalo', $this->U_limitemaxregalo])
            ->andFilterWhere(['like', 'U_observacion', $this->U_observacion])
            ->andFilterWhere(['like', 'U_reglatipo', $this->U_reglatipo])
            ->andFilterWhere(['like', 'U_reglaunidad', $this->U_reglaunidad])
            ->andFilterWhere(['like', 'U_bonificaciontipo', $this->U_bonificaciontipo])
            ->andFilterWhere(['like', 'U_reglabonificacion', $this->U_reglabonificacion])
			->andFilterWhere(['like', 'territorio', $this->territorio])
			->andFilterWhere(['like', 'usuario', $this->usuario])
            ->andFilterWhere(['like', 'U_bonificacionunidad', $this->U_bonificacionunidad]);

        return $dataProvider;
    }
}
