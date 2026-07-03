<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class SalesInvoiceSearch extends SalesInvoice
{
    public $customer_name;

    public function rules()
    {
        return [
            [['id', 'customer_id', 'created_by'], 'integer'],
            [['invoice_no', 'invoice_date', 'status', 'customer_name'], 'safe'],
            [['sub_total', 'discount', 'tax', 'grand_total'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = SalesInvoice::find()->joinWith(['customer']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['invoice_date' => SORT_DESC],
                'attributes' => [
                    'id', 'invoice_no', 'invoice_date', 'grand_total', 'status',
                    'customer_name' => [
                        'asc' => ['customer.name' => SORT_ASC],
                        'desc' => ['customer.name' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sales_invoice.id' => $this->id,
            'sales_invoice.status' => $this->status,
            'sales_invoice.invoice_date' => $this->invoice_date,
        ]);

        $query->andFilterWhere(['like', 'sales_invoice.invoice_no', $this->invoice_no])
              ->andFilterWhere(['like', 'customer.name', $this->customer_name]);

        return $dataProvider;
    }
}
