<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $invoice_id
 * @property int $product_id
 * @property int $qty
 * @property float $unit_price
 * @property float $line_total
 *
 * @property SalesInvoice $invoice
 * @property Product $product
 */
class SalesInvoiceItem extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%sales_invoice_item}}';
    }

    public function rules()
    {
        return [
            [['invoice_id', 'product_id', 'qty', 'unit_price'], 'required'],
            [['invoice_id', 'product_id', 'qty'], 'integer'],
            [['qty'], 'integer', 'min' => 1],
            [['unit_price', 'line_total'], 'number'],
            [['product_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice',
            'product_id' => 'Product',
            'qty' => 'Qty',
            'unit_price' => 'Unit Price',
            'line_total' => 'Line Total',
        ];
    }

    public function beforeSave($insert)
    {
        $this->line_total = $this->qty * $this->unit_price;
        return parent::beforeSave($insert);
    }

    public function getInvoice()
    {
        return $this->hasOne(SalesInvoice::class, ['id' => 'invoice_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }
}
