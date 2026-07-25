<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
?>
<div class="top-bar">
    <a href="?page=admin/dashboard" class="btn-icon">←</a>
    <span class="top-bar-title">Gerenciar Ocorrências</span>
    <div style="width: 40px;"></div>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <form action="?page=admin/ocorrencias" method="GET" style="margin-bottom: 16px;">
        <input type="hidden" name="page" value="admin/ocorrencias">
        <div style="display: flex; gap: 8px;">
            <input type="text" name="search" class="form-input" placeholder="Buscar por id ou título..." value="<?= sanitize($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn btn-primary" style="width: 80px; height: 44px;">Filtrar</button>
        </div>
    </form>

    <div style="display: flex; flex-direction: column; gap: 10px;">
        <?php foreach ($ocorrencias as $oc): ?>
            <div class="card" style="margin: 0; padding: 12px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span class="id-tag">#<?= $oc['id'] ?> | <?= sanitize($oc['categoria']) ?></span>
                    <span class="badge badge-gray"><?= sanitize($oc['status']) ?></span>
                </div>
                <div style="font-weight: 700; font-size: 14px; margin: 4px 0;"><?= sanitize($oc['titulo']) ?></div>
                <div style="font-size: 12px; color: var(--text-muted);">
                    Cidadão: <?= sanitize($oc['usuario_nome'] ?? 'Anônimo') ?>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 10px;">
                    <a href="?page=ocorrencia/detalhe&id=<?= $oc['id'] ?>" class="btn btn-secondary" style="height: 34px; font-size: 12px;">Ver Detalhes</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
