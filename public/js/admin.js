// Sidebar toggle para móvil
document.querySelector('.sidebar-toggle')?.addEventListener('click', function() {
    document.querySelector('.admin-sidebar')?.classList.toggle('open');
});

// Confirmar eliminación
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('¿Estás seguro de eliminar este elemento?')) {
            e.preventDefault();
        }
    });
});