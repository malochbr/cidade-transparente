<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
?>
<div class="top-bar">
    <a href="?page=perfil" class="btn-icon">←</a>
    <span class="top-bar-title">Painel Admin</span>
    <div style="width: 40px;"></div>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 8px; margin-bottom: 16px;">
        <a href="?page=admin/ocorrencias" class="btn btn-outline" style="height: 38px; font-size: 12px; flex: 1;">📋 Ocorrências</a>
        <a href="?page=admin/usuarios" class="btn btn-outline" style="height: 38px; font-size: 12px; flex: 1;">👥 Usuários</a>
    </div>

    <!-- Cards de Métricas em Grid -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 20px;">
        <div class="card" style="padding: 12px; text-align: center; border-left: 4px solid var(--blue);">
            <div style="font-size: 11px; color: var(--text-muted);">Total Ocorrências</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--text); margin-top: 2px;"><?= $stats['total'] ?></div>
        </div>

        <div class="card" style="padding: 12px; text-align: center; border-left: 4px solid var(--orange);">
            <div style="font-size: 11px; color: var(--text-muted);">Em Andamento</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--orange); margin-top: 2px;"><?= $stats['em_andamento'] ?></div>
        </div>

        <div class="card" style="padding: 12px; text-align: center; border-left: 4px solid var(--green);">
            <div style="font-size: 11px; color: var(--text-muted);">Resolvidas</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--green); margin-top: 2px;"><?= $stats['resolvida'] ?></div>
        </div>

        <div class="card" style="padding: 12px; text-align: center; border-left: 4px solid var(--red);">
            <div style="font-size: 11px; color: var(--text-muted);">Atrasadas</div>
            <div style="font-size: 22px; font-weight: 800; color: var(--red); margin-top: 2px;"><?= $stats['atrasada'] ?></div>
        </div>
    </div>

    <!-- Ocorrências Recentes -->
    <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 10px;">Recentes para Moderação</h3>

    <?php foreach ($recentes as $oc): ?>
        <a href="?page=ocorrencia/detalhe&id=<?= $oc['id'] ?>" class="card occurrence-card" style="margin-bottom: 8px; padding: 12px;">
            <div class="header">
                <span class="id-tag">#<?= $oc['id'] ?> — <?= sanitize($oc['categoria']) ?></span>
                <span class="badge badge-orange"><?= sanitize($oc['status']) ?></span>
            </div>
            <div class="title" style="font-size: 14px;"><?= sanitize($oc['titulo']) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
