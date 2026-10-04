<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SmartSchool ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Terapkan tema lebih awal (hindari kedip saat pindah halaman)
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
<body class="min-h-screen bg-[#F4F5F7] text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen bg-[#F4F5F7] dark:bg-slate-950">
        @include('partials.sidebar')

        <div class="flex flex-1 flex-col min-h-screen ml-0 lg:ml-[248px] bg-[#F4F5F7] dark:bg-slate-950">
            <main class="flex-1 p-5 space-y-3">
                @include('partials.topbar')
                @include('partials.flash')
                @yield('content')
            </main>
            @include('partials.footer')
        </div>
    </div>

    <script>
        // Status tema awal (sinkron dgn localStorage) + transisi animasi
        function syncThemeState(isDark) {
            // Topbar: tombolnya sendiri adalah switch. Sidebar: switch ada di dalam.
            var sw = document.querySelector('.theme-toggle[role="switch"], .theme-toggle [role="switch"]');
            if (sw) sw.setAttribute('aria-checked', isDark ? 'true' : 'false');
            document.querySelectorAll('.theme-toggle').forEach(function (el) {
                // title dijaga sinkron (fallback bila CSS tooltip nonaktif),
                // data-tip selalu netral agar tidak bentrok animasi.
                el.setAttribute('title', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
            });
        }
        (function () {
            var isDark = document.documentElement.classList.contains('dark');
            syncThemeState(isDark);
        })();

        function toggleDark(btn) {
            var root = document.documentElement;

            // Nyalakan transisi warna mulus khusus saat pergantian tema
            root.classList.add('theme-anim');
            clearTimeout(window.__ssThemeTimer);
            window.__ssThemeTimer = setTimeout(function () {
                root.classList.remove('theme-anim');
            }, 600);

            root.classList.toggle('dark');
            var isDark = root.classList.contains('dark');
            try { localStorage.setItem('ss-dark', isDark ? '1' : '0'); } catch (e) {}

            syncThemeState(isDark);

            // Efek klik: ripple + denting knob (ulangi animasi tiap klik)
            var host = btn ? btn.closest('.theme-toggle') : document.querySelector('.theme-toggle');
            if (host) {
                host.classList.remove('toggle-pop');
                void host.offsetWidth; // paksa reflow agar animasi bisa diulang
                host.classList.add('toggle-pop');
            }
        }

        // Mobile sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }
    </script>
</body>
</html>
