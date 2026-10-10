@extends('layouts.admin')
@section('title', 'Prototype Billing Generator')
@section('page-title', 'Billing Generator — Prototype')
@section('page-subtitle', 'Pratinjau redesign. Data nyata, tombol generate nonaktif.')

@section('content')
@php
    $rp = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $grouped = ($categories ?? collect())->groupBy('type');
    $typeLabel = ['bulanan' => 'Rutin Bulanan', 'semesteran' => 'Paket Semester', 'sekali' => 'Sekali Bayar', 'bebas' => 'Bebas / Cicil'];
@endphp

{{-- Mode + ringkasan atas --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="ss-card lg:col-span-2 space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">1. Pilih Mode</h2>
                <p class="text-xs text-slate-400">Satuan = 1 kategori. Paket = semua kategori semesteran sekaligus.</p>
            </div>
            <div class="flex rounded-xl border border-slate-200 dark:border-slate-700 p-1 text-xs font-semibold" role="tablist">
                <button type="button" id="pt-mode-single" onclick="ptMode('single')" class="rounded-lg bg-blue-600 px-4 py-2 text-white">Satuan</button>
                <button type="button" id="pt-mode-package" onclick="ptMode('package')" class="rounded-lg px-4 py-2 text-slate-500">Paket Semester</button>
            </div>
        </div>
        <div id="pt-package-box" class="hidden rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950 p-3 text-xs space-y-2">
            <div class="font-bold text-emerald-800 dark:text-emerald-200">Isi paket semester aktif (nominal ikut kategori)</div>
            <div class="flex flex-wrap gap-1.5" id="pt-package-chips"></div>
            <div class="text-emerald-700 dark:text-emerald-300 font-semibold" id="pt-package-total">-</div>
        </div>
    </div>
    <div class="ss-card space-y-2">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Ringkasan</h2>
        <dl class="text-xs space-y-1.5">
            <div class="flex justify-between"><dt class="text-slate-400">Mode</dt><dd class="font-bold" id="pt-sum-mode">Satuan</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Kategori</dt><dd class="font-bold text-right" id="pt-sum-cat">-</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Periode</dt><dd class="font-bold" id="pt-sum-period">-</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Target</dt><dd class="font-bold" id="pt-sum-target">-</dd></div>
            <div class="flex justify-between text-sm"><dt class="text-slate-400">Total estimasi</dt><dd class="font-bold text-blue-600" id="pt-sum-total">-</dd></div>
        </dl>
        <button type="button" disabled title="Prototype — nonaktif" class="w-full rounded-lg bg-slate-200 dark:bg-slate-700 px-4 py-2.5 text-xs font-semibold text-slate-400 cursor-not-allowed">Generate (nonaktif di prototype)</button>
    </div>
</div>

{{-- Kategori --}}
<div class="ss-card space-y-3">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">2. Pilih Kategori</h2>
            <p class="text-xs text-slate-400">Dikelompokkan per tipe. Klik kartu untuk pilih.</p>
        </div>
        <input type="text" id="pt-cat-search" placeholder="Cari kode / nama..." class="ss-input !w-auto text-xs !py-2">
    </div>
    <div id="pt-cat-groups" class="space-y-4">
        @foreach($grouped as $type => $cats)
        <div data-pt-group="{{ $type }}">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">{{ $typeLabel[$type] ?? $type }} ({{ $cats->count() }})</div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($cats as $cat)
                <button type="button" data-pt-cat data-code="{{ $cat->code }}" data-name="{{ $cat->name }}" data-amount="{{ (int) $cat->default_amount }}" data-type="{{ $cat->type }}"
                    onclick="ptPickCat(this)"
                    class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 text-left hover:border-blue-500 transition">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-bold">{{ $cat->code }}</span>
                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-500">{{ $cat->type }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 truncate">{{ $cat->name }}</div>
                    <div class="text-xs font-bold text-slate-800 dark:text-white mt-1">{{ $rp($cat->default_amount) }}</div>
                </button>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Periode + target --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="ss-card lg:col-span-2 space-y-3">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">3. Periode & Target</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
            <label class="space-y-1"><span class="font-semibold text-slate-500">Tahun Ajaran</span>
                <select id="pt-ta" class="ss-input !py-2" onchange="ptRecalc()"><option>2026/2027</option><option>2025/2026</option></select></label>
            <label class="space-y-1"><span class="font-semibold text-slate-500">Semester</span>
                <select id="pt-sem" class="ss-input !py-2" onchange="ptRecalc()"><option value="ganjil">Ganjil</option><option value="genap">Genap</option></select></label>
            <label class="space-y-1"><span class="font-semibold text-slate-500">Bulan</span>
                <select id="pt-month" class="ss-input !py-2" onchange="ptRecalc()">@for($m=1;$m<=12;$m++)<option value="{{ $m }}">{{ $m }}</option>@endfor</select></label>
            <label class="space-y-1"><span class="font-semibold text-slate-500">Jatuh tempo</span>
                <input type="date" id="pt-due" class="ss-input !py-2" value="{{ date('Y-m-d', strtotime('+14 days')) }}" onchange="ptRecalc()"></label>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
            <label class="space-y-1"><span class="font-semibold text-slate-500">Nominal / siswa</span>
                <input type="number" id="pt-amount" class="ss-input !py-2 font-bold" value="{{ (int) ($categories->first()->default_amount ?? 0) }}" oninput="ptRecalc()"></label>
            <label class="space-y-1"><span class="font-semibold text-slate-500">Cari kelas</span>
                <input type="text" id="pt-class-search" class="ss-input !py-2" placeholder="Cari kelas..." oninput="ptFilterClass()"></label>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
            <table class="w-full text-left text-xs">
                <thead><tr class="bg-slate-50 dark:bg-slate-800 text-slate-400 text-[10px] uppercase">
                    <th class="py-2 px-3"><input type="checkbox" checked onchange="ptCheckAll(this.checked)"></th>
                    <th class="py-2 px-3">Kelas</th><th class="py-2 px-3 text-right">Siswa</th><th class="py-2 px-3 text-right">Subtotal</th>
                </tr></thead>
                <tbody id="pt-class-body">
                    @foreach(($classes ?? collect()) as $cls)
                    <tr data-pt-class="{{ strtolower($cls->name) }}" class="border-t border-slate-100 dark:border-slate-800">
                        <td class="py-2 px-3"><input type="checkbox" checked data-count="{{ $cls->students_count ?? 0 }}" onchange="ptRecalc()"></td>
                        <td class="py-2 px-3 font-semibold">{{ $cls->name }}</td>
                        <td class="py-2 px-3 text-right">{{ $cls->students_count ?? 0 }}</td>
                        <td class="py-2 px-3 text-right font-bold" data-subtotal>0</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="ss-card space-y-3">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Pratinjau Hasil</h2>
        <p class="text-xs text-slate-400">Perkiraan sebelum generate. Duplikat dilewati otomatis.</p>
        <div class="rounded-xl bg-blue-50 dark:bg-blue-950 border border-blue-100 dark:border-blue-900 p-3">
            <div class="text-xs text-blue-600">Total estimasi</div>
            <div class="text-xl font-bold text-blue-700" id="pt-prev-total">-</div>
            <div class="text-[11px] text-blue-500" id="pt-prev-detail">-</div>
        </div>
        <div class="text-xs space-y-1.5">
            <div class="flex justify-between"><span class="text-slate-400">Akan dibuat</span><b id="pt-prev-new">-</b></div>
            <div class="flex justify-between"><span class="text-slate-400">Dilewati (duplikat)</span><b id="pt-prev-skip">-</b></div>
        </div>
    </div>
</div>

{{-- Riwayat inline, bukan modal --}}
<div class="ss-card space-y-3">
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Riwayat Generate Terakhir</h2>
        <span class="text-xs text-slate-400">Inline, tanpa popup</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
        @forelse(($history ?? collect())->take(6) as $h)
        <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-3 text-xs">
            <div class="font-bold text-blue-600">{{ $h->feeCategory->code ?? '-' }} · {{ $h->period_month }}/{{ $h->period_year }}</div>
            <div class="text-slate-500">{{ $h->feeCategory->name ?? '-' }}</div>
            <div class="mt-1 font-semibold">{{ $h->total }} siswa · Rp {{ number_format($h->nominal, 0, ',', '.') }}</div>
        </div>
        @empty
        <p class="text-xs text-slate-400">Belum ada riwayat.</p>
        @endforelse
    </div>
</div>

<script>
var ptModeCur = 'single', ptCat = null;
function fmtRp(n){ return 'Rp ' + Number(n||0).toLocaleString('id-ID'); }
function ptMode(m){
    ptModeCur = m;
    document.getElementById('pt-mode-single').className = 'rounded-lg px-4 py-2 ' + (m==='single' ? 'bg-blue-600 text-white' : 'text-slate-500');
    document.getElementById('pt-mode-package').className = 'rounded-lg px-4 py-2 ' + (m==='package' ? 'bg-blue-600 text-white' : 'text-slate-500');
    document.getElementById('pt-package-box').classList.toggle('hidden', m!=='package');
    ptRecalc();
}
function ptPickCat(btn){
    document.querySelectorAll('[data-pt-cat]').forEach(function(b){ b.classList.remove('!border-blue-600','!bg-blue-50'); });
    btn.classList.add('!border-blue-600','!bg-blue-50');
    ptCat = { code: btn.dataset.code, name: btn.dataset.name, amount: parseInt(btn.dataset.amount||'0',10) };
    document.getElementById('pt-amount').value = ptCat.amount;
    ptRecalc();
}
document.getElementById('pt-cat-search').addEventListener('input', function(){
    var q = this.value.toLowerCase();
    document.querySelectorAll('[data-pt-cat]').forEach(function(b){
        b.style.display = ((b.dataset.code+' '+b.dataset.name).toLowerCase().includes(q)) ? '' : 'none';
    });
});
function ptFilterClass(){
    var q = document.getElementById('pt-class-search').value.toLowerCase();
    document.querySelectorAll('#pt-class-body tr').forEach(function(tr){
        tr.style.display = (tr.dataset.ptClass||'').includes(q) ? '' : 'none';
    });
}
function ptCheckAll(on){
    document.querySelectorAll('#pt-class-body input[type=checkbox]').forEach(function(c){ c.checked = on; });
    ptRecalc();
}
function ptRecalc(){
    var amount = parseInt(document.getElementById('pt-amount').value||'0',10);
    var count = 0;
    document.querySelectorAll('#pt-class-body tr').forEach(function(tr){
        var cb = tr.querySelector('input[type=checkbox]');
        var n = parseInt(cb.dataset.count||'0',10);
        var sub = cb.checked ? n*amount : 0;
        tr.querySelector('[data-subtotal]').textContent = fmtRp(sub);
        if(cb.checked) count += n;
    });
    var total = count*amount;
    var catLabel = ptCat ? (ptCat.code+' · '+ptCat.name) : '-';
    document.getElementById('pt-sum-mode').textContent = ptModeCur==='single' ? 'Satuan' : 'Paket Semester';
    document.getElementById('pt-sum-cat').textContent = ptModeCur==='single' ? catLabel : 'Paket ('+document.querySelectorAll('#pt-package-chips span').length+' kategori)';
    document.getElementById('pt-sum-period').textContent = document.getElementById('pt-ta').value+' · '+document.getElementById('pt-sem').value;
    document.getElementById('pt-sum-target').textContent = count+' siswa';
    document.getElementById('pt-sum-total').textContent = fmtRp(total);
    document.getElementById('pt-prev-total').textContent = fmtRp(total);
    document.getElementById('pt-prev-detail').textContent = count+' siswa × '+fmtRp(amount);
    document.getElementById('pt-prev-new').textContent = count+' tagihan';
    document.getElementById('pt-prev-skip').textContent = 'dihitung saat generate';
}
(function(){
    var chips = document.getElementById('pt-package-chips');
    var sum = 0, n = 0;
    @foreach(($categories ?? collect())->where('type','semesteran') as $cat)
        chips.innerHTML += '<span class="rounded-full bg-white dark:bg-slate-800 border px-2.5 py-1 font-semibold">{{ $cat->code }} · Rp {{ number_format($cat->default_amount,0,',','.') }}</span>';
    @endforeach
    document.getElementById('pt-package-total').textContent = '{{ ($categories ?? collect())->where('type','semesteran')->count() }} kategori semesteran';
    var first = document.querySelector('[data-pt-cat]');
    if(first) ptPickCat(first);
    ptRecalc();
})();
</script>
@endsection
