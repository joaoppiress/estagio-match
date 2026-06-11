/* Runs synchronously in <head> to apply saved accessibility prefs before paint
   (prevents a flash of the wrong theme / font size). */
(function () {
    try {
        var p = JSON.parse(localStorage.getItem('em-prefs')) || {};
        var r = document.documentElement;
        r.setAttribute('data-theme', p.theme === 'dark' ? 'dark' : 'light');
        r.setAttribute('data-contrast', p.contrast === 'high' ? 'high' : 'normal');
        if (p.fontScale) r.style.setProperty('--font-scale', String(p.fontScale));
        if (p.motion === 'reduce') r.setAttribute('data-motion', 'reduce');
    } catch (e) { /* no-op */ }
})();
