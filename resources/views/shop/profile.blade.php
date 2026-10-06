<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลสมาชิก - {{ $customer->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('shop.index') }}" class="text-xl font-bold text-blue-600">🛒 POS SHOP</a>
            <form action="{{ route('customer.logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-red-500 hover:underline">ออกจากระบบ</button>
            </form>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8 space-y-6">
        <!-- บัตรข้อมูลสมาชิก -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <p class="text-xs text-blue-200">บัตรสมาชิกร้านค้า</p>
                <h1 class="text-2xl font-bold mt-1">{{ $customer->name }}</h1>
                <p class="text-sm text-blue-100 font-mono mt-1">📞 {{ $customer->phone }}</p>
            </div>
            <div class="bg-white/20 backdrop-blur-md px-6 py-4 rounded-xl text-center">
                <p class="text-xs uppercase text-blue-100">แต้มสะสมทั้งหมด</p>
                <p class="text-3xl font-extrabold mt-1">{{ $customer->points }} <span class="text-sm font-normal">แต้ม</span></p>
            </div>
        </div>

        <!-- ประวัติการซื้อสินค้า -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">ประวัติการซื้อสินค้าของคุณ</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 text-xs">
                        <tr>
                            <th class="py-2 px-3">วันที่</th>
                            <th class="py-2 px-3">เลขที่ใบเสร็จ</th>
                            <th class="py-2 px-3 text-right">ยอดรวม</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customer->orders as $order)
                        <tr>
                            <td class="py-2 px-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-3 font-mono text-blue-600">{{ $order->invoice_no }}</td>
                            <td class="py-2 px-3 text-right font-bold">฿{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-gray-400">ยังไม่มีประวัติการซื้อสินค้า</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('shop.index') }}" class="text-sm text-blue-600 hover:underline">← กลับไปเลือกซื้อสินค้า</a>
        </div>
    </main>

</body>
</html>