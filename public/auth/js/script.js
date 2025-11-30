// Fungsi untuk toggle tema
function toggleTheme() {
    const html = document.documentElement;
    const icon = document.getElementById('theme-icon');
    const button = document.getElementById('theme-toggle-btn');

    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        icon.textContent = '🌙';
        button.title = 'Aktifkan mode gelap';
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        icon.textContent = '☀️';
        button.title = 'Aktifkan mode terang';
    }
}

// Saat halaman dimuat, sesuaikan tema dari localStorage
window.addEventListener('DOMContentLoaded', () => {
    const theme = localStorage.getItem('theme');
    const html = document.documentElement;
    const icon = document.getElementById('theme-icon');
    const button = document.getElementById('theme-toggle-btn');

    if (theme === 'dark') {
        html.classList.add('dark');
        icon.textContent = '☀️';
        button.title = 'Aktifkan mode terang';
    } else {
        html.classList.remove('dark');
        icon.textContent = '🌙';
        button.title = 'Aktifkan mode gelap';
    }
});

function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = '🙈';
    } else {
        input.type = 'password';
        icon.textContent = '👁️';
    }
}


