<!DOCTYPE html>
<html lang="id" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') | {{ $siteBrand['school_name'] ?? 'SmartSchool' }} Portal</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
<body class="min-h-screen bg-[#F4F5F7] text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen bg-[#F4F5F7] dark:bg-slate-950">
        @include('partials.portal-sidebar')

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
        function syncThemeState(isDark) {
            var sw = document.querySelector('.theme-toggle[role="switch"], .theme-toggle [role="switch"]');
            if (sw) sw.setAttribute('aria-checked', isDark ? 'true' : 'false');
            document.querySelectorAll('.theme-toggle').forEach(function (el) {
                el.setAttribute('title', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
            });
        }
        (function () {
            var isDark = document.documentElement.classList.contains('dark');
            syncThemeState(isDark);
        })();

        function toggleDark(btn) {
            var root = document.documentElement;
            root.classList.add('theme-anim');
            clearTimeout(window.__ssThemeTimer);
            window.__ssThemeTimer = setTimeout(function () {
                root.classList.remove('theme-anim');
            }, 600);
            root.classList.toggle('dark');
            var isDark = root.classList.contains('dark');
            try { localStorage.setItem('ss-dark', isDark ? '1' : '0'); } catch (e) {}
            syncThemeState(isDark);
            var host = btn ? btn.closest('.theme-toggle') : document.querySelector('.theme-toggle');
            if (host) {
                host.classList.remove('toggle-pop');
                void host.offsetWidth;
                host.classList.add('toggle-pop');
            }
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        function fmtCountUp(v, kind) {
            if (kind === 'rp') return 'Rp ' + Math.round(v).toLocaleString('id-ID');
            if (kind === 'pct1') return v.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + '%';
            return Math.round(v).toLocaleString('id-ID');
        }
        function prepScope(scope) {
            scope.querySelectorAll('[data-count-up]').forEach(function (el) {
                el.textContent = fmtCountUp(0, el.getAttribute('data-count-fmt') || 'int');
            });
            void scope.offsetWidth;
        }
        function animateDonut(scope) {
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce) return;
            scope.querySelectorAll('[data-count-up]').forEach(function (el) {
                var target = parseFloat(el.getAttribute('data-count-up') || '0') || 0;
                var fmtKind = el.getAttribute('data-count-fmt') || 'int';
                var t0 = null, dur = 1100;
                function frame(t) {
                    if (!t0) t0 = t;
                    var p = Math.min((t - t0) / dur, 1);
                    var eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = fmtCountUp(target * eased, fmtKind);
                    if (p < 1) requestAnimationFrame(frame);
                }
                requestAnimationFrame(frame);
            });
        }
        (function () {
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce) return;
            prepScope(document);
            var cards = document.querySelectorAll('.ss-card');
            if (!cards.length) return;
            if ('IntersectionObserver' in window) {
                var seen = new WeakSet();
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting && !seen.has(e.target)) {
                            seen.add(e.target);
                            animateDonut(e.target);
                            io.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.2 });
                cards.forEach(function (c) { io.observe(c); });
                setTimeout(function () { cards.forEach(function (c) { if (!seen.has(c)) { seen.add(c); animateDonut(c); } }); io.disconnect(); }, 2500);
            } else {
                cards.forEach(animateDonut);
            }
        })();
    </script>
</body>
</html>
