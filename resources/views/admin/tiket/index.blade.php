@extends('layouts.admin')
@section('title', 'Tiket Bantuan')
@section('breadcrumb', 'Lainnya / Tiket Bantuan')
@section('page-title', 'Tiket Bantuan Wali')
@section('page-subtitle', 'Balas, tutup, dan buka kembali tiket wali.')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([['label' => 'Terbuka', 'val' => $stats['open'] ?? 0, 'cls' => 'text-amber-600'], ['label' => 'Dijawab', 'val' => $stats['answered'] ?? 0, 'cls' => 'text-blue-600'], ['label' => 'Ditutup', 'val' => $stats['closed'] ?? 0, 'cls' => 'text-emerald-600']] as $s)
        <div class="ss-card">
            <div class="text-xs font-medium text-slate-500">{{ $s['label'] }}</div>
            <div class="text-2xl font-bold {{ $s['cls'] }}">{{ $s['val'] }}</div>
        </div>
        @endforeach
    </div>

    <x-ss-table>
        <x-slot name="filters">
            <form method="GET" action="{{ route('admin.tiket') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor / subjek / wali..." class="ss-input !w-auto flex-1 min-w-[200px]">
                <select name="status" onchange="this.form.submit()" class="ss-input !w-auto">
                    <option value="">Semua Status</option>
                    @foreach(['open' => 'Terbuka', 'answered' => 'Dijawab', 'closed' => 'Ditutup'] as $v => $l)
                    <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
            </form>
        </x-slot>
        <x-slot name="head">
            <th class="py-2.5 px-3">Tiket</th>
            <th class="py-2.5 px-3">Wali / Siswa</th>
            <th class="py-2.5 px-3">Subjek</th>
            <th class="py-2.5 px-3 text-center">Status</th>
            <th class="py-2.5 px-3 text-right">Aksi</th>
        </x-slot>
        @forelse($tickets as $t)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition align-top">
            <td class="py-3 px-3 font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">{{ $t->ticket_number }}<div class="text-[10px] font-normal text-slate-400">{{ $t->created_at?->format('d M Y H:i') }}</div></td>
            <td class="py-3 px-3"><div class="font-medium">{{ $t->user->name ?? '-' }}</div><div class="text-[11px] text-slate-400">{{ $t->user->student->name ?? '-' }}</div></td>
            <td class="py-3 px-3">
                <div class="font-semibold">{{ $t->subject }}</div>
                <div class="text-[11px] text-slate-500">{{ $t->message }}</div>
                @foreach($t->replies as $r)
                <div class="mt-1 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2 text-[11px]">{{ $r->message }}<div class="text-[10px] text-slate-400">{{ $r->created_at?->format('d M H:i') }}</div></div>
                @endforeach
                <form method="POST" action="{{ route('admin.tiket.reply', $t->id) }}" class="mt-2 flex gap-1.5">
                    @csrf
                    <input type="text" name="message" required placeholder="Balas wali..." class="ss-input !py-1.5 text-[11px]">
                    <button class="rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-blue-700">Kirim</button>
                </form>
            </td>
            <td class="py-3 px-3 text-center"><x-ss-pill status="{{ $t->status === 'open' ? 'pending' : ($t->status === 'closed' ? 'paid' : 'aktif') }}" label="{{ ucfirst($t->status) }}" /></td>
            <td class="py-3 px-3 text-right whitespace-nowrap">
                @if($t->status !== 'closed')
                <form method="POST" action="{{ route('admin.tiket.close', $t->id) }}" class="inline">@csrf<button class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold hover:bg-slate-50">Tutup</button></form>
                @else
                <form method="POST" action="{{ route('admin.tiket.reopen', $t->id) }}" class="inline">@csrf<button class="rounded-md border border-slate-200 dark:border-slate-700 px-2 py-1 text-[11px] font-semibold hover:bg-slate-50">Buka</button></form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="py-6 text-center text-slate-400">Belum ada tiket.</td></tr>
        @endforelse
        <x-slot name="pagination">{{ $tickets->links() }}</x-slot>
    </x-ss-table>
@endsection
