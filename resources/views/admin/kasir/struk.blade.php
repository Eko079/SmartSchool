@extends('layouts.admin')
@section('title', 'Struk Kasir')
@section('page-title', 'Struk Pembayaran')
@section('page-subtitle', 'Simpan / cetak sebagai bukti untuk wali.')
@section('content')
    <div class="ss-card max-w-lg space-y-4 shadow-xs">
        <div class="text-center pb-3 border-b border-slate-200 dark:border-slate-800">
            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ \App\Models\SchoolSetting::get('school_name', 'SmartSchool') }}</div>
            <div class="text-[11px] text-slate-400">Struk pembayaran kasir • {{ now()->format('d/m/Y H:i') }}</div>
            <div class="text-[11px] text-slate-400">{{ $payments->first()->student->name ?? '' }} • NIS {{ $payments->first()->student->nis ?? '' }}</div>
        </div>
        <table class="w-full text-xs">
            <tbody>
                @foreach($payments as $p)
                <tr class="border-b border-slate-100 dark:border-slate-800">
                    <td class="py-2.5 pr-2">
                        <div class="font-medium text-slate-800 dark:text-white">{{ $p->bill->display_name ?? $p->bill_id }}</div>
                        <div class="text-[11px] text-slate-400">{{ $p->bill->bill_code ?? '' }} • {{ $p->invoice_number }} • {{ $p->payment_method }}</div>
                        @if($p->note)<div class="text-[11px] text-slate-400">{{ $p->note }}</div>@endif
                    </td>
                    <td class="py-2.5 pl-2 text-right font-bold text-slate-800 dark:text-white whitespace-nowrap">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="space-y-1 text-xs border-t border-slate-200 dark:border-slate-800 pt-3">
            <div class="flex justify-between font-bold text-sm text-slate-800 dark:text-white"><span>Total</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
            @if(!is_null($cash))
            <div class="flex justify-between text-slate-600 dark:text-slate-300"><span>Tunai</span><span>Rp {{ number_format($cash, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-slate-600 dark:text-slate-300"><span>Kembalian</span><span>Rp {{ number_format(max(0, $cash - $total), 0, ',', '.') }}</span></div>
            @endif
            <div class="text-[11px] text-slate-400">Kasir: {{ auth()->user()->name ?? '-' }}</div>
        </div>
        <div class="flex gap-2 no-print">
            <button type="button" onclick="window.print()" class="flex-1 rounded-lg bg-blue-600 py-2.5 text-xs font-bold text-white hover:bg-blue-700">Cetak</button>
            <a href="{{ route('admin.kasir') }}" class="flex-1 text-center rounded-lg border border-slate-200 dark:border-slate-700 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">Transaksi Baru</a>
        </div>
    </div>
    <style>@media print { .no-print { display: none !important; } }</style>
@endsection
