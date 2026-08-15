/**
 * Conexão Igreja - Scripts Principais
 */

document.addEventListener('DOMContentLoaded', function() {
    // Auto-fechar toasts
    document.querySelectorAll('.toast').forEach(function(toast) {
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(function() {
                toast.remove();
            }, 400);
        }, 5000);
    });
    
    // Confirmar logout
    document.querySelectorAll('.logout-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja sair?')) {
                e.preventDefault();
            }
        });
    });
});

// Tema claro/escuro
function toggleTheme() {
    document.body.classList.toggle('dark');
    const icon = document.getElementById('themeIcon');
    if (icon) {
        icon.classList.toggle('fa-moon');
        icon.classList.toggle('fa-sun');
    }
    localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
}

// Carregar tema salvo
if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark');
    const icon = document.getElementById('themeIcon');
    if (icon) {
        icon.classList.replace('fa-moon', 'fa-sun');
    }
}

window.toggleTheme = toggleTheme;
