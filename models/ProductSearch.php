<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    public $category_name;
    public $supplier_name;

    public function rules()
    {
        return [
            [['id', 'category_id', 'supplier_id', 'stock_qty', 'reorder_level'], 'integer'],
            [['sku', 'name', 'status', 'category_name', 'supplier_name'], 'safe'],
            [['unit_price'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Product::find()->joinWith(['category', 'supplier']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'attributes' => [
                    'id', 'sku', 'name', 'unit_price', 'stock_qty', 'status',
                    'category_name' => [
                        'asc' => ['category.name' => SORT_ASC],
                        'desc' => ['category.name' => SORT_DESC],
                    ],
                    'supplier_name' => [
                        'asc' => ['supplier.name' => SORT_ASC],
                        'desc' => ['supplier.name' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'product.id' => $this->id,
            'product.category_id' => $this->category_id,
            'product.supplier_id' => $this->supplier_id,
            'product.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'product.sku', $this->sku])
              ->andFilterWhere(['like', 'product.name', $this->name])
              ->andFilterWhere(['like', 'category.name', $this->category_name])
              ->andFilterWhere(['like', 'supplier.name', $this->supplier_name]);

        return $dataProvider;
    }
}
