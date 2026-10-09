@extends('layouts.admin')
@section('title', 'Preview Import Siswa')
@section('breadcrumb', 'Master Data / Siswa / Preview Import')
@section('page-title', 'Preview Import Siswa')
@section('page-subtitle', 'Periksa status tiap baris sebelum masuk database.')

@section('content')
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="ss-card"><div class="text-xs text-slate-500">Total Baris</div><div class="text-2xl font-bold">{{ $summary['total'] }}</div></div>
        <div class="ss-card"><div class="text-xs text-slate-500">OK</div><div class="text-2xl font-bold text-emerald-600">{{ $summary['ok'] }}</div></div>
        <div class="ss-card"><div class="text-xs text-slate-500">Duplikat</div><div class="text-2xl font-bold text-amber-600">{{ $summary['duplikat'] }}</div></div>
        <div class="ss-card"><div class="text-xs text-slate-500">Error</div><div class="text-2xl font-bold text-rose-600">{{ $summary['error'] }}</div></div>
    </div>

    <x-ss-table>
        <x-slot name="head">
            <th class="py-2.5 px-3">No</th>
            <th class="py-2.5 px-3">NIS / Nama</th>
            <th class="py-2.5 px-3">Kelas</th>
            <th class="py-2.5 px-3 text-center">Status Baris</th>
            <th class="py-2.5 px-3">Keterangan</th>
        </x-slot>
        @foreach($rows as $r)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <td class="py-3 px-3 text-slate-400">{{ $r['no'] }}</td>
            <td class="py-3 px-3"><div class="font-bold">{{ $r['nis'] ?: '-' }}</div><div class="text-[11px] text-slate-500">{{ $r['name'] ?: '-' }}</div></td>
            <td class="py-3 px-3">{{ $r['kelas'] ?: '-' }}</td>
            <td class="py-3 px-3 text-center">
                @if($r['state'] === 'ok')<x-ss-pill status="paid" label="OK" />
                @elseif($r['state'] === 'duplikat')<x-ss-pill status="pending" label="Duplikat" />
                @else<x-ss-pill status="failed" label="Error" />@endif
            </td>
            <td class="py-3 px-3 text-[11px] text-slate-500">{{ $r['reason'] }}</td>
        </tr>
        @endforeach
    </x-ss-table>

    <div class="ss-card flex flex-wrap items-center justify-end gap-2">
        <a href="{{ route('admin.siswa') }}" class="rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs font-semibold">Batal</a>
        @if($summary['ok'] > 0)
            @if($summary['error'] > 0 || $summary['duplikat'] > 0)
            <form method="POST" action="{{ route('admin.siswa.import.confirm') }}" onsubmit="return confirm('Tidak semua valid ({{ $summary['ok'] }} OK, {{ $summary['duplikat'] }} duplikat, {{ $summary['error'] }} error). Yakin hanya import data valid?');">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="mode" value="valid">
                <button class="rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-700">Import Data Valid Saja ({{ $summary['ok'] }})</button>
            </form>
            @else
            <form method="POST" action="{{ route('admin.siswa.import.confirm') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="mode" value="all">
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Import Semua ({{ $summary['ok'] }})</button>
            </form>
            @endif
        @else
        <span class="text-xs text-rose-500 font-semibold">Tidak ada baris valid untuk diimport.</span>
        @endif
    </div>
@endsection
