{{-- Bottom nav mobile: Beranda / Siswa / Tagihan / Laporan / Lainnya --}}
@php
    $mNav = [
        ['route' => 'admin.dashboard', 'label' => 'Beranda', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['route' => 'admin.siswa', 'label' => 'Siswa', 'icon' => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75'],
        ['route' => 'admin.billing', 'label' => 'Tagihan', 'icon' => 'M6 2h12v20l-3-2-3 2-3-2-3 2zM9 7h6M9 11h6'],
        ['route' => 'admin.laporan', 'label' => 'Laporan', 'icon' => 'M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M9 13h6M9 17h6'],
        ['route' => 'admin.pengaturan', 'label' => 'Lainnya', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
    ];
@endphp
<nav class="m-bottomnav" aria-label="Navigasi bawah">
    @foreach($mNav as $n)
        <a href="{{ Route::has($n['route']) ? route($n['route']) : '#' }}"
           class="m-bnav-item {{ request()->routeIs($n['route']) ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $n['icon'] }}"/></svg>
            <span>{{ $n['label'] }}</span>
        </a>
    @endforeach
</nav>
