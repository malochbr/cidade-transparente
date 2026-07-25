<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();

$badgeClass = 'badge-orange';
$statusText = 'Em andamento';
if ($ocorrencia['status'] === 'encaminhada') { $badgeClass = 'badge-blue'; $statusText = 'Encaminhada'; }
elseif ($ocorrencia['status'] === 'resolvida') { $badgeClass = 'badge-green'; $statusText = 'Resolvida'; }
elseif ($ocorrencia['status'] === 'cancelada') { $badgeClass = 'badge-red'; $statusText = 'Cancelada'; }
elseif ($ocorrencia['status'] === 'atrasada') { $badgeClass = 'badge-red'; $statusText = 'Atrasada'; }

// Icon por categoria
$catIcon = '⚠️';
if ($ocorrencia['categoria'] === 'iluminacao_publica') $catIcon = '💡';
elseif ($ocorrencia['categoria'] === 'alagamento') $catIcon = '💧';
elseif ($ocorrencia['categoria'] === 'terreno_baldio') $catIcon = '🌿';
elseif ($ocorrencia['categoria'] === 'limpeza_urbana') $catIcon = '🗑️';
elseif ($ocorrencia['categoria'] === 'outros') $catIcon = '•••';
?>
<div class="screen-body">
    <div class="detail-header">
        <a href="?page=painel" class="back-btn" title="Voltar">←</a>
        <span class="detail-title">Ocorrência #<?= $ocorrencia['id'] ?></span>
    </div>

    <?php if ($flash): ?>
        <div style="padding: 0 16px 10px;">
            <div class="flash-message flash-<?= sanitize($flash['type']) ?>" style="padding: 12px; border-radius: 8px; font-size: 13px;">
                <span><?= sanitize($flash['message']) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Card Principal de Detalhe -->
    <div class="occ-detail-card">
        <div class="occ-detail-icon"><?= $catIcon ?></div>
        <div class="occ-detail-info">
            <div class="occ-detail-title"><?= sanitize($ocorrencia['titulo']) ?></div>
            <span class="badge <?= $badgeClass ?>" style="display:inline-block; margin: 2px 0 4px;"><?= $statusText ?></span>
            <div class="occ-detail-addr"><?= sanitize(($ocorrencia['rua'] ?? '') . ', ' . ($ocorrencia['numero'] ?? '') . ' – ' . ($ocorrencia['bairro'] ?? '')) ?></div>
            <div class="occ-detail-time">🕙 <?= date('d/m/Y, H:i', strtotime($ocorrencia['data_registro'])) ?></div>
        </div>
    </div>

    <!-- Abas: Detalhes | Atualizações | Apoios -->
    <div class="detail-tabs">
        <div class="detail-tab active" data-tab="tabUpdates" onclick="switchDetailTab('tabUpdates', this)">Atualizações</div>
        <div class="detail-tab" data-tab="tabDetails" onclick="switchDetailTab('tabDetails', this)">Detalhes</div>
        <div class="detail-tab" data-tab="tabSupports" onclick="switchDetailTab('tabSupports', this)">Apoios (<?= $ocorrencia['total_apoios'] ?>)</div>
    </div>

    <!-- Aba 1: Atualizações (Timeline) -->
    <div id="tabUpdates" class="tab-content" style="display: block;">
        <div class="timeline">
            <?php if (empty($ocorrencia['historico'])): ?>
                <div class="timeline-item">
                    <div class="tl-dot"></div>
                    <div class="tl-content">
                        <div class="tl-time"><?= date('d/m/Y, H:i', strtotime($ocorrencia['data_registro'])) ?></div>
                        <div class="tl-text">Ocorrência registrada pelo cidadão</div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($ocorrencia['historico'] as $hist): ?>
                    <div class="timeline-item">
                        <div class="tl-dot"></div>
                        <div class="tl-content">
                            <div class="tl-time"><?= date('d/m/Y, H:i', strtotime($hist['data'])) ?></div>
                            <div class="tl-text"><?= sanitize($hist['observacao'] ?? ('Status atualizado para: ' . strtoupper(str_replace('_', ' ', $hist['status_novo'])))) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Aba 2: Detalhes (Descrição + Mídias) -->
    <div id="tabDetails" class="tab-content" style="display: none; padding: 0 16px;">
        <div style="background: var(--gray-50); border: 1px solid var(--border); border-radius: 12px; padding: 16px;">
            <h4 style="font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 6px;">Descrição</h4>
            <p style="font-size: 13px; line-height: 1.5; color: var(--text);"><?= nl2br(sanitize($ocorrencia['descricao'])) ?></p>

            <?php if (!empty($ocorrencia['midias'])): ?>
                <h4 style="font-size: 14px; font-weight: 700; color: var(--text); margin: 16px 0 8px 0;">Mídias registradas</h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                    <?php foreach ($ocorrencia['midias'] as $m): ?>
                        <div onclick="openLightbox('<?= sanitize($m['arquivo']) ?>', '<?= sanitize($m['tipo']) ?>')" style="height: 80px; border-radius: 8px; overflow: hidden; background: #000; cursor: pointer; position: relative;">
                            <?php if ($m['tipo'] === 'video'): ?>
                                <video src="<?= sanitize($m['arquivo']) ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;"></video>
                                <span style="position: absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:#fff; font-size:20px;">▶</span>
                            <?php else: ?>
                                <img src="<?= sanitize($m['arquivo']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Aba 3: Apoios -->
    <div id="tabSupports" class="tab-content" style="display: none; padding: 0 16px;">
        <div style="background: var(--gray-50); border: 1px solid var(--border); border-radius: 12px; padding: 20px; text-align: center;">
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">
                Esta ocorrência conta com <strong><?= $ocorrencia['total_apoios'] ?></strong> <?= $ocorrencia['total_apoios'] == 1 ? 'apoio' : 'apoios' ?> da comunidade.
            </p>

            <?php if (isLoggedIn()): ?>
                <form action="?page=ocorrencia/apoiar&id=<?= $ocorrencia['id'] ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="support-btn" style="width: 100%; margin: 0;">
                        <span class="thumb">👍</span>
                        <?= $userApoiou ? 'Apoio registrado (Clique para remover)' : 'Apoiar ocorrência' ?>
                    </button>
                </form>
            <?php else: ?>
                <a href="?page=auth/login" class="support-btn" style="width: 100%; margin: 0; text-decoration: none;">
                    <span class="thumb">👍</span> Faça login para apoiar
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botão de Apoio Direto Embaixo da Timeline -->
    <div style="margin-top: 8px;">
        <?php if (isLoggedIn()): ?>
            <form action="?page=ocorrencia/apoiar&id=<?= $ocorrencia['id'] ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="support-btn">
                    <span class="thumb">👍</span>
                    <?= $userApoiou ? 'Apoio registrado' : 'Apoiar ocorrência' ?>
                </button>
            </form>
        <?php else: ?>
            <a href="?page=auth/login" class="support-btn">
                <span class="thumb">👍</span> Apoiar ocorrência
            </a>
        <?php endif; ?>
    </div>

    <!-- Seção de Comentários -->
    <div style="padding: 12px 16px 24px;">
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 10px;">Comentários (<?= count($ocorrencia['comentarios']) ?>)</h4>
        <?php if (empty($ocorrencia['comentarios'])): ?>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Seja o primeiro a comentar nesta ocorrência.</p>
        <?php else: ?>
            <?php foreach ($ocorrencia['comentarios'] as $com): ?>
                <div style="background: var(--gray-50); padding: 10px 12px; border-radius: 10px; margin-bottom: 8px; border: 1px solid var(--border);">
                    <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; margin-bottom:3px;">
                        <span><?= sanitize($com['usuario_nome']) ?></span>
                        <span style="font-weight:400; color:var(--text-muted);"><?= date('d/m, H:i', strtotime($com['data'])) ?></span>
                    </div>
                    <div style="font-size:13px; color:var(--text); line-height: 1.4;"><?= sanitize($com['texto']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
            <form action="?page=ocorrencia/comentar&id=<?= $ocorrencia['id'] ?>" method="POST" style="margin-top: 12px;">
                <?= csrf_field() ?>
                <textarea name="texto" class="textarea-field" style="min-height: 64px; font-size: 13px;" placeholder="Escreva um comentário público..." required></textarea>
                <button type="submit" class="btn-primary" style="padding: 12px; font-size: 14px; margin-top: 8px;">Enviar comentário</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function switchDetailTab(tabId, el) {
    document.querySelectorAll('.detail-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
    
    el.classList.add('active');
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
}
</script>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
