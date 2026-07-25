<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
?>
<div class="top-bar">
    <a href="?page=home" class="btn-icon">←</a>
    <span class="top-bar-title">Notificações</span>
    <div style="width: 40px;"></div>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
        <span style="font-size: 13px; color: var(--text-muted);">Suas atualizações recentes</span>
        <?php if (!empty($notificacoes)): ?>
            <form action="?page=notificacoes/marcar-todas" method="POST" style="margin: 0;">
                <?= csrf_field() ?>
                <button type="submit" style="background:none; border:none; color:var(--green); font-size:12px; font-weight:600; cursor:pointer;">
                    Marcar todas como lidas
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($notificacoes)): ?>
        <div class="card" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
            <div style="font-size: 36px; margin-bottom: 8px;">🔔</div>
            <p style="font-size: 15px; font-weight: 600;">Nenhuma notificação</p>
            <p style="font-size: 13px; margin-top: 4px;">Você está em dia com todas as atualizações.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notificacoes as $n): ?>
            <a href="?page=notificacoes/marcar-lida&id=<?= $n['id'] ?><?= $n['ocorrencia_id'] ? '&ocorrencia_id='.$n['ocorrencia_id'] : '' ?>" 
               class="card" 
               style="display: block; text-decoration: none; color: inherit; margin-bottom: 10px; border-left: 4px solid <?= $n['visualizada'] ? 'var(--gray-300)' : 'var(--green)' ?>; background: <?= $n['visualizada'] ? '#fff' : 'var(--green-bg)' ?>;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 4px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">
                        <?= $n['visualizada'] ? 'Lida' : '🔔 Nova' ?>
                    </span>
                    <span style="font-size: 11px; color: var(--text-muted);"><?= date('d/m/Y H:i', strtotime($n['data'])) ?></span>
                </div>
                <div style="font-size: 14px; font-weight: <?= $n['visualizada'] ? '400' : '600' ?>; color: var(--text);">
                    <?= sanitize($n['mensagem']) ?>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
