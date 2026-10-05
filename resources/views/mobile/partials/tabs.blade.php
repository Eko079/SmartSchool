{{-- Tabs mobile: Beranda / Data Siswa / Tagihan / Laporan / Lainnya --}}
@php
    $mTabs = [
        ['route' => 'm.dashboard', 'label' => 'Beranda'],
        ['route' => 'm.siswa', 'label' => 'Data Siswa'],
        ['route' => 'm.tagihan', 'label' => 'Tagihan'],
        ['route' => 'm.laporan', 'label' => 'Laporan'],
        ['route' => 'm.pengaturan', 'label' => 'Lainnya'],
    ];
@endphp
<div class="m-tabs" role="tablist" aria-label="Navigasi mobile">
    @foreach($mTabs as $t)
        <a href="{{ Route::has($t['route']) ? route($t['route']) : '#' }}"
           class="m-tab {{ request()->routeIs($t['route']) ? 'active' : '' }}">{{ $t['label'] }}</a>
    @endforeach
</div>
