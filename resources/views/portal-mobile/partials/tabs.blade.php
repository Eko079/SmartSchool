{{-- Tabs portal-mobile: Beranda / Tagihan / Riwayat / Profil / Lainnya --}}
@php
    $pmTabs = [
        ['route' => 'portal.dashboard', 'label' => 'Beranda'],
        ['route' => 'portal.tagihan', 'label' => 'Tagihan'],
        ['route' => 'portal.riwayat', 'label' => 'Riwayat'],
        ['route' => 'portal.profil', 'label' => 'Profil'],
        ['route' => 'portal.pengaturan', 'label' => 'Lainnya'],
    ];
@endphp
<div class="m-tabs" role="tablist" aria-label="Navigasi portal mobile">
    @foreach($pmTabs as $t)
        <a href="{{ Route::has($t['route']) ? route($t['route']) : '#' }}"
           class="m-tab {{ request()->routeIs($t['route'] . '*') ? 'active' : '' }}">{{ $t['label'] }}</a>
    @endforeach
</div>
