<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
$sessionData = $_SESSION['nova_ocorrencia'] ?? [];
?>
<div class="page-header">
    <a href="?page=nova-ocorrencia&step=1" class="back-btn">←</a>
    <div class="page-header-info">
        <h2>Nova ocorrência</h2>
        <p>Informe os detalhes do problema</p>
    </div>
</div>

<div class="stepper">
    <div class="step done">
        <div class="step-num">✓</div>
        <span class="step-label">Categoria</span>
    </div>
    <div class="step-divider done"></div>
    <div class="step active">
        <div class="step-num">2</div>
        <span class="step-label">Localização</span>
    </div>
    <div class="step-divider"></div>
    <div class="step inactive">
        <div class="step-num">3</div>
        <span class="step-label">Detalhes</span>
    </div>
</div>

<div class="screen-body">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>" style="margin: 0 20px 10px 20px;">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <div class="section-label">
        <strong>Localização</strong>
        Confirme o local da ocorrência
    </div>

    <!-- Mapa Interativo Fiel ao Figma -->
    <div class="map-placeholder">
        <div class="map-grid"></div>
        <svg style="position:absolute;inset:0;width:100%;height:100%" viewBox="0 0 375 220">
            <line x1="0" y1="110" x2="375" y2="110" stroke="#fff" stroke-width="8" opacity="0.6"/>
            <line x1="0" y1="70" x2="375" y2="70" stroke="#fff" stroke-width="5" opacity="0.4"/>
            <line x1="0" y1="160" x2="375" y2="160" stroke="#fff" stroke-width="5" opacity="0.4"/>
            <line x1="120" y1="0" x2="120" y2="220" stroke="#fff" stroke-width="6" opacity="0.5"/>
            <line x1="250" y1="0" x2="250" y2="220" stroke="#fff" stroke-width="6" opacity="0.5"/>
            <line x1="60" y1="0" x2="60" y2="220" stroke="#fff" stroke-width="4" opacity="0.3"/>
            <line x1="310" y1="0" x2="310" y2="220" stroke="#fff" stroke-width="4" opacity="0.3"/>
            <rect x="65" y="15" width="50" height="50" rx="4" fill="rgba(255,255,255,0.3)"/>
            <rect x="125" y="15" width="120" height="50" rx="4" fill="rgba(255,255,255,0.25)"/>
            <rect x="65" y="115" width="50" height="40" rx="4" fill="rgba(255,255,255,0.25)"/>
            <rect x="125" y="115" width="120" height="40" rx="4" fill="rgba(255,255,255,0.2)"/>
            <rect x="255" y="15" width="50" height="50" rx="4" fill="rgba(255,255,255,0.25)"/>
            <rect x="255" y="115" width="50" height="40" rx="4" fill="rgba(255,255,255,0.2)"/>
        </svg>
        <div class="map-pin"><div class="map-pin-head"></div></div>
        <div class="map-user-dot"></div>
    </div>

    <!-- Card de Endereço Identificado -->
    <div class="address-card">
        <div class="addr-detail">
            <div class="addr-label">Endereço identificado</div>
            <div class="addr-main" id="addrMainDisplay"><?= sanitize(($sessionData['rua'] ?? 'Rua das Flores, 123') . ' – ' . ($sessionData['bairro'] ?? 'Centro')) ?></div>
            <div class="addr-sub" id="addrSubDisplay"><?= sanitize(($sessionData['cidade'] ?? 'João Pessoa') . ' – ' . ($sessionData['estado'] ?? 'PB')) ?></div>
        </div>
        <span class="edit-icon" id="btnEditarCampos" title="Editar endereço">✏️</span>
    </div>

    <form action="?page=nova-ocorrencia&action=step2" method="POST" style="padding:0 20px 20px;">
        <?= csrf_field() ?>
        <input type="hidden" name="latitude" value="-7.11500000">
        <input type="hidden" name="longitude" value="-34.86300000">

        <div id="camposManuais" style="display: none; margin-bottom: 16px;">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">UF</label>
                    <input type="text" name="estado" class="form-input" value="<?= sanitize($sessionData['estado'] ?? 'PB') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Cidade</label>
                    <input type="text" name="cidade" class="form-input" value="<?= sanitize($sessionData['cidade'] ?? 'João Pessoa') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Bairro</label>
                <input type="text" id="bairro" name="bairro" class="form-input" placeholder="Ex: Centro" value="<?= sanitize($sessionData['bairro'] ?? 'Centro') ?>" required>
            </div>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;">
                <div class="form-group">
                    <label class="form-label">Rua / Avenida</label>
                    <input type="text" id="rua" name="rua" class="form-input" placeholder="Ex: Rua das Flores" value="<?= sanitize($sessionData['rua'] ?? 'Rua das Flores') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Número</label>
                    <input type="text" id="numero" name="numero" class="form-input" placeholder="123" value="<?= sanitize($sessionData['numero'] ?? '123') ?>">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary">Usar esta localização</button>
        <button type="button" id="btnSelecionarMapa" class="btn-secondary">Selecionar no mapa</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('btnEditarCampos');
    const camposManuais = document.getElementById('camposManuais');
    const mapBtn = document.getElementById('btnSelecionarMapa');

    editBtn.addEventListener('click', function() {
        if (camposManuais.style.display === 'none') {
            camposManuais.style.display = 'block';
        } else {
            camposManuais.style.display = 'none';
        }
    });

    mapBtn.addEventListener('click', function() {
        alert('GPS simulado: Localização atualizada para o centro da cidade.');
    });
});
</script>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
