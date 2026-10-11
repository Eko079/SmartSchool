@extends('layouts.admin')
@section('title', 'Kasir')
@section('page-title', 'Kasir')
@section('page-subtitle', 'Terima pembayaran tunai / transfer langsung di meja bendahara.')
@section('content')
    <div class="ss-card space-y-4 shadow-xs">
        <form method="GET" action="{{ route('admin.kasir') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari NIS / nama / nomor tagihan..."
                class="ss-input text-xs !py-2 flex-1 min-w-[200px] max-w-sm">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Cari</button>
        </form>

        @if(request()->filled('q') && !$student)
            <div class="space-y-2">
                @forelse($students as $s)
                    <a href="{{ route('admin.kasir', ['q' => request('q'), 'student_id' => $s->id]) }}"
                        class="flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2.5 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <span class="font-medium text-slate-800 dark:text-white">{{ $s->name }} <span class="text-slate-400">• NIS {{ $s->nis }}</span></span>
                        <span class="text-slate-400">{{ $s->classRoom->name ?? '-' }}</span>
                    </a>
                @empty
                    <p class="py-4 text-center text-xs text-slate-400">Tidak ketemu. Coba kata kunci lain.</p>
                @endforelse
            </div>
        @endif

        @if($student)
            <div class="rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-950 px-4 py-3 text-xs">
                <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $student->name }}</div>
                <div class="text-slate-500 dark:text-slate-400">NIS {{ $student->nis }} • {{ $student->classRoom->name ?? '-' }} • Wali: {{ $student->guardian_name ?? '-' }}</div>
            </div>

            @if($bills->isEmpty())
                <p class="py-4 text-center text-xs text-emerald-600 dark:text-emerald-400 font-semibold">Semua tagihan {{ $student->name }} sudah lunas.</p>
            @else
                <form id="kasir-form" method="POST" action="{{ route('admin.kasir.bayar') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-left text-slate-400 border-b border-slate-200 dark:border-slate-800">
                                    <th class="py-2.5 px-3 w-8"><input type="checkbox" id="kasir-check-all" class="rounded text-blue-600" checked></th>
                                    <th class="py-2.5 px-3">Tagihan</th>
                                    <th class="py-2.5 px-3">No. Tagihan</th>
                                    <th class="py-2.5 px-3 text-right">Pokok</th>
                                    <th class="py-2.5 px-3 text-right">Denda</th>
                                    <th class="py-2.5 px-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bills as $b)
                                <tr class="border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <td class="py-3 px-3"><input type="checkbox" name="bill_ids[]" value="{{ $b->id }}" class="kasir-check rounded text-blue-600" data-total="{{ (int) $b->fresh_total }}" checked></td>
                                    <td class="py-3 px-3">
                                        <div class="font-medium text-slate-800 dark:text-white">{{ $b->display_name }}</div>
                                        <div class="text-[11px] text-slate-400">Jatuh tempo {{ $b->due_date ? \Carbon\Carbon::parse($b->due_date)->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3 font-mono text-[11px] text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $b->bill_code ?? '-' }}</td>
                                    <td class="py-3 px-3 text-right text-slate-600 dark:text-slate-300">Rp {{ number_format($b->amount - $b->paid_amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right {{ $b->fresh_fine > 0 ? 'text-rose-500 font-semibold' : 'text-slate-400' }}">Rp {{ number_format($b->fresh_fine, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-slate-800 dark:text-white">Rp {{ number_format($b->fresh_total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-3 rounded-xl bg-slate-50 dark:bg-slate-800 p-4">
                        <div class="space-y-2">
                            <div class="text-xs font-bold text-slate-700 dark:text-slate-200">Metode</div>
                            <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-200 cursor-pointer">
                                <input type="radio" name="method" value="tunai" class="rounded text-blue-600" checked onchange="kasirMethod()"> Tunai
                            </label>
                            <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-200 cursor-pointer">
                                <input type="radio" name="method" value="transfer" class="rounded text-blue-600" onchange="kasirMethod()"> Transfer bank
                            </label>
                            <div id="kasir-cash-row">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">Uang diterima (Rp)</label>
                                <input type="number" name="cash" id="kasir-cash" min="0" class="ss-input text-xs !py-2" placeholder="Contoh: 1000000" oninput="kasirTick()">
                            </div>
                            <div id="kasir-ref-row" style="display:none">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">No. referensi / nama pengirim <span class="text-rose-500">*</span></label>
                                <input type="text" name="ref" id="kasir-ref" maxlength="100" class="ss-input text-xs !py-2" placeholder="Contoh: TRF-88213 / Budi (BCA)">
                            </div>
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex justify-between text-slate-500 dark:text-slate-400"><span><span id="kasir-count">0</span> tagihan dipilih</span></div>
                            <div class="flex justify-between font-bold text-base text-slate-800 dark:text-white"><span>Total</span><span id="kasir-total">Rp 0</span></div>
                            <div class="flex justify-between text-slate-600 dark:text-slate-300" id="kasir-change-row"><span>Kembalian</span><span id="kasir-change" class="font-bold">Rp 0</span></div>
                            <button type="submit" class="mt-2 w-full rounded-lg bg-emerald-600 py-2.5 font-bold text-white hover:bg-emerald-700">Bayar</button>
                        </div>
                    </div>
                </form>

                <script>
                function kasirFmt(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); }
                function kasirMethod() {
                    var tunai = document.querySelector('input[name="method"]:checked').value === 'tunai';
                    document.getElementById('kasir-cash-row').style.display = tunai ? '' : 'none';
                    document.getElementById('kasir-ref-row').style.display = tunai ? 'none' : '';
                    document.getElementById('kasir-change-row').style.display = tunai ? '' : 'none';
                    kasirTick();
                }
                function kasirTick() {
                    var total = 0, n = 0;
                    document.querySelectorAll('.kasir-check').forEach(function (c) {
                        if (c.checked) { total += parseInt(c.getAttribute('data-total') || '0', 10); n++; }
                    });
                    document.getElementById('kasir-total').textContent = kasirFmt(total);
                    document.getElementById('kasir-count').textContent = n;
                    var cash = parseInt((document.getElementById('kasir-cash') || {}).value || '0', 10);
                    document.getElementById('kasir-change').textContent = kasirFmt(Math.max(0, cash - total));
                    var all = document.getElementById('kasir-check-all');
                    if (all) {
                        var boxes = document.querySelectorAll('.kasir-check');
                        all.checked = boxes.length > 0 && n === boxes.length;
                    }
                }
                document.getElementById('kasir-check-all').addEventListener('change', function () {
                    var on = this.checked;
                    document.querySelectorAll('.kasir-check').forEach(function (c) { c.checked = on; });
                    kasirTick();
                });
                document.querySelectorAll('.kasir-check').forEach(function (c) { c.addEventListener('change', kasirTick); });
                document.getElementById('kasir-form').addEventListener('submit', function (e) {
                    var total = 0, n = 0;
                    document.querySelectorAll('.kasir-check').forEach(function (c) {
                        if (c.checked) { total += parseInt(c.getAttribute('data-total') || '0', 10); n++; }
                    });
                    if (!n) { e.preventDefault(); ssAlert('Centang dulu tagihannya.'); return; }
                    var method = document.querySelector('input[name="method"]:checked').value;
                    var ref = ((document.getElementById('kasir-ref') || {}).value || '').trim();
                    if (method === 'tunai') {
                        var cash = parseInt((document.getElementById('kasir-cash') || {}).value || '0', 10);
                        if (!cash || cash < total) { e.preventDefault(); ssAlert('Uang diterima kurang dari total (' + kasirFmt(total) + ').'); return; }
                    } else {
                        if (!ref) { e.preventDefault(); ssAlert('Isi dulu no. referensi / nama pengirim transfer.'); return; }
                    }
                    e.preventDefault();
                    var bayarText = method === 'tunai'
                        ? kasirFmt(parseInt(document.getElementById('kasir-cash').value || '0', 10))
                        : kasirFmt(total) + ' via transfer (ref: ' + ref + ')';
                    ssConfirm('Terima ' + bayarText + ' untuk ' + n + ' tagihan ' + '({{ addslashes($student->name) }})?', 'Bayar').then(function (ok) {
                        if (ok) document.getElementById('kasir-form').submit();
                    });
                });
                kasirTick();
                </script>
            @endif
        @elseif(!request()->filled('q'))
            <p class="py-6 text-center text-xs text-slate-400">Cari siswa dulu untuk mulai transaksi.</p>
        @endif
    </div>
@endsection
