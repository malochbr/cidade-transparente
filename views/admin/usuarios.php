<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
?>
<div class="top-bar">
    <a href="?page=admin/dashboard" class="btn-icon">←</a>
    <span class="top-bar-title">Gerenciar Usuários</span>
    <div style="width: 40px;"></div>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <div style="display: flex; flex-direction: column; gap: 10px;">
        <?php foreach ($usuarios as $u): ?>
            <?php
                $cpfMasked = substr($u['cpf'], 0, 3) . '.***.***-' . substr($u['cpf'], -2);
                $badgeRep = 'badge-green';
                if ($u['reputacao'] < 500) $badgeRep = 'badge-red';
                elseif ($u['reputacao'] < 800) $badgeRep = 'badge-orange';
            ?>
            <div class="card" style="margin: 0; padding: 12px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight: 700; font-size: 14px;"><?= sanitize($u['nome']) ?></span>
                    <span class="badge <?= $u['ativo'] ? 'badge-green' : 'badge-red' ?>"><?= $u['ativo'] ? 'Ativo' : 'Bloqueado' ?></span>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                    CPF: <?= $cpfMasked ?> | Email: <?= sanitize($u['email']) ?>
                </div>
                <div style="font-size: 12px; margin-top: 4px;">
                    Perfil: <strong><?= sanitize($u['perfil']) ?></strong> | Reputação: <span class="badge <?= $badgeRep ?>"><?= $u['reputacao'] ?> pts</span>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 10px;">
                    <form action="?page=admin/usuarios/toggle&id=<?= $u['id'] ?>" method="POST" style="flex:1;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn <?= $u['ativo'] ? 'btn-danger' : 'btn-outline' ?>" style="height: 34px; font-size: 12px;">
                            <?= $u['ativo'] ? 'Desativar' : 'Ativar' ?>
                        </button>
                    </form>

                    <!-- Modal / Form de Ajuste de Reputação -->
                    <form action="?page=admin/usuarios/ajustar-reputacao" method="POST" style="flex:1; display:flex; gap:4px;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="usuario_id" value="<?= $u['id'] ?>">
                        <input type="number" name="pontos" class="form-input" style="height: 34px; padding: 4px 8px; font-size: 12px;" placeholder="+/- Pts" required>
                        <button type="submit" class="btn btn-secondary" style="height: 34px; width: 44px; padding: 0; font-size: 12px;">Salvar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
