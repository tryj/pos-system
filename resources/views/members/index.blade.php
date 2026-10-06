<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">👥 จัดการสมาชิกร้านค้า</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- ฟอร์มเพิ่มสมาชิก -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="font-bold text-gray-800 mb-4">+ เพิ่มสมาชิกใหม่</h3>
                <form action="{{ route('members.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold">ชื่อ - นามสกุล</label>
                        <input type="text" name="name" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">เบอร์โทรศัพท์ (ใช้ค้นหา)</label>
                        <input type="text" name="phone" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded text-sm w-full">บันทึกสมาชิก</button>
                    </div>
                </form>
            </div>

            <!-- ตารางรายชื่อสมาชิก -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="font-bold text-gray-800 mb-4">รายชื่อสมาชิกทั้งหมด</h3>
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                        <tr>
                            <th class="py-2 px-3">ชื่อ - นามสกุล</th>
                            <th class="py-2 px-3">เบอร์โทรศัพท์</th>
                            <th class="py-2 px-3 text-center">แต้มสะสม</th>
                            <th class="py-2 px-3 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($members as $m)
                        <tr>
                            <td class="py-2 px-3 font-medium">{{ $m->name }}</td>
                            <td class="py-2 px-3 font-mono">{{ $m->phone }}</td>
                            <td class="py-2 px-3 text-center font-bold text-blue-600">{{ $m->points }} แต้ม</td>
                            <td class="py-2 px-3 text-center">
                                <form action="{{ route('members.destroy', $m->id) }}" method="POST" onsubmit="return confirm('ยืนยันลบสมาชิก?')">
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
</x-app-layout>