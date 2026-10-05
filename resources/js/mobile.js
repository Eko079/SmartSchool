// SmartSchool Mobile JS — live filter + theme + animasi grafik (tanpa framework).
(function () {
    // Live filter daftar kartu (debounce 120ms, Enter dicegah).
    var input = document.querySelector('[data-m-filter]');
    if (input) {
        var target = input.getAttribute('data-m-filter');
        var timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                var q = input.value.toLowerCase();
                document.querySelectorAll(target).forEach(function (el) {
                    el.style.display = el.innerText.toLowerCase().includes(q) ? '' : 'none';
                });
            }, 120);
        });
        input.form && input.form.addEventListener('submit', function (e) { e.preventDefault(); });
    }

    // Toggle tema gelap/terang (sinkron dgn desktop via ss-dark) + ganti ikon.
    function syncMThemeIcon() {
        var isDark = document.documentElement.classList.contains('dark');
        document.querySelectorAll('.m-iconbtn .m-ic-sun, .m-iconbtn .m-ic-moon').forEach(function (svg) {
            var isSun = svg.classList.contains('m-ic-sun');
            svg.style.display = (isDark && isSun) || (!isDark && !isSun) ? 'none' : '';
        });
    }
    window.mToggleDark = function () {
        var root = document.documentElement;
        root.classList.add('m-theme-anim');
        clearTimeout(window.__mThemeTimer);
        window.__mThemeTimer = setTimeout(function () { root.classList.remove('m-theme-anim'); }, 500);
        root.classList.toggle('dark');
        try { localStorage.setItem('ss-dark', root.classList.contains('dark') ? '1' : '0'); } catch (e) {}
        syncMThemeIcon();
    };
    syncMThemeIcon();

    // ---------- Animasi ala web desktop: count-up + bar grow + seg fill ----------
    function fmtM(v, kind) {
        if (kind === 'rp-short') {
            var trim = function (x) { return rtrimNum(x); };
            if (v >= 1000000000) return 'Rp ' + trim(v / 1000000000) + ' M';
            if (v >= 1000000) return 'Rp ' + trim(v / 1000000) + ' Jt';
            if (v >= 1000) return 'Rp ' + trim(v / 1000) + ' rb';
            return 'Rp ' + Math.round(v).toLocaleString('id-ID');
        }
        if (kind === 'pct1') return v.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + '%';
        return Math.round(v).toLocaleString('id-ID');
    }
    function rtrimNum(x) {
        var s = x.toFixed(1).replace('.', ',');
        return s.replace(/,0$/, '').replace(/(\,\d)0$/, '$1');
    }
    function prepM(scope) {
        scope.querySelectorAll('[data-m-bar]').forEach(function (bar) {
            bar.style.transition = 'none';
            bar.style.height = '0px';
        });
        scope.querySelectorAll('[data-m-seg]').forEach(function (seg) {
            seg.style.transition = 'none';
            seg.style.width = '0%';
        });
        scope.querySelectorAll('[data-m-count]').forEach(function (el) {
            el.textContent = fmtM(0, el.getAttribute('data-m-fmt') || 'int');
        });
        void scope.offsetWidth;
        scope.querySelectorAll('[data-m-bar], [data-m-seg]').forEach(function (el) {
            el.style.transition = '';
        });
    }
    function animateM(scope) {
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce) return;
        scope.querySelectorAll('[data-m-count]').forEach(function (el) {
            var target = parseFloat(el.getAttribute('data-m-count') || '0') || 0;
            var kind = el.getAttribute('data-m-fmt') || 'int';
            var t0 = null, dur = 1000;
            function frame(t) {
                if (!t0) t0 = t;
                var p = Math.min((t - t0) / dur, 1);
                var eased = 1 - Math.pow(1 - p, 3);
                el.textContent = fmtM(target * eased, kind);
                if (p < 1) requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);
        });
        scope.querySelectorAll('[data-m-bar]').forEach(function (bar, i) {
            var h = parseFloat(bar.getAttribute('data-m-bar') || '0');
            setTimeout(function () {
                requestAnimationFrame(function () { bar.style.height = h + 'px'; });
            }, Math.min(i * 60, 600));
        });
        scope.querySelectorAll('[data-m-seg]').forEach(function (seg) {
            var w = parseFloat(seg.getAttribute('data-m-seg') || '0');
            requestAnimationFrame(function () { seg.style.width = w + '%'; });
        });
    }
    (function () {
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce) return;
        prepM(document);
        var seen = new WeakSet();
        function run(el) { if (seen.has(el)) return; seen.add(el); animateM(el); }
        var cards = document.querySelectorAll('.m-card, .m-hero');
        if (!cards.length) return;
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { run(e.target); io.unobserve(e.target); }
                });
            }, { threshold: 0.2 });
            cards.forEach(function (c) { io.observe(c); });
            setTimeout(function () { cards.forEach(run); io.disconnect(); }, 2500);
        } else {
            cards.forEach(run);
        }
    })();
})();
