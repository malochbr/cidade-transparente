<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();

$nomeUsuario = (!empty($user) && !empty($user['nome'])) ? $user['nome'] : 'Cidadão';
$inicial = strtoupper(mb_substr($nomeUsuario, 0, 1, 'UTF-8'));
$primeiroNome = sanitize(explode(' ', $nomeUsuario)[0] ?? 'Cidadão');
?>
<div class="top-bar">
    <div style="display:flex; align-items:center; gap:10px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--green-bg); color: var(--green); display:flex; align-items:center; justify-content:center; font-weight:700;">
            <?= $inicial ?>
        </div>
        <div>
            <div style="font-size:14px; font-weight:700; color:var(--text);">Olá, <?= $primeiroNome ?></div>
            <div style="font-size:11px; color:var(--text-muted);">Bem-vindo de volta!</div>
        </div>
    </div>
    <a href="?page=notificacoes" class="btn-icon" title="Notificações" style="position:relative;">
        🔔
    </a>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Banner CTA Verde -->
    <div style="background: var(--green); border-radius: var(--radius); padding: 18px; color: #fff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(27,107,53,0.25);">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Nova ocorrência</h3>
            <p style="font-size: 12px; opacity: 0.9;">Registre um problema na sua cidade</p>
        </div>
        <a href="?page=nova-ocorrencia" style="width: 36px; height: 36px; border-radius: 50%; background: #ffffff; color: var(--green); display: flex; align-items: center; justify-content: center; font-size: 22px; text-decoration: none; font-weight: 700;">+</a>
    </div>

    <!-- Minhas Ocorrências Recentes -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
        <h3 style="font-size: 16px; font-weight: 700;">Minhas ocorrências</h3>
        <a href="?page=ocorrencias" style="font-size: 13px; color: var(--green); font-weight: 600; text-decoration: none;">Ver todas</a>
    </div>

    <?php if (empty($minhasOcorrencias)): ?>
        <div class="card" style="text-align: center; padding: 24px 16px; color: var(--text-muted);">
            <div style="font-size: 32px; margin-bottom: 8px;">📋</div>
            <p style="font-size: 14px;">Você ainda não registrou nenhuma ocorrência.</p>
        </div>
    <?php else: ?>
        <?php foreach ($minhasOcorrencias as $oc): ?>
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
                    📍 <?= sanitize($oc['rua'] . ', ' . $oc['bairro']) ?>
                </div>
                <div class="footer">
                    <span>🕒 <?= date('d/m/Y H:i', strtotime($oc['data_registro'])) ?></span>
                    <span><?= sanitize($oc['secretaria_nome'] ?? 'Aguardando atribuição') ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Banner Painel Público -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin: 24px 0 12px 0;">
        <h3 style="font-size: 16px; font-weight: 700;">Painel público</h3>
        <a href="?page=painel" style="font-size: 13px; color: var(--green); font-weight: 600; text-decoration: none;">Ver painel</a>
    </div>

    <a href="?page=painel" style="display:flex; align-items:center; justify-content:space-between; background: var(--gray-50); border: 1px solid var(--gray-200); padding: 14px 16px; border-radius: var(--radius); text-decoration:none; color: inherit;">
        <div style="font-size: 13px; color: var(--text-muted); max-width: 80%;">
            Acompanhe as ocorrências da sua região e ajude a transformar a cidade.
        </div>
        <div style="font-size: 24px;">📈</div>
    </a>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
