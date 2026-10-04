@extends('layouts.admin')
@section('title', 'Billing Generator')
@section('breadcrumb', 'Transaksi / Billing Generator')
@section('page-title', 'Billing Generator')
@section('page-subtitle', 'Generate tagihan massal per kelas.')

@section('topbar-actions')
<button type="button" title="Riwayat generate" onclick="document.getElementById('history-modal').classList.remove('hidden')"
    class="ss-tip ss-tip-bottom flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 shadow-xs"
    data-tip="Riwayat generate">
    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    Riwayat Generate
</button>
@endsection

@section('content')
    @include('admin.billing.stepper')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            @include('admin.billing.form')
        </div>
        <div>
            @include('admin.billing.summary')
        </div>
    </div>

    @if(($recentBills ?? collect())->isNotEmpty())
    <x-ss-table>
        <x-slot name="head">
            <th class="py-2.5 px-3">Invoice</th>
            <th class="py-2.5 px-3">Siswa</th>
            <th class="py-2.5 px-3">Kategori</th>
            <th class="py-2.5 px-3">Nominal</th>
            <th class="py-2.5 px-3 text-center">Status</th>
        </x-slot>

        @foreach($recentBills ?? [] as $b)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <td class="py-3 px-3 font-semibold text-blue-600 dark:text-blue-400">{{ $b->bill_code }}</td>
            <td class="py-3 px-3">
                <div class="font-medium text-slate-800 dark:text-white">{{ $b->student->name ?? '-' }}</div>
                <div class="text-[11px] text-slate-400">{{ $b->student->classRoom->name ?? '-' }}</div>
            </td>
            <td class="py-3 px-3">{{ $b->feeCategory->name ?? '-' }}</td>
            <td class="py-3 px-3 font-bold">Rp {{ number_format($b->amount, 0, ',', '.') }}</td>
            <td class="py-3 px-3 text-center"><x-ss-pill :status="$b->status" /></td>
        </tr>
        @endforeach
    </x-ss-table>
    @endif

    @include('admin.billing.confirm-modal')
    @include('admin.billing.history-modal')
@endsection
