<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 ภาพรวมระบบ (POS Dashboard)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- สรุปตัวเลข KPI -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500">ยอดขายวันนี้</p>
                    <p class="text-3xl font-extrabold text-green-600 mt-2">฿{{ number_format($todaySales ?? 0, 2) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-500">จำนวนบิลวันนี้</p>
                    <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ $todayBills ?? 0 }} <span class="text-sm text-gray-400 font-normal">รายการ</span></p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <p class="text-sm font-medium text-gray-500">สินค้าต้องเติมสต็อกด่วน</p>
                    <p class="text-3xl font-extrabold text-red-600 mt-2">{{ count($lowStockProducts ?? []) }} <span class="text-sm text-gray-400 font-normal">รายการ</span></p>
                </div>
            </div>

            <!-- ตารางแสดงสินค้าใกล้หมด และ บิลล่าสุด -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">⚠️ สินค้าใกล้หมด</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                                <tr>
                                    <th class="py-2 px-3">ชื่อสินค้า</th>
                                    <th class="py-2 px-3 text-center">คงเหลือ</th>
                                    <th class="py-2 px-3 text-right">จุดเตือน</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($lowStockProducts as $item)
                                    <tr>
                                        <td class="py-2 px-3 font-medium text-gray-900">{{ $item->name }}</td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-bold">{{ $item->stock }}</span>
                                        </td>
                                        <td class="py-2 px-3 text-right">{{ $item->alert_stock }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-gray-400">สต็อกสินค้าเพียงพอทุกรายการ</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">🧾 บิลขายล่าสุด</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                                <tr>
                                    <th class="py-2 px-3">เลขที่ใบเสร็จ</th>
                                    <th class="py-2 px-3">แคชเชียร์</th>
                                    <th class="py-2 px-3 text-right">ยอดเงิน</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="py-2 px-3 font-mono text-blue-600">{{ $order->invoice_no }}</td>
                                        <td class="py-2 px-3">{{ $order->user->name ?? '-' }}</td>
                                        <td class="py-2 px-3 text-right font-bold">฿{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-gray-400">ยังไม่มีรายการขายวันนี้</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>