<?php
require_once __DIR__ . '/../layout/header.php';
$flash = getFlash();
?>
<div class="top-bar">
    <a href="?page=perfil" class="btn-icon">←</a>
    <span class="top-bar-title">Meus dados</span>
    <div style="width: 40px;"></div>
</div>

<div class="content">
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= sanitize($flash['type']) ?>">
            <span><?= sanitize($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="flash-message flash-error">
            <div>
                <?php foreach ($errors as $err): ?><p>• <?= sanitize($err) ?></p><?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <form action="?page=perfil/meus-dados" method="POST">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label">Nome Completo</label>
            <input type="text" name="nome" class="form-input" value="<?= sanitize($user['nome']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">CPF (somente leitura)</label>
            <input type="text" class="form-input" value="<?= sanitize($user['cpf']) ?>" disabled style="background: var(--gray-100); opacity: 0.7;">
        </div>

        <div class="form-group">
            <label class="form-label">E-mail (somente leitura)</label>
            <input type="text" class="form-input" value="<?= sanitize($user['email']) ?>" disabled style="background: var(--gray-100); opacity: 0.7;">
        </div>

        <div class="form-group">
            <label class="form-label">Telefone / WhatsApp</label>
            <input type="text" name="telefone" class="form-input" data-mask="telefone" value="<?= sanitize($user['telefone'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Salvar alterações</button>
    </form>
</div>

<?php
require_once __DIR__ . '/../layout/nav.php';
require_once __DIR__ . '/../layout/footer.php';
?>
