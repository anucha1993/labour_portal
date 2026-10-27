/* Worker portal – small behaviours, no framework. */
(function () {
    'use strict';

    // Show / hide password
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = btn.closest('label').querySelector('input');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.classList.toggle('is-on', show);
            input.focus();
        });
    });

    // Birthday field: digits only (workers type 06121997); keep slashes out of the value.
    document.querySelectorAll('input[data-password]').forEach(function (input) {
        input.addEventListener('input', function () {
            var v = input.value.replace(/[^0-9]/g, '').slice(0, 8);
            if (v !== input.value) input.value = v;
        });
    });

    // Prevent double submit + show loading state
    document.querySelectorAll('form[data-submit-once]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (!btn) return;
            if (form.dataset.submitted) { return; }
            form.dataset.submitted = '1';
            btn.classList.add('is-loading');
            btn.textContent = 'กำลังเข้าสู่ระบบ… / Signing in…';
        });
    });

    // Thin progress bar when leaving the page (feels like an app)
    var bar = document.createElement('div');
    bar.className = 'page-progress';
    document.body.appendChild(bar);
    window.addEventListener('beforeunload', function () { bar.classList.add('on'); });
    window.addEventListener('pageshow', function () { bar.classList.remove('on'); });
    document.querySelectorAll('a[target="_blank"]').forEach(function (a) {
        a.addEventListener('click', function () { bar.classList.remove('on'); });
    });

    // Auto-hide flash messages
    setTimeout(function () {
        document.querySelectorAll('.flash-ok, .flash-info').forEach(function (el) {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 450);
        });
    }, 4000);
})();
