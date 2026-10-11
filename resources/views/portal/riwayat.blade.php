@extends('layouts.portal')
@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="ss-card">
        <form method="GET" action="{{ route('portal.riwayat') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. kuitansi, tagihan..." class="ss-input !w-auto flex-1 min-w-[200px]">
            <select name="status" class="ss-input !w-auto">
                @foreach(['all' => 'Semua Status', 'success' => 'Lunas', 'pending' => 'Menunggu', 'failed' => 'Gagal'] as $val => $label)
                <option value="{{ $val }}" {{ request('status', 'all') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
            @if($payments->total() > 0)
            <a href="{{ route('portal.riwayat.cetak') }}" target="_blank" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700">Cetak Rekap</a>
            @endif
        </form>
    </div>

    <x-ss-table>
        <x-slot name="head">
            <th class="py-2.5 px-3">No. Kuitansi</th>
            <th class="py-2.5 px-3">No. Urut</th>
            <th class="py-2.5 px-3">Tagihan</th>
            <th class="py-2.5 px-3">Jumlah</th>
            <th class="py-2.5 px-3 text-center">Status</th>
            <th class="py-2.5 px-3">Tanggal Bayar</th>
            <th class="py-2.5 px-3">Cara Bayar</th>
            <th class="py-2.5 px-3 text-right">Aksi</th>
        </x-slot>

        @forelse($payments as $i => $pay)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <td class="py-3 px-3 font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ $pay->invoice_number }}</td>
            <td class="py-3 px-3 text-slate-400">{{ ($payments->firstItem() ?? 1) + $i }}</td>
            <td class="py-3 px-3 font-medium text-slate-800 dark:text-white">{{ $pay->bill->display_name ?? '-' }}<div class="text-[10px] font-semibold text-slate-400">{{ $pay->bill->bill_code ?? '' }}</div></td>
            <td class="py-3 px-3 font-bold whitespace-nowrap">{{ $rp($pay->amount) }}</td>
            <td class="py-3 px-3 text-center"><x-ss-pill status="{{ $pay->status }}" label="{{ $pay->status_label }}" /></td>
            <td class="py-3 px-3 whitespace-nowrap">{{ $pay->paid_at?->format('d M Y H:i') ?? '-' }}</td>
            <td class="py-3 px-3 whitespace-nowrap text-slate-500">{{ $pay->method_label }}</td>
            <td class="py-3 px-3 text-right whitespace-nowrap">
                <a href="{{ route('portal.kuitansi', $pay->id) }}" target="_blank" class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-[11px] font-semibold hover:border-blue-500 hover:text-blue-600">Cetak</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="py-6 text-center text-slate-400">{{ request()->filled('q') || (request()->filled('status') && request('status') !== 'all') ? 'Tidak ketemu. Coba kata kunci atau status lain.' : 'Belum ada riwayat pembayaran.' }}</td></tr>
        @endforelse

        <x-slot name="pagination">{{ $payments->links() }}</x-slot>
    </x-ss-table>
@endsection
