<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
$selectedCat = $_SESSION['nova_ocorrencia']['categoria'] ?? 'buraco_na_via';
?>
<div class="screen-body">
    <div class="page-header">
        <a href="?page=home" class="back-btn" title="Voltar">←</a>
        <div class="page-header-info">
            <h2>Nova ocorrência</h2>
            <p>Informe os detalhes do problema</p>
        </div>
    </div>

    <div class="stepper">
        <div class="step active">
            <div class="step-num">1</div>
            <span class="step-label">Categoria</span>
        </div>
        <div class="step-divider"></div>
        <div class="step inactive">
            <div class="step-num">2</div>
            <span class="step-label">Localização</span>
        </div>
        <div class="step-divider"></div>
        <div class="step inactive">
            <div class="step-num">3</div>
            <span class="step-label">Detalhes</span>
        </div>
    </div>

    <div class="section-label">
        <strong>Categoria</strong>
        Selecione o tipo de problema
    </div>

    <?php if ($flash): ?>
        <div style="padding: 0 20px 10px;">
            <div class="flash-message flash-<?= sanitize($flash['type']) ?>" style="padding: 12px; border-radius: 8px; font-size: 13px;">
                <span><?= sanitize($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <form action="?page=nova-ocorrencia&action=step1" method="POST" id="formStep1" style="display:flex; flex-direction:column; flex:1;">
        <?= csrf_field() ?>
        <input type="hidden" name="categoria" id="categoria_input" value="<?= sanitize($selectedCat) ?>">

        <div class="category-grid">
            <div class="category-card <?= $selectedCat === 'buraco_na_via' ? 'selected' : '' ?>" data-val="buraco_na_via">
                <span class="cat-icon">⚠️</span>
                <span class="cat-label">Buraco na via</span>
            </div>
            <div class="category-card <?= $selectedCat === 'iluminacao_publica' ? 'selected' : '' ?>" data-val="iluminacao_publica">
                <span class="cat-icon">💡</span>
                <span class="cat-label">Iluminação pública</span>
            </div>
            <div class="category-card <?= $selectedCat === 'alagamento' ? 'selected' : '' ?>" data-val="alagamento">
                <span class="cat-icon">💧</span>
                <span class="cat-label">Alagamento</span>
            </div>
            <div class="category-card <?= $selectedCat === 'terreno_baldio' ? 'selected' : '' ?>" data-val="terreno_baldio">
                <span class="cat-icon">🌿</span>
                <span class="cat-label">Terreno baldio</span>
            </div>
            <div class="category-card <?= $selectedCat === 'limpeza_urbana' ? 'selected' : '' ?>" data-val="limpeza_urbana">
                <span class="cat-icon">🗑️</span>
                <span class="cat-label">Limpeza urbana</span>
            </div>
            <div class="category-card <?= $selectedCat === 'outros' ? 'selected' : '' ?>" data-val="outros">
                <span class="cat-icon">•••</span>
                <span class="cat-label">Outros</span>
            </div>
        </div>

        <div class="bottom-action">
            <button type="submit" id="btnProximo" class="btn-primary">Próximo</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.category-card');
    const input = document.getElementById('categoria_input');

    cards.forEach(card => {
        card.addEventListener('click', function() {
            cards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const val = this.getAttribute('data-val');
            if (input && val) {
                input.value = val;
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
