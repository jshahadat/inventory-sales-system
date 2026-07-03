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
 * @property Product[] $products
 */
class Supplier extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%supplier}}';
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
            'name' => 'Supplier Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
        ];
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['supplier_id' => 'id']);
    }
}
