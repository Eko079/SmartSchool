@extends('layouts.portal')
@section('title', 'Tagihan Saya')
@section('breadcrumb', 'Portal / Tagihan')
@section('page-title', 'Tagihan Saya')
@section('page-subtitle', 'Cari invoice, filter status, klik Detail untuk bayar.')

@section('content')
    @php $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.'); @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="ss-card">
            <div class="text-xs font-medium text-slate-500">Total Menunggu Dibayar</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white" data-count-up="{{ (float) $summary['waiting'] }}" data-count-fmt="rp">{{ $rp($summary['waiting']) }}</div>
        </div>
        <div class="ss-card">
            <div class="text-xs font-medium text-slate-500">Total Sudah Dibayar</div>
            <div class="text-2xl font-bold text-emerald-600" data-count-up="{{ (float) $summary['paid'] }}" data-count-fmt="rp">{{ $rp($summary['paid']) }}</div>
        </div>
    </div>

    <div class="ss-card">
        <form method="GET" action="{{ route('portal.tagihan') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari invoice, kategori..." class="ss-input !w-auto flex-1 min-w-[200px]">
            <select name="academic_year" class="ss-input !w-auto">
                <option value="">Semua TA</option>
                @foreach(($academicYears ?? collect()) as $ta)
                <option value="{{ $ta }}" {{ request('academic_year') === $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
            <select name="semester" class="ss-input !w-auto">
                <option value="">Semua Semester</option>
                <option value="ganjil" {{ request('semester') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="genap" {{ request('semester') === 'genap' ? 'selected' : '' }}>Genap</option>
            </select>
            <select name="fee_category_id" class="ss-input !w-auto">
                <option value="">Semua Kategori</option>
                @foreach(($categories ?? collect()) as $cat)
                <option value="{{ $cat->id }}" {{ (string) request('fee_category_id') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="ss-input !w-auto">
                @foreach(['all' => 'Semua Status', 'unpaid' => 'Belum Bayar', 'partial' => 'Sebagian', 'paid' => 'Lunas', 'overdue' => 'Menunggak'] as $val => $label)
                <option value="{{ $val }}" {{ request('status', 'all') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
        </form>
    </div>

    <div class="ss-card space-y-2">
        @forelse($bills as $bill)
        <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 p-3">
            <div class="flex-1 min-w-[200px]">
                <div class="text-xs font-bold text-slate-800 dark:text-white">{{ $bill->bill_code }} • {{ $bill->feeCategory->name ?? '-' }}</div>
                <div class="text-[11px] text-slate-400">Jatuh tempo {{ $bill->due_date?->format('d M Y') ?? '-' }} • {{ $rp($bill->amount) }}</div>
                @if($bill->academic_year || $bill->semester)
                <div class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold">TA {{ $bill->academic_year ?? '-' }} • Semester {{ $bill->semester ? ucfirst($bill->semester) : '-' }}</div>
                @endif
            </div>
            <x-ss-pill status="{{ $bill->status }}" label="{{ ucfirst($bill->status) }}" />
            <a href="{{ route('portal.tagihan.show', $bill->id) }}" class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold hover:border-blue-500 hover:text-blue-600">Detail</a>
            @if($bill->status !== 'paid')
            <button type="button" onclick="payBill({{ $bill->id }})" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Bayar</button>
            @endif
        </div>
        @empty
        <p class="text-xs text-slate-400">Tidak ada tagihan yang cocok.</p>
        @endforelse

        <div class="pt-2">{{ $bills->links() }}</div>
    </div>

    <script>
    function payBill(id) {
        var method = prompt('Pilih metode: bca_va, bni_va, qris', 'bca_va');
        if (!method) return;
        fetch('/tagihan/' + id + '/bayar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ method: method })
        }).then(function (r) { return r.json(); }).then(function (j) {
            alert('VA: ' + (j.transaction.va_number || '-') + ' • Order: ' + (j.transaction.order_id || '-'));
            location.reload();
        }).catch(function () { alert('Gagal membuat pembayaran.'); });
    }
    </script>
@endsection
