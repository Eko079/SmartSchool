@extends('layouts.portal')
@section('title', 'Bantuan')
@section('breadcrumb', 'Portal / Bantuan')
@section('page-title', 'Pusat Bantuan')
@section('page-subtitle', 'Cara bayar, kuitansi & kontak bendahara.')

@section('content')
    <div class="rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-800 p-6 text-white space-y-3 shadow-md">
        <h2 class="text-lg font-bold">Bingung cara bayar?</h2>
        <p class="text-xs text-blue-100">Cari panduan bayar VA / QRIS & unduh kuitansi</p>
        <form id="help-search-form" class="flex items-center gap-2 max-w-xl">
            <input type="text" id="help-search" placeholder="Cari: cara bayar VA, QRIS, kuitansi..." autocomplete="off" class="w-full rounded-xl bg-white text-slate-800 text-xs py-2.5 px-4 outline-none">
            <button type="submit" class="rounded-xl bg-blue-500 hover:bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition">Cari</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('portal.tagihan') }}" class="ss-card flex flex-col justify-between gap-3 hover:border-blue-500 transition">
            <div class="space-y-1.5">
                <h3 class="text-sm font-bold">Cara Bayar</h3>
                <p class="text-xs text-slate-500">Bayar via VA, QRIS & cek status lunas.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600">Lihat tagihan →</span>
        </a>
        <a href="{{ route('portal.riwayat') }}" class="ss-card flex flex-col justify-between gap-3 hover:border-blue-500 transition">
            <div class="space-y-1.5">
                <h3 class="text-sm font-bold">Kuitansi</h3>
                <p class="text-xs text-slate-500">Unduh & cetak bukti pembayaran.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600">Buka riwayat →</span>
        </a>
        <div class="ss-card flex flex-col justify-between gap-3">
            <div class="space-y-1.5">
                <h3 class="text-sm font-bold">Kontak Bendahara</h3>
                <p class="text-xs text-slate-500">{{ $siteBrand['school_phone'] ?? '(021) 5090-1234' }} • {{ $siteBrand['school_email'] ?? 'support@smartschool.id' }}</p>
            </div>
            <span class="text-xs text-slate-400">Jam layanan 08.00–17.00</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="ss-card lg:col-span-2 space-y-2">
            <h2 class="text-sm font-bold">Pertanyaan Umum (FAQ)</h2>
            <div id="faq-list" class="space-y-2">
                @forelse($faqs as $faq)
                <details data-faq class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                    <summary class="text-xs font-semibold cursor-pointer">{{ $faq->question }}</summary>
                    <p class="text-xs text-slate-500 mt-1.5">{{ $faq->answer }}</p>
                </details>
                @empty
                <p class="text-xs text-slate-400">Belum ada FAQ.</p>
                @endforelse
            </div>
        </div>

        <div class="ss-card space-y-3">
            <h2 class="text-sm font-bold">Buat Tiket Bantuan</h2>
            <p class="text-xs text-slate-500">Dapat nomor TKT + SLA 1x24 jam.</p>
            <form method="POST" action="{{ route('portal.bantuan.store') }}" class="space-y-2">
                @csrf
                <input type="text" name="subject" class="ss-input" placeholder="Subjek" required>
                <textarea name="message" rows="3" class="ss-input" placeholder="Ceritakan kendala..." required></textarea>
                <button type="submit" class="ss-btn-primary !py-2.5 !text-sm">Kirim Tiket</button>
            </form>
            <div class="space-y-2 border-t border-slate-200 dark:border-slate-700 pt-3">
                @forelse($tickets as $ticket)
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-bold">{{ $ticket->ticket_number }}</span>
                        <x-ss-pill status="{{ $ticket->status === 'open' ? 'pending' : 'paid' }}" label="{{ ucfirst($ticket->status) }}" />
                    </div>
                    <div class="font-semibold mt-1">{{ $ticket->subject }}</div>
                    <div class="text-slate-500">{{ $ticket->message }}</div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Belum ada tiket.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
    (function () {
        var form = document.getElementById('help-search-form');
        var input = document.getElementById('help-search');
        if (!form || !input) return;
        function applyFilter() {
            var q = input.value.trim().toLowerCase();
            document.querySelectorAll('details[data-faq]').forEach(function (d) {
                var hit = q === '' || (d.innerText || '').toLowerCase().indexOf(q) !== -1;
                d.style.display = hit ? '' : 'none';
                if (q !== '' && hit) d.open = true;
            });
        }
        input.addEventListener('input', applyFilter);
        form.addEventListener('submit', function (e) { e.preventDefault(); applyFilter(); });
    })();
    </script>
@endsection
