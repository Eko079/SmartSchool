<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') | {{ $siteBrand['school_name'] ?? 'SmartSchool' }} Portal</title>
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
        <header class="m-hero">
            <div class="m-glow m-glow-1"></div>
            <div class="m-glow m-glow-2"></div>

            <div class="m-toprow">
                @php $pmUser = Auth::guard('wali')->user() ?? Auth::user(); @endphp
                <div class="m-avatar">{{ strtoupper(substr($pmUser->student->name ?? $pmUser->name ?? 'W', 0, 1)) }}</div>
                <div class="m-meta">
                    <div class="m-hi">Halo, {{ explode(' ', $pmUser->name ?? 'Wali')[0] }}</div>
                    <div class="m-name">{{ $pmUser->student->name ?? $siteBrand['school_name'] ?? 'SmartSchool' }}</div>
                </div>
                <button type="button" class="m-iconbtn" aria-label="Notifikasi" title="Notifikasi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    @if(!empty($hasNotif))<span class="m-dot"></span>@endif
                </button>
                <button type="button" class="m-iconbtn" aria-label="Mode gelap terang" title="Mode gelap terang" onclick="mToggleDark(this)">
                    <svg class="m-ic-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                    <svg class="m-ic-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/></svg>
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

        <main class="m-main">
            @hasSection('breadcrumb')
                <div class="m-crumb">@yield('breadcrumb')</div>
            @endif

            @include('portal-mobile.partials.tabs')
            @include('mobile.partials.flash')

            @yield('content')

            <div class="m-foot">
                <div class="m-foot-tx">© {{ date('Y') }} {{ $siteBrand['school_name'] ?? 'SmartSchool' }} • Portal</div>
            </div>
            <div class="m-bottompad"></div>
        </main>

        @include('portal-mobile.partials.bottomnav')

        <div id="m-scrim" class="m-scrim" onclick="document.getElementById('m-drawer').classList.remove('open');this.classList.remove('open')"></div>
        <aside id="m-drawer" class="m-drawer">
            <div class="m-drawer-head">
                <div class="m-avatar">{{ strtoupper(substr($siteBrand['school_name'] ?? 'S', 0, 1)) }}</div>
                <div>
                    <div class="m-drawer-title">{{ $siteBrand['school_name'] ?? 'SmartSchool' }}</div>
                    <div class="m-drawer-sub">Portal Siswa</div>
                </div>
            </div>
            @php
                $pmMenu = [
                    ['route' => 'portal.dashboard', 'label' => 'Beranda'],
                    ['route' => 'portal.tagihan', 'label' => 'Tagihan'],
                    ['route' => 'portal.riwayat', 'label' => 'Riwayat Pembayaran'],
                    ['route' => 'portal.profil', 'label' => 'Profile'],
                    ['route' => 'portal.pengaturan', 'label' => 'Pengaturan'],
                    ['route' => 'portal.bantuan', 'label' => 'Bantuan'],
                ];
            @endphp
            <nav class="m-drawer-nav">
                @foreach($pmMenu as $it)
                    <a href="{{ Route::has($it['route']) ? route($it['route']) : '#' }}"
                       class="m-drawer-link {{ request()->routeIs($it['route'] . '*') ? 'active' : '' }}">{{ $it['label'] }}</a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('portal.logout') }}" class="m-drawer-foot">
                @csrf
                <button type="submit" class="m-drawer-out">Keluar</button>
            </form>
        </aside>
    </div>
</body>
</html>
