@extends('layouts.admin')
@section('title', 'Jejak Audit')
@section('page-title', 'Jejak Audit')
@section('page-subtitle', 'Siapa melakukan apa dan kapan. Hanya Super Admin.')
@section('content')
    <x-ss-table>
        <x-slot name="filters">
            <form method="GET" action="{{ route('admin.audit') }}" class="flex flex-wrap items-center gap-2">
                <select name="aksi" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                    <option value="">Semua Aksi</option>
                    @foreach($aksiList as $a)
                        <option value="{{ $a }}" {{ request('aksi') === $a ? 'selected' : '' }}>{{ \App\Models\AuditLog::label($a) }}</option>
                    @endforeach
                </select>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()"
                    class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs py-2 px-3 text-slate-700 dark:text-slate-200 outline-none">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / rincian..."
                    class="ss-input text-xs !py-2 w-44 sm:w-56">
                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
                @if(request()->anyFilled(['aksi', 'q', 'tanggal']))
                    <a href="{{ route('admin.audit') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
                @endif
            </form>
        </x-slot>

        <x-slot name="head">
            <th class="py-2.5 px-3">Waktu</th>
            <th class="py-2.5 px-3">Pelaku</th>
            <th class="py-2.5 px-3">Aksi</th>
            <th class="py-2.5 px-3">Rincian</th>
        </x-slot>

        @forelse($logs as $log)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <td class="py-3 px-3 whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
            <td class="py-3 px-3 font-medium text-slate-800 dark:text-white">{{ $log->user->name ?? '(sistem)' }}</td>
            <td class="py-3 px-3"><span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-1 text-[10px] font-bold text-slate-600 dark:text-slate-300">{{ \App\Models\AuditLog::label($log->aksi) }}</span></td>
            <td class="py-3 px-3 text-slate-600 dark:text-slate-300">{{ $log->detail ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="py-6 text-center text-slate-400">Belum ada jejak tercatat.</td>
        </tr>
        @endforelse

        @if($logs->hasPages())
        <x-slot name="pagination">
            {{ $logs->links() }}
        </x-slot>
        @endif
    </x-ss-table>
@endsection
