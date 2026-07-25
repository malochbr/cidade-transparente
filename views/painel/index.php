<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
$currentStatus = $_GET['status'] ?? '';
?>
<div class="screen-body" style="padding: 0;">
    <!-- Top Sticky Header: Title, Search & Filter Chips -->
    <div class="sticky-header">
        <div style="padding: 14px 20px 4px; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 20px;">☰</span>
            <span style="font-size: 17px; font-weight: 800; color: var(--text);">Painel público</span>
        </div>
        <div style="padding: 0 20px 8px; font-size: 12px; color: var(--text-muted);">Acompanhe as ocorrências da sua região</div>

        <?php if ($flash): ?>
            <div style="padding: 0 20px 8px;">
                <div class="flash-message flash-<?= sanitize($flash['type']) ?>" style="padding: 10px 12px; border-radius: 8px; font-size: 13px;">
                    <span><?= sanitize($flash['message']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search Bar & Filter Button -->
        <form action="?page=painel" method="GET" class="search-bar" style="margin: 4px 16px 8px;">
            <input type="hidden" name="page" value="painel">
            <?php if ($currentStatus): ?>
                <input type="hidden" name="status" value="<?= sanitize($currentStatus) ?>">
            <?php endif; ?>
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Buscar ocorrência..." value="<?= sanitize($_GET['search'] ?? '') ?>">
            </div>
            <button type="submit" class="filter-btn" title="Filtrar">⚙</button>
        </form>

        <!-- Filter Chips Row -->
        <div class="chips-row" style="padding: 0 16px 10px;">
            <a href="?page=painel" class="chip <?= empty($currentStatus) ? 'active' : '' ?>">Todas</a>
            <a href="?page=painel&status=em_andamento" class="chip <?= $currentStatus === 'em_andamento' ? 'active' : '' ?>">Em andamento</a>
            <a href="?page=painel&status=encaminhada" class="chip <?= $currentStatus === 'encaminhada' ? 'active' : '' ?>">Encaminhadas</a>
            <a href="?page=painel&status=resolvida" class="chip <?= $currentStatus === 'resolvida' ? 'active' : '' ?>">Resolvidas</a>
            <a href="?page=painel&status=atrasada" class="chip <?= $currentStatus === 'atrasada' ? 'active' : '' ?>">Atrasadas</a>
        </div>
    </div>

    <!-- Occurrences List Scroll Area -->
    <div class="occurrences-scroll-list" style="padding: 12px 16px 24px;">
        <?php if (empty($ocorrencias)): ?>
            <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                <div style="font-size: 36px; margin-bottom: 8px;">🔍</div>
                <p style="font-size: 15px; font-weight: 700; color: var(--text);">Nenhuma ocorrência encontrada</p>
                <p style="font-size: 13px; margin-top: 4px;">Tente alterar os termos da busca ou filtros.</p>
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

                    $apoiosCount = $oc['total_apoios'] ?? 0;
                    $comentCount = $oc['total_comentarios'] ?? 0;
                    $dateFormatted = date('d/m/Y', strtotime($oc['data_registro']));
                ?>
                <a href="?page=ocorrencia/detalhe&id=<?= $oc['id'] ?>" class="occ-list-card">
                    <div class="occ-list-top">
                        <div class="occ-list-title"><?= sanitize($oc['titulo']) ?></div>
                        <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                    </div>
                    <div class="occ-list-addr"><?= sanitize(($oc['rua'] ?? '') . ', ' . ($oc['numero'] ?? '') . ' – ' . ($oc['bairro'] ?? '')) ?></div>
                    <div class="occ-list-meta">
                        <span>👍 <?= $apoiosCount ?> <?= $apoiosCount === 1 ? 'apoio' : 'apoios' ?></span>
                        <span>💬 <?= $comentCount ?> <?= $comentCount === 1 ? 'comentário' : 'comentários' ?></span>
                        <span style="margin-left: auto;"><?= $dateFormatted ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
