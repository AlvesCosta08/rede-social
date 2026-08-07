// ============================================================
// SISTEMA GLOBAL DE ATUALIZAÇÃO DE FOTO
// ============================================================

// Evento personalizado para atualizar fotos
const FotoEvent = {
    // Dispara o evento quando uma foto é atualizada
    atualizada: function(fotoUrl) {
        document.dispatchEvent(new CustomEvent('fotoAtualizada', {
            detail: { fotoUrl: fotoUrl }
        }));
    },
    
    // Dispara o evento quando uma foto é removida
    removida: function() {
        document.dispatchEvent(new CustomEvent('fotoRemovida'));
    }
};

// Expõe globalmente
window.FotoEvent = FotoEvent;

// ============================================================
// FUNÇÃO PARA ATUALIZAR TODAS AS FOTOS DA PÁGINA
// ============================================================

function atualizarTodasFotos(url) {
    // Atualiza todas as imagens de perfil
    document.querySelectorAll('img[id="fotoPerfil"], img[id="fotoPreview"], .avatar img, .profile-avatar img, .member-avatar img').forEach(img => {
        const timestamp = new Date().getTime();
        img.src = url + '?t=' + timestamp;
        img.style.display = 'block';
        img.onerror = function() {
            this.style.display = 'none';
            const placeholder = this.parentElement.querySelector('.placeholder, .avatar-placeholder');
            if (placeholder) {
                placeholder.style.display = 'flex';
            }
        };
    });
    
    // Oculta placeholders
    document.querySelectorAll('.placeholder, .avatar-placeholder').forEach(el => {
        el.style.display = 'none';
    });
}

function removerTodasFotos() {
    // Remove todas as imagens
    document.querySelectorAll('img[id="fotoPerfil"], img[id="fotoPreview"], .avatar img, .profile-avatar img, .member-avatar img').forEach(img => {
        img.remove();
    });
    
    // Mostra placeholders
    document.querySelectorAll('.placeholder, .avatar-placeholder').forEach(el => {
        el.style.display = 'flex';
    });
}

// Expõe globalmente
window.atualizarTodasFotos = atualizarTodasFotos;
window.removerTodasFotos = removerTodasFotos;

// ============================================================
// ESCUTA EVENTOS GLOBAIS
// ============================================================

document.addEventListener('fotoAtualizada', function(e) {
    console.log('📸 Foto atualizada globalmente:', e.detail.fotoUrl);
    atualizarTodasFotos(e.detail.fotoUrl);
});

document.addEventListener('fotoRemovida', function() {
    console.log('🗑️ Foto removida globalmente');
    removerTodasFotos();
});

console.log('🔄 Sistema global de atualização de fotos carregado');
