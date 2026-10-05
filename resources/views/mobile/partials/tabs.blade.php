{{-- Tabs mobile: Beranda / Data Siswa / Tagihan / Laporan / Lainnya --}}
@php
    $mTabs = [
        ['route' => 'admin.dashboard', 'label' => 'Beranda'],
        ['route' => 'admin.siswa', 'label' => 'Data Siswa'],
        ['route' => 'admin.billing', 'label' => 'Tagihan'],
        ['route' => 'admin.laporan', 'label' => 'Laporan'],
        ['route' => 'admin.pengaturan', 'label' => 'Lainnya'],
    ];
@endphp
<div class="m-tabs" role="tablist" aria-label="Navigasi mobile">
    @foreach($mTabs as $t)
        <a href="{{ Route::has($t['route']) ? route($t['route']) : '#' }}"
           class="m-tab {{ request()->routeIs($t['route']) ? 'active' : '' }}">{{ $t['label'] }}</a>
    @endforeach
</div>
