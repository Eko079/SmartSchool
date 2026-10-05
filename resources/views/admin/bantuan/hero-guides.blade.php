{{-- Hero Search Banner --}}
<div class="rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-800 p-6 text-white space-y-3 shadow-md">
    <div class="max-w-2xl space-y-1">
        <h2 class="text-lg font-bold">Butuh bantuan cepat?</h2>
        <p class="text-xs text-blue-100">Panduan kelola siswa, tagihan, billing & laporan.</p>
    </div>
    <form id="help-search-form" class="flex items-center gap-2 max-w-xl">
        <div class="relative flex-1">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="help-search" placeholder="Cari: tambah siswa, tagihan, laporan..."
                autocomplete="off" class="w-full rounded-xl bg-white text-slate-800 text-xs py-2.5 pl-10 pr-3 outline-none shadow-xs">
        </div>
        <button type="submit" class="rounded-xl bg-blue-500 hover:bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white transition">Cari</button>
    </form>
</div>

<script>
(function () {
    var form = document.getElementById('help-search-form');
    var input = document.getElementById('help-search');
    if (!form || !input) return;
    function applyHelpFilter() {
        var q = input.value.trim().toLowerCase();
        document.querySelectorAll('details[data-faq]').forEach(function (d) {
            var hit = q === '' || (d.innerText || '').toLowerCase().indexOf(q) !== -1;
            d.style.display = hit ? '' : 'none';
            if (q !== '' && hit) d.open = true;
        });
        var empty = document.getElementById('help-live-empty');
        var anyVisible = Array.from(document.querySelectorAll('details[data-faq]')).some(function (d) {
            return d.style.display !== 'none';
        });
        if (!anyVisible && q !== '') {
            if (!empty) {
                var p = document.createElement('p');
                p.id = 'help-live-empty';
                p.className = 'text-xs text-slate-400';
                p.textContent = 'Tidak ada panduan yang cocok.';
                document.getElementById('faq-list').appendChild(p);
            }
        } else if (empty) {
            empty.remove();
        }
    }
    var t = null;
    input.addEventListener('input', function () {
        clearTimeout(t);
        t = setTimeout(applyHelpFilter, 120);
    });
    form.addEventListener('submit', function (e) { e.preventDefault(); applyHelpFilter(); });
})();
</script>

{{-- 3 Quick Guides — kartu statis navigasi (tanpa JS mati), bukan data DB --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('admin.siswa') }}" class="ss-card ss-tip flex flex-col justify-between gap-3 hover:border-blue-500 transition" data-tip="Kelola siswa">
        <div class="space-y-1.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Panduan Siswa</h3>
            <p class="text-xs text-slate-500">Tambah, edit, impor CSV & kelola status siswa.</p>
        </div>
        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400">Buka Data Siswa
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </span>
    </a>

    <a href="{{ route('admin.kategori-tagihan') }}" class="ss-card ss-tip flex flex-col justify-between gap-3 hover:border-blue-500 transition" data-tip="Atur nominal">
        <div class="space-y-1.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Kategori Tagihan</h3>
            <p class="text-xs text-slate-500">Buat SPP, seragam, kegiatan & nominal default.</p>
        </div>
        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400">Buka Kategori
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </span>
    </a>

    <a href="{{ route('admin.billing') }}" class="ss-card ss-tip flex flex-col justify-between gap-3 hover:border-blue-500 transition" data-tip="Generate massal">
        <div class="space-y-1.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 14l6-6M9 8h.01M15 14h.01M4 4h16v16H4z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800 dark:text-white">Billing & Laporan</h3>
            <p class="text-xs text-slate-500">Generate per kelas, cek bayar & unduh kas.</p>
        </div>
        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400">Buka Billing
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </span>
    </a>
</div>
