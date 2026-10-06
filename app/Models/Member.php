<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    // เพิ่มตัวแปร $fillable เพื่ออนุญาตให้บันทึกข้อมูลฟิลด์เหล่านี้
    protected $fillable = [
        'name',
        'phone',
        'points',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}