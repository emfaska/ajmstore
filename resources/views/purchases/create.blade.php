@extends('layouts.app')

@section('page_title', 'Tambah Transaksi Pembelian')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('purchases.index') }}" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Transaksi Pembelian</h2>
            <p class="text-sm text-gray-500 font-normal">Buat faktur pembelian stok barang dari supplier.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <!-- Left: Form Header Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h3 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3">Informasi Faktur</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Invoice Number -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Invoice <span class="text-red-500">*</span></label>
                        <input type="text" name="invoice_number" value="{{ old('invoice_number', $invoiceNumber) }}" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold text-gray-700" placeholder="Contoh: INV-PRC-xxxx">
                        @error('invoice_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Supplier -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Purchase Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pembelian <span class="text-red-500">*</span></label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('purchase_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Metode Pembayaran <span class="text-red-500" id="payment_method_required" style="display:none;">*</span></label>
                        <select name="payment_method_id" id="payment_method_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">-- Pilih Metode Pembayaran --</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Detail Item Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-800">Daftar Barang</h3>
                    <button type="button" id="btn-add-item" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="table-items">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 w-[45%]">Produk / Barang</th>
                                <th scope="col" class="px-3 py-3 text-center w-[15%]">Qty</th>
                                <th scope="col" class="px-3 py-3 text-right w-[20%]">Harga Beli</th>
                                <th scope="col" class="px-3 py-3 text-right w-[20%]">Subtotal</th>
                                <th scope="col" class="px-3 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="item-rows">
                            <!-- Dynamic rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Summary & Action -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h3 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3">Ringkasan & Status</h3>
                
                <div class="space-y-4 text-sm">
                    <!-- Status Transaksi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Transaksi <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Draft (Pending)</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1">Stok produk hanya akan ditambahkan jika status transaksi diset ke 'Selesai'.</p>
                    </div>

                    <!-- Status Pembayaran -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                        <select name="payment_status" id="payment_status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="unpaid" {{ old('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas (Unpaid)</option>
                            <option value="partially_paid" {{ old('payment_status') === 'partially_paid' ? 'selected' : '' }}>Sebagian (Partially Paid)</option>
                            <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                        </select>
                        <p class="text-[11px] text-gray-500 mt-1">Transaksi kas keluar otomatis dibuat jika status pembayaran diset 'Lunas'.</p>
                    </div>

                    <hr class="border-gray-100 my-2">

                    <!-- Pricing Summary -->
                    <div class="flex justify-between font-medium text-gray-600">
                        <span>Total Nilai Barang:</span>
                        <span id="txt-subtotal-summary">Rp 0,00</span>
                    </div>

                    <div class="flex justify-between font-bold text-gray-800 text-base border-t border-gray-100 pt-3">
                        <span>Grand Total:</span>
                        <span id="txt-grand-total" class="text-blue-600">Rp 0,00</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 flex flex-col gap-3">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition-colors text-center text-sm">
                        Simpan Transaksi
                    </button>
                    <a href="{{ route('purchases.index') }}" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors text-center text-sm">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Product Row JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const products = @json($products);
    const itemRows = document.getElementById('item-rows');
    const btnAddItem = document.getElementById('btn-add-item');
    const paymentStatus = document.getElementById('payment_status');
    const paymentMethodRequired = document.getElementById('payment_method_required');
    const paymentMethodId = document.getElementById('payment_method_id');
    
    let rowIndex = 0;

    // Tambah baris baru
    function addRow() {
        const tr = document.createElement('tr');
        tr.className = 'border-b border-gray-100 row-item';
        tr.dataset.index = rowIndex;

        let options = '<option value="">-- Pilih Produk --</option>';
        products.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.purchase_price}">${p.name} (Stok: ${p.stock})</option>`;
        });

        tr.innerHTML = `
            <td class="px-3 py-3">
                <select name="items[${rowIndex}][product_id]" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm select-product">
                    ${options}
                </select>
            </td>
            <td class="px-3 py-3">
                <input type="number" name="items[${rowIndex}][quantity]" required min="1" value="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm text-center input-qty">
            </td>
            <td class="px-3 py-3">
                <input type="number" step="0.01" name="items[${rowIndex}][cost_price]" required min="0" value="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm text-right input-price">
            </td>
            <td class="px-3 py-3 text-right font-semibold text-gray-800 txt-subtotal">
                Rp 0,00
            </td>
            <td class="px-3 py-3 text-center">
                <button type="button" class="text-red-500 hover:text-red-700 btn-remove-row">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </td>
        `;

        itemRows.appendChild(tr);
        rowIndex++;
        calculateGrandTotal();
    }

    // Event listener untuk menambah baris
    btnAddItem.addEventListener('click', addRow);

    // Initial Row
    addRow();

    // Event delegation untuk handle perubahan dan tombol hapus
    itemRows.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('.row-item').length > 1) {
                row.remove();
                calculateGrandTotal();
            } else {
                alert('Transaksi harus memiliki minimal 1 item.');
            }
        }
    });

    itemRows.addEventListener('change', function (e) {
        const target = e.target;
        const row = target.closest('tr');

        if (target.classList.contains('select-product')) {
            const selectedOption = target.options[target.selectedIndex];
            const defaultPrice = selectedOption.dataset.price || 0;
            row.querySelector('.input-price').value = defaultPrice;
            updateRowSubtotal(row);
        }

        if (target.classList.contains('input-qty') || target.classList.contains('input-price')) {
            updateRowSubtotal(row);
        }
    });

    // Hitung Subtotal per Baris
    function updateRowSubtotal(row) {
        const qty = parseInt(row.querySelector('.input-qty').value) || 0;
        const price = parseFloat(row.querySelector('.input-price').value) || 0;
        const subtotal = qty * price;

        row.querySelector('.txt-subtotal').textContent = formatRupiah(subtotal);
        calculateGrandTotal();
    }

    // Hitung Grand Total Keseluruhan
    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.row-item').forEach(row => {
            const qty = parseInt(row.querySelector('.input-qty').value) || 0;
            const price = parseFloat(row.querySelector('.input-price').value) || 0;
            total += qty * price;
        });

        document.getElementById('txt-subtotal-summary').textContent = formatRupiah(total);
        document.getElementById('txt-grand-total').textContent = formatRupiah(total);
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
    }

    // Manage status pembayaran lunas require method pembayaran
    function togglePaymentMethodRequirement() {
        if (paymentStatus.value === 'paid') {
            paymentMethodRequired.style.display = 'inline';
            paymentMethodId.setAttribute('required', 'required');
        } else {
            paymentMethodRequired.style.display = 'none';
            paymentMethodId.removeAttribute('required');
        }
    }

    paymentStatus.addEventListener('change', togglePaymentMethodRequirement);
    togglePaymentMethodRequirement();
});
</script>
@endsection
