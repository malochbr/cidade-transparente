/* ============================================================
   CIDADE TRANSPARENTE — JAVASCRIPT VANILLA
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Auto-fechar mensagens flash após 4 segundos
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function (flash) {
        setTimeout(function () {
            flash.style.opacity = '0';
            flash.style.transition = 'opacity 0.5s ease';
            setTimeout(function () { flash.remove(); }, 500);
        }, 4000);
    });

    // 2. Máscara para CPF (000.000.000-00)
    const cpfInputs = document.querySelectorAll('[data-mask="cpf"]');
    cpfInputs.forEach(function (input) {
        input.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = v;
        });
    });

    // 3. Máscara para Telefone ((00) 00000-0000)
    const phoneInputs = document.querySelectorAll('[data-mask="telefone"]');
    phoneInputs.forEach(function (input) {
        input.addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            v = v.replace(/^(\d{2})(\d)/g, '($1) $2');
            v = v.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = v;
        });
    });

    // 4. Toggle de visibilidade da senha (olho)
    const toggleBtns = document.querySelectorAll('.toggle-password-btn');
    toggleBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = '🙈';
                } else {
                    input.type = 'password';
                    this.textContent = '👁️';
                }
            }
        });
    });

    // 5. Indicador de força de senha
    const senhaInput = document.getElementById('senha_cadastro');
    const forcaMeter = document.getElementById('forca_senha_meter');
    const forcaText = document.getElementById('forca_senha_texto');

    if (senhaInput && forcaMeter && forcaText) {
        senhaInput.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            if (val.length === 0) {
                forcaText.textContent = '';
                forcaMeter.style.width = '0%';
            } else if (score <= 1) {
                forcaText.textContent = 'Senha Fraca';
                forcaText.style.color = 'var(--red)';
                forcaMeter.style.width = '33%';
                forcaMeter.style.backgroundColor = 'var(--red)';
            } else if (score === 2 || score === 3) {
                forcaText.textContent = 'Senha Média';
                forcaText.style.color = 'var(--orange)';
                forcaMeter.style.width = '66%';
                forcaMeter.style.backgroundColor = 'var(--orange)';
            } else {
                forcaText.textContent = 'Senha Forte';
                forcaText.style.color = 'var(--green)';
                forcaMeter.style.width = '100%';
                forcaMeter.style.backgroundColor = 'var(--green)';
            }
        });
    }

    // 6. Tab Switcher genérico
    const tabs = document.querySelectorAll('.tab[data-tab]');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.closest('.tab-container') || document;
            parent.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            parent.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
            
            this.classList.add('active');
            const targetContent = document.getElementById(this.getAttribute('data-tab'));
            if (targetContent) targetContent.style.display = 'block';
        });
    });
});

// Lightbox functions
function openLightbox(src, type = 'imagem') {
    const modal = document.getElementById('lightboxModal');
    const content = document.getElementById('lightboxContent');
    if (!modal || !content) return;

    if (type === 'video') {
        content.innerHTML = `<video src="${src}" controls autoplay style="max-width:90%; max-height:80vh;"></video>`;
    } else {
        content.innerHTML = `<img src="${src}" alt="Mídia" style="max-width:90%; max-height:80vh;">`;
    }
    modal.style.display = 'flex';
}

function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    if (modal) modal.style.display = 'none';
}
