<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') | SmartSchool Mobile</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/mobile.css', 'resources/js/mobile.js'])
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('ss-dark');
                if (saved === '1' || (saved === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body class="m-body">
    <div class="m-shell">
        {{-- ===== HERO: gradient navy -> biru, glow, top row, revenue, KPI ===== --}}
        <header class="m-hero">
            <div class="m-glow m-glow-1"></div>
            <div class="m-glow m-glow-2"></div>

            <div class="m-toprow">
                <div class="m-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
                <div class="m-meta">
                    <div class="m-hi">Halo, {{ explode(' ', Auth::user()->name ?? 'Admin')[0] }}</div>
                    <div class="m-name">{{ $schoolName ?? config('app.name', 'SmartSchool') }} · {{ Auth::user()->name ?? 'Admin' }}</div>
                </div>
                <button type="button" class="m-iconbtn" aria-label="Notifikasi" title="Notifikasi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    @if(!empty($hasNotif))<span class="m-dot"></span>@endif
                </button>
                <button type="button" class="m-iconbtn" aria-label="Menu" onclick="document.getElementById('m-drawer').classList.toggle('open');document.getElementById('m-scrim').classList.toggle('open')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                </button>
            </div>

            @hasSection('hero')
                @yield('hero')
            @endif

            @hasSection('kpi')
                <div class="m-kpi">
                    @yield('kpi')
                </div>
            @endif
        </header>

        {{-- ===== BODY ===== --}}
        <main class="m-main">
            @hasSection('breadcrumb')
                <div class="m-crumb">@yield('breadcrumb')</div>
            @endif

            @include('mobile.partials.tabs')

            @include('mobile.partials.flash')

            @yield('content')

            <div class="m-foot">
                <div class="m-foot-tx">SmartSchool ERP · v2.4</div>
                <div class="m-foot-sub">Data nyata dari database sekolah</div>
            </div>
            <div class="m-bottompad"></div>
        </main>

        {{-- ===== BOTTOM NAV ===== --}}
        @include('mobile.partials.bottomnav')

        {{-- ===== DRAWER (menu samping mobile) ===== --}}
        <div id="m-scrim" class="m-scrim" onclick="document.getElementById('m-drawer').classList.remove('open');this.classList.remove('open')"></div>
        <aside id="m-drawer" class="m-drawer">
            <div class="m-drawer-head">
                <div class="m-avatar">S</div>
                <div>
                    <div class="m-drawer-title">SmartSchool</div>
                    <div class="m-drawer-sub">ERP Digitalisasi</div>
                </div>
            </div>
            @php
                $mMenu = [
                    ['route' => 'm.dashboard', 'label' => 'Beranda'],
                    ['route' => 'm.siswa', 'label' => 'Data Siswa'],
                    ['route' => 'm.tagihan', 'label' => 'Tagihan'],
                    ['route' => 'm.laporan', 'label' => 'Laporan'],
                    ['route' => 'm.pengaturan', 'label' => 'Pengaturan'],
                    ['route' => 'm.bantuan', 'label' => 'Bantuan'],
                ];
            @endphp
            <nav class="m-drawer-nav">
                @foreach($mMenu as $it)
                    <a href="{{ Route::has($it['route']) ? route($it['route']) : '#' }}"
                       class="m-drawer-link {{ request()->routeIs($it['route']) ? 'active' : '' }}">{{ $it['label'] }}</a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="m-drawer-foot">
                @csrf
                <button type="submit" class="m-drawer-out">Keluar</button>
            </form>
        </aside>
    </div>
</body>
</html>
