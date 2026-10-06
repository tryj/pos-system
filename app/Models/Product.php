<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'barcode',
        'name',
        'cost_price',
        'price',
        'stock',
        'alert_stock',
        'image', // <-- เพิ่มตรงนี้
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}