@extends('layouts.admin')
@section('title', 'Kenaikan Kelas')
@section('page-title', 'Kenaikan Kelas')
@section('page-subtitle', 'Naikkan siswa massal setahun sekali. Coret yang tinggal kelas.')

@section('content')
    <div class="ss-card space-y-3">
        <form method="GET" action="{{ route('admin.kenaikan') }}" class="flex flex-wrap items-center gap-2 text-xs">
            <label class="font-semibold text-slate-700 dark:text-slate-300">Tahun ajaran tujuan</label>
            <select name="academic_year" onchange="this.form.submit()" class="ss-input !py-2">
                @foreach($options as $ta)
                    <option value="{{ $ta }}" {{ $target === $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
            @if($doneAt)
                <span class="rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Sudah diproses {{ $doneAt }}</span>
            @endif
        </form>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" id="kenaikan-search" placeholder="Cari nama / NIS..." autocomplete="off" class="ss-input text-xs !py-2 !pl-9 w-56">
        </div>
    </div>

    <form id="kenaikan-form" method="POST" action="{{ route('admin.kenaikan.process') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="academic_year" value="{{ $target }}">

        @php
            $groupTitle = ['naik' => 'Naik Kelas', 'lulus' => 'Lulus', 'tanpa_kelas' => 'Kelas Tujuan Belum Ada'];
        @endphp
        @foreach(['naik', 'lulus', 'tanpa_kelas'] as $key)
        <div class="ss-card space-y-2" data-group="{{ $key }}">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-sm font-bold text-slate-800 dark:text-white">{{ $groupTitle[$key] }} ({{ count($groups[$key]) }})</h2>
                @if($key !== 'tanpa_kelas' && count($groups[$key]))
                    <div class="flex items-center gap-3 text-[11px]">
                        <button type="button" onclick="kenaikanCheck('{{ $key }}', true)" class="text-blue-600 font-semibold">Pilih Semua</button>
                        <button type="button" onclick="kenaikanCheck('{{ $key }}', false)" class="text-slate-400 font-semibold">Hapus Semua</button>
                    </div>
                @endif
            </div>
            @if($key === 'tanpa_kelas' && count($groups[$key]))
                <p class="text-[11px] text-amber-700 dark:text-amber-300">Buatkan dulu kelas tujuannya di Data Siswa, baru jalankan lagi.</p>
            @endif
            <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($groups[$key] as $row)
                    @php $st = $row['student']; @endphp
                    <label class="kenaikan-row flex items-center gap-3 py-2 px-1 text-xs cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 rounded-lg">
                        @if($key === 'tanpa_kelas')
                            <input type="checkbox" disabled class="rounded text-slate-300">
                        @else
                            <input type="checkbox" name="students[]" value="{{ $st->id }}" {{ $row['done'] ? '' : 'checked' }} {{ $row['done'] ? 'disabled' : '' }} class="kenaikan-check rounded text-blue-600" data-group-check="{{ $key }}">
                        @endif
                        <span class="flex-1 min-w-0">
                            <span class="block font-semibold text-slate-800 dark:text-white truncate">{{ $st->name }} <span class="font-normal text-slate-400">({{ $st->nis }})</span></span>
                            <span class="block text-[11px] text-slate-400">
                                {{ $st->classRoom->name ?? '-' }}
                                @if($key === 'naik') → <span class="font-semibold text-emerald-600">{{ $row['target']->name }}</span>@endif
                                @if($key === 'lulus') → <span class="font-semibold text-slate-500">Lulus</span>@endif
                                @if($row['done']) • <span class="text-emerald-600 font-semibold">sudah diproses</span>@endif
                            </span>
                        </span>
                    </label>
                @empty
                    <p class="py-4 text-center text-xs text-slate-400">Tidak ada.</p>
                @endforelse
            </div>
        </div>
        @endforeach

        <div class="ss-card flex flex-wrap items-center justify-between gap-3">
            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300 cursor-pointer">
                <input type="checkbox" name="force" value="1" class="rounded text-blue-600">
                Jalankan lagi untuk yang belum diproses
                <span class="text-slate-400" title="Centang ini bila proses sebelumnya belum tuntas">?</span>
            </label>
            <button type="submit" onclick="return confirm('Jalankan kenaikan kelas {{ $target }} untuk yang dicentang?')" class="ss-btn-primary !py-2.5 px-6 text-xs">Proses Kenaikan</button>
        </div>
    </form>

    <script>
    function kenaikanCheck(group, on) {
        document.querySelectorAll('.kenaikan-check[data-group-check="' + group + '"]').forEach(function (c) {
            if (!c.disabled) c.checked = on;
        });
    }
    (function () {
        var input = document.getElementById('kenaikan-search');
        if (!input) return;
        var timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                var q = input.value.trim().toLowerCase();
                document.querySelectorAll('.kenaikan-row').forEach(function (r) {
                    r.style.display = (!q || (r.innerText || '').toLowerCase().indexOf(q) !== -1) ? '' : 'none';
                });
            }, 120);
        });
    })();
    </script>
@endsection
