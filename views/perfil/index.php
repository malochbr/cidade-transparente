<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();

$user = getCurrentUser();
$reputacao = $user['reputacao'] ?? 1250;

$repLabel = 'Reputação Ótima';
if ($reputacao < 500) {
    $repLabel = 'Reputação Baixa';
} elseif ($reputacao < 800) {
    $repLabel = 'Reputação Boa';
}

$handle = !empty($user['email']) ? explode('@', $user['email'])[0] : 'paulosergio';
$nomeUsuario = $user['nome'] ?? 'Paulo Sergio';
$unreadNotifsCount = $unreadNotifsCount ?? 2;
?>
<div class="screen-body">
    <div class="profile-top">
        <span style="font-size: 20px;">☰</span>
        <span class="profile-top-title">Meu perfil</span>
        <a href="?page=perfil/meus-dados" class="settings-icon" title="Configurações">⚙️</a>
    </div>

    <?php if ($flash): ?>
        <div style="padding: 0 16px 10px;">
            <div class="flash-message flash-<?= sanitize($flash['type']) ?>" style="padding: 12px; border-radius: 8px; font-size: 13px;">
                <span><?= sanitize($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Card de Perfil Fiel ao Protótipo -->
    <div class="profile-card">
        <div class="avatar">👤</div>
        <div>
            <div class="profile-name"><?= sanitize($nomeUsuario) ?></div>
            <div class="profile-handle">@<?= sanitize($handle) ?></div>
            <div class="profile-badges">
                <div class="profile-badge"><span class="star">⭐</span> <?= $repLabel ?></div>
                <div class="profile-badge"><span class="star">⭐</span> <?= number_format($reputacao, 0, ',', '.') ?> pontos</div>
            </div>
        </div>
    </div>

    <!-- Lista de Opções -->
    <div class="menu-list">
        <a href="?page=perfil/meus-dados" class="menu-item">
            <div class="menu-icon-wrap">👤</div>
            <span class="menu-item-label">Meus dados</span>
            <span class="menu-item-arrow">›</span>
        </a>

        <a href="?page=ocorrencias" class="menu-item">
            <div class="menu-icon-wrap">📋</div>
            <span class="menu-item-label">Minhas ocorrências</span>
            <span class="menu-item-arrow">›</span>
        </a>

        <a href="?page=notificacoes" class="menu-item">
            <div class="menu-icon-wrap">🔔</div>
            <span class="menu-item-label">Notificações</span>
            <?php if ($unreadNotifsCount > 0): ?>
                <span class="menu-notif-badge"><?= $unreadNotifsCount ?></span>
            <?php endif; ?>
            <span class="menu-item-arrow">›</span>
        </a>

        <div class="menu-divider"></div>

        <a href="?page=perfil/alterar-senha" class="menu-item">
            <div class="menu-icon-wrap">🛡️</div>
            <span class="menu-item-label">Segurança</span>
            <span class="menu-item-arrow">›</span>
        </a>

        <div class="menu-item">
            <div class="menu-icon-wrap">❓</div>
            <span class="menu-item-label">Ajuda e suporte</span>
            <span class="menu-item-arrow">›</span>
        </div>

        <?php if (getUserRole() === 'administrador'): ?>
            <div class="menu-divider"></div>
            <a href="?page=admin/dashboard" class="menu-item">
                <div class="menu-icon-wrap" style="background:var(--orange-bg);">⚙️</div>
                <span class="menu-item-label" style="color:var(--orange); font-weight:700;">Painel Administrativo</span>
                <span class="menu-item-arrow" style="color:var(--orange);">›</span>
            </a>
        <?php endif; ?>

        <div class="menu-divider"></div>

        <a href="?page=auth/logout" class="menu-item danger">
            <div class="menu-icon-wrap" style="background:#FFEBEE">🚪</div>
            <span class="menu-item-label" style="color:#E53935">Sair</span>
            <span class="menu-item-arrow" style="color:#E53935">›</span>
        </a>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
