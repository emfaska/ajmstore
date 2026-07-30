@extends('layouts.app')

@section('title', 'Kasir POS - AJM Store')

@section('content')
<div class="h-full space-y-4" x-data="{
    products: @json($products),
    customers: @json($customers),
    vehicles: @json($vehicles),
    paymentMethods: @json($paymentMethods),
    
    // POS Cart State
    cart: [],
    searchQuery: '',
    scanQuery: '',
    
    // Form Inputs
    customerId: '',
    vehicleId: '',
    paymentMethodId: '',
    transactionType: 'penjualan_umum',
    notes: '',
    discount: 0,
    tax: 0,
    status: 'completed',
    paymentStatus: 'paid',
    cashReceived: 0,
    
    // Computed Values
    get filteredProducts() {
        if (!this.searchQuery) return this.products;
        let q = this.searchQuery.toLowerCase();
        return this.products.filter(p => 
            p.name.toLowerCase().includes(q) || 
            (p.sku && p.sku.toLowerCase().includes(q)) || 
            (p.barcode && p.barcode.toLowerCase().includes(q))
        );
    },
    
    get filteredVehicles() {
        if (!this.customerId) return this.vehicles;
        return this.vehicles.filter(v => v.customer_id == this.customerId);
    },
    
    get subtotal() {
        return this.cart.reduce((sum, item) => sum + (item.quantity * item.selling_price), 0);
    },
    
    get grandTotal() {
        let total = this.subtotal - parseFloat(this.discount || 0) + parseFloat(this.tax || 0);
        return total > 0 ? total : 0;
    },
    
    get change() {
        if (this.isCashPayment) {
            let chg = parseFloat(this.cashReceived || 0) - this.grandTotal;
            return chg > 0 ? chg : 0;
        }
        return 0;
    },
    
    get isCashPayment() {
        if (!this.paymentMethodId) return false;
        let selectedMethod = this.paymentMethods.find(m => m.id == this.paymentMethodId);
        return selectedMethod && selectedMethod.name.toLowerCase().includes('tunai');
    },

    addToCart(product) {
        if (product.stock <= 0) {
            alert('Stok produk ' + product.name + ' habis!');
            return;
        }
        
        let existingItem = this.cart.find(item => item.product_id === product.id);
        if (existingItem) {
            if (existingItem.quantity >= product.stock) {
                alert('Stok tidak mencukupi untuk menambah kuantitas!');
                return;
            }
            existingItem.quantity++;
            this.calculateItemSubtotal(existingItem);
        } else {
            this.cart.push({
                product_id: product.id,
                name: product.name,
                sku: product.sku,
                barcode: product.barcode,
                selling_price: parseFloat(product.sale_price),
                original_price: parseFloat(product.sale_price),
                discount_item: 0,
                quantity: 1,
                subtotal: parseFloat(product.sale_price),
                stock: product.stock
            });
        }
    },
    
    incrementQty(item) {
        if (item.quantity >= item.stock) {
            alert('Stok tidak mencukupi!');
            return;
        }
        item.quantity++;
        this.calculateItemSubtotal(item);
    },
    
    decrementQty(item) {
        if (item.quantity > 1) {
            item.quantity--;
            this.calculateItemSubtotal(item);
        }
    },
    
    removeItem(index) {
        this.cart.splice(index, 1);
    },
    
    updateItemDiscount(item, discountAmount) {
        let discount = parseFloat(discountAmount || 0);
        if (discount < 0) discount = 0;
        if (discount > item.original_price) {
            alert('Diskon item tidak boleh melebihi harga jual!');
            discount = 0;
        }
        item.discount_item = discount;
        item.selling_price = item.original_price - discount;
        this.calculateItemSubtotal(item);
    },
    
    calculateItemSubtotal(item) {
        item.subtotal = item.quantity * item.selling_price;
    },
    
    scanBarcode() {
        if (!this.scanQuery) return;
        let p = this.products.find(prod => 
            (prod.barcode && prod.barcode.toLowerCase() === this.scanQuery.trim().toLowerCase()) || 
            (prod.sku && prod.sku.toLowerCase() === this.scanQuery.trim().toLowerCase())
        );
        if (p) {
            this.addToCart(p);
            this.scanQuery = '';
        } else {
            alert('Produk dengan SKU/Barcode ini tidak ditemukan!');
        }
    },
    
    updatePaymentStatus() {
        if (this.paymentStatus === 'paid') {
            this.status = 'completed';
        } else {
            this.status = 'pending'; // Draft / Belum Lunas
        }
    },
    
    validateCheckout(e) {
        if (this.cart.length === 0) {
            alert('Keranjang belanja kosong!');
            e.preventDefault();
            return false;
        }
        if (!this.paymentMethodId) {
            alert('Pilih metode pembayaran!');
            e.preventDefault();
            return false;
        }
        if (this.isCashPayment && parseFloat(this.cashReceived) < this.grandTotal) {
            alert('Uang tunai yang diterima kurang dari total tagihan!');
            e.preventDefault();
            return false;
        }
        return true;
    }
}">
    <!-- Grid POS Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-8.5rem)] overflow-hidden">
        
        <!-- LEFT PANEL: Product Search & Catalog (Grid 7 Cols) -->
        <div class="lg:col-span-7 flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <!-- Search & Scan Header -->
            <div class="p-4 bg-gray-50 border-b border-gray-100 space-y-3">
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Text Search -->
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" x-model="searchQuery" placeholder="Cari Nama Produk / SKU / Barcode..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <!-- Barcode Scanner -->
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m5-6h.01M9 10h.01M10 16h4m4-4h.01M6 12h.01M4 12h.01M20 12h.01M12 2a10 10 0 100 20 10 10 0 000-20z"></path></svg>
                        </span>
                        <input type="text" x-model="scanQuery" @keyup.enter="scanBarcode()" placeholder="Scan Barcode / Enter SKU..." class="w-full pl-10 pr-4 py-2 bg-blue-50/50 border border-blue-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold text-blue-900 placeholder-blue-400">
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50/30">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)" class="group flex flex-col justify-between bg-white p-3 rounded-xl border border-gray-100 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer select-none">
                            <!-- Product Image / Generic Icon -->
                            <div class="relative w-full aspect-square bg-slate-100 rounded-lg flex items-center justify-center mb-3 overflow-hidden">
                                <template x-if="product.image">
                                    <img :src="'/storage/' + product.image" alt="Product Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </template>
                                <template x-if="!product.image">
                                    <svg class="w-12 h-12 text-slate-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </template>
                                <!-- Stock Badge -->
                                <div class="absolute top-2 right-2">
                                    <span :class="product.stock <= product.min_stock ? 'bg-red-500 text-white' : 'bg-slate-900/80 text-white'" class="px-2 py-0.5 rounded text-[10px] font-semibold">
                                        Stok: <span x-text="product.stock"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Product Details -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 line-clamp-2" x-text="product.name"></h4>
                                <div class="text-[10px] text-gray-500 mt-0.5">
                                    SKU: <span x-text="product.sku || '-'"></span>
                                </div>
                            </div>

                            <!-- Product Price -->
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-sm font-bold text-blue-600">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(product.sale_price)"></span>
                                </span>
                                <span class="w-6 h-6 rounded-full bg-blue-50 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Shopping Cart & Submit (Grid 5 Cols) -->
        <div class="lg:col-span-5 flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <!-- Form Start -->
            <form action="{{ route('sales.store') }}" method="POST" @submit="return validateCheckout($event)" class="flex flex-col h-full">
                @csrf
                <!-- Invoice & Date Header -->
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Invoice</span>
                        <h3 class="text-sm font-bold text-gray-800" x-text="invoiceNumber"></h3>
                        <input type="hidden" name="invoice_number" :value="invoiceNumber">
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Tanggal</span>
                        <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" class="block w-full mt-0.5 py-1 px-2 text-xs border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Transaction Metadata Fields (Customer, Vehicle, etc) -->
                <div class="p-4 bg-gray-50/50 border-b border-gray-100 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Jenis Transaksi -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Transaksi</label>
                            <select name="transaction_type" x-model="transactionType" class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="penjualan_umum">Penjualan Umum</option>
                                <option value="bengkel">Bengkel</option>
                            </select>
                        </div>
                        
                        <!-- Customer -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Pelanggan</label>
                            <select name="customer_id" x-model="customerId" class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Umum / Tanpa Pelanggan --</option>
                                <template x-for="c in customers" :key="c.id">
                                    <option :value="c.id" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Vehicle Selection (Only show if transaction type is Bengkel) -->
                    <div x-show="transactionType === 'bengkel'" x-transition class="bg-blue-50/30 p-3 rounded-lg border border-blue-100">
                        <label class="block text-xs font-semibold text-blue-900 mb-1">Kendaraan Pelanggan</label>
                        <select name="vehicle_id" x-model="vehicleId" :required="transactionType === 'bengkel'" class="w-full text-xs rounded-lg border-blue-200 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">-- Pilih Kendaraan --</option>
                            <template x-for="v in filteredVehicles" :key="v.id">
                                <option :value="v.id" x-text="v.license_plate + ' (' + v.brand + ' ' + v.model + ')'"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Cart Items List (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-[150px]">
                    <template x-for="(item, index) in cart" :key="item.product_id">
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 flex flex-col gap-2">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h5 class="text-xs font-bold text-gray-800" x-text="item.name"></h5>
                                    <p class="text-[10px] text-gray-400" x-text="'SKU: ' + (item.sku || '-')"></p>
                                </div>
                                <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>

                            <!-- Quantity Adjuster, Price, and Item Discount -->
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <!-- Qty controls -->
                                <div class="col-span-4 flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white">
                                    <button type="button" @click="decrementQty(item)" class="px-2 py-1 text-gray-500 hover:bg-gray-100 font-bold">-</button>
                                    <input type="text" readonly :value="item.quantity" class="w-full text-center border-0 text-xs p-1 font-semibold text-gray-800">
                                    <button type="button" @click="incrementQty(item)" class="px-2 py-1 text-gray-500 hover:bg-gray-100 font-bold">+</button>
                                </div>

                                <!-- Diskon Item input -->
                                <div class="col-span-4 relative">
                                    <input type="number" :value="item.discount_item" @input="updateItemDiscount(item, $event.target.value)" placeholder="Diskon" class="w-full p-1 text-xs border border-gray-300 rounded-lg text-right pr-5">
                                    <span class="absolute right-1.5 top-1.5 text-[10px] text-gray-400">Rp</span>
                                </div>

                                <!-- Subtotal display -->
                                <div class="col-span-4 text-right">
                                    <span class="text-xs font-bold text-gray-800">
                                        Rp <span x-text="new Intl.NumberFormat('id-ID').format(item.subtotal)"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Hidden inputs for backend submission -->
                            <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                            <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                            <input type="hidden" :name="'items['+index+'][selling_price]'" :value="item.selling_price">
                        </div>
                    </template>

                    <!-- Cart Empty State -->
                    <div x-show="cart.length === 0" class="flex flex-col items-center justify-center py-12 text-center text-gray-400 space-y-2">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-xs">Keranjang Belanja Masih Kosong</p>
                    </div>
                </div>

                <!-- Notes & Description -->
                <div class="px-4 py-2 border-t border-gray-100 bg-gray-50/30">
                    <textarea name="notes" x-model="notes" placeholder="Catatan / Keterangan tambahan (Opsional)..." class="w-full text-xs border-gray-300 rounded-lg p-2 resize-none" rows="2"></textarea>
                </div>

                <!-- Payment Summary, Payment Selector, Cash calculation -->
                <div class="p-4 bg-gray-50 border-t border-gray-200 space-y-4 shadow-inner">
                    <!-- Calculation Panel -->
                    <div class="space-y-1.5 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(subtotal)"></span></span>
                        </div>
                        <div class="flex justify-between items-center gap-4">
                            <span>Diskon Penjualan</span>
                            <div class="relative w-32">
                                <input type="number" name="discount" x-model.number="discount" class="w-full p-1 border border-gray-300 rounded text-right text-xs pr-5">
                                <span class="absolute right-1 top-1 text-[10px] text-gray-400">Rp</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center gap-4">
                            <span>Pajak (PPN)</span>
                            <div class="relative w-32">
                                <input type="number" name="tax" x-model.number="tax" class="w-full p-1 border border-gray-300 rounded text-right text-xs pr-5">
                                <span class="absolute right-1 top-1 text-[10px] text-gray-400">Rp</span>
                            </div>
                        </div>
                        <div class="flex justify-between text-base font-bold text-gray-900 border-t border-gray-200 pt-2">
                            <span>Grand Total</span>
                            <span class="text-blue-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(grandTotal)"></span></span>
                        </div>
                    </div>

                    <!-- Payment Details (Method & Status) -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Metode Pembayaran</label>
                            <select name="payment_method_id" x-model="paymentMethodId" required class="w-full text-xs rounded-lg border-gray-300 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Pembayaran --</option>
                                <template x-for="method in paymentMethods" :key="method.id">
                                    <option :value="method.id" x-text="method.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Status Pembayaran</label>
                            <select name="payment_status" x-model="paymentStatus" @change="updatePaymentStatus()" class="w-full text-xs rounded-lg border-gray-300 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                                <option value="paid">Lunas</option>
                                <option value="unpaid">Belum Dibayar</option>
                            </select>
                            <input type="hidden" name="status" :value="status">
                        </div>
                    </div>

                    <!-- Cash Input Panel (Only shown if Cash / Tunai is chosen) -->
                    <div x-show="isCashPayment" x-collapse class="bg-blue-50/50 p-3 rounded-xl border border-blue-100 space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-bold text-blue-900">Uang Diterima (Tunai)</label>
                            <div class="relative w-40">
                                <input type="number" name="cash_received" x-model.number="cashReceived" class="w-full p-1 text-xs border border-blue-200 rounded-lg text-right pr-6 focus:ring-blue-500 focus:border-blue-500 font-bold bg-white text-blue-900">
                                <span class="absolute right-2 top-1.5 text-[10px] text-blue-400 font-bold">Rp</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-blue-800 pt-1.5 border-t border-blue-200/50">
                            <span>Kembalian</span>
                            <span class="font-extrabold text-sm text-green-600">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(change)"></span>
                            </span>
                            <input type="hidden" name="change" :value="change">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition-colors flex items-center justify-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-5 4h6m-6 4h6m-6 4h6"></path></svg>
                        Simpan Transaksi Penjualan
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection
