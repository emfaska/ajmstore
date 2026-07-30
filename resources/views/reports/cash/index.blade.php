@extends('layouts.app')

@section('title', 'Laporan Kas - AJM Store')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Arus Kas</h2>
            <p class="text-sm text-gray-500">Monitor kas masuk, kas keluar, dan saldo kas berjalan berdasarkan periode waktu.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.cash.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg shadow hover:bg-emerald-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Ekspor PDF (Cetak)
            </a>
        </div>
    </div>

    <!-- Date Filter Section -->
    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('reports.cash') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <!-- Start Date -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Hingga Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Action buttons -->
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 transition-colors">Tampilkan Laporan</button>
                <a href="{{ route('reports.cash') }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <!-- Financial KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Saldo Awal -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Saldo Awal</span>
                <h3 class="text-lg font-bold text-gray-850 mt-0.5">Rp {{ number_format($saldoAwal, 2, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Total Kas Masuk -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Kas Masuk (Debit)</span>
                <h3 class="text-lg font-bold text-green-600 mt-0.5">+ Rp {{ number_format($kasMasuk, 2, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Total Kas Keluar -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Kas Keluar (Credit)</span>
                <h3 class="text-lg font-bold text-red-600 mt-0.5">- Rp {{ number_format($kasKeluar, 2, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Saldo Akhir -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Saldo Akhir</span>
                <h3 class="text-lg font-bold text-blue-600 mt-0.5">Rp {{ number_format($saldoAkhir, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Statement Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-800">Rincian Buku Kas (Rekening Koran)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Tanggal</th>
                        <th scope="col" class="px-6 py-4">Keterangan</th>
                        <th scope="col" class="px-6 py-4">Referensi</th>
                        <th scope="col" class="px-6 py-4">Metode Pembayaran</th>
                        <th scope="col" class="px-6 py-4 text-right">Debit (+)</th>
                        <th scope="col" class="px-6 py-4 text-right">Credit (-)</th>
                        <th scope="col" class="px-6 py-4 text-right">Saldo Kas</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal Row -->
                    <tr class="bg-slate-50 border-b font-semibold">
                        <td class="px-6 py-4 text-gray-500" colspan="4">SALDO SEBELUM PERIODE {{ Carbon::parse($startDate)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">-</td>
                        <td class="px-6 py-4 text-right">-</td>
                        <td class="px-6 py-4 text-right text-gray-900">Rp {{ number_format($saldoAwal, 2, ',', '.') }}</td>
                    </tr>

                    @php
                        $runningBalance = $saldoAwal;
                    @endphp

                    @forelse($transactions as $tx)
                        @php
                            if ($tx->type === 'debit') {
                                $runningBalance += $tx->amount;
                            } else {
                                $runningBalance -= $tx->amount;
                            }
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <!-- Tanggal -->
                            <td class="px-6 py-4 text-gray-600">
                                {{ Carbon::parse($tx->transaction_date)->format('d/m/Y') }}
                            </td>
                            <!-- Keterangan -->
                            <td class="px-6 py-4 text-gray-800 font-medium">
                                {{ $tx->description }}
                            </td>
                            <!-- Referensi -->
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                @if($tx->referenceable_type)
                                    @php
                                        $typeParts = explode('\\', $tx->referenceable_type);
                                        $typeName = end($typeParts);
                                    @endphp
                                    {{ $typeName }} #{{ $tx->referenceable_id }}
                                @else
                                    Manual
                                @endif
                            </td>
                            <!-- Metode Pembayaran -->
                            <td class="px-6 py-4 text-gray-650">
                                {{ $tx->paymentMethod->name ?? 'N/A' }}
                            </td>
                            <!-- Debit -->
                            <td class="px-6 py-4 text-right text-green-600 font-semibold">
                                @if($tx->type === 'debit')
                                    + Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <!-- Credit -->
                            <td class="px-6 py-4 text-right text-red-600 font-semibold">
                                @if($tx->type === 'credit')
                                    - Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <!-- Saldo -->
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                Rp {{ number_format($runningBalance, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada transaksi kas pada periode tanggal ini.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Saldo Akhir Row -->
                    <tr class="bg-blue-50/50 font-bold border-t border-gray-300">
                        <td class="px-6 py-4 text-blue-900" colspan="4">SALDO HINGGA PERIODE {{ Carbon::parse($endDate)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right text-green-600">+ Rp {{ number_format($kasMasuk, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-red-600">- Rp {{ number_format($kasKeluar, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-blue-700 text-base">Rp {{ number_format($saldoAkhir, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
