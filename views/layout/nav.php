<?php
/**
 * Bottom Navigation Bar Layout Template — Fiel ao Figma
 */
$activePage = $activePage ?? 'home';

// Contagem de notificações não lidas
$unreadNotifsCount = 0;
if (isLoggedIn()) {
    require_once __DIR__ . '/../../models/Notificacao.php';
    $notifModel = new Notificacao(Database::getInstance());
    $unreadNotifsCount = $notifModel->countNaoLidas($_SESSION['user_id']);
}
?>
<nav class="bottom-nav">
    <a href="?page=home" class="nav-item <?= $activePage === 'home' ? 'active' : '' ?>">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 0v9h4v-9m-4 0H6m9 9v-9m0 0l2 2"/></svg>
        <span>Início</span>
    </a>
    <a href="?page=ocorrencias" class="nav-item <?= $activePage === 'ocorrencias' ? 'active' : '' ?>">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        <span>Ocorrências</span>
    </a>
    <a href="?page=nova-ocorrencia" class="nav-center-btn" title="Nova Ocorrência">+</a>
    <a href="?page=painel" class="nav-item <?= $activePage === 'painel' ? 'active' : '' ?>">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Painel</span>
    </a>
    <a href="?page=perfil" class="nav-item <?= $activePage === 'perfil' ? 'active' : '' ?>" style="position:relative;">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        <span>Perfil</span>
        <?php if ($unreadNotifsCount > 0): ?>
            <span class="notif-badge" style="top:2px; right:12px;"><?= $unreadNotifsCount > 9 ? '9+' : $unreadNotifsCount ?></span>
        <?php endif; ?>
    </a>
</nav>
