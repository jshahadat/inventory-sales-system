<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $category_id
 * @property int $supplier_id
 * @property string $sku
 * @property string $name
 * @property float $unit_price
 * @property int $stock_qty
 * @property int $reorder_level
 * @property string $status
 *
 * @property Category $category
 * @property Supplier $supplier
 * @property SalesInvoiceItem[] $salesInvoiceItems
 */
class Product extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%product}}';
    }

    public function rules()
    {
        return [
            [['category_id', 'supplier_id', 'sku', 'name', 'unit_price'], 'required'],
            [['category_id', 'supplier_id', 'stock_qty', 'reorder_level'], 'integer'],
            [['unit_price'], 'number', 'min' => 0],
            [['status'], 'in', 'range' => ['active', 'inactive']],
            [['sku'], 'string', 'max' => 50],
            [['sku'], 'unique'],
            [['name'], 'string', 'max' => 150],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Supplier::class, 'targetAttribute' => ['supplier_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category',
            'supplier_id' => 'Supplier',
            'sku' => 'SKU',
            'name' => 'Product Name',
            'unit_price' => 'Unit Price',
            'stock_qty' => 'Stock Qty',
            'reorder_level' => 'Reorder Level',
            'status' => 'Status',
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id' => 'supplier_id']);
    }

    public function getSalesInvoiceItems()
    {
        return $this->hasMany(SalesInvoiceItem::class, ['product_id' => 'id']);
    }

    public function getIsLowStock()
    {
        return $this->stock_qty <= $this->reorder_level;
    }
}
