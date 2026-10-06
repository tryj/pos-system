<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📈 รายงานยอดขาย</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- ตัวกรองช่วงวันที่ -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="text-xs font-semibold">ตั้งแต่วันที่</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">ถึงวันที่</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm flex-1">ค้นหา</button>
                        <a href="{{ route('reports.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded text-sm flex items-center justify-center">ล้างค่า</a>
                    </div>
                </form>
            </div>

            <!-- ตารางรายการบิล -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800">ประวัติการขาย</h3>
                    <div class="text-right">
                        <span class="text-xs text-gray-500">ยอดขายรวมทั้งหมดในหน้านี้:</span>
                        <span class="text-lg font-bold text-green-600 ml-2">฿{{ number_format($totalSales, 2) }}</span>
                    </div>
                </div>

                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                        <tr>
                            <th class="py-2 px-3">วัน-เวลา</th>
                            <th class="py-2 px-3">เลขที่ใบเสร็จ</th>
                            <th class="py-2 px-3">แคชเชียร์</th>
                            <th class="py-2 px-3">สมาชิก</th>
                            <th class="py-2 px-3 text-center">ช่องทาง</th>
                            <th class="py-2 px-3 text-right">ยอดเงิน</th>
                            <th class="py-2 px-3 text-center">ใบเสร็จ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($orders as $o)
                        <tr>
                            <td class="py-2 px-3 text-xs">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2 px-3 font-mono text-blue-600">{{ $o->invoice_no }}</td>
                            <td class="py-2 px-3">{{ $o->user->name }}</td>
                            <td class="py-2 px-3">{{ $o->member->name ?? '-' }}</td>
                            <td class="py-2 px-3 text-center font-bold text-xs uppercase">{{ $o->payment_method }}</td>
                            <td class="py-2 px-3 text-right font-bold text-gray-900">฿{{ number_format($o->total_amount, 2) }}</td>
                            <td class="py-2 px-3 text-center">
                                <a href="{{ route('pos.receipt', $o->id) }}" target="_blank" class="text-blue-500 hover:underline text-xs">เปิดพิมพ์</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-400">ไม่พบข้อมูลการขาย</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $orders->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>