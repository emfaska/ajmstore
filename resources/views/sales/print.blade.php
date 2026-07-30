<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 10px;
            width: 80mm;
            max-width: 80mm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .header {
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .items-table th, .items-table td {
            padding: 4px 0;
            text-align: left;
        }
        .summary-container {
            width: 100%;
            margin-top: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Header -->
    <div class="header text-center">
        <h1>AJM STORE</h1>
        <p>Jl. Raya Utama No. 45, Kebumen</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="divider"></div>

    <!-- Info Transaksi -->
    <table class="info-table">
        <tr>
            <td width="35%">No. Invoice</td>
            <td width="5%">:</td>
            <td>{{ $sale->invoice_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Transaksi</td>
            <td>:</td>
            <td class="font-bold">{{ ($sale->transaction_type === 'bengkel') ? 'Bengkel' : 'Penjualan Umum' }}</td>
        </tr>
        @if($sale->customer)
            <tr>
                <td>Pelanggan</td>
                <td>:</td>
                <td>{{ $sale->customer->name }}</td>
            </tr>
        @endif
        @if($sale->vehicle)
            <tr>
                <td>Kendaraan</td>
                <td>:</td>
                <td>{{ $sale->vehicle->license_plate }} ({{ $sale->vehicle->brand }})</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <!-- Item List -->
    <table class="items-table">
        <thead>
            <tr class="font-bold">
                <th width="50%">Item</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="35%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td colspan="3">
                        <div class="font-bold">{{ $item->product->name }}</div>
                        <div>{{ $item->quantity }} x Rp {{ number_format($item->selling_price, 0, ',', '.') }}</div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Summary -->
    <div class="summary-container">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($sale->discount > 0)
            <div class="summary-row">
                <span>Diskon:</span>
                <span>-Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if($sale->tax > 0)
            <div class="summary-row">
                <span>Pajak (PPN):</span>
                <span>Rp {{ number_format($sale->tax, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="summary-row font-bold" style="font-size: 13px;">
            <span>Grand Total:</span>
            <span>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
        </div>
        
        <div class="divider"></div>

        <div class="summary-row">
            <span>Pembayaran:</span>
            <span class="font-bold">{{ $sale->paymentMethod->name }}</span>
        </div>
        <div class="summary-row">
            <span>Status:</span>
            <span class="font-bold">{{ ($sale->payment_status === 'paid') ? 'LUNAS' : 'BELUM BAYAR' }}</span>
        </div>

        @if($cashReceived > 0)
            <div class="summary-row">
                <span>Uang Tunai:</span>
                <span>Rp {{ number_format($cashReceived, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row font-bold">
                <span>Kembalian:</span>
                <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="divider"></div>

    <!-- Notes if any -->
    @if($sale->notes)
        <div style="font-size: 10px; margin-bottom: 10px;">
            <span class="font-bold">Catatan:</span><br>
            {{ $sale->notes }}
        </div>
        <div class="divider"></div>
    @endif

    <!-- Footer -->
    <div class="footer text-center">
        <p class="font-bold">TERIMA KASIH</p>
        <p>Atas Kunjungan Anda di AJM Store</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
    </div>

    <!-- Print Button (No Print class) -->
    <div class="no-print text-center" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 6px 12px; font-weight: bold; background: #3b82f6; color: #fff; border: 0; border-radius: 4px; cursor: pointer;">Cetak Ulang</button>
        <button onclick="window.close()" style="padding: 6px 12px; font-weight: bold; background: #ef4444; color: #fff; border: 0; border-radius: 4px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

</body>
</html>
