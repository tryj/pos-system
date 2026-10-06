<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'invoice_no', 'user_id', 'member_id', 'subtotal',
        'discount', 'total_amount', 'cash_received', 'change_amount', 'payment_method'
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }
}
