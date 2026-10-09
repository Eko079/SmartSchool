@php
    $activeCount = ($classes ?? collect())->sum('students_count');
    $firstAmount = (int) ($categories->first()->default_amount ?? 0);
@endphp
<form id="billing-form" method="POST" action="{{ route('admin.billing.generate') }}" class="ss-card space-y-4">
    @csrf
    <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
        <h2 class="text-sm font-bold text-slate-800 dark:text-white">Parameter Tagihan Massal</h2>
        <span class="text-xs text-slate-400 font-mono">INV-[THN][BLN]-[KATEGORI]-[ID]</span>
    </div>

    {{-- Kategori tagihan selector --}}
    <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">KATEGORI TAGIHAN</label>
        @if(($categories ?? collect())->isEmpty())
            <p class="text-xs text-slate-400">Belum ada kategori aktif. <a href="{{ route('admin.kategori-tagihan') }}" class="text-blue-600 hover:underline">Buat dulu di Kategori Tagihan</a>.</p>
        @else
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            @foreach($categories ?? [] as $idx => $cat)
            <label class="flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-blue-500 cursor-pointer transition text-center has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-950 has-[:checked]:text-blue-600 dark:has-[:checked]:text-blue-400">
                <input type="radio" name="fee_category_id" value="{{ $cat->id }}" class="sr-only" {{ $idx === 0 ? 'checked' : '' }}
                    data-code="{{ $cat->code }}" data-name="{{ $cat->name }}" data-amount="{{ (int) $cat->default_amount }}"
                    onchange="billingCategoryChanged(this)">
                <span class="text-sm font-bold">{{ $cat->code }}</span>
                <span class="text-[10px] text-slate-400 font-normal truncate max-w-full">{{ $cat->name }}</span>
            </label>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Tahun Ajaran & Semester --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">TAHUN AJARAN</label>
            <select name="academic_year" class="ss-input text-xs !py-2">
                @php $curY = (int) date('Y'); $curM = (int) date('n'); $defTA = $curM >= 7 ? $curY . '/' . ($curY + 1) : ($curY - 1) . '/' . $curY; @endphp
                @foreach([$curY - 1 . '/' . $curY, $curY . '/' . ($curY + 1), ($curY + 1) . '/' . ($curY + 2)] as $ta)
                    <option value="{{ $ta }}" {{ $ta === $defTA ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">SEMESTER</label>
            <select name="semester" class="ss-input text-xs !py-2">
                <option value="ganjil" {{ (int) date('n') >= 7 ? 'selected' : '' }}>Ganjil (Jul–Des)</option>
                <option value="genap" {{ (int) date('n') < 7 ? 'selected' : '' }}>Genap (Jan–Jun)</option>
            </select>
        </div>
    </div>

    {{-- Periode & Tanggal Jatuh Tempo --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">BULAN PERIODE</label>
            <select name="period_month" id="bill-month" class="ss-input text-xs !py-2" onchange="billingRecalc()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == (int) date('n') ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">TAHUN PERIODE</label>
            <select name="period_year" id="bill-year" class="ss-input text-xs !py-2" onchange="billingRecalc()">
                @foreach([date('Y') - 1, date('Y'), date('Y') + 1] as $y)
                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">JATUH TEMPO</label>
            <input type="date" name="due_date" id="bill-due" value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="ss-input text-xs !py-2" required onchange="billingRecalc()">
        </div>
    </div>

    {{-- Nominal --}}
    <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">NOMINAL PER SISWA</label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
            <input id="input-amount" type="number" name="amount" value="{{ $firstAmount }}" min="1" class="ss-input text-xs font-bold !py-2 !pl-9" required oninput="billingRecalc()">
        </div>
    </div>

    {{-- Target Kelas --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">TARGET KELAS SISWA</label>
            <div class="flex items-center gap-3">
                <button type="button" onclick="billingCheckAll(true)" class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold cursor-pointer">Pilih Semua</button>
                <button type="button" onclick="billingCheckAll(false)" class="text-[11px] text-slate-400 font-semibold cursor-pointer">Hapus Semua</button>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            @foreach($classes ?? [] as $cls)
            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 text-xs cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
                <input type="checkbox" name="classes[]" value="{{ $cls->id }}" checked data-count="{{ $cls->students_count }}" data-name="{{ $cls->name }}"
                    class="class-checkbox rounded text-blue-600 focus:ring-0" onchange="billingRecalc()">
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $cls->name }}</span>
                <span class="text-[10px] text-slate-400">({{ $cls->students_count }})</span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Submit: buka popup konfirmasi berisi angka nyata, bukan submit langsung --}}
    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end">
        <button type="button" onclick="openConfirmModal()" class="ss-btn-primary !py-2.5 px-6 text-xs">
            Review & Generate
        </button>
    </div>
</form>

<form method="POST" action="{{ route('admin.billing.package') }}" class="ss-card space-y-3" onsubmit="return confirm('Generate 1 paket semester penuh untuk kelas terpilih? Duplikat dilewati otomatis.');">
    @csrf
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
        <div>
            <h2 class="text-sm font-bold text-slate-800 dark:text-white">Generate Paket 1 Semester (1-Klik)</h2>
            <p class="text-[11px] text-slate-400">Semua kategori semesteran aktif sekaligus • nominal ikut kategori</p>
        </div>
        <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Paket</span>
    </div>
    <div class="grid grid-cols-2 gap-2 text-xs">
        <select name="academic_year" class="ss-input !py-2">@php $cy = (int) date('Y'); @endphp @foreach([$cy - 1 . '/' . $cy, $cy . '/' . ($cy + 1)] as $ta)<option value="{{ $ta }}">{{ $ta }}</option>@endforeach</select>
        <select name="semester" class="ss-input !py-2"><option value="ganjil">Ganjil</option><option value="genap">Genap</option></select>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        @foreach(($classes ?? collect()) as $cls)
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 p-2 text-[11px] cursor-pointer"><input type="checkbox" name="classes[]" value="{{ $cls->id }}" checked class="rounded text-blue-600"> {{ $cls->name }} ({{ $cls->students_count }})</label>
        @endforeach
    </div>
    <button class="ss-btn-primary !py-2.5 text-xs">Generate Paket Semester</button>
</form>

<script>
function billingCheckedCount() {
    var n = 0;
    document.querySelectorAll('.class-checkbox:checked').forEach(function (c) {
        n += parseInt(c.getAttribute('data-count') || '0', 10);
    });
    return n;
}
function billingSelectedCategory() {
    var r = document.querySelector('input[name="fee_category_id"]:checked');
    return r ? { code: r.getAttribute('data-code'), name: r.getAttribute('data-name'), amount: r.getAttribute('data-amount') } : { code: '-', name: '-', amount: '0' };
}
function billingCategoryChanged(radio) {
    document.getElementById('input-amount').value = radio.getAttribute('data-amount');
    billingRecalc();
}
function billingCheckAll(on) {
    document.querySelectorAll('.class-checkbox').forEach(function (c) { c.checked = on; });
    billingRecalc();
}
function fmtRp(n) {
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
}
function billingRecalc() {
    var cat = billingSelectedCategory();
    var amount = parseInt(document.getElementById('input-amount').value || cat.amount || '0', 10);
    var count = billingCheckedCount();
    var total = amount * count;

    var set = function (id, v) { var el = document.getElementById(id); if (el) el.textContent = v; };
    set('step-target-count', count);
    set('sum-total', fmtRp(total));
    set('sum-detail', count + ' siswa × ' + fmtRp(amount));
    set('sum-target', count + ' siswa');
    set('sum-cat', (cat.code || '') + ' • ' + (document.getElementById('bill-month').selectedOptions[0].text + ' ' + document.getElementById('bill-year').value));
    set('sum-amount', fmtRp(amount));
    set('sum-due', document.getElementById('bill-due').value || '-');
    set('sum-valid', count + ' dari ' + count + ' target lolos validasi duplikat');
    set('btn-generate-label', 'Generate ' + count + ' Tagihan');
}
document.addEventListener('DOMContentLoaded', billingRecalc);
document.getElementById('billing-form').addEventListener('submit', function (e) {
    // Anti submit kosong: server juga menolak, tapi cegah dini di sini.
    if (typeof billingCheckedCount === 'function' && billingCheckedCount() === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 kelas target dulu.');
    }
});
</script>
