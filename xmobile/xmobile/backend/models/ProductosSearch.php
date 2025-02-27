<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Productos;

/**
 * ProductosSearch represents the model behind the search form of `backend\models\Productos`.
 */
class ProductosSearch extends Productos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'SerialNum', 'ManageSerialNumbers', 'ManageBatchNumbers', 'ForceSelectionOfSerialNumber'], 'integer'],
            [['ItemCode', 'ItemName', 'ItemsGroupCode', 'ForeignName', 'CustomsGroupCode', 'BarCode', 'PurchaseItem', 'SalesItem', 'InventoryItem', 'UserText', 'QuantityOnStock', 'QuantityOrderedFromVendors', 'QuantityOrderedByCustomers', 'SalesUnit', 'SalesUnitVolume', 'PurchaseUnit', 'DefaultWarehouse', 'ManageStockByWarehouse', 'Series', 'UoMGroupEntry', 'DefaultSalesUoMEntry', 'User', 'Status', 'DateUpdate', 'Manufacturer', 'NoDiscounts', 'created_at', 'updated_at', 'combo', 'producto_std1', 'producto_std2', 'producto_std3', 'producto_std4', 'producto_std5', 'producto_std6', 'producto_std7', 'producto_std8', 'producto_std9', 'producto_std10'], 'safe'],
            [['SalesUnitLength', 'SalesUnitWidth', 'SalesUnitHeight'], 'number'],
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
        $query = Productos::find();

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
            'SerialNum' => $this->SerialNum,
            'ManageSerialNumbers' => $this->ManageSerialNumbers,
            'ManageBatchNumbers' => $this->ManageBatchNumbers,
            'SalesUnitLength' => $this->SalesUnitLength,
            'SalesUnitWidth' => $this->SalesUnitWidth,
            'SalesUnitHeight' => $this->SalesUnitHeight,
            'ForceSelectionOfSerialNumber' => $this->ForceSelectionOfSerialNumber,
            'DateUpdate' => $this->DateUpdate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'ItemCode', $this->ItemCode])
            ->andFilterWhere(['like', 'ItemName', $this->ItemName])
            ->andFilterWhere(['like', 'ItemsGroupCode', $this->ItemsGroupCode])
            ->andFilterWhere(['like', 'ForeignName', $this->ForeignName])
            ->andFilterWhere(['like', 'CustomsGroupCode', $this->CustomsGroupCode])
            ->andFilterWhere(['like', 'BarCode', $this->BarCode])
            ->andFilterWhere(['like', 'PurchaseItem', $this->PurchaseItem])
            ->andFilterWhere(['like', 'SalesItem', $this->SalesItem])
            ->andFilterWhere(['like', 'InventoryItem', $this->InventoryItem])
            ->andFilterWhere(['like', 'UserText', $this->UserText])
            ->andFilterWhere(['like', 'QuantityOnStock', $this->QuantityOnStock])
            ->andFilterWhere(['like', 'QuantityOrderedFromVendors', $this->QuantityOrderedFromVendors])
            ->andFilterWhere(['like', 'QuantityOrderedByCustomers', $this->QuantityOrderedByCustomers])
            ->andFilterWhere(['like', 'SalesUnit', $this->SalesUnit])
            ->andFilterWhere(['like', 'SalesUnitVolume', $this->SalesUnitVolume])
            ->andFilterWhere(['like', 'PurchaseUnit', $this->PurchaseUnit])
            ->andFilterWhere(['like', 'DefaultWarehouse', $this->DefaultWarehouse])
            ->andFilterWhere(['like', 'ManageStockByWarehouse', $this->ManageStockByWarehouse])
            ->andFilterWhere(['like', 'Series', $this->Series])
            ->andFilterWhere(['like', 'UoMGroupEntry', $this->UoMGroupEntry])
            ->andFilterWhere(['like', 'DefaultSalesUoMEntry', $this->DefaultSalesUoMEntry])
            ->andFilterWhere(['like', 'User', $this->User])
            ->andFilterWhere(['like', 'Status', $this->Status])
            ->andFilterWhere(['like', 'Manufacturer', $this->Manufacturer])
            ->andFilterWhere(['like', 'NoDiscounts', $this->NoDiscounts])
            ->andFilterWhere(['like', 'combo', $this->combo])
            ->andFilterWhere(['like', 'producto_std1', $this->producto_std1])
            ->andFilterWhere(['like', 'producto_std2', $this->producto_std2])
            ->andFilterWhere(['like', 'producto_std3', $this->producto_std3])
            ->andFilterWhere(['like', 'producto_std4', $this->producto_std4])
            ->andFilterWhere(['like', 'producto_std5', $this->producto_std5])
            ->andFilterWhere(['like', 'producto_std6', $this->producto_std6])
            ->andFilterWhere(['like', 'producto_std7', $this->producto_std7])
            ->andFilterWhere(['like', 'producto_std8', $this->producto_std8])
            ->andFilterWhere(['like', 'producto_std9', $this->producto_std9])
            ->andFilterWhere(['like', 'producto_std10', $this->producto_std10]);

        return $dataProvider;
    }
}
