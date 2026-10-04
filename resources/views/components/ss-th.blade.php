{{-- Header kolom bisa-klik untuk sorting server-side (tanpa tooltip).
  Pakai bersama komponen <x-ss-table>.

  Contoh:
    <x-ss-th sort="name" label="Nama Siswa" />
    <x-ss-th sort="nis" label="NIS" align="center" />

  - sort  : kunci sorting (nis, name, class, status, latest)
  - label : teks header
  - align : left | center | right
  - baseUrl: opsional, default route halaman ini
--}}
@props(['sort', 'label', 'align' => 'left', 'baseUrl' => null])

@php
    $curSort = request('sort', 'latest');
    $curDir = request('dir') === 'asc' ? 'asc' : 'desc';
    $isActive = $curSort === $sort;
    // Klik kedua membalik arah, pindah kolom mulai dari desc (data terbaru/terbesar dulu).
    $nextDir = ($isActive && $curDir === 'desc') ? 'asc' : 'desc';
    $url = ($baseUrl ?? url()->current()) . '?' . http_build_query(array_merge(
        request()->except(['sort', 'dir', 'page']),
        ['sort' => $sort, 'dir' => $nextDir]
    ));
    $alignCls = $align === 'center' ? 'text-center' : ($align === 'right' ? 'text-right' : 'text-left');
@endphp

<th class="py-2.5 px-3 {{ $alignCls }}">
    <a href="{{ $url }}"
        class="inline-flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : '' }}">
        {{ $label }}
        <span class="text-[9px] {{ $isActive ? '' : 'opacity-40' }}">{{ $isActive ? ($curDir === 'asc' ? '▲' : '▼') : '⇅' }}</span>
    </a>
</th>
