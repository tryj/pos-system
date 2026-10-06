<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// ให้หน้าแรก (/) ชี้ไปที่หน้าร้านแสดงสินค้าทั่วไป
Route::get('/', function () {
    return redirect()->route('shop.index');
});

// ==========================================
// 🛍️ ส่วนของลูกค้าทั่วไป (หน้าร้าน & สมาชิก)
// ==========================================

// 1. หน้าร้านค้าแสดงภาพสินค้าทั้งหมด
Route::get('/shop', function () {
    $products = Product::where('stock', '>', 0)->latest()->get();
    return view('shop.index', compact('products'));
})->name('shop.index');

// 2. หน้าฟอร์มสร้างบัญชี / สมัครสมาชิกของลูกค้า
Route::get('/customer/register', function () {
    return view('shop.register');
})->name('customer.register');

// 3. บันทึกข้อมูลการสร้างบัญชีสมาชิก
Route::post('/customer/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|unique:members,phone',
    ]);

    $member = Member::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'points' => 0,
    ]);

    // สมัครเสร็จให้ Login อัตโนมัติและพาไปหน้าบัตรสมาชิก
    session(['customer_id' => $member->id]);

    return redirect()->route('customer.profile')->with('success', 'สร้างบัญชีสมาชิกสำเร็จ! สามารถใช้เบอร์โทรศัพท์เพื่อสะสมแต้มหน้าร้านได้ทันที');
})->name('customer.register.store');

// 4. หน้าเข้าสู่ระบบสมาชิก
Route::get('/customer/login', function () {
    return view('shop.login');
})->name('customer.login');

// 5. ตรวจสอบการเข้าสู่ระบบด้วยเบอร์โทรศัพท์
Route::post('/customer/login', function (Request $request) {
    $request->validate([
        'phone' => 'required|string',
    ]);

    $member = Member::where('phone', $request->phone)->first();

    if (!$member) {
        return back()->withErrors(['phone' => 'ไม่พบข้อมูลสมาชิกที่ลงทะเบียนด้วยเบอร์โทรศัพท์นี้']);
    }

    session(['customer_id' => $member->id]);

    return redirect()->route('customer.profile')->with('success', 'เข้าสู่ระบบสำเร็จ');
})->name('customer.login.store');

// 6. หน้าดูโปรไฟล์ แต้มสะสม และประวัติการซื้อของสมาชิก
Route::get('/customer/profile', function () {
    if (!session()->has('customer_id')) {
        return redirect()->route('customer.login')->withErrors(['phone' => 'กรุณาเข้าสู่ระบบก่อน']);
    }

    $customer = Member::with('orders')->findOrFail(session('customer_id'));
    return view('shop.profile', compact('customer'));
})->name('customer.profile');

// 7. ออกจากระบบสมาชิก
Route::post('/customer/logout', function () {
    session()->forget('customer_id');
    return redirect()->route('shop.index')->with('success', 'ออกจากระบบเรียบร้อย');
})->name('customer.logout');

// 8. บันทึกคำสั่งซื้อออนไลน์จากหน้าเว็บ (ตัดสต็อก & บันทึกแต้ม)
Route::post('/shop/checkout', function (Request $request) {
    if (!session()->has('customer_id')) {
        return response()->json(['error' => 'กรุณาเข้าสู่ระบบสมาชิกก่อนสั่งซื้อ'], 401);
    }

    $request->validate([
        'cart' => 'required|array|min:1',
        'cart.*.id' => 'required|exists:products,id',
        'cart.*.qty' => 'required|integer|min:1',
    ]);

    $memberId = session('customer_id');

    return DB::transaction(function () use ($request, $memberId) {
        $totalAmount = 0;
        $items = [];

        foreach ($request->cart as $item) {
            $product = Product::lockForUpdate()->findOrFail($item['id']);
            if ($product->stock < $item['qty']) {
                return response()->json(['error' => "สินค้า {$product->name} มีจำนวนในสต็อกไม่เพียงพอ"], 422);
            }

            // ตัดสต็อกสินค้า
            $product->decrement('stock', $item['qty']);
            $subtotal = $product->price * $item['qty'];
            $totalAmount += $subtotal;

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'price' => $product->price,
                'subtotal' => $subtotal
            ];
        }

        // ดึงพนักงานหรือ Admin คนแรกเป็นผู้ดูแลบิล
        $defaultUserId = User::first()->id ?? 1;

        $order = Order::create([
            'invoice_no' => 'WEB-' . date('YmdHis') . '-' . rand(100, 999),
            'user_id' => $defaultUserId,
            'member_id' => $memberId,
            'subtotal' => $totalAmount,
            'discount' => 0,
            'total_amount' => $totalAmount,
            'cash_received' => $totalAmount,
            'change_amount' => 0,
            'payment_method' => 'qr_promptpay',
        ]);

        foreach ($items as $item) {
            $order->items()->create($item);
        }

        // เพิ่มแต้มสะสมให้สมาชิก (ทุก 100 บาท ได้ 1 แต้ม)
        $earnedPoints = floor($totalAmount / 100);
        if ($earnedPoints > 0) {
            Member::where('id', $memberId)->increment('points', $earnedPoints);
        }

        return response()->json([
            'success' => true,
            'message' => 'สั่งซื้อสินค้าสำเร็จ!',
            'redirect' => route('customer.profile')
        ]);
    });
})->name('shop.checkout');


// ==========================================
// 🔒 ส่วนระบบหลังร้าน & POS (พนักงานต้อง Login ก่อน)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', function () {
        $todaySales = Order::whereDate('created_at', today())->sum('total_amount');
        $todayBills = Order::whereDate('created_at', today())->count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'alert_stock')->get();
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('dashboard', compact('todaySales', 'todayBills', 'lowStockProducts', 'recentOrders'));
    })->name('dashboard');

    // Employees
    Route::resource('employees', EmployeeController::class)->only(['index', 'store', 'destroy']);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. POS Sale & Payment
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');

    // 3. Products & Stock
    Route::resource('products', ProductController::class);

    // 4. Members
    Route::resource('members', MemberController::class);

    // 5. Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';