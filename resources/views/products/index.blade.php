<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📦 จัดการสินค้าและสต็อก</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- ฟอร์มเพิ่มสินค้าใหม่ (รองรับการอัปโหลดรูปภาพ) -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="font-bold text-gray-800 mb-4">+ เพิ่มสินค้าใหม่</h3>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold">บาร์โค้ด</label>
                        <input type="text" name="barcode" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">ชื่อสินค้า</label>
                        <input type="text" name="name" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">ราคาทุน</label>
                        <input type="number" step="0.01" name="cost_price" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">ราคาขาย</label>
                        <input type="number" step="0.01" name="price" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">จำนวนสต็อก</label>
                        <input type="number" name="stock" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">แจ้งเตือนเมื่อสต็อกต่ำกว่า</label>
                        <input type="number" name="alert_stock" value="5" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold">รูปภาพสินค้า (.jpg, .png, .webp)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm border border-gray-300 rounded p-1 bg-gray-50">
                    </div>
                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm shadow">
                            บันทึกสินค้า
                        </button>
                    </div>
                </form>
            </div>

            <!-- ตารางสินค้าทั้งหมด -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="font-bold text-gray-800 mb-4">รายการสินค้าทั้งหมด</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                            <tr>
                                <th class="py-2 px-3 text-center">รูปภาพ</th>
                                <th class="py-2 px-3">บาร์โค้ด</th>
                                <th class="py-2 px-3">ชื่อสินค้า</th>
                                <th class="py-2 px-3 text-right">ทุน</th>
                                <th class="py-2 px-3 text-right">ราคาขาย</th>
                                <th class="py-2 px-3 text-center">คงเหลือ</th>
                                <th class="py-2 px-3 text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($products as $p)
                            <tr>
                                <td class="py-2 px-3 text-center">
                                    @if($p->image)
                                        <img src="{{ asset('storage/' . $p->image) }}" class="w-10 h-10 object-cover rounded-lg mx-auto border shadow-sm">
                                    @else
                                        <span class="text-gray-300 text-xs">ไม่มีรูป</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 font-mono">{{ $p->barcode }}</td>
                                <td class="py-2 px-3 font-medium">{{ $p->name }}</td>
                                <td class="py-2 px-3 text-right">฿{{ number_format($p->cost_price, 2) }}</td>
                                <td class="py-2 px-3 text-right">฿{{ number_format($p->price, 2) }}</td>
                                <td class="py-2 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $p->stock <= $p->alert_stock ? 'bg-red-100 text-red-700 font-bold' : 'bg-gray-100' }}">
                                        {{ $p->stock }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <form action="{{ route('products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('ยืนยันลบสินค้านี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline text-xs">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>