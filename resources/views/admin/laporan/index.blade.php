@extends('layouts.admin')
@section('title', 'Laporan Pembayaran')
@section('breadcrumb', 'Laporan / Rekap Pembayaran')
@section('page-title', 'Laporan Pembayaran')
@section('page-subtitle')
    Rekap lunas, tunggakan & arus kas TA {{ $academicYear ?? '' }}.
@endsection

@section('topbar-actions')
<div class="flex items-center gap-2">
    <button type="button" title="Cetak PDF" onclick="window.print()"
        class="ss-tip ss-tip-bottom flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 shadow-xs"
        data-tip="Cetak PDF">
        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Export PDF
    </button>
    <button type="button" title="Unduh Excel" id="btn-export-excel"
        class="ss-tip ss-tip-bottom flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 shadow-xs"
        data-tip="Unduh Excel">
        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Excel
    </button>
</div>
@endsection

@section('content')
    @include('admin.laporan.stats')
    @include('admin.laporan.charts')
    @include('admin.laporan.table')
    @include('admin.laporan.invoice-modal')

    {{-- Export Excel CSV dari baris yang tampil (tanpa backend tambahan) --}}
    <script>
    (function () {
        var btn = document.getElementById('btn-export-excel');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var tbody = document.getElementById('ss-table-body');
            if (!tbody) return;
            var rows = [['Invoice', 'Siswa', 'NIS', 'Kategori', 'Nominal', 'Tgl Bayar', 'Metode', 'Status']];
            tbody.querySelectorAll('tr').forEach(function (tr) {
                if (tr.style.display === 'none' || tr.querySelector('td[colspan]')) return;
                var c = tr.querySelectorAll('td');
                if (c.length < 7) return;
                var siswa = (c[1].innerText || '').split('\n');
                rows.push([
                    (c[0].innerText || '').trim(),
                    (siswa[0] || '').trim(),
                    ((siswa[1] || '').replace('NIS:', '').split('•')[0] || '').trim(),
                    (c[2].innerText || '').trim(),
                    (c[3].innerText || '').trim(),
                    (c[4].innerText || '').trim(),
                    (c[5].innerText || '').trim(),
                    (c[6].innerText || '').trim(),
                ]);
            });
            var csv = rows.map(function (r) {
                return r.map(function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(',');
            }).join('\n');
            var blob = new Blob(["\ufeff" + csv], { type: 'text/csv;charset=utf-8;' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'laporan-pembayaran.csv';
            a.click();
            URL.revokeObjectURL(a.href);
        });
    })();
    </script>
@endsection
