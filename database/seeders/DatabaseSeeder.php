<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ผู้ใช้งาน (Admin & Cashier)
        User::create([
            'name' => 'Admin Manager',
            'email' => 'admin@pos.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Cashier Somchai',
            'email' => 'cashier@pos.local',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        // 2. หมวดหมู่สินค้า
        $drink = Category::create(['name' => 'เครื่องดื่ม']);
        $snack = Category::create(['name' => 'ขนมขบเคี้ยว']);

        // 3. สินค้าตัวอย่าง
        Product::create([
            'category_id' => $drink->id,
            'barcode' => '8850123456789',
            'name' => 'กาแฟกระป๋องพร้อมดื่ม',
            'cost_price' => 12.00,
            'price' => 17.00,
            'stock' => 50,
            'alert_stock' => 5,
        ]);

        Product::create([
            'category_id' => $drink->id,
            'barcode' => '8850987654321',
            'name' => 'ชาเขียวรสน้ำผึ้งมะนาว',
            'cost_price' => 15.00,
            'price' => 20.00,
            'stock' => 30,
            'alert_stock' => 5,
        ]);

        Product::create([
            'category_id' => $snack->id,
            'barcode' => '8850112233445',
            'name' => 'มันฝรั่งทอดกรอบ รสสาหร่าย',
            'cost_price' => 18.00,
            'price' => 25.00,
            'stock' => 4,
            'alert_stock' => 5,
        ]);

        // 4. สมาชิกตัวอย่าง
        Member::create([
            'name' => 'คุณสมศรี รักดี',
            'phone' => '0812345678',
            'points' => 120,
        ]);
    }
}