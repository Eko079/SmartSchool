// SmartSchool Mobile JS — live filter + theme + drawer (tanpa framework).
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

    // Toggle tema gelap/terang (sinkron dgn desktop via ss-dark).
    window.mToggleDark = function () {
        var root = document.documentElement;
        root.classList.toggle('dark');
        try { localStorage.setItem('ss-dark', root.classList.contains('dark') ? '1' : '0'); } catch (e) {}
    };
})();
