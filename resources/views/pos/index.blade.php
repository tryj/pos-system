<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🛒 จุดขายหน้าร้าน (POS Terminal)
        </h2>
    </x-slot>

    <div class="py-6" x-data="posApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
            <!-- ซ้าย: ค้นหาและสินค้า -->
            <div class="w-full md:w-2/3 bg-white p-6 shadow-sm sm:rounded-lg">
                <input type="text" x-model="search" placeholder="🔍 ยิงบาร์โค้ด หรือ พิมพ์ชื่อสินค้า..." 
                       class="w-full mb-4 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 max-h-[550px] overflow-y-auto pr-1">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)" 
                             class="border rounded-xl p-4 cursor-pointer hover:shadow-md hover:border-blue-500 transition flex flex-col justify-between bg-gray-50">
                            <div>
                                <span class="text-xs font-mono text-gray-400" x-text="product.barcode"></span>
                                <h4 class="font-bold text-gray-800 text-sm mt-1" x-text="product.name"></h4>
                            </div>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-blue-600 font-extrabold text-base" x-text="'฿' + Number(product.price).toFixed(2)"></span>
                                <span class="text-xs bg-gray-200 px-2 py-0.5 rounded text-gray-600" x-text="'เหลือ ' + product.stock"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ขวา: ตะกร้าและชำระเงิน -->
            <div class="w-full md:w-1/3 bg-white p-6 shadow-sm sm:rounded-lg flex flex-col justify-between h-[650px]">
                <div>
                    <h3 class="font-bold text-lg text-gray-800 border-b pb-2 flex justify-between">
                        <span>รายการสั่งซื้อ</span>
                        <button @click="cart = []" class="text-xs text-red-500 hover:underline">ล้างบิล</button>
                    </h3>

                    <div class="overflow-y-auto max-h-[300px] mt-4 space-y-3">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500" x-text="'฿' + item.price + ' x ' + item.qty"></p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="changeQty(index, -1)" class="w-6 h-6 bg-gray-200 rounded font-bold">-</button>
                                    <span class="w-6 text-center text-sm font-bold" x-text="item.qty"></span>
                                    <button @click="changeQty(index, 1)" class="w-6 h-6 bg-gray-200 rounded font-bold">+</button>
                                    <button @click="cart.splice(index, 1)" class="text-red-500 ml-1 text-xs">✕</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="border-t pt-4 space-y-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-500">สมาชิกร้านค้า (เบอร์โทร):</label>
                        <select x-model="memberId" class="w-full mt-1 text-sm border-gray-300 rounded-lg">
                            <option value="">-- ไม่ระบุสมาชิก --</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" @click="paymentMethod = 'cash'" 
                                :class="paymentMethod === 'cash' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="flex-1 py-2 rounded-lg font-semibold text-xs transition">💵 เงินสด</button>
                        <button type="button" @click="paymentMethod = 'qr_promptpay'" 
                                :class="paymentMethod === 'qr_promptpay' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                class="flex-1 py-2 rounded-lg font-semibold text-xs transition">📱 PromptPay</button>
                    </div>

                    <div x-show="paymentMethod === 'cash'">
                        <input type="number" x-model.number="cashReceived" placeholder="รับเงินมา (บาท)..." 
                               class="w-full text-sm border-gray-300 rounded-lg">
                    </div>

                    <div class="flex justify-between text-2xl font-black text-gray-900 border-t pt-2">
                        <span>รวมสุทธิ</span>
                        <span class="text-blue-600" x-text="'฿' + totalAmount.toFixed(2)"></span>
                    </div>

                    <button @click="submitOrder()" :disabled="cart.length === 0"
                            class="w-full py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 text-white font-bold rounded-xl shadow transition">
                        ยืนยันการขาย & ตัดสต็อก
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function posApp() {
            return {
                products: @json($products),
                cart: [],
                search: '',
                memberId: '',
                paymentMethod: 'cash',
                cashReceived: 0,

                get filteredProducts() {
                    return this.products.filter(p => 
                        p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                        p.barcode.includes(this.search)
                    );
                },
                addToCart(product) {
                    let item = this.cart.find(i => i.id === product.id);
                    if (item) {
                        if (item.qty < product.stock) item.qty++;
                        else alert('สต็อกมีไม่พอ');
                    } else {
                        this.cart.push({ id: product.id, name: product.name, price: Number(product.price), qty: 1, stock: product.stock });
                    }
                },
                changeQty(index, delta) {
                    let item = this.cart[index];
                    if (item.qty + delta > item.stock) {
                        alert('สต็อกมีไม่พอ');
                        return;
                    }
                    item.qty += delta;
                    if (item.qty <= 0) this.cart.splice(index, 1);
                },
                get totalAmount() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                async submitOrder() {
                    if (this.paymentMethod === 'cash' && this.cashReceived < this.totalAmount) {
                        alert('จำนวนเงินที่รับมาต้องไม่น้อยกว่ายอดรวมสุทธิ');
                        return;
                    }
                    try {
                        let response = await fetch("{{ route('pos.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                member_id: this.memberId || null,
                                payment_method: this.paymentMethod,
                                cash_received: this.paymentMethod === 'cash' ? this.cashReceived : this.totalAmount
                            })
                        });
                        let data = await response.json();
                        if (data.success) {
                            window.open(data.receipt_url, '_blank', 'width=400,height=600');
                            location.reload();
                        } else {
                            alert(data.error || 'เกิดข้อผิดพลาดในการทำรายการ');
                        }
                    } catch (e) {
                        alert('ไม่สามารถบันทึกรายการขายได้');
                    }
                }
            }
        }
    </script>
</x-app-layout>