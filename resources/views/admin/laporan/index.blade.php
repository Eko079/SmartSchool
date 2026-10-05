@extends('layouts.admin')
@section('title', 'Laporan Pembayaran')
@section('breadcrumb', 'Laporan / Rekap Pembayaran')
@section('page-title', 'Laporan Pembayaran')
@section('page-subtitle')
    Rekap lunas, tunggakan dan arus kas TA {{ $academicYear ?? '' }}.
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
