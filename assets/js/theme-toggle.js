document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn-theme-toggle');
    if (!btn) return;

    const iconSun  = btn.querySelector('.theme-icon-sun');
    const iconMoon = btn.querySelector('.theme-icon-moon');

    function setTheme(theme) {
        const isDark = theme === 'dark';

        document.body.classList.toggle('theme-dark', isDark);
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';

        try {
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (e) {}

        if (iconSun && iconMoon) {
            iconSun.style.display  = isDark ? 'none' : 'inline';
            iconMoon.style.display = isDark ? 'inline' : 'none';
        }
    }

    // Tema inicial baseado no que já foi aplicado lá em cima
    const initialIsDark = document.body.classList.contains('theme-dark');

    if (initialIsDark) {
        setTheme('dark');
    } else {
        // Se não tiver classe ainda, respeita o saved/prefers-color-scheme
        let saved = null;
        try {
            saved = localStorage.getItem('theme');
        } catch (e) {}

        if (saved === 'dark' || saved === 'light') {
            setTheme(saved);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            setTheme('dark');
        } else {
            setTheme('light');
        }
    }

    btn.addEventListener('click', function () {
        const isDarkNow = document.body.classList.contains('theme-dark');
        setTheme(isDarkNow ? 'light' : 'dark');
    });
});
