<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $address
 *
 * @property SalesInvoice[] $salesInvoices
 */
class Customer extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%customer}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['phone'], 'string', 'max' => 30],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 128],
            [['address'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Customer Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
        ];
    }

    public function getSalesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, ['customer_id' => 'id']);
    }
}
