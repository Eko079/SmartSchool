@extends('layouts.mobile')
@section('title', 'Tiket Bantuan')
@section('breadcrumb', 'Lainnya / Tiket')

@section('hero')
    <div class="m-rev-label">TIKET WALI MASUK</div>
    <div class="m-rev-row">
        <div class="m-rev-value" data-m-count="{{ $stats['open'] ?? 0 }}" data-m-fmt="int">{{ $stats['open'] ?? 0 }}</div>
        <span class="m-delta">terbuka</span>
    </div>
    <div class="m-rev-sub">{{ $stats['answered'] ?? 0 }} dijawab • {{ $stats['closed'] ?? 0 }} ditutup</div>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.tiket') }}" class="m-search">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tiket, wali..." aria-label="Cari tiket">
    </form>

    @forelse($tickets as $t)
        <div class="m-card">
            <div class="m-stu">
                <div class="m-stu-mid">
                    <div class="m-stu-name">{{ $t->ticket_number }} • {{ $t->subject }}</div>
                    <div class="m-stu-sub">{{ $t->user->name ?? '-' }} • {{ $t->created_at?->format('d M Y H:i') }}</div>
                    <div class="m-stu-sub">{{ $t->message }}</div>
                    @foreach($t->replies as $r)
                    <div class="m-stu-sub">↳ {{ $r->message }}</div>
                    @endforeach
                </div>
                @if($t->status === 'open')<span class="m-pill m-pill-warn">Terbuka</span>
                @elseif($t->status === 'closed')<span class="m-pill m-pill-ok">Ditutup</span>
                @else<span class="m-pill m-pill-warn">Dijawab</span>@endif
            </div>
            <form method="POST" action="{{ route('admin.tiket.reply', $t->id) }}" class="m-actions">
                @csrf
                <input type="text" name="message" required placeholder="Balas..." class="m-btn" style="flex:1">
                <button class="m-btn m-btn-primary">Kirim</button>
            </form>
            <div class="m-actions">
                @if($t->status !== 'closed')
                <form method="POST" action="{{ route('admin.tiket.close', $t->id) }}">@csrf<button class="m-btn">Tutup</button></form>
                @else
                <form method="POST" action="{{ route('admin.tiket.reopen', $t->id) }}">@csrf<button class="m-btn">Buka</button></form>
                @endif
            </div>
        </div>
    @empty
        <div class="m-card"><div class="m-fdesc">Belum ada tiket.</div></div>
    @endforelse

    @if($tickets->hasPages())
        <div class="m-card"><div class="m-actions">
            @if($tickets->previousPageUrl())<a class="m-btn" href="{{ $tickets->previousPageUrl() }}">Kembali</a>@endif
            @if($tickets->nextPageUrl())<a class="m-btn m-btn-primary" href="{{ $tickets->nextPageUrl() }}">Lanjut</a>@endif
        </div></div>
    @endif
@endsection
