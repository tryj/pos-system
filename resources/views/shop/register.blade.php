<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างบัญชีสมาชิก - POS Shop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center mb-6">
            <a href="{{ route('shop.index') }}" class="text-2xl font-black text-blue-600">🛒 POS SHOP</a>
            <h2 class="text-xl font-bold text-gray-800 mt-2">สร้างบัญชีสมาชิกใหม่</h2>
            <p class="text-xs text-gray-500 mt-1">สมัครเพื่อสะสมแต้มและรับสิทธิประโยชน์หน้าร้าน</p>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('customer.register.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ชื่อ - นามสกุล</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="เช่น นายสมชาย ใจดี"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">เบอร์โทรศัพท์มือถือ (ใช้สำหรับล็อกอิน/สะสมแต้ม)</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="เช่น 0812345678"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow text-sm transition">
                    ยืนยันการสมัครสมาชิก
                </button>
            </div>
        </form>

        <div class="text-center mt-6 border-t pt-4">
            <a href="{{ route('shop.index') }}" class="text-xs text-gray-500 hover:text-blue-600">
                ← กลับสู่หน้าร้านค้า
            </a>
        </div>
    </div>

</body>
</html>