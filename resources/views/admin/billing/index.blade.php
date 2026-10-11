@extends('layouts.admin')
@section('title', 'Billing Generator')
@section('page-title', 'Billing Generator')
@section('page-subtitle', 'Generate tagihan massal per kelas.')

@section('content')
    @if(session('success'))<div class="ss-card !border-emerald-300 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="ss-card !border-rose-300 text-xs font-semibold text-rose-600">{{ session('error') }}</div>@endif
    <div class="flex justify-end">
        <form method="POST" action="{{ route('admin.billing.overdue') }}" onsubmit="return confirm('Hitung ulang denda + status menunggak sekarang?');">
            @csrf
            <button type="submit" class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100">Hitung Denda Sekarang</button>
        </form>
    </div>
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
            <td class="py-3 px-3">{{ $b->display_name }}</td>
            <td class="py-3 px-3 font-bold">Rp {{ number_format($b->amount, 0, ',', '.') }}@if((float) $b->fine_amount > 0)<div class="text-[10px] font-semibold text-rose-500">+ denda Rp {{ number_format($b->fine_amount, 0, ',', '.') }}</div>@endif</td>
            <td class="py-3 px-3 text-center"><x-ss-pill :status="$b->status" /></td>
        </tr>
        @endforeach
    </x-ss-table>
    @endif

    @include('admin.billing.confirm-modal')
    @include('admin.billing.history-modal')
@endsection
