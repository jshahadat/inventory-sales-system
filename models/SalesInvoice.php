<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $invoice_no
 * @property int $customer_id
 * @property int $created_by
 * @property string $invoice_date
 * @property float $sub_total
 * @property float $discount
 * @property float $tax
 * @property float $grand_total
 * @property string $status
 *
 * @property Customer $customer
 * @property User $creator
 * @property SalesInvoiceItem[] $items
 */
class SalesInvoice extends ActiveRecord
{
    /** @var SalesInvoiceItem[] used only in the create/update form, not persisted directly */
    public $itemsData = [];

    public static function tableName()
    {
        return '{{%sales_invoice}}';
    }

    public function rules()
    {
        return [
            [['invoice_no', 'customer_id', 'created_by', 'invoice_date'], 'required'],
            [['customer_id', 'created_by'], 'integer'],
            [['invoice_date'], 'date', 'format' => 'php:Y-m-d'],
            [['sub_total', 'discount', 'tax', 'grand_total'], 'number'],
            [['status'], 'in', 'range' => ['draft', 'paid', 'due', 'cancelled']],
            [['invoice_no'], 'string', 'max' => 30],
            [['invoice_no'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_no' => 'Invoice No',
            'customer_id' => 'Customer',
            'created_by' => 'Created By',
            'invoice_date' => 'Invoice Date',
            'sub_total' => 'Sub Total',
            'discount' => 'Discount',
            'tax' => 'Tax',
            'grand_total' => 'Grand Total',
            'status' => 'Status',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getItems()
    {
        return $this->hasMany(SalesInvoiceItem::class, ['invoice_id' => 'id']);
    }

    /** Recalculate sub_total & grand_total from current items (call before save) */
    public function recalculateTotals()
    {
        $subTotal = 0;
        foreach ($this->items as $item) {
            $subTotal += $item->line_total;
        }
        $this->sub_total = $subTotal;
        $this->grand_total = $subTotal - $this->discount + $this->tax;
    }

    public static function generateInvoiceNo()
    {
        $last = self::find()->orderBy(['id' => SORT_DESC])->one();
        $next = $last ? ((int) substr($last->invoice_no, 4)) + 1 : 1;
        return 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
