<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
?>
<div class="top-bar">
    <a href="?page=home" class="btn-icon">←</a>
    <span class="top-bar-title">Minhas Ocorrências</span>
    <a href="?page=nova-ocorrencia" class="btn-icon" title="Nova Ocorrência">+</a>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (empty($ocorrencias)): ?>
        <div class="card" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
            <div style="font-size: 36px; margin-bottom: 8px;">📋</div>
            <p style="font-size: 15px; font-weight: 600;">Nenhuma ocorrência registrada</p>
            <p style="font-size: 13px; margin-top: 4px;">Você ainda não registrou nenhum problema na sua cidade.</p>
            <a href="?page=nova-ocorrencia" class="btn btn-primary" style="margin-top: 16px;">Registrar ocorrência</a>
        </div>
    <?php else: ?>
        <?php foreach ($ocorrencias as $oc): ?>
            <?php
                $badgeClass = 'badge-orange';
                $statusText = 'Em andamento';
                if ($oc['status'] === 'encaminhada') { $badgeClass = 'badge-blue'; $statusText = 'Encaminhada'; }
                elseif ($oc['status'] === 'resolvida') { $badgeClass = 'badge-green'; $statusText = 'Resolvida'; }
                elseif ($oc['status'] === 'cancelada') { $badgeClass = 'badge-red'; $statusText = 'Cancelada'; }
                elseif ($oc['status'] === 'atrasada') { $badgeClass = 'badge-red'; $statusText = 'Atrasada'; }
            ?>
            <a href="?page=ocorrencia/detalhe&id=<?= $oc['id'] ?>" class="card occurrence-card">
                <div class="header">
                    <span class="id-tag">#<?= $oc['id'] ?></span>
                    <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                </div>
                <div class="title"><?= sanitize($oc['titulo']) ?></div>
                <div class="location">
                    📍 <?= sanitize(($oc['rua'] ?? '') . ', ' . ($oc['bairro'] ?? '')) ?>
                </div>
                <div class="footer">
                    <span>🕒 <?= date('d/m/Y H:i', strtotime($oc['data_registro'])) ?></span>
                    <span><?= sanitize($oc['secretaria_nome'] ?? 'Em triagem') ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
