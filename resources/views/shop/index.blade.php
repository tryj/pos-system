<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านค้าออนไลน์ - สินค้าทั้งหมด</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans" x-data="shopApp()">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            
            <a href="{{ route('shop.index') }}" class="flex items-center gap-2 text-2xl font-black text-blue-600">
                <span>🛒</span>
                <span>POS STORE</span>
            </a>

            <div class="flex items-center gap-3">
                <!-- ปุ่มเปิดตะกร้าสินค้า -->
                <button @click="openCart = true" class="relative p-2 text-gray-700 hover:text-blue-600 transition">
                    <span class="text-2xl">🛍️</span>
                    <span x-show="totalCount > 0" x-text="totalCount" 
                          class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center"></span>
                </button>

                @if(session()->has('customer_id'))
                    <a href="{{ route('customer.profile') }}" 
                       class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 text-xs sm:text-sm font-bold rounded-xl hover:bg-green-100 transition">
                        👤 บัญชีของฉัน
                    </a>
                @else
                    <a href="{{ route('customer.login') }}" class="text-xs sm:text-sm font-semibold text-gray-700 hover:text-blue-600">เข้าสู่ระบบ</a>
                    <a href="{{ route('customer.register') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl transition">สมัครสมาชิก</a>
                @endif

                <div class="h-5 w-px bg-gray-200 hidden sm:block"></div>
                <a href="{{ route('login') }}" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">ระบบพนักงาน</a>
            </div>

        </div>
    </header>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">สินค้าพร้อมจำหน่าย</h1>
                <p class="text-xs text-gray-500">เลือกดูสินค้าและกดสั่งซื้อออนไลน์ได้ทันที</p>
            </div>
            
            <input type="text" x-model="searchQuery" placeholder="🔍 ค้นหาสินค้า..." 
                   class="w-full sm:w-64 px-4 py-2 text-sm border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- รายการสินค้า -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @forelse($products as $product)
                <div x-show="!searchQuery || '{{ strtolower($product->name) }}'.includes(searchQuery.toLowerCase())"
                     class="bg-white rounded-2xl border border-gray-200 overflow-hidden flex flex-col justify-between shadow-sm hover:shadow-md transition">
                    
                    <div class="h-40 bg-gray-100 relative flex items-center justify-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl text-gray-300">📦</span>
                        @endif
                        <span class="absolute top-2 right-2 bg-white/90 text-[10px] font-bold px-2 py-0.5 rounded-full text-gray-600">
                            เหลือ {{ $product->stock }}
                        </span>
                    </div>

                    <div class="p-3 flex flex-col flex-1 justify-between">
                        <div>
                            <span class="text-[10px] font-mono text-gray-400">#{{ $product->barcode }}</span>
                            <h3 class="font-bold text-gray-800 text-sm line-clamp-2">{{ $product->name }}</h3>
                        </div>

                        <div class="mt-3 pt-2 border-t flex flex-col gap-2">
                            <span class="text-base font-extrabold text-blue-600">฿{{ number_format($product->price, 2) }}</span>
                            <button @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})"
                                    class="w-full py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                                + สั่งซื้อ
                            </button>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-400">ไม่มีสินค้าจำหน่าย</div>
            @endforelse
        </div>
    </main>

    <!-- ตะกร้าสินค้าด้านข้าง (Drawer Cart) -->
    <div x-show="openCart" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="openCart = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex">
            <div class="w-screen max-w-md bg-white shadow-xl flex flex-col justify-between p-6">
                <div>
                    <div class="flex justify-between items-center border-b pb-4">
                        <h2 class="text-lg font-bold text-gray-800">🛍️ ตะกร้าสินค้า</h2>
                        <button @click="openCart = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                    </div>

                    <div class="overflow-y-auto max-h-[420px] mt-4 space-y-3">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border">
                                <div>
                                    <h4 class="font-bold text-sm text-gray-800" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500" x-text="'฿' + item.price + ' x ' + item.qty"></p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="changeQty(index, -1)" class="w-6 h-6 bg-gray-200 rounded font-bold text-xs">-</button>
                                    <span class="w-6 text-center text-xs font-bold" x-text="item.qty"></span>
                                    <button @click="changeQty(index, 1)" class="w-6 h-6 bg-gray-200 rounded font-bold text-xs">+</button>
                                    <button @click="cart.splice(index, 1)" class="text-red-500 ml-2 text-xs">✕</button>
                                </div>
                            </div>
                        </template>
                        <p x-show="cart.length === 0" class="text-center text-gray-400 py-8 text-sm">ยังไม่มีสินค้าในตะกร้า</p>
                    </div>
                </div>

                <div class="border-t pt-4 space-y-3">
                    <div class="flex justify-between text-xl font-bold">
                        <span>ยอดรวมสุทธิ</span>
                        <span class="text-blue-600" x-text="'฿' + totalAmount.toFixed(2)"></span>
                    </div>

                    @if(session()->has('customer_id'))
                        <button @click="checkout()" :disabled="cart.length === 0"
                                class="w-full py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 text-white font-bold rounded-xl shadow transition">
                            ยืนยันสั่งซื้อสินค้า
                        </button>
                    @else
                        <a href="{{ route('customer.login') }}" class="block text-center w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow transition">
                            เข้าสู่ระบบสมาชิกก่อนสั่งซื้อ
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function shopApp() {
            return {
                searchQuery: '',
                openCart: false,
                cart: [],

                addToCart(id, name, price, stock) {
                    let item = this.cart.find(i => i.id === id);
                    if (item) {
                        if (item.qty < stock) {
                            item.qty++;
                            this.openCart = true;
                        } else {
                            alert('สินค้าในสต็อกมีไม่พอ');
                        }
                    } else {
                        this.cart.push({ id, name, price, qty: 1, stock });
                        this.openCart = true;
                    }
                },
                changeQty(index, delta) {
                    let item = this.cart[index];
                    if (item.qty + delta > item.stock) {
                        alert('สินค้าในสต็อกมีไม่พอ');
                        return;
                    }
                    item.qty += delta;
                    if (item.qty <= 0) this.cart.splice(index, 1);
                },
                get totalCount() {
                    return this.cart.reduce((sum, item) => sum + item.qty, 0);
                },
                get totalAmount() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                async checkout() {
                    if (this.cart.length === 0) return;
                    try {
                        let response = await fetch("{{ route('shop.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ cart: this.cart })
                        });
                        let data = await response.json();
                        if (data.success) {
                            alert(data.message);
                            window.location.href = data.redirect;
                        } else {
                            alert(data.error || 'เกิดข้อผิดพลาดในการสั่งซื้อ');
                        }
                    } catch (e) {
                        alert('ไม่สามารถส่งคำสั่งซื้อได้');
                    }
                }
            }
        }
    </script>
</body>
</html>