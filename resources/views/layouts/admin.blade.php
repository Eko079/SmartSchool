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

        // Animasi chart: donat draw-in + bar grow + angka count-up, saat masuk viewport.
        // Prinsip: markup Blade = nilai akhir (aman no-JS/SEO); JS reset ke 0 lalu animasikan.
        function fmtCountUp(v, kind) {
            if (kind === 'rp') return 'Rp ' + Math.round(v).toLocaleString('id-ID');
            if (kind === 'pct1') return v.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + '%';
            return Math.round(v).toLocaleString('id-ID');
        }
        function prepScope(scope) {
            scope.querySelectorAll('circle.ss-donut-seg').forEach(function (seg) {
                var circ = parseFloat(seg.getAttribute('data-circ') || '87.96');
                var off = parseFloat(seg.getAttribute('data-off') || '0');
                seg.style.transition = 'none';
                seg.setAttribute('stroke-dasharray', '0 ' + circ);
                seg.setAttribute('stroke-dashoffset', String(-off));
            });
            scope.querySelectorAll('.ss-bar[data-bar-h]').forEach(function (bar) {
                bar.style.transition = 'none';
                bar.style.height = '0%';
            });
            scope.querySelectorAll('[data-count-up]').forEach(function (el) {
                el.textContent = fmtCountUp(0, el.getAttribute('data-count-fmt') || 'int');
            });
            scope.querySelectorAll('[data-donut-count]').forEach(function (el) {
                var decimals = parseInt(el.getAttribute('data-donut-decimals') || '0', 10) || 0;
                el.textContent = (0).toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + '%';
            });
            // Paksa reflow agar state awal 0% ke-paint sebelum transisi jalan.
            void scope.offsetWidth;
            scope.querySelectorAll('circle.ss-donut-seg, .ss-bar[data-bar-h]').forEach(function (el) {
                el.style.transition = '';
            });
        }
        function animateDonut(scope) {
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce) return;
            scope.querySelectorAll('[data-donut-count]').forEach(function (el) {
                var raw = (el.getAttribute('data-donut-count') || '0').replace(/[^0-9.,-]/g, '').replace(',', '.');
                var target = parseFloat(raw) || 0;
                var decimals = parseInt(el.getAttribute('data-donut-decimals') || '0', 10) || 0;
                var fmt = function (v) {
                    return v.toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + '%';
                };
                var t0 = null, dur = 900;
                function frame(t) {
                    if (!t0) t0 = t;
                    var p = Math.min((t - t0) / dur, 1);
                    var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
                    el.textContent = fmt(target * eased);
                    if (p < 1) requestAnimationFrame(frame);
                }
                requestAnimationFrame(frame);
            });
            scope.querySelectorAll('circle.ss-donut-seg').forEach(function (seg) {
                var len = parseFloat(seg.getAttribute('data-len') || '0');
                var circ = parseFloat(seg.getAttribute('data-circ') || '87.96');
                seg.style.transition = 'stroke-dasharray 0.9s cubic-bezier(.22,.61,.36,1)';
                requestAnimationFrame(function () {
                    seg.setAttribute('stroke-dasharray', len + ' ' + circ);
                });
            });
            // Bar chart grow: 0% → tinggi akhir, stagger per batang.
            scope.querySelectorAll('.ss-bar[data-bar-h]').forEach(function (bar, i) {
                var h = parseFloat(bar.getAttribute('data-bar-h') || '0');
                setTimeout(function () {
                    requestAnimationFrame(function () { bar.style.height = h + '%'; });
                }, Math.min(i * 55, 660));
            });
            // Count-up kartu statistik: Rp / int / persen naik bertahap.
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
        // Tooltip hover segmen donat (bubble seragam kayak bar chart).
        (function () {
            document.querySelectorAll('[data-donut-tip]').forEach(function (box) {
                var wrap = box.parentElement;
                if (!wrap) return;
                wrap.querySelectorAll('circle.ss-donut-seg[data-seg-tip]').forEach(function (seg) {
                    seg.addEventListener('mouseenter', function () {
                        box.textContent = seg.getAttribute('data-seg-tip') || '';
                        box.classList.remove('hidden');
                    });
                    seg.addEventListener('mouseleave', function () {
                        box.classList.add('hidden');
                    });
                });
            });
        })();
        (function () {
            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce) return; // biarkan nilai akhir Blade tampil langsung
            prepScope(document);
            var seen = new WeakSet();
            function runScope(el) {
                if (seen.has(el)) return;
                seen.add(el);
                animateDonut(el);
            }
            var cards = document.querySelectorAll('.ss-card');
            if (!cards.length) return;
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) {
                            runScope(e.target);
                            io.unobserve(e.target);
                        }
                    });
                }, { threshold: 0.2 });
                cards.forEach(function (c) { io.observe(c); });
                setTimeout(function () { cards.forEach(runScope); io.disconnect(); }, 2500);
            } else {
                cards.forEach(runScope);
            }
        })();
    </script>
</body>
</html>
