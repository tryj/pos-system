<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">👔 จัดการพนักงาน (Employees)</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- ฟอร์มเพิ่มพนักงาน -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="font-bold text-gray-800 mb-4">+ เพิ่มพนักงานใหม่</h3>
                <form action="{{ route('employees.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold">ชื่อ - นามสกุล</label>
                        <input type="text" name="name" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">อีเมล (ใช้ Login)</label>
                        <input type="email" name="email" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="text-xs font-semibold">สิทธิ์การใช้งาน (Role)</label>
                        <select name="role" required class="w-full text-sm border-gray-300 rounded">
                            <option value="cashier">Cashier (แคชเชียร์)</option>
                            <option value="admin">Admin (ผู้จัดการ)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold">รหัสผ่านเริ่มต้น</label>
                        <input type="password" name="password" required class="w-full text-sm border-gray-300 rounded">
                    </div>
                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded text-sm">
                            บันทึกพนักงาน
                        </button>
                    </div>
                </form>
            </div>

            <!-- ตารางรายชื่อพนักงาน -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h3 class="font-bold text-gray-800 mb-4">รายชื่อพนักงานทั้งหมด</h3>
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs">
                        <tr>
                            <th class="py-2 px-3">ชื่อ</th>
                            <th class="py-2 px-3">อีเมล</th>
                            <th class="py-2 px-3 text-center">สิทธิ์ (Role)</th>
                            <th class="py-2 px-3 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($employees as $emp)
                        <tr>
                            <td class="py-2 px-3 font-medium">{{ $emp->name }}</td>
                            <td class="py-2 px-3 font-mono">{{ $emp->email }}</td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-bold {{ $emp->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                    {{ strtoupper($emp->role) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-center">
                                @if($emp->id !== auth()->id())
                                <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('ยืนยันลบพนักงาน?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs">ลบ</button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400">กำลังใช้งาน</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>